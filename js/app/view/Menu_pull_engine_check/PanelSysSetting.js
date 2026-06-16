Ext.define('Koltiva.view.Menu_pull_engine_check.PanelSysSetting' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Menu_pull_engine_check.PanelSysSetting',
    width: '100%',
    minHeight: 100,
    title: lang('Grid Sys Setting'),
    style: 'border:1px solid #CCC;',
    renderTo: 'ext-content',
    loadMask: true,
    selType: 'rowmodel',
    listeners: {
        afterRender: function(component, eOpts){
        	var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Store
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Menu_pull_engine_check.MainGridSysSetting');

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Menu_pull_engine_check.MainGridSysSetting-Grid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            height: 150,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true,
                displayMsg: lang('Showing') + ' {0} ' + lang('to') + ' {1} ' + lang('of') + ' {2} ' + lang('data')
            }],
            columns:[{
                text: '',
                xtype: 'actioncolumn',
                width: '4%',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                        handler: function (grid, rowIndex, colIndex, item, e, record) {
                            if(Ext.isDefined(Ext.getCmp('Koltiva.view.Menu_pull_engine_check.MainGridSysSetting-Grid-ContextMenu'))){
                                Ext.getCmp('Koltiva.view.Menu_pull_engine_check.MainGridSysSetting-Grid-ContextMenu').destroy();
                            }

                            thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
                                cls: 'Sfr_ConMenu',
                                id:'Koltiva.view.Menu_pull_engine_check.MainGridSysSetting-Grid-ContextMenu',
                                items: [{
                                        icon: varjs.config.base_url + 'images/icons/new/view.png',
                                        text: lang('Update'),
                                        hidden: m_act_update,
                                        cls: 'Sfr_BtnConMenuWhite',
                                        handler: function () {
                                            var sm = Ext.getCmp('Koltiva.view.Menu_pull_engine_check.MainGridSysSetting-Grid').getSelectionModel().getSelection()[0];

                                            if (!sm) {
                                                Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                                                return false;
                                            }else{
                                                var win = Ext.create('widget.window', {
                                                    title: lang('Sys Setting Detail'),
                                                    id: 'Koltiva.view.Menu_pull_engine_check.PanelSysSetting-GridSysSetting',
                                                    cls: 'Sfr_LayoutPopupWindows',
                                                    modal: true,
                                                    width: '60%',
                                                    height: 680,
                                                    layout: 'fit',
                                                    items: Ext.create('Ext.form.Panel', {
                                                        height: 590,
                                                        width: '100%',
                                                        bodyPadding: 5,
                                                        autoScroll: true,
                                                        xtype: 'form',
                                                        id: 'Koltiva.view.Menu_pull_engine_check.PanelSysSetting-GridSysSetting-Form',
                                                        items: [{
                                                            xtype: 'textfield',
                                                            id: 'Koltiva.view.Menu_pull_engine_check.PanelSysSetting-GridSysSetting-Form-SetID',
                                                            name: 'Koltiva.view.Menu_pull_engine_check.PanelSysSetting-GridSysSetting-Form-SetID',
                                                            fieldLabel: lang('Set ID'),
                                                            allowBlank: true,
                                                            value: sm.get('SetID'),
                                                            width: '100%',
                                                            labelAlign: 'left',
                                                            labelWidth: 100,
                                                            readOnly: true
                                                        },{
                                                            xtype: 'textfield',
                                                            fieldLabel: lang('Set Key'),
                                                            allowBlank: true,
                                                            value: sm.get('SetKey'),
                                                            width: '100%',
                                                            labelAlign: 'left',
                                                            labelWidth: 100,
                                                            readOnly: true
                                                        },{
                                                            xtype: 'textfield',
                                                            fieldLabel: lang('Set Name'),
                                                            allowBlank: true,
                                                            value: sm.get('SetName'),
                                                            width: '100%',
                                                            labelAlign: 'left',
                                                            labelWidth: 100,
                                                            readOnly: true
                                                        },{
                                                            xtype: 'textareafield',
                                                            id: 'Koltiva.view.Menu_pull_engine_check.PanelSysSetting-GridSysSetting-Form-SetValue',
                                                            name: 'Koltiva.view.Menu_pull_engine_check.PanelSysSetting-GridSysSetting-Form-SetValue',
                                                            fieldLabel: lang('Set Value'),
                                                            baseCls: 'Sfr_FormInputMandatory',
                                                            allowBlank: false,
                                                            value: sm.get('SetValue'),
                                                            width: '100%',
                                                            minHeight: 350,
                                                            labelAlign: 'left',
                                                            labelWidth: 100,
                                                            readOnly: false,
                                                            regex:/^((([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z\s?]{2,5}){1,25})*(\s*?;\s*?)*)*$/,
                                                            regexText:lang('This field must contain single or multiple valid email addresses separated by semicolon (;)'),
                                                            blankText : lang('Please enter email address(s)'),
                                                        },{
                                                            xtype:'tbspacer',
                                                            height:10
                                                        },{
                                                            xtype: 'label',
                                                            cls: 'x-form-item-label', 
                                                            margin:0, 
                                                            padding:0,
                                                            html:`<div style="margin-left:468px;color:#ED2F0D;">*${lang("This field must contain single or multiple valid email addresses separated by semicolon (;)")}</div>`
                                                        }
                                                        ],
                                                        buttons: [{
                                                            icon: varjs.config.base_url + 'images/icons/new/save.png',
                                                            cls: 'Sfr_BtnFormBlue',
                                                            overCls: 'Sfr_BtnFormBlue-Hover',
                                                            text: lang('Save'),
                                                            id: 'Koltiva.view.Menu_pull_engine_check.PanelSysSetting-Form-BtnSave',
                                                            handler: function () {
                                                                let Formnya = Ext.getCmp('Koltiva.view.Menu_pull_engine_check.PanelSysSetting-GridSysSetting-Form').getForm();

                                                                if (Formnya.isValid()) {
                                                                    Formnya.submit({
                                                                        url: m_api + '/menu_pull_engine_check/update_value_setting',
                                                                        method: 'POST',
                                                                        waitMsg: lang('Saving data'),
                                                                        success: function (fp, o) {
                                                                            var r = Ext.decode(o.response.responseText);

                                                                            Ext.MessageBox.show({
                                                                                title: lang('Information'),
                                                                                msg: lang(r.message),
                                                                                buttons: Ext.MessageBox.OK,
                                                                                animateTarget: 'mb9',
                                                                                icon: 'ext-mb-success',
                                                                                fn: function (btn) {
                                                                                    if (btn == 'ok') {
                                                                                        var MainGrid = [];
                                                                                        if (Ext.getCmp('Koltiva.view.Menu_pull_engine_check.MainGrid') == undefined) {
                                                                                            MainGrid = Ext.create('Koltiva.view.Menu_pull_engine_check.MainGrid');
                                                                                        } else {
                                                                                            Ext.getCmp('Koltiva.view.Menu_pull_engine_check.MainGrid').destroy();
                                                                                            MainGrid = Ext.create('Koltiva.view.Menu_pull_engine_check.MainGrid');
                                                                                        }
                                                                                    }
                                                                                }
                                                                            });

                                                                            win.close();
                                                                        },
                                                                        failure: function (fp, o) {
                                                                            try {
                                                                                var r = Ext.decode(o.response.responseText);
                                                                                Ext.MessageBox.show({
                                                                                    title: lang('Error'),
                                                                                    msg: lang(r.message),
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
                                                        },{
                                                            icon: varjs.config.base_url + 'images/icons/new/close.png',
                                                            text: lang('Close'),
                                                            cls:'Sfr_BtnFormGrey',
                                                            overCls:'Sfr_BtnFormGrey-Hover',
                                                            handler: function () {
                                                                win.close();
                                                            }
                                                        }]
                                                    })
                                                }).show()
                                            }

                                            
                                        }
                                    }]
                            });

                            thisObj.ContextMenuGrid.showAt(e.getXY());
                        }
                    }]
            },{
                text: lang('Set ID'),
                dataIndex: 'SetID',
                hidden: true
            },{
                text: lang('Set Key'),
                dataIndex: 'SetKey',
                flex: 10
            },{
                text: lang('Set Name'),
                dataIndex: 'SetName',
                flex: 10
            },{
                text: lang('Set Value'),
                dataIndex: 'SetValue',
                flex: 10
            }]
        }];

        this.callParent(arguments);
    }
});