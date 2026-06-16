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

Ext.define('Koltiva.view.System.Transman.MainFormTranslate', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.System.Transman.MainFormTranslate',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;

            Ext.getCmp('Koltiva.view.System.Transman.MainFormTranslate-labelInfoInsert').update('<div id="header_title_farmer"> Translation Module ' + thisObj.viewVar.ModuleName + '</strong></div>');
            Ext.getCmp('Koltiva.view.System.Transman.MainFormTranslate-labelInfoInsert').doLayout();
        }
    },
    initComponent: function () {
        var thisObj = this;
        let labelWidth = 200;

        thisObj.PanelTranslate = 
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
        
        Ext.onReady(function () {
            Ext.tip.QuickTipManager.init();
        
            Ext.Ajax.request({
                url: m_header,
                method: 'GET',
                success: function (response) {
                    var column = [];
                    var field = [];
                    var data = Ext.JSON.decode(response.responseText);
                    var column = data.header;
                    var lang_list = data.lang;
        
                    Ext.each(data.header, function (one, idx, all) {
                        field.push(one.dataIndex);
                    });
        
                    var storeCategory = Ext.create('Ext.data.Store', {
                        extend: 'Ext.data.Model',
                        fields: ['id', 'label'],
                        autoLoad: true,
                        proxy: {
                            type: 'ajax',
                            url: m_api + '/common/cmb_menu_category',
                            reader: {
                                type: 'json',
                                root: 'data'
                            }
                        }
                    });
        
                    var store = Ext.create('Ext.data.Store', {
                        fields: field,
                        autoLoad: true,
                        pageSize: 50,
                        proxy: {
                            type: 'ajax',
                            url: m_api + '/transman/main_grid_translate',
                            reader: {
                                type: 'json',
                                root: 'data',
                                totalProperty: 'total'
                            }
                        },
                        listeners: {
                            'beforeload': function (store, options) {
                                store.proxy.extraParams.TransManID = thisObj.viewVar.TransManID;
                                store.proxy.extraParams.key = Ext.getCmp('Translation-PanelSearch-KeyString').getValue();
                            }
                        }
                    });        
                    
                    //========================================================== GRID (BEGIN) ===============================================================================//
        
                    //ContextMenu
                    var ContextMenuGridMain = Ext.create('Ext.menu.Menu', {
                        cls: 'Sfr_ConMenu',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                            text: lang('Update'),
                            cls: 'Sfr_BtnConMenuWhite',
                            hidden: m_act_update,
                            handler: function () {
                                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                                if (!sm) {
                                    Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                                    return false;
                                } else {
                                    var id = sm.get('key');
        
                                    var win = Ext.create('widget.window', {
                                        title: lang('Edit') + ' ' + lang('Translation'),
                                        id: 'win-translation',
                                        cls: 'Sfr_LayoutPopupWindows',
                                        modal: true,
                                        width: '60%',
                                        height: 500,
                                        layout: 'fit',
                                        items: Ext.create('Ext.form.Panel', {
                                            height: 490,
                                            width: '100%',
                                            bodyPadding: 5,
                                            autoScroll: true,
                                            id: 'frm-edit-translation',
                                            listeners: {
                                                beforerender: function (c) {
                                                    //console.log('id:'+id);
                                                    c.getForm().load({
                                                        url: m_crud,
                                                        method: 'GET',
                                                        params: {
                                                            id: id
                                                        },
                                                        success: function (form, action) {
                                                            var r = Ext.decode(action.response.responseText);
                                                            //console.log(r);
                                                            Ext.getCmp('CategorySet').setValue(r.data[0].set);
                                                            var lang_exist = r.data;
                                                            var lang_exist_count = lang_exist.length;
                                                            for (var i = 0; i < lang_exist_count; i++) {
                                                                var clsflag = '';
                                                                var tmpcd = lang_exist[i]['dataIndex'];
                                                                if (lang_exist[i]['dataIndex']) {
                                                                    clsflag = 'flag' + tmpcd.charAt(0).toUpperCase() + tmpcd.slice(1);
                                                                }
        
                                                                Ext.getCmp('LanguageFieldset').add([{
                                                                    xtype: 'textfield',
                                                                    fieldLabel: lang_exist[i]['name'] + '<div class="flag ' + clsflag + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>',
                                                                    name: lang_exist[i]['dataIndex'],
                                                                    allowBlank: false,
                                                                    value: lang_exist[i]['text'],
                                                                    width: '100%',
                                                                    labelAlign: 'left',
                                                                    labelWidth: 30
                                                                }, {
                                                                    xtype: 'textfield',
                                                                    name: 'trans_id_' + lang_exist[i]['dataIndex'],
                                                                    value: lang_exist[i]['trans_id'],
                                                                    inputType: 'hidden'
                                                                }]);
                                                            }
                                                        }
                                                    });
        
                                                }
                                            },
                                            items: [{
                                                    xtype: 'textfield',
                                                    id: 'key_old',
                                                    name: 'key_old',
                                                    value: id,
                                                    hidden: true
                                                },{
                                                    xtype: 'textfield',
                                                    name: 'key',
                                                    fieldLabel: lang('Key'),
                                                    allowBlank: false,
                                                    value: id,
                                                    width: '100%',
                                                    labelAlign: 'left',
                                                    labelWidth: 30
                                                }, {
                                                    xtype: 'combobox',
                                                    id: 'CategorySet',
                                                    name: 'CategorySet',
                                                    fieldLabel: lang('Category'),
                                                    store: storeCategory,
                                                    width: '100%',
                                                    labelAlign: 'left',
                                                    labelWidth: 30,
                                                    queryMode: 'local',
                                                    hidden:true,
                                                    displayField: 'label',
                                                    valueField: 'id'
                                                }, {
                                                    xtype: 'fieldset',
                                                    title: lang('Language'),
                                                    id: 'LanguageFieldset'
                                                },{
                                                    fieldLabel: lang('Translation Validation Check'),
                                                    labelAlign: 'left',
                                                    labelWidth: 205,
                                                    xtype: 'radiogroup',
                                                    columns: 2,
                                                    id: 'frm-edit-translation-RowTranslationValidation',
                                                    items: [{
                                                        boxLabel: lang('Yes'),
                                                        name: 'frm-edit-translation-TranslationValidation',
                                                        inputValue: '1',
                                                        checked:true,
                                                        id: 'frm-edit-translation-TranslationValidation1',
                                                        listeners: {
                                                            change: function () {
                                                                return false;
                                                            }
                                                        }
                                                    }, {
                                                        boxLabel: lang('No'),
                                                        name: 'frm-edit-translation-TranslationValidation',
                                                        inputValue: '2',
                                                        id: 'frm-edit-translation-TranslationValidation2',
                                                        listeners: {
                                                            change: function () {
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }
                                            ],
                                            buttons: [{
                                                id: 'saveButton',
                                                icon: varjs.config.base_url + 'images/icons/new/save.png',
                                                cls:'Sfr_BtnFormBlue',
                                                overCls:'Sfr_BtnFormBlue-Hover',
                                                text: lang('Save'),
                                                handler: function () {
                                                    var form = this.up('form').getForm();
                                                    form.submit({
                                                        url: m_crud,
                                                        method: 'PUT',
                                                        waitMsg: 'Sending data...',
                                                        success: function(rp, o){
                                                            var r = Ext.decode(o.response.responseText);
                                                            Ext.MessageBox.show({
                                                                title: 'Information',
                                                                msg: r.message,
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-success'
                                                            });
        
                                                            win.close();
                                                            store.load();
                                                        },
                                                        failure: function(rp, o){
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
                                            }, {
                                                icon: varjs.config.base_url + 'images/icons/new/close.png',
                                                text: lang('Close'),
                                                cls:'Sfr_BtnFormGrey',
                                                overCls:'Sfr_BtnFormGrey-Hover',
                                                handler: function () {
                                                    win.close();
                                                }
                                            }]
                                        })
                                    }).show();
                                }
                            }
                        }]
                    });
        
                    //Tambah Context Menu ke column
                    column.unshift({
                        text: '',
                        xtype: 'actioncolumn',
                        width: '3%',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/action.png',
                            handler: function (grid, rowIndex, colIndex, item, e, record) {
                                ContextMenuGridMain.showAt(e.getXY());
                            }
                        }]
                    });
        
                    var grid = Ext.create('Ext.grid.Panel', {
                        store: store,
                        id: 'grid',
                        minHeight: 250,
                        style: 'border:1px solid #CCC;margin-left:7px;',
                        renderTo: 'ext-content',
                        loadMask: true,
                        cls: 'Sfr_GridNew',
                        selType: 'rowmodel',
                        enableColumnHide: false,
                        viewConfig: {
                            deferEmptyText: false,
                            emptyText: GetDefaultContentNoData()
                        },
                        dockedItems: [{
                            xtype: 'pagingtoolbar',
                            store: store,
                            dock: 'bottom',
                            displayInfo: true
                        }, {
                            xtype: 'toolbar',
                            items: [{
                                id: 'Translation-PanelSearch-KeyString',
                                xtype: 'textfield',
                                baseCls:'Sfr_TxtfieldSearchGrid',
                                width: 400,
                                emptyText: lang('Key Search'),
                                listeners: {
                                    specialkey: SubmitOnEnterMainGrid
                                }
                            }]
                        }],
                        columns: column,
                        listeners: {
                            'afterRender': function () {
                                var thisObj = this;
                            }
                        }
                    });
        
                    function SubmitOnEnterMainGrid(field, event) {
                        if (event.getKey() == event.ENTER) {
                            Ext.getCmp('grid').getStore().loadPage(1);
                        }
                    }
        
                    //========================================================== GRID (END) ===============================================================================//
                }
            });
        
            function displayFormWindow() {
                if (!win.isVisible()) {
                    DataForm.getForm().reset();
                    win.show();
                } else {
                    win.hide(this, function () {});
                    win.toFront();
                }
            }
        });

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border: false,
            layout: {
                type: 'hbox'
            },
            items: [{
                id: 'Koltiva.view.System.Transman.MainFormTranslate-labelInfoInsert',
                html: '<div id="header_title_farmer">' + lang('Translation Management') + '</div>'
            }]
        }, {
            items: [{
                id: 'Koltiva.view.System.Transman.MainFormTranslate-LinkBackToList',
                html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.System.Transman.MainFormTranslate\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Main List') + '</a></li></div>'
            }]
        }, thisObj.PanelTranslate];
        //========================================================== LAYOUT UTAMA (End) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.System.Transman.MainFormTranslate').destroy(); //destory current view
        Ext.getCmp('grid').destroy(); //destory current view
        
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