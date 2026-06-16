/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 18 2020
 *  File : MainForm.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    - OpsiDisplay
    - TransManID
*/

Ext.define('SourceCodeFilesGrid.Model', {
    extend: 'Ext.data.Model',
    fields: ['TransManID','FilePath','IsFileExist','OptionInput']
});

Ext.define('Koltiva.view.System.Transman.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.System.Transman.MainForm',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;

            if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
                if(thisObj.viewVar.OpsiDisplay == 'view') {
                    Ext.getCmp('Koltiva.view.System.Transman.MainForm-Form-BtnSave').setVisible(false);
                }

                //load formnya
                Ext.getCmp('Koltiva.view.System.Transman.MainForm-Form').getForm().load({
                    url: m_api + '/transman/main_form_open',
                    method: 'GET',
                    params: {
                        TransManID: this.viewVar.TransManID
                    },
                    success: function (form, action) {
                        var r = Ext.decode(action.response.responseText);
                    },
                    failure: function (form, action) {
                        Ext.MessageBox.show({
                            title: lang('Failed'),
                            msg: lang('Failed to retrieve data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });

            }
        }
    },
    initComponent: function () {
        var thisObj = this;
        let labelWidth = 200;

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.ObjPanelMain = Ext.create('Ext.panel.Panel', {
            title: lang('Main Data'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            collapsible: true,
            style:'margin-top:0px;padding-top:0px;',
            items: [{
                xtype: 'form',
                id: 'Koltiva.view.System.Transman.MainForm-Form',
                fileUpload: true,
                buttonAlign: 'right',
                cls: 'Sfr_PanelSubLayoutForm',
                items:[{
                    layout: 'column',
                    border: false,
                    padding: 10,
                    items: [{
                        columnWidth: 1,
                        layout: 'form',
                        style:'margin-right:20px;',
                        items:[{
                            xtype: 'textfield',
                            id: 'Koltiva.view.System.Transman.MainForm-Form-TransManID',
                            name: 'Koltiva.view.System.Transman.MainForm-Form-TransManID',
                            inputType: 'hidden'
                        },{
                            xtype: 'textfield',
                            id: 'Koltiva.view.System.Transman.MainForm-Form-ModuleName',
                            name: 'Koltiva.view.System.Transman.MainForm-Form-ModuleName',
                            fieldLabel: lang('Module Name'),
                            labelWidth: labelWidth,
                            allowBlank: false,
                            baseCls: 'Sfr_FormInputMandatory'
                        },{
                            xtype: 'textareafield',
                            id: 'Koltiva.view.System.Transman.MainForm-Form-ModuleDescription',
                            name: 'Koltiva.view.System.Transman.MainForm-Form-ModuleDescription',
                            fieldLabel: lang('Description'),
                            labelWidth: labelWidth
                        }]
                    }]
                }],
                buttons: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Save'),
                    cls: 'Sfr_BtnFormBlue',
                    overCls: 'Sfr_BtnFormBlue-Hover',
                    id: 'Koltiva.view.System.Transman.MainForm-Form-BtnSave',
                    handler: function () {
                        let Formnya = Ext.getCmp('Koltiva.view.System.Transman.MainForm-Form').getForm();

                        if (Formnya.isValid()) {
                            Formnya.submit({
                                url: m_api + '/transman/main_form',
                                method: 'POST',
                                waitMsg: lang('Saving data'),
                                params: {
                                    OpsiDisplay: thisObj.viewVar.OpsiDisplay
                                },
                                success: function (fp, o) {
                                    var r = Ext.decode(o.response.responseText);

                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data saved'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success',
                                        fn: function (btn) {
                                            if (btn == 'ok') {
                                                Ext.getCmp('Koltiva.view.System.Transman.MainForm').destroy(); //destory current view
                                                let FormMain = [];

                                                if(Ext.getCmp('Koltiva.view.System.Transman.MainForm') == undefined){
                                                    FormMain = Ext.create('Koltiva.view.System.Transman.MainForm', {
                                                        viewVar: {
                                                            OpsiDisplay: 'update',
                                                            TransManID: r.TransManID
                                                        }
                                                    });
                                                }else{
                                                    //destroy, create ulang
                                                    Ext.getCmp('Koltiva.view.System.Transman.MainForm').destroy();
                                                    FormMain = Ext.create('Koltiva.view.System.Transman.MainForm', {
                                                        viewVar: {
                                                            OpsiDisplay: 'update',
                                                            TransManID: r.TransManID
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    });
                                },
                                failure: function (fp, o) {
                                    try {
                                        var r = Ext.decode(o.response.responseText);
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
                        } else {
                            Ext.MessageBox.show({
                                title: lang('Attention'),
                                msg: lang('Form not complete yet'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }

                    }
                }]
            }]
        });
        //========================================================== LAYOUT UTAMA (Begin) ========================================//

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.ObjPanelDetail = [];
        if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {

            //Main Store
            thisObj.StoreMainFormDetailGrid = Ext.create('Koltiva.store.System.Transman.SourceCodeFilesGrid', {
                storeVar: {
                    TransManID: thisObj.viewVar.TransManID
                }
            });

            let SourceCodeFilesRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
                id: 'Koltiva.view.System.Transman.MainForm-SourceCodeFilesGridRowEdit',
                clicksToMoveEditor: 0,
                autoCancel: false,
                errorSummary: false,
                clicksToEdit: 2
            });

            //Context Menu
            thisObj.ContextMenuDetailGrid = Ext.create('Ext.menu.Menu',{
                cls:'Sfr_ConMenu',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    cls:'Sfr_BtnConMenuWhite',
                    hidden: m_act_delete,
                    handler: function() {
                        let sm = Ext.getCmp('Koltiva.view.System.Transman.MainForm-DetailGrid').getSelectionModel().getSelection()[0];

                        Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/transman/source_code_files',
                                    method: 'DELETE',
                                    params: {
                                        TransManID: sm.get('TransManID'),
                                        FilePath: sm.get('FilePath')
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
                                        thisObj.StoreMainFormDetailGrid.load();
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

            thisObj.ObjPanelDetail = Ext.create('Ext.panel.Panel', {
                title: lang('Module Source Code Files'),
                frame: true,
                cls: 'Sfr_PanelLayoutForm',
                collapsible: true,
                style:'margin-top:0px;padding-top:0px;',
                items: [{
                    xtype: 'grid',
                    id: 'Koltiva.view.System.Transman.MainForm-DetailGrid',
                    style: 'border:1px solid #CCC;',
                    cls:'Sfr_GridNew',
                    loadMask: true,
                    selType: 'rowmodel',
                    store: thisObj.StoreMainFormDetailGrid,
                    enableColumnHide: false,
                    minHeight:150,
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: GetDefaultContentNoData()
                    },
                    dockedItems: [{
                        xtype: 'toolbar',
                        dock:'top',
                        items: [{
                            xtype:'button',
                            icon: varjs.config.base_url + 'images/icons/new/add.png',
                            text: lang('Add File'),
                            hidden: m_act_add,
                            cls:'Sfr_BtnGridGreen',
                            overCls:'Sfr_BtnGridGreen-Hover',
                            handler: function() {
                                SourceCodeFilesRowEditing.cancelEdit();                                
                                let r = Ext.create('SourceCodeFilesGrid.Model', {
                                    TransManID: thisObj.viewVar.TransManID,
                                    FilePath: '',
                                    IsFileExist: '',
                                    OptionInput: 'Insert'
                                });
                                thisObj.StoreMainFormDetailGrid.insert(0,r);
                                SourceCodeFilesRowEditing.startEdit(0,0);
                            }
                        }]
                    }],
                    columns:[{
                        text: '',
                        xtype:'actioncolumn',
                        width: '4%',
                        items:[{
                            icon: varjs.config.base_url + 'images/icons/new/action.png',
                            handler: function(grid, rowIndex, colIndex, item, e, record) {
                                thisObj.ContextMenuDetailGrid.showAt(e.getXY());
                            }
                        }]
                    },{
                        text: 'No',
                        width: '4%',
                        xtype: 'rownumberer'
                    },{
                        text: lang('TransManID'),
                        dataIndex: 'TransManID',
                        hidden: true
                    },{
                        text: lang('File Path'),
                        dataIndex: 'FilePath',
                        width:'52%',
                        editor:{
                            xtype: 'textfield',
                            id: 'Koltiva.view.System.Transman.MainForm-DetailGrid-ReditFilePath'
                        }
                    },{
                        text: lang('File Exist'),
                        dataIndex: 'IsFileExist',
                        width:'38%',
                        renderer: function (value) {
                            let RetVal;
                            let FileCheck = parseInt(value);
        
                            switch(FileCheck){
                                case 1:
                                    RetVal = '<span class="Sfr_GridColGreenRounded">'+lang('Yes')+'</span>';
                                break;
                                case 2:
                                    RetVal = '<span class="Sfr_GridColRedRounded">'+lang('No')+'</span>';
                                break;
                                default:
                                    RetVal = '-';
                                break;
                            }
        
                            return RetVal;
                        }
                    }],
                    plugins: [SourceCodeFilesRowEditing],
                    listeners: {
                        'canceledit': function(editor, e, eOpts) {
                            thisObj.StoreMainFormDetailGrid.load();
                        },
                        'edit': function(editor, e) {
                            let OptionInput;
                            if (e.record.data.OptionInput == 'Insert') {
                                OptionInput = 'Insert';
                            }

                            Ext.Ajax.request({
                                waitMsg: lang('Please wait'),
                                url: m_api + '/transman/source_code_files',
                                method: 'POST',
                                params: {
                                    OptionInput: OptionInput,
                                    TransManID: e.record.data.TransManID,
                                    FilePath: e.record.data.FilePath
                                },
                                success: function(rp, o) {
                                    var r = Ext.decode(rp.responseText);

                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data saved'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
    
                                    //refresh store
                                    thisObj.StoreMainFormDetailGrid.load();
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
                    }
                }]
            });

        }
        //========================================================== LAYOUT UTAMA (Begin) ========================================//

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border: false,
            layout: {
                type: 'hbox'
            },
            items: [{
                id: 'Koltiva.view.System.Transman.MainForm-labelInfoInsert',
                html: '<div id="header_title_farmer">' + lang('Translation Management Form') + '</div>'
            }]
        }, {
            items: [{
                id: 'Koltiva.view.System.Transman.MainForm-LinkBackToList',
                html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.System.Transman.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Main List') + '</a></li></div>'
            }]
        }, {
            html: '<br />'
        }, {
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 0.4,
                items: [
                    thisObj.ObjPanelMain
                ]
            },{
                //RIGHT CONTENT
                columnWidth: 0.595,
                style:'padding-left:10px',
                items: [
                    thisObj.ObjPanelDetail
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (End) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.System.Transman.MainForm').destroy(); //destory current view
        let MainGrid = [];
        if (Ext.getCmp('Koltiva.view.System.Transman.MainGrid') == undefined) {
            MainGrid = Ext.create('Koltiva.view.System.Transman.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.System.Transman.MainGrid').destroy();
            MainGrid = Ext.create('Koltiva.view.System.Transman.MainGrid');
        }
    }
});