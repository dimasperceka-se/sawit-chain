/*
* @Author: muhammad hidayaturrohman
* @Date:   2020-11-05 
* @Last Modified by:   muhammad hidayaturrohman
* @Last Modified time: 2020-11-05
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. PartnerID
*/
var contextMenuGridRefinery = Ext.create('Ext.menu.Menu',{
    items:[{
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Update'),
        hidden: m_act_update,
        handler: function(){
            var sm = Ext.getCmp('Koltiva.view.Refinery.CompanyOwnedEstate-GridCompanyOwnedEstate').getSelectionModel().getSelection()[0];
            var winFromAddData = Ext.create('Koltiva.view.Refinery.FormAddData',{
                PartnerID:m_PartnerID,
                opsiDisplay : 'update',
                RefineryTCID : sm.get('RefineryTCID'),
                SourceType: 2
            });

            if (!winFromAddData.isVisible()) {
                winFromAddData.center();
                winFromAddData.show();
            } else {
                winFromAddData.close();
            }
        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/delete.png',
        text: lang('Delete'),
        hidden: m_act_delete,
        handler: function(){
            var sm = Ext.getCmp('Koltiva.view.Refinery.CompanyOwnedEstate-GridCompanyOwnedEstate').getSelectionModel().getSelection()[0];

            Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/refinery/form_tc',
                        method: 'DELETE',
                        params: {
                            RefineryTCID : sm.get('RefineryTCID')
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

                            Ext.getCmp('Koltiva.view.Refinery.PanelTabTracebilityDeclarationDocument').getForm().load({
                                url: m_api + '/refinery/refinery_tracebilityDeclaration',
                                method: 'GET',
                                params: {
                                    PartnerID: sm.get('PartnerID'),
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
                            Ext.getCmp('Koltiva.view.Refinery.CompanyOwnedEstate-GridCompanyOwnedEstate').getStore().load();
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

var storeGridCompanyOwnedEstate = Ext.create('Koltiva.store.Refinery.GridCompanyOwnedEstate');
Ext.define('Koltiva.view.Refinery.GridCompanyOwnedEstate' ,{
    extend: 'Ext.panel.Panel',
    title: lang('Grid Company Owned Estates (Estate Inti)'),
    frame: true,
    collapsible:false,
    id: 'Koltiva.view.Refinery.GridCompanyOwnedEstate',
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
            id: 'Koltiva.view.Refinery.CompanyOwnedEstate-GridCompanyOwnedEstate',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridCompanyOwnedEstate,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Refinery.GridMainRefinery-gridToolbar',
                store: storeGridCompanyOwnedEstate,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    id:'button_add_data',
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add New Data'),
                    handler: function() {
                        $('#loader-ext').show();
                        setTimeout(function(){
                            $('#loader-ext').hide();
                            var winFromAddData = Ext.create('Koltiva.view.Refinery.FormAddData',{
                                PartnerID:thisObj.viewVar.PartnerID,
                                SourceType: 2
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
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record, row, action) {  
                        // var sm = Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual-gridMainGrid').getSelectionModel().getSelection()[0];
                        if(record.data.Generated == "No"){
                            contextMenuGridRefinery.showAt(e.getXY());
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
                width: '30%'
            },{
                text: lang('Garden Type'),
                dataIndex: 'GardenType',
                width: '41%'
            },{
                text: lang('Annual Production'),
                dataIndex: 'AnnualProduction',
                width: '25%'
            },{
                text: lang('Garden Area (Ha)'),
                dataIndex: 'GardenAreaHa',
                width: '25%'
            },{
                text: lang('Tonnage Bridge'),
                dataIndex: 'FFBSupply',
                width: '25%'
            },{
                text: lang('Tracebility'),
                dataIndex: 'Tracebility',
                width: '10%'
            }]
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            
            //load store Document
            var grid_company_owned_estate = Ext.data.StoreManager.lookup('Koltiva.store.Refinery.GridCompanyOwnedEstate');
            grid_company_owned_estate.setStoreVar({
                PartnerID:thisObj.viewVar.PartnerID,
                RefineryTCDID:thisObj.viewVar.RefineryTCDID,
            });
            grid_company_owned_estate.load();
            
        }
    }
});