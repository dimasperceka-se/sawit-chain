/*
* @Author: nikolius
* @Date:   2017-07-28 10:28:56
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-31 15:22:27
*/

/*
    Param2 yg diperlukan ketika load View ini
    - MillID
    - CallFrom
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.SME.SPCodePanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.SME.SPCodePanel',
    title: lang('SPB Code'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridSPCodePanel = Ext.create('Koltiva.store.SME.GridSPCodePanel', {
            storeVar: {
                MemberID: thisObj.viewVar.MemberID,
                CallFrom: thisObj.viewVar.CallFrom
            }
        });

        //context menu
        var contextMenuGridSPCode = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('Edit'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.SME.SPCodePanel-gridSPCode').getSelectionModel().getSelection()[0];

                    var WinFormSPCode = Ext.create('Koltiva.view.SME.WinFormSPCode', {
                        viewVar: {
                            SMESPCodeID: sm.get('SMESPCodeID'),
                            MemberID: thisObj.viewVar.MemberID,
                            OpsiDisplay: 'update',
                            CallerStore: thisObj.MainGrid,
                            CallFrom: thisObj.viewVar.CallFrom
                        }
                    });
                    if (!WinFormSPCode.isVisible()) {
                        WinFormSPCode.center();
                        WinFormSPCode.show();
                    } else {
                        WinFormSPCode.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.SME.SPCodePanel-gridSPCode').getSelectionModel().getSelection()[0];
    
                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/sme/submit_sp_code',
                                method: 'DELETE',
                                params: {
                                    SMESPCodeID: sm.get('SMESPCodeID')
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
                                    Ext.getCmp('Koltiva.view.SME.SPCodePanel-gridSPCode').getStore().load();
                                },
                                failure: function(response, opts) {
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
            xtype: 'grid',
            id: 'Koltiva.view.SME.SPCodePanel-gridSPCode',
            loadMask: true,
            minHeight:125,
            selType: 'rowmodel',
            store: storeGridSPCodePanel,
            scroll: false,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    id: 'Koltiva.view.SME.SPCodePanel.btnAdd',
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    handler: function() {
                        var WinFormSPCode = Ext.create('Koltiva.view.SME.WinFormSPCode', {
                            viewVar: {
                                MemberID: thisObj.viewVar.MemberID,
                                OpsiDisplay: 'insert',
                                CallerStore: thisObj.MainGrid,
                                CallFrom: thisObj.viewVar.CallFrom
                            }
                        });
                        if (!WinFormSPCode.isVisible()) {
                            WinFormSPCode.center();
                            WinFormSPCode.show();
                        } else {
                            WinFormSPCode.close();
                        }
                    }
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                flex: 0.3,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridSPCode.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('SMESPCodeID'),
                dataIndex: 'SMESPCodeID',
                hidden:true
            },{
                text: lang('Mill Name'),
                dataIndex: 'MillName',
                flex: 1,
            },{
                text: lang('No Surat'),
                dataIndex: 'SuratNr',
                flex: 1,
            },{
                text: lang('Date Start'),
                dataIndex: 'DateStart',
                flex: 1,
            },{
                text: lang('Date End'),
                dataIndex: 'DateEnd',
                flex: 1,
            },{
                text: lang('Remarks'),
                dataIndex: 'Remarks',
                flex: 1,
            }]
        }];

        this.callParent(arguments);
    }
});