/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Oct 09 2018
 *  File : WinFormImsAssetFarmerAPDRcp.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - CallerStore (Store Grid Farmer APD)
    - IMSID
    - FarmerID
    - FarmerName
*/

Ext.define('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcp' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcp',
    title: lang('IMS - Farmer APD Receipt'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '90%',
    height: '48%',
    overflowY: 'auto',    
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.store_farmer_apd_receipt = Ext.create('Koltiva.store.IMS.ImsAssetRcpGridFarmerApdRcp',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID,
                FarmerID: thisObj.viewVar.FarmerID
            }
        });

        thisObj.ContextMenuMainGrid = Ext.create('Ext.menu.Menu',{
            cls:'Sfr_ConMenu',
            items:[{                
                icon: varjs.config.base_url + 'images/icons/new/printout.png',
                text: lang('Print Receipt'),
                cls:'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetFarmerAPDRcp-GridMain').getSelectionModel().getSelection()[0];
                    preview_cetak_surat(m_api+'/ims_asset_rcp/farmer_apd_rcp_printout/'+sm.get('RcpID')+'/'+thisObj.viewVar.IMSID);
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/page_edit.png',
                text: lang('Update / Set Received Status'),
                cls:'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetFarmerAPDRcp-GridMain').getSelectionModel().getSelection()[0];
                    
                    var WinFormImsAssetFarmerAPDRcpMaster = Ext.create('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster',{
                        viewVar: {
                            CallerStore: thisObj.store_farmer_apd_receipt,
                            IMSID:thisObj.viewVar.IMSID,
                            FarmerID: thisObj.viewVar.FarmerID,
                            FarmerName: thisObj.viewVar.FarmerName,
                            OpsiDisplay: 'update',
                            RcpID: sm.get('RcpID')
                        }
                    });
                    if (!WinFormImsAssetFarmerAPDRcpMaster.isVisible()) {
                        WinFormImsAssetFarmerAPDRcpMaster.center();
                        WinFormImsAssetFarmerAPDRcpMaster.show();
                    } else {
                        WinFormImsAssetFarmerAPDRcpMaster.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetFarmerAPDRcp-GridMain').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims_asset_rcp/farmer_apd_rcp',
                                method: 'DELETE',
                                params: {
                                    RcpID: sm.get('RcpID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    thisObj.store_farmer_apd_receipt.load();
                                },
                                failure: function(response, o) {
                                    var pesanNya;
                                    if(o.result.message != undefined){
                                        pesanNya = o.result.message;
                                    }else{
                                        pesanNya = lang('Connection error');
                                    }
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: pesanNya,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        }
                    });

                }
            }]
        });
        
        thisObj.items = [{
            xtype: 'form',
            padding:'5 25 5 8',
            items:[{
                xtype: 'gridpanel',
                title: lang('Receipt Data'),
                id: 'Koltiva.view.IMS.WinImsAssetFarmerAPDRcp-GridMain',
                style: 'border:1px solid #CCC;margin:4px;',
                store: thisObj.store_farmer_apd_receipt,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No Data Available')
                },
                dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/new/add.png',
                        margin: '0px 0px 0px 6px',
                        hidden: m_act_add,
                        cls:'Sfr_BtnGridGreen',
                        overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Add Receipt'),
                        handler: function() {
                            var WinFormImsAssetFarmerAPDRcpMaster = Ext.create('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster');
                            WinFormImsAssetFarmerAPDRcpMaster.setViewVar({
                                CallerStore: thisObj.store_farmer_apd_receipt,
                                IMSID:thisObj.viewVar.IMSID,
                                FarmerID: thisObj.viewVar.FarmerID,
                                FarmerName: thisObj.viewVar.FarmerName,
                                OpsiDisplay: 'insert'
                            });
                            if (!WinFormImsAssetFarmerAPDRcpMaster.isVisible()) {
                                WinFormImsAssetFarmerAPDRcpMaster.center();
                                WinFormImsAssetFarmerAPDRcpMaster.show();
                            } else {
                                WinFormImsAssetFarmerAPDRcpMaster.close();
                            }
                        }
                    }]
                }],
                columns: [{
                    dataIndex: 'RcpID',
                    hidden: true
                },{
                    text: lang('Action'),
                    xtype:'actioncolumn',
                    width: '6%',
                    items:[{
                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                        tooltip: 'Action',
                        handler: function(grid, rowIndex, colIndex, item, e, record) {
                            thisObj.ContextMenuMainGrid.showAt(e.getXY());
                        }
                    }]
                },{
                    text: lang('Transaction Number'),
                    width: '14%',
                    dataIndex: 'RcpTransNumber'
                },{
                    text: lang('Date'),
                    width: '10%',
                    dataIndex: 'RcpDate'
                },{
                    text: lang('APD - Kotak Pestisida'),
                    width: '10%',
                    //dataIndex: 'KotakPestisida'
                    renderer: function (t, meta, record) {
                        var data = record.getData();
                        var DisplayColumn;

                        if(parseInt(data.KotakPestisida) > 0){
                            DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                        }else{
                            DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                        }

                        return DisplayColumn;
                    }
                },{
                    text: lang('APD - Masker'),
                    width: '10%',
                    //dataIndex: 'Masker'
                    renderer: function (t, meta, record) {
                        var data = record.getData();
                        var DisplayColumn;

                        if(parseInt(data.Masker) > 0){
                            DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                        }else{
                            DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                        }

                        return DisplayColumn;
                    }
                },{
                    text: lang('APD - Sarung Tangan'),
                    width: '10%',
                    //dataIndex: 'SarungTangan'
                    renderer: function (t, meta, record) {
                        var data = record.getData();
                        var DisplayColumn;

                        if(parseInt(data.SarungTangan) > 0){
                            DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                        }else{
                            DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                        }

                        return DisplayColumn;
                    }
                },{
                    text: lang('APD - Goggles'),
                    width: '10%',
                    //dataIndex: 'Goggles'
                    renderer: function (t, meta, record) {
                        var data = record.getData();
                        var DisplayColumn;

                        if(parseInt(data.Goggles) > 0){
                            DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                        }else{
                            DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                        }

                        return DisplayColumn;
                    }
                },{
                    text: lang('APD - Boots'),
                    width: '10%',
                    //dataIndex: 'Boots'
                    renderer: function (t, meta, record) {
                        var data = record.getData();
                        var DisplayColumn;

                        if(parseInt(data.Boots) > 0){
                            DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                        }else{
                            DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                        }

                        return DisplayColumn;
                    }
                },{
                    text: lang('APD - Mantel'),
                    width: '10%',
                    //dataIndex: 'Mantel'
                    renderer: function (t, meta, record) {
                        var data = record.getData();
                        var DisplayColumn;

                        if(parseInt(data.Mantel) > 0){
                            DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                        }else{
                            DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                        }

                        return DisplayColumn;
                    }
                },{
                    text: lang('Received Status'),
                    width: '9%',
                    //dataIndex: 'ReceiverStatus'
                    renderer: function (t, meta, record) {
                        var data = record.getData();
                        var DisplayColumn;

                        if(data.ReceiverStatus == '1'){
                            DisplayColumn = lang('Yes');
                        }else{
                            DisplayColumn = lang('No');
                        }

                        return DisplayColumn;
                    }
                }]
            }]
        }];

        thisObj.buttons = [{
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            thisObj.setTitle(lang('IMS - Farmer APD Receipt')+' ['+thisObj.viewVar.FarmerID+' - '+thisObj.viewVar.FarmerName+']')
        }
    }
});