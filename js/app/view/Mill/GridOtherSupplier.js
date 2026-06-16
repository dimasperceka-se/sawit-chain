/*
* @Author: nikolius
* @Date:   2017-08-22 11:02:50
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-05 15:03:03
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. PartnerID
*/
var contextMenuGridMill = Ext.create('Ext.menu.Menu',{
    items:[{
        id:'UpdateOS',
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Update'),
        handler: function() {
            var sm = Ext.getCmp('Koltiva.view.Mill.OtherSupplier-GridOtherSupplier').getSelectionModel().getSelection()[0];
            var winFromAddData = Ext.create('Koltiva.view.Mill.FormAddData',{                
                PartnerID:m_PartnerID,
                opsiDisplay : 'update',
                MillTCID : sm.get('MillTCID'),
                SourceType: 4
            });

            if (!winFromAddData.isVisible()) {
                winFromAddData.center();
                winFromAddData.show();
            } else {
                winFromAddData.close();
            }
        }
    },{
        id: 'DeleteOS',
        icon: varjs.config.base_url + 'images/icons/new/delete.png',
        text: lang('Delete'),
        hidden: m_act_delete,
        handler: function(){
            var sm = Ext.getCmp('Koltiva.view.Mill.OtherSupplier-GridOtherSupplier').getSelectionModel().getSelection()[0];

            Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/mill/form_tc',
                        method: 'DELETE',
                        params: {
                            MillTCID : sm.get('MillTCID')
                        },
                        success: function(response, opts) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data deleted'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            var palm_trdec_list_searchp = JSON.parse(localStorage.getItem('palm_trdec_list_searchp'));
                            if(palm_trdec_list_searchp != null){
                                Year        = palm_trdec_list_searchp.Year;
                                Period      = palm_trdec_list_searchp.Period;
                            }else{
                                Year        = m_year;
                                Period      = m_period;
                            }

                            Ext.getCmp('Koltiva.view.Mill.PanelTabTracebilityDeclarationDocument').getForm().load({
                                url: m_api + '/mill/mill_tracebilityDeclaration',
                                method: 'GET',
                                params: {
                                    PartnerID: m_PartnerID,
                                    Year : Year,
                                    Period : Period
                                },
                                success: function(form, action) {
                                    var r = Ext.decode(action.response.responseText);
                                },
                                failure: function(form, action) {
                                    Ext.MessageBox.show({
                                        title: 'Failed',
                                        msg: 'Failed to retrieve data',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });

                            //refresh store
                            Ext.getCmp('Koltiva.view.Mill.OtherSupplier-GridOtherSupplier').getStore().load();
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
var storeGridOtherSupplier = Ext.create('Koltiva.store.Mill.GridOtherSupplier');
Ext.define('Koltiva.view.Mill.GridOtherSupplier' ,{
    extend: 'Ext.panel.Panel',
    title: lang('Grid Other Suppliers (Direct Smallholder, Dealer/Agent/Vendor'),
    frame: true,
    collapsible:false,
    id: 'Koltiva.view.Mill.GridOtherSupplier',
    margin:'0 0 20 0',
    viewConfig: {
        deferEmptyText: false,
        emptyText: lang('No data Available')
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Mill.OtherSupplier-GridOtherSupplier',
            loadMask: true,
            selType: 'rowmodel',
            minHeight:300,
            style: 'border:1px solid #CCC;margin-top:4px;overflow-x: hidden;',
            store: storeGridOtherSupplier,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Mill.GridOtherSupplier-gridToolbar',
                store: storeGridOtherSupplier,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    id:'button_add_data_OS',
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add New Data'),
                    handler: function() {
                        $('#loader-ext').show();
                        setTimeout(function(){
                            $('#loader-ext').hide();
                            var winFromAddData = Ext.create('Koltiva.view.Mill.FormAddData',{
                                PartnerID:thisObj.viewVar.PartnerID,
                                SourceType: 4
                            });

                            if (!winFromAddData.isVisible()) {
                                winFromAddData.center();
                                winFromAddData.show();
                            } else {
                                winFromAddData.close();
                            }
                        }, 10);
                    }
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                width:70,
                id: 'Koltiva.view.Mill.GridOtherSupplier-Action',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record, row, action) {  
                        // var sm = Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual-gridMainGrid').getSelectionModel().getSelection()[0];
                        if(record.data.Generated == "No"){
                            contextMenuGridMill.showAt(e.getXY());
                        }else{
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('This record is from Farmgate, To delete this record please contact Administrator.'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-warning'
                            });
                            return false;
                        }
                    }
                }]
            },{
                text: lang('SME Name'),
                dataIndex: 'SupplierName',
                flex: 1
            },{
                text: lang('Garden Type'),
                dataIndex: 'GardenType',
                flex: 1
            },{
                text: lang('Annual Production (KG)'),
                dataIndex: 'AnnualProduction',
                flex: 1
            },{
                text: lang('Weight Bridge (KG)'),
                dataIndex: 'FFBSupply',
                flex: 1
            },{
                text: lang('Tracebility'),
                dataIndex: 'Tracebility',
                flex: 1
            }]
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            if(thisObj.viewVar.opsiDisplay == "view"){
                Ext.getCmp('button_add_data_OS').destroy();
            }
            
            //load store Document
            var grid_company_owned_estate = Ext.data.StoreManager.lookup('Koltiva.store.Mill.GridOtherSupplier');
            grid_company_owned_estate.setStoreVar({
                PartnerID:thisObj.viewVar.PartnerID,
                MillTCDID:thisObj.viewVar.MillTCDID,
            });
            grid_company_owned_estate.load();
            
        }
    }
});