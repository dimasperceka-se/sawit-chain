/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Sep 17 2018
 *  File : GridMainDocument.js
 *******************************************/

Ext.define('Koltiva.view.CMS.GridMainDocument' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.ForumTopic',
    margin: '15px 15px 15px 15px',
    renderTo: 'ext-content',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.StoreGridMain = Ext.create('Koltiva.store.CMS.GridMainDocument');

        var ContextMenuGrid = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View Document'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.CMS.GridMainDocument-GridMain').getSelectionModel().getSelection()[0];

                    var WinFormDocumentView = Ext.create('Koltiva.view.CMS.WinFormDocumentView', {
                        viewVar: {
                            DocID: sm.get('DocID')
                        }
                    });
                    if (!WinFormDocumentView.isVisible()) {
                        WinFormDocumentView.center();
                        WinFormDocumentView.show();
                    } else {
                        WinFormDocumentView.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.CMS.GridMainDocument-GridMain').getSelectionModel().getSelection()[0];
                    var WinFormDocument = Ext.create('Koltiva.view.CMS.WinFormDocument', {
                        viewVar: {
                            OpsiDisplay: 'update',
                            DocID: sm.get('DocID'),
                            CallerStore: thisObj.StoreGridMain
                        }
                    });
                    if (!WinFormDocument.isVisible()) {
                        WinFormDocument.center();
                        WinFormDocument.show();
                    } else {
                        WinFormDocument.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,                
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.CMS.GridMainDocument-GridMain').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/cms/document',
                                method: 'DELETE',
                                params: {
                                    DocID: sm.get('DocID')
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
                                    thisObj.StoreGridMain.load();
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
            xtype: 'grid',
            id: 'Koltiva.view.CMS.GridMainDocument-GridMain',
            style: 'border:1px solid #CCC;margin-top:4px;',
            height: 650,
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No Data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.CMS.GridMainDocument-GridMain-GridToolbar',
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
                        var WinFormDocument = Ext.create('Koltiva.view.CMS.WinFormDocument', {
                            viewVar: {
                                OpsiDisplay: 'insert',
                                CallerStore: thisObj.StoreGridMain
                            }
                        });
                        if (!WinFormDocument.isVisible()) {
                            WinFormDocument.center();
                            WinFormDocument.show();
                        } else {
                            WinFormDocument.close();
                        }
                    }
                }]
            }],
            columns: [{
                dataIndex: 'DocID',
                hidden:true
            },{
                text: lang('Action'),
                xtype:'actioncolumn',
                width: '6%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        ContextMenuGrid.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Name'),
                dataIndex: 'Name',
                width: '22%'
            },{
                text: lang('Description'),
                dataIndex: 'Description',
                width: '32%'
            },{                
                text: lang('Status'),
                dataIndex: 'StatusType',
                width: '10%'
            },{
                text: lang('Posted By'),
                dataIndex: 'PostedBy',
                width: '15%'
            },{
                text: lang('Last Updated'),
                dataIndex: 'LastUpdated',
                width: '14%'
            }]
        }];

        this.callParent(arguments);
    }
});