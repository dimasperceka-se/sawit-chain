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
        fields: ['FirstBuyerID','PartnerName', 'PartnerIndustry', 'PartnerFullName', 'PartnerProgramName'],
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
            }
        }
    });
    var partners = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'partners',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

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
            partners.load({
                params: {
                    PartnerID: data.FirstBuyerPartnerID
                }
            });
            Ext.getCmp('FirstBuyerID').setValue(data.FirstBuyerID);
            Ext.getCmp('FirstBuyerPartnerID').setValue(data.FirstBuyerPartnerID);
        } else {            
            Ext.getCmp('FirstBuyerPartnerID').setValue('');
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        id: 'dataForm',
        frame: false,
        width: 450,
        // height: 350,
        // autoScroll:true,
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
                                        id: 'FirstBuyerID',
                                        name: 'FirstBuyerID',
                                    }, {
                                        id: 'FirstBuyerPartnerID',
                                        name: 'FirstBuyerPartnerID',
                                        xtype: 'combobox',
                                        fieldLabel: lang('Name'),
                                        store: partners,
                                        displayField: 'label',
                                        valueField: 'id',
                                        allowBlank: false,
                                        queryMode: 'local',
                                        listeners: {
                                            change: function (cb, nv, ov) {
                                                Ext.Ajax.request({
                                                    url: m_crud + 'set_partner',
                                                    method: 'GET',
                                                    params: {PartnerID: Ext.getCmp('FirstBuyerPartnerID').getValue()},
                                                    success: function (fp, o) {
                                                        var data = Ext.decode(fp.responseText);
                                                        Ext.getCmp('PartnerIndustry').setValue(data.PartnerIndustry);
                                                        Ext.getCmp('PartnerFullName').setValue(data.PartnerFullName);
                                                        Ext.getCmp('PartnerProgramName').setValue(data.PartnerProgramName);
                                                    },
                                                    failure: function (response, opts) {
                                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                                    }
                                                });
                                            }
                                        }
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Industry'),
                                        labelWidth: 120,
                                        id: 'PartnerIndustry',
                                        name: 'PartnerIndustry',
                                        readOnly: true
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Full Name'),
                                        labelWidth: 120,
                                        id: 'PartnerFullName',
                                        name: 'PartnerFullName',
                                        readOnly: true
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Program Name'),
                                        labelWidth: 120,
                                        id: 'PartnerProgramName',
                                        name: 'PartnerProgramName',
                                        readOnly: true
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
                if (Ext.getCmp('FirstBuyerID').getValue() === '') methode = 'POST'; else methode = 'PUT';
                if (form.isValid()) {
                    form.submit({
                        url: m_crud + 'data',
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', o.result.message);
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
        title: lang('Data First Buyer'),
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        // width: 450,
        // minWidth: 350,
        // height: 350,
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
                    params: {FirstBuyerID: sm.get('FirstBuyerID')},
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
                            params: {FirstBuyerID: smb.raw.FirstBuyerID},
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
                        partners.load();
                        displayFormWindow(true);
                        set_form_value();
                    },
                    cls: m_act_add?'':'hidden'
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
                dataIndex: 'FirstBuyerID',
                hidden: true
            },
            {
                text: 'No',
                xtype: 'rownumberer',
                align: 'center',
                width: 50,
            },
            {
                text: lang('Name'),
                flex: 2,
                dataIndex: 'PartnerName'
            },
            {
                text: lang('Industry'),
                flex: 2,
                dataIndex: 'PartnerIndustry'
            },
            {
                text: lang('Full Name'),
                flex: 2,
                dataIndex: 'PartnerFullName'
            },
            {
                text: lang('Program Name'),
                flex: 2,
                dataIndex: 'PartnerProgramName'
            }]
    });
});
