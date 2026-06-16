if (Ext.getCmp('win')) Ext.getCmp('win').destroy();
var form_data = {};
var store;
var DataForm;
var win;
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    
    store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            'id',
            'CoopTrainingName',
            'AltName',
            'Abbreviation',
            'StatusCode',
            'Remarks',
        ],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'count'
            }
        }
    });

    var status_code = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [
        {
            "id": "active",
            "name": lang("Active")
        }, 
        {
            "id": "inactive",
            "name": lang("Inactive")
        }, 
        // {
        //     "id": "nullified",
        //     "name": lang("Nullified")
        // },
        ]
    });

    function displayFormWindow(editable) {
        if (editable===false) {            
            DataForm.query('.textfield, .textareafield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(true);});
            Ext.getCmp('saveButton').hide();
        } else {
            DataForm.query('.textfield, .textareafield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(false);});
            Ext.getCmp('saveButton').show();
        }
        if (!win.isVisible()) {
            win.show();
        } else {
            win.hide(this, function () {});
            win.toFront();
        }
    }

    function set_form_value(data) {
        DataForm.getForm().reset();
        form_data = data;
        if (data) {
            Ext.getCmp('id').setValue(data.id);
            Ext.getCmp('CoopTrainingName').setValue(data.CoopTrainingName);
            Ext.getCmp('AltName').setValue(data.AltName);
            Ext.getCmp('Abbreviation').setValue(data.Abbreviation);
            Ext.getCmp('StatusCode').setValue(data.StatusCode);
            Ext.getCmp('Remarks').setValue(data.Remarks);
        } else {
            Ext.getCmp('StatusCode').setValue('active');
        }
    }


    DataForm = Ext.create('Ext.form.Panel', {
        id: 'dataForm',
        frame: false,
        width: 450,
        height: 350,
        autoScroll:true,
        bodyPadding: 10,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            padding: 10,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'textfield',
                id: 'id',
                name: 'id',
                inputType: 'hidden'
            },
            {
                xtype: 'textfield',
                fieldLabel: lang('Name'),
                allowBlank: false,
                id: 'CoopTrainingName',
                name: 'CoopTrainingName'
            },
            {
                xtype: 'textfield',
                fieldLabel: lang('Alternative Name'),
                allowBlank: false,
                id: 'AltName',
                name: 'AltName'
            },
            {
                xtype: 'textfield',
                fieldLabel: lang('Abbreviation'),
                allowBlank: false,
                id: 'Abbreviation',
                name: 'Abbreviation'
            },
            {
                id: 'StatusCode',
                name: 'StatusCode',
                xtype: 'combobox',
                anchor: '50%',
                fieldLabel: lang('Status'),
                store: status_code,
                displayField: 'name',
                allowBlank: false,
                valueField: 'id',
                queryMode: 'local',
            },
            {
                xtype: 'textareafield',
                fieldLabel: lang('Remarks'),
                allowBlank: true,
                id: 'Remarks',
                name: 'Remarks'
            },
        ],
        buttons: [{
            id: 'saveButton',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('id').getValue() === '') methode = 'POST'; else methode = 'PUT';
                if (form.isValid()) {
                    form.submit({
                        url: m_crud,
                        method: methode,
                        waitMsg: lang('Sending data...'),
                        success: function (fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                        }
                    });
                    win.hide(this, function () {
                        store.load();
                    });
                }
            }
        }, {
            text: 'Close',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function () {
                win.hide();
            }
        }]
    });

    win = Ext.create('widget.window', {
        title: lang('Cooperative Training'),
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: 450,
        // minWidth: 570,
        height: 350,
        layout: 'fit',
        items: [DataForm]
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            filterRecord();
        }
    }

    var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/view.png',
            text: lang('View'),
            hidden: false,
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormWindow(false);
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {id: sm.get('id')},
                    success: function (fp, o) {
                        var data = Ext.decode(fp.responseText);
                        set_form_value(data);
                    }
                });
            }
        },
        {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: !m_act_update,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormWindow(true);
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {id: sm.get('id')},
                    success: function (fp, o) {
                        var data = Ext.decode(fp.responseText);
                        set_form_value(data);
                    }
                });
            }
        },
        {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: !m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_crud,
                            method: 'DELETE',
                            params: {id: smb.raw.id},
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Info', lang('Data deleted'));
                                        store.load();
                                    break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                                }
                            },
                            failure: function (response, opts) {
                                // var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                            }
                        });
                    }
                });
            }
        }
        ]
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 250,
        //title: 'CPG Training',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function(view, record, item, index, e){
               contextMenuGrid.showAt(e.getXY());
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        }, {
            xtype: 'toolbar',
            items: [
                {
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    scope: this,
                    cls: m_act_add,
                    handler: function () {
                        displayFormWindow(true);
                        set_form_value();
                    }
                }, 
            ]
        }],
        columns: [
           {
               dataIndex: 'id',
               hidden:true
           },
            {
                text: lang('No'),
                xtype: 'rownumberer',
                align: 'center',
                width: '100px'
            },
            {
                text: lang('Training Name'),
                flex: 2,
                dataIndex: 'CoopTrainingName',
            },
            {
                text: lang('Alternative Name'),
                flex: 2,
                dataIndex: 'AltName',
            },
            {
                text: lang('Abbreviation'),
                flex: 1,
                dataIndex: 'Abbreviation',
            },
            {
                text: lang('StatusCode'),
                flex: 1,
                dataIndex: 'StatusCode',
            },
            {
                text: lang('Remarks'),
                flex: 3,
                dataIndex: 'Remarks',
            },
        ],
    });
});
