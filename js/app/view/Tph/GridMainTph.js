/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu May 02 2019
 *  File : GridMainTph.js
 *******************************************/

Ext.define('Koltiva.view.Tph.GridMainTph' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Tph.GridMainTph',
    renderTo: 'ext-content',
    listeners: {
        afterRender: function(){
            var thisObj = this;
            document.getElementById('divCommonContentRegion').style.display = 'block';

            //Load Grid
            thisObj.StoreGridMain.storeVar.TextSearch = Ext.getCmp('Koltiva.view.Tph.GridMainTph-textSearch').getValue();
            thisObj.StoreGridMain.load();
        }
    },
    SubmitOnEnterGrid: function(){
        var thisObj = this;

        //Load Grid
        Ext.getCmp('Koltiva.view.Tph.GridMainTph').StoreGridMain.storeVar.TextSearch = Ext.getCmp('Koltiva.view.Tph.GridMainTph-textSearch').getValue();
        Ext.getCmp('Koltiva.view.Tph.GridMainTph').StoreGridMain.load();
    },
    initComponent: function() {
        var thisObj = this;

        //store
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Tph.GridMain');

        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Tph.GridMainTph-GridMain').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.Tph.GridMainTph').destroy(); //destory current view
                    //create object View untuk FormMainTrader
                    if(Ext.getCmp('Koltiva.view.Tph.FormMain') == undefined){
                        var FormMainTrader = Ext.create('Koltiva.view.Tph.FormMain', {
                            OpsiDisplay: 'view',
                            viewVar: {
                                CollectpointID: sm.get('CollectpointID')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Tph.FormMain').destroy();
                        var FormMainTrader = Ext.create('Koltiva.view.Tph.FormMain', {
                            OpsiDisplay: 'view',
                            viewVar: {
                                CollectpointID: sm.get('CollectpointID')
                            }
                        });
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Tph.GridMainTph-GridMain').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.Tph.GridMainTph').destroy(); //destory current view
                    //create object View untuk FormMainTrader
                    if(Ext.getCmp('Koltiva.view.Tph.FormMain') == undefined){
                        var FormMainTrader = Ext.create('Koltiva.view.Tph.FormMain', {
                            OpsiDisplay: 'update',
                            viewVar: {
                                CollectpointID: sm.get('CollectpointID')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Tph.FormMain').destroy();
                        var FormMainTrader = Ext.create('Koltiva.view.Tph.FormMain', {
                            OpsiDisplay: 'update',
                            viewVar: {
                                CollectpointID: sm.get('CollectpointID')
                            }
                        });
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Tph.GridMainTph-GridMain').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/tph/tph_form',
                                method: 'DELETE',
                                params: {
                                    CollectpointID: sm.get('CollectpointID')
                                },
                                success: function(rp, o) {
                                    var r = Ext.decode(rp.responseText);
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
                                
                                    //refresh store
                                    Ext.getCmp('Koltiva.view.Tph.GridMainTph').StoreGridMain.load();
                                },
                                failure: function(rp, o) {
                                    try {
                                        var r = Ext.decode(rp.responseText);
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                    catch(err) {
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: 'Connection Error',
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            }]
        });

        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.3,
                layout: 'form',
                items:[{}]
            },{
                columnWidth: 0.7,
                xtype: 'panel',
                frame: false,
                id: 'Koltiva.view.Tph.GridMainTph-GridInformation',
                html: ''
            }]
        },{
            xtype: 'grid',
            id: 'Koltiva.view.Tph.GridMainTph-GridMain',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Tph.GridMainTph-GridMain-GridToolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: m_act_add,
                    handler: function() {
                        Ext.getCmp('Koltiva.view.Tph.GridMainTph').destroy(); //destory current view

                        //create object View untuk FormMainTrader
                        if(Ext.getCmp('Koltiva.view.Tph.FormMain') == undefined){
                            var FormMainTrader = Ext.create('Koltiva.view.Tph.FormMain', {
                                OpsiDisplay: 'insert'
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Tph.FormMain').destroy();
                            var FormMainTrader = Ext.create('Koltiva.view.Tph.FormMain', {
                                OpsiDisplay: 'insert'
                            });
                        }
                    }
                },{
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.Tph.GridMainTph-textSearch',
                    xtype: 'textfield',
                    width: 400,
                    emptyText: lang('Cari berdasar nama/ID')+', '+lang('Press \'Enter\' to search'),
                    hidden: true,
                    listeners: {
                        specialkey: thisObj.SubmitOnEnterGrid
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/add-filter.png',
                    text:lang('Apply Filter'),
                    cls:'Sfr_BtnGridPaleBlue',
                    overCls:'Sfr_BtnGridPaleBlue-Hover',
                    handler: function() {
                        var WinApplyFilter = Ext.create('Koltiva.view.Tph.WinApplyFilter', {
                            viewVar: {
                                MainGrid: thisObj.storeGridMain
                            }
                        });
                        if (!WinApplyFilter.isVisible()) {
                            WinApplyFilter.center();
                            WinApplyFilter.show();
                        } else {
                            WinApplyFilter.close();
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/reload.png', cls:'Sfr_BtnGridBlue', overCls:'Sfr_BtnGridBlue-Hover',
                    tooltip: lang('Reload'),
                    handler: function() {
                        thisObj.StoreGridMain.storeVar.TextSearch = Ext.getCmp('Koltiva.view.Tph.GridMainTph-textSearch').getValue();
                        thisObj.StoreGridMain.load();
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
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuGrid.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('CollectpointID'),
                dataIndex: 'CollectpointID',
                hidden:true
            },{
                text: lang('TPH ID'),
                flex:1,
                dataIndex: 'CollectpointDisplayID'
            },{
                text: lang('Name'),
                flex:3,
                dataIndex: 'Name'
            },{
                text: lang('Type'),
                flex:1,
                dataIndex: 'OrgTypeLabel',
                renderer: function (value) {
                    var RetVal;

                    if(value != null && value != ''){
                        switch(value){
                            case 'farmer':
                                RetVal = lang('Farmer');
                            break;
                            case 'agent':
                                RetVal = lang('SME');
                            break;
                            case 'collective':
                                RetVal = lang('Collective');
                            break;
                            default:
                                RetVal = '-';
                            break;
                        }
                    }else{
                        RetVal = '-';
                    }

                    return RetVal;
                }
            },{
                text: lang('Responsible'),
                flex:3,
                dataIndex: 'OrgIDLabel'
            },{
                text: lang('Sub District'),
                flex:2,
                dataIndex: 'SubDistrict'
            },{
                text: lang('Village'),
                flex:2,
                dataIndex: 'Village'
            },{
                text: lang('Latitude'),
                flex:1,
                dataIndex: 'Latitude'
            },{
                text: lang('Longitude'),
                flex:1,
                dataIndex: 'Longitude'
            },{
                text: lang('Last Updated'),
                flex:2.5,
                dataIndex: 'LastUpdated'
            }]
        }];

        this.callParent(arguments);
    }
});