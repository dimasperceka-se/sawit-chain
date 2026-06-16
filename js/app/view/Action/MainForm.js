Ext.define('Koltiva.view.Action.MainForm', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Action.MainForm',
    title: lang('Form'),
    modal: true,
    width: '34%',
    height: 220,
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.items = [{
            xtype: 'form',
            height: '100%',
            width: '100%',
            bodyPadding: 5,
            id: 'Koltiva.view.Action.MainForm-Form',
            items: [
                {
                    xtype: 'textfield',
                    id: 'Koltiva.view.Action.MainForm-AksiID',
                    name: 'Koltiva.view.Action.MainForm-AksiID',
                    hidden: true
                },{
                    xtype: 'textfield',
                    id: 'Koltiva.view.Action.MainForm-AksiName',
                    name: 'Koltiva.view.Action.MainForm-AksiName',
                    fieldLabel: lang('Name'),
                    width: 490,
                    labelWidth: 120,
                    labelAlign: 'left',
                    allowBlank: false,
                },{
                    xtype: 'textfield',
                    id: 'Koltiva.view.Action.MainForm-AksiFungsi',
                    name: 'Koltiva.view.Action.MainForm-AksiFungsi',
                    fieldLabel: lang('Function'),
                    width: 490,
                    labelWidth: 120,
                    labelAlign: 'left',
                    allowBlank: false,
                },{
                    html: '<br>'
                },{
                    html: '<br>'
                }
            ],
            buttons: [
                {
                    id: 'Koltiva.view.Action.MainForm-ButtonSave',
                    text: lang('Save'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function() {
                        var form = this.up('form').getForm(),
                            method = (thisObj.viewVar.opsiDisplay == 'add') ? 'POST': 'PUT';
                        if (form.isValid()) {
                            form.submit({
                                url: m_crud,
                                method: method,
                                clientValidation: true,
                                waitMsg: 'Sending data...',
                                success: function(form, action) {
                                    thisObj.viewVar.caller.reloadGridAndCloseWin();
                                    Ext.MessageBox.alert(lang('Success'), lang(action.result.message));
                                },
                                failure: function(form, action) {
                                    thisObj.viewVar.caller.reloadGridAndCloseWin();
                                    Ext.MessageBox.alert(lang('Success'), lang(action.result.message));
                                }
                            });
                        }
                    }
                }, {
                    text: lang('Close'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-grey',
                    disabled: false, 
                    handler: function() {
                        thisObj.close();
                    }
                }
            ]
        }]

        this.callParent(arguments);
    }
});