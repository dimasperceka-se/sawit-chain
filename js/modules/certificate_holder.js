Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);

var form_data = {};
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    var selected_role = null;
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['CertHolderID', 'HolderName', 'HolderType', 'ProgramName', 'GIPNumber', 'CertProgMemberID', 'CertProgMemberDate'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 'data',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.key = Ext.getCmp('key').getValue();
                store.proxy.extraParams.holderType = Ext.getCmp('holderType').getValue();
            }
        }
    });
    
    var holder_type = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_crud+'holder_type',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var holders = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'holders',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var programs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'certification_programs',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    programs.load();

    function displayFormWindow(editable) {
        /*if (editable===false) {            
            DataForm.query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(true);});
            DataForm.query('.itemselector').forEach(function(c){c.setDisabled(true);});
            Ext.getCmp('Photo').hide();
            Ext.getCmp('saveButton').hide();
        } else {
            DataForm.query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(false);});
            DataForm.query('.itemselector').forEach(function(c){c.setDisabled(false);});
            Ext.getCmp('Photo').show();
            Ext.getCmp('saveButton').show();
        }*/
        if (!win.isVisible()) {
            //resetForm();
            win.show();
        } else {
            win.hide(this, function () {
            });
            win.toFront();
        }
    }

    function set_form_value(data) {
        form_data = data;
        Ext.getCmp('dataForm').getForm().reset();
        if(data) {
            Ext.getCmp('CertHolderID').setValue(data.CertHolderID);
            Ext.getCmp('ObjType').setValue(data.ObjType);
            Ext.getCmp('ObjID').setValue(data.ObjID);
            Ext.getCmp('HoldersCertProgID').setValue(data.CertProgID);
            Ext.getCmp('GIPNumber').setValue(data.GIPNumber);
            Ext.getCmp('CertProgMemberID').setValue(data.CertProgMemberID);
            Ext.getCmp('CertProgMemberDate').setValue(data.CertProgMemberDate);
        } else {            
            //Ext.getCmp('NationalityNm_local').setValue(true);
            //Ext.getCmp('StatusCd').setValue('active');
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
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
            // anchor: '100%'
        },
        items: [
            {
                xtype: 'panel',
                autoScroll: true,
                items: [
                    {
                        layout: 'column',
                        border: false,
                        items: [
                            {
                                columnWidth: 1,
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'hiddenfield',
                                        id: 'CertHolderID',
                                        name: 'CertHolderID',
                                    }, {
                                        xtype: 'combobox',
                                        fieldLabel: lang('Type'),
                                        id: 'ObjType',
                                        name: 'ObjType',
                                        store: holder_type,
                                        allowBlank: false,
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                        listeners: {
                                            change: function (cb, nv, ov) {
                                                Ext.getCmp('ObjID').setValue('');
                                                holders.load({
                                                    params: {
                                                        OrgType: Ext.getCmp('ObjType').getValue()
                                                    }
                                                });
                                            }
                                        }
                                    }, {
                                        id: 'ObjID',
                                        name: 'ObjID',
                                        xtype: 'combobox',
                                        fieldLabel: lang('Holder Name'),
                                        store: holders,
                                        displayField: 'label',
                                        valueField: 'id',
                                        allowBlank: false,
                                        queryMode: 'local',
                                        listeners: {
                                            
                                        }
                                    }, {
                                        xtype: 'combobox',
                                        fieldLabel: lang('Program Name'),
                                        id: 'HoldersCertProgID',
                                        name: 'CertProgID',
                                        store: programs,
                                        allowBlank: false,
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('GIP Number'),
                                        labelWidth: 120,
                                        id: 'GIPNumber',
                                        name: 'GIPNumber'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Member ID'),
                                        labelWidth: 120,
                                        id: 'CertProgMemberID',
                                        name: 'CertProgMemberID'
                                    }, {
                                        xtype: 'datefield',
                                        fieldLabel: lang('Member Date'),
                                        id: 'CertProgMemberDate',
                                        name: 'CertProgMemberDate',
                                        format: 'Y-m-d'
                                    }
                                ]
                            }
                        ]
                    },
                ],
            }
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
                if (Ext.getCmp('CertHolderID').getValue() === '') methode = 'POST'; else methode = 'PUT';
                if (form.isValid()) {
                    form.submit({
                        url: m_crud + 'data',
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function (fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                        },
                        failure: function (response, opts) {
                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                        }
                    });
                    win.hide(this, function () {
                        store.load({
                            params: {
                                key: Ext.getCmp('key').getValue()
                            }
                        });
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
    var win = Ext.create('widget.window', {
        title: lang('Data Certification Holder'),
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: 450,
        minWidth: 350,
        height: 350,
        layout: 'fit',
        items: [DataForm]
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            filterRecord();
        }
    }

    function filterRecord() {
        store.load({
            params: {
                start: 0,
                key: Ext.getCmp('key').getValue(),
                holderType: Ext.getCmp('holderType').getValue(),
            }
        });
    }
    var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: !m_act_update,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormWindow(true);
                Ext.Ajax.request({
                    url: m_crud + 'detail',
                    method: 'GET',
                    params: {CertHolderID: sm.get('CertHolderID')},
                    success: function (fp, o) {
                        var data = Ext.decode(fp.responseText);
                        set_form_value(data);
                    },
                    failure: function (response, opts) {
                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
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
                            url: m_crud + 'data',
                            method: 'DELETE',
                            params: {CertHolderID: smb.raw.CertHolderID},
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                    store.load();
                                    Ext.MessageBox.alert('Success', obj.message);
                                    break;
                                    default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                                }
                            },
                            failure: function (response, opts) {
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
        //title: 'User List',
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
                    text: 'Add',
                    scope: this,
                    handler: function() {
                        displayFormWindow(true);
                        set_form_value();
                    },
                    cls: m_act_add?'':'hidden'
                },                  
                {
                    xtype: 'combobox',
                    id: 'holderType',
                    name: 'holderType',
                    emptyText: lang('Type'),
                    store: holder_type,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        
                    }
                },
                {
                    xtype: 'textfield',
                    emptyText: lang('Keyword'),
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'key',
                    listeners: {
                        specialkey: submitOnEnter
                    }
                }, 
                {
                    xtype: 'button',
                    margin: '0px 0px 0px 6px',
                    text: 'Search',
                    handler: function () {
                        filterRecord();
                    }
                }]
        }],
        columns: [
            {
                text: 'ID',
                dataIndex: 'CertHolderID',
                hidden: true
            },
            {
                text: 'No',
                xtype: 'rownumberer',
                align: 'center',
                width: 50,
            },
            {
                text: lang('Holder Name'),
                flex: 3,
                dataIndex: 'HolderName'
            },
            {
                text: lang('Type'),
                flex: 2,
                dataIndex: 'HolderType'
            },
            {
                text: lang('Program Name'),
                flex: 3,
                dataIndex: 'ProgramName'
            },
            {
                text: lang('GIP Number'),
                flex: 2,
                dataIndex: 'GIPNumber'
            },
            {
                text: lang('Member ID'),
                flex: 2,
                dataIndex: 'CertProgMemberID'
            },
            {
                text: lang('Member Date'),
                flex: 2,
                dataIndex: 'CertProgMemberDate'
            }]
    });
});
