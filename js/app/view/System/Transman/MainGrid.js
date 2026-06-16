/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 18 2020
 *  File : MainGrid.js
 *******************************************/
//override time out ajax exts js yg cuman 30 detikan jadi 10 menit
Ext.Ajax.timeout = 600000;
Ext.override(Ext.form.Basic, {
    timeout: Ext.Ajax.timeout / 1000
});
Ext.override(Ext.data.proxy.Server, {
    timeout: Ext.Ajax.timeout
});
Ext.override(Ext.data.Connection, {
    timeout: Ext.Ajax.timeout
});

Ext.define('Koltiva.view.System.Transman.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.System.Transman.MainGrid',
    renderTo: 'ext-content',
    style:'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function(component, eOpts){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Store
        thisObj.StoreGridMain = Ext.create('Koltiva.store.System.Transman.MainGrid');

        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu',{
            cls:'Sfr_ConMenu',
	        items:[{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls:'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.System.Transman.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.System.Transman.MainGrid').destroy(); //destory current view
                    let MainForm = [];
                    if(Ext.getCmp('Koltiva.view.System.Transman.MainForm') == undefined){
                        MainForm = Ext.create('Koltiva.view.System.Transman.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                TransManID: sm.get('TransManID')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.System.Transman.MainForm').destroy();
                        MainForm = Ext.create('Koltiva.view.System.Transman.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                TransManID: sm.get('TransManID')
                            }
                        });
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Translate Management'),
                cls:'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.System.Transman.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    if(sm.get('KeysCount') == 0){
                        Ext.MessageBox.show({
                            title: lang('Failed'),
                            msg: lang('Please Generate Translate Key First !'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                        return;
                    }

                    Ext.getCmp('Koltiva.view.System.Transman.MainGrid').destroy(); //destory current view
                    let MainForm = [];
                    if(Ext.getCmp('Koltiva.view.System.Transman.MainFormTranslate') == undefined){
                        MainForm = Ext.create('Koltiva.view.System.Transman.MainFormTranslate', {
                            viewVar: {
                                TransManID: sm.get('TransManID'),
                                ModuleName: sm.get('ModuleName')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.System.Transman.MainFormTranslate').destroy();
                        MainForm = Ext.create('Koltiva.view.System.Transman.MainFormTranslate', {
                            viewVar: {
                                TransManID: sm.get('TransManID'),
                                ModuleName: sm.get('ModuleName')
                            }
                        });
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/system-2.png',
                text: lang('Generate Translation Key'),
                cls:'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.System.Transman.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.show({
                        msg: 'Please wait...',
                        progressText: 'Generating...',
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        animateTarget: 'mb9'
                    });

                    Ext.Ajax.request({
                        url: m_api + '/transman/generate_trans_key',
                        method: 'POST',
                        waitMsg: lang('Please Wait'),
                        params: {
                            TransManID: sm.get('TransManID')
                        },
                        success: function(rp, o) {
                            Ext.MessageBox.hide();
                            try {
                                var r = Ext.decode(rp.responseText);
                                
                                Ext.MessageBox.show({
                                    title: lang('Information'),
                                    msg: lang('Generate Successfully'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                //refresh store
                                thisObj.StoreGridMain.load();
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
                        },
                        failure: function(rp, o) {
                            Ext.MessageBox.hide();
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
            },{
                icon: varjs.config.base_url + 'images/icons/new/export.png',
                text: lang('Export Translation Key'),
                cls:'Sfr_BtnConMenuWhite',
                // hidden:true,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.System.Transman.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.show({
                        msg: 'Please wait...',
                        progressText: 'Generating...',
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        animateTarget: 'mb9'
                    });

                    Ext.Ajax.request({
                        url: m_api + '/transman/export_translation_key',
                        method: 'POST',
                        waitMsg: lang('Please Wait'),
                        params: {
                            TransManID: sm.get('TransManID')
                        },
                        success: function(rp, o) {
                            Ext.MessageBox.hide();
                            try {
                                var r = Ext.decode(rp.responseText);
                                window.location = r.filenya;
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
                        },
                        failure: function(rp, o) {
                            Ext.MessageBox.hide();
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
            },{
                icon: varjs.config.base_url + 'images/icons/new/export.png',
                text: lang('Compare Translation with Mobile'),
                hidden:true,
                cls:'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.System.Transman.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.show({
                        msg: 'Please wait...',
                        progressText: 'Generating...',
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        animateTarget: 'mb9'
                    });

                    Ext.Ajax.request({
                        url: m_api + '/transman/export_compare_translation_mobile',
                        method: 'POST',
                        waitMsg: lang('Please Wait'),
                        params: {
                            TransManID: sm.get('TransManID')
                        },
                        success: function(rp, o) {
                            Ext.MessageBox.hide();
                            try {
                                var r = Ext.decode(rp.responseText);
                                window.location = r.filenya;
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
                        },
                        failure: function(rp, o) {
                            Ext.MessageBox.hide();
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
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite',
	            hidden: m_act_delete,
	            handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.System.Transman.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/transman/main_data',
                                method: 'DELETE',
                                params: {
                                    TransManID: sm.get('TransManID')
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
                                failure: function(rp, o) {
                                    try {
                                        var r = Ext.decode(rp.responseText);
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                    catch(err) {
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: lang('Connection Error'),
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
            xtype: 'grid',
            id: 'Koltiva.view.System.Transman.MainGrid-Grid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true,
                displayMsg: lang('Showing')+' {0} '+lang('to')+' {1} '+lang('of')+' {2} '+lang('data')
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add'),
                    hidden: m_act_add,
                    cls:'Sfr_BtnGridGreen',
                    overCls:'Sfr_BtnGridGreen-Hover',
                    handler: function() {
                        Ext.getCmp('Koltiva.view.System.Transman.MainGrid').destroy(); //destory current view
                    	let MainForm = [];
                        if(Ext.getCmp('Koltiva.view.System.Transman.MainForm') == undefined){
                            MainForm = Ext.create('Koltiva.view.System.Transman.MainForm', {
                            	viewVar: {
                                    OpsiDisplay: 'insert',
                                    TransManID: null
		                        }
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.System.Transman.MainForm').destroy();
                            MainForm = Ext.create('Koltiva.view.System.Transman.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'insert',
                                    TransManID: null
		                        }
                            });
                        }
                    }
                }]
            }],
            columns:[{
            	text: '',
                xtype:'actioncolumn',
                width:'4%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuGrid.showAt(e.getXY());
                    }
                }]
            },{
                text: 'No',
                width:'4%',
                xtype: 'rownumberer'
            },{
                text: lang('TransManID'),
                dataIndex: 'TransManID',
                hidden: true
            },{
                text: lang('Module Name'),
                dataIndex: 'ModuleName',
                width:'15%'
            },{
                text: lang('Description'),
                dataIndex: 'ModuleDescription',
                width:'40%'
            },{
                text: lang('Source Code Files Count'),
                dataIndex: 'FilesCount',
                width:'15%'
            },{
                text: lang('Translation Keys Count'),
                dataIndex: 'KeysCount',
                width:'15%'
            }]
        }];

        this.callParent(arguments);
    }
});