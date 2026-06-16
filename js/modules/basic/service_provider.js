if (Ext.getCmp('win')) Ext.getCmp('win').destroy();
var form_data = {};
var store;
var DataForm;
var win;
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();

    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'ServiceProvName','OfficialName','Abbreviation','BsnSectorID','Sector','Address','DistrictID','District','OfficialPhone','OfficialEmail','Photo','Logo','StatusCode','Remarks',],
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

    var sectors = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_sector_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var provinces = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_province_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var districts = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_district_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });    

    var status_code = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [{
            "id": "active",
            "name": lang("Active")
        }, {
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
            DataForm.query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(true);});
            Ext.getCmp('saveButton').hide();
        } else {
            DataForm.query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(false);});
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
            Ext.getCmp('ServiceProvName').setValue(data.ServiceProvName);
            Ext.getCmp('OfficialName').setValue(data.OfficialName);
            Ext.getCmp('Abbreviation').setValue(data.Abbreviation);
            Ext.getCmp('BsnSectorID').setValue(data.BsnSectorID);
            Ext.getCmp('Address').setValue(data.Address);
            Ext.getCmp('ProvinceID').setValue(data.ProvinceID);
            Ext.getCmp('DistrictID').setValue(data.DistrictID);
            Ext.getCmp('OfficialPhone').setValue(data.OfficialPhone);
            Ext.getCmp('OfficialEmail').setValue(data.OfficialEmail);
            Ext.getCmp('Photo').setValue(data.Photo);
            Ext.getCmp('Logo').setValue(data.Logo);
            Ext.getCmp('StatusCode').setValue(data.StatusCode);
            Ext.getCmp('Remarks').setValue(data.Remarks);
        } else {
            Ext.getCmp('StatusCode').setValue('active');
        }
    }


    var DataForm = Ext.create('Ext.form.Panel', {
        id: 'dataForm',
        frame: false,
        width: 900,
        height: 450,
        autoScroll:true,
        fileUpload: true,
        enctype:'multipart/form-data',
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
                                columnWidth: 0.5,
                                layout: 'form',
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
                                        allowBlank: true,
                                        id: 'ServiceProvName',
                                        name: 'ServiceProvName'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Official Name'),
                                        allowBlank: true,
                                        id: 'OfficialName',
                                        name: 'OfficialName'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Abbreviation'),
                                        allowBlank: true,
                                        id: 'Abbreviation',
                                        name: 'Abbreviation'
                                    },
                                    {
                                        id: 'BsnSectorID',
                                        name: 'BsnSectorID',
                                        xtype: 'combobox',
                                        fieldLabel: lang('Sector'),
                                        store: sectors,
                                        displayField: 'label',
                                        valueField: 'id',
                                        allowBlank: false,
                                        queryMode: 'local',
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Address'),
                                        allowBlank: true,
                                        id: 'Address',
                                        name: 'Address'
                                    },
                                    {
                                        id: 'ProvinceID',
                                        name: 'ProvinceID',
                                        xtype: 'combobox',
                                        fieldLabel: lang('Province'),
                                        store: provinces,
                                        displayField: 'label',
                                        valueField: 'id',
                                        allowBlank: false,
                                        queryMode: 'local',
                                        listeners: {
                                            change: function (cb, nv, ov) {
                                                Ext.getCmp('DistrictID').setValue('');
                                                districts.load({
                                                    params: {
                                                        ProvinceID: Ext.getCmp('ProvinceID').getValue()
                                                    }
                                                });

                                                if (typeof(form_data) !== 'undefined')
                                                if (typeof(form_data.DistrictID) !== 'undefined') {
                                                    Ext.getCmp('DistrictID').setValue(form_data.DistrictID);
                                                }
                                            }
                                        }
                                    },
                                    {
                                        id: 'DistrictID',
                                        name: 'DistrictID',
                                        xtype: 'combobox',
                                        fieldLabel: lang('District'),
                                        store: districts,
                                        displayField: 'label',
                                        valueField: 'id',
                                        allowBlank: false,
                                        queryMode: 'local',
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Official Phone'),
                                        allowBlank: true,
                                        id: 'OfficialPhone',
                                        name: 'OfficialPhone'
                                    },
                                    {
                                        xtype: 'textfield',
                                        vtype: 'email',
                                        fieldLabel: lang('Official Email'),
                                        allowBlank: true,
                                        id: 'OfficialEmail',
                                        name: 'OfficialEmail'
                                    },
                                ]
                            },
                            {
                                columnWidth: 0.5,
                                padding: '0 0 0 10',
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'fileuploadfield',
                                        fieldLabel: lang('Photo'),
                                        labelWidth: 120,
                                        id: 'Photo',
                                        padding: 5,
                                        name: 'Photo',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function (fb, v) {
                                                // do something
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'fileuploadfield',
                                        fieldLabel: lang('Logo'),
                                        labelWidth: 120,
                                        id: 'Logo',
                                        padding: 5,
                                        name: 'Logo',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function (fb, v) {
                                                // do something
                                            }
                                        }
                                    },
                                    {
                                        id: 'StatusCode',
                                        name: 'StatusCode',
                                        xtype: 'combobox',
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
                                ]
                            },
                        ]
                    }
                ]
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
                        success: function (form, action) {
                            var response = Ext.decode(action.response.responseText);
                            var msg = lang('Data saved.');
                            if (response.errors !== null) {
                                if (typeof(response.errors.Photo) !== 'undefined') {
                                    msg += "<br/>Photo Error : "+response.errors.Photo;
                                }
                                if (typeof(response.errors.Logo) !== 'undefined') {
                                    msg += "<br/>Logo Error: "+response.errors.Logo;
                                }
                            }
                            console.log(msg);
                            Ext.MessageBox.alert('Success', msg);
                        },
                        failure: function (response, opts) {
                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                        }
                    });
                    win.hide(this, function () {
                        store.load({
                            // params: {
                            //     key: Ext.getCmp('key').getValue()
                            // }
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
        title: 'Data User',
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: 900,
        minWidth: 570,
        height: 450,
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
                                    store.load();
                                    break;
                                    default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                                }
                            },
                            failure: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
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
                    hidden: !m_act_add,
                    handler: function () {
                        displayFormWindow(true);
                        DataForm.getForm().reset();
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
                width: '50px',
                align: 'center',
            },
            {
                text: lang('Name'),
                flex: 1,
                dataIndex: 'ServiceProvName',
            },
            {
                text: lang('Official Name'),
                flex: 1,
                dataIndex: 'OfficialName',
            },
            {
                text: lang('Abbreviation'),
                flex: 1,
                dataIndex: 'Abbreviation',
            },
            {
                text: lang('Sector'),
                flex: 1,
                dataIndex: 'Sector',
            },
            {
                text: lang('Address'),
                flex: 1,
                dataIndex: 'Address',
            },
            {
                text: lang('District'),
                flex: 1,
                dataIndex: 'District',
            },
            {
                text: lang('Phone'),
                flex: 1,
                dataIndex: 'OfficialPhone',
            },
            {
                text: lang('Email'),
                flex: 1,
                dataIndex: 'OfficialEmail',
            },
            // {
            //     text: lang('Photo'),
            //     flex: 1,
            //     dataIndex: 'Photo',
            // },
            // {
            //     text: lang('Logo'),
            //     flex: 1,
            //     dataIndex: 'Logo',
            // },
            {
                text: lang('Status'),
                flex: 1,
                dataIndex: 'StatusCode',
            },
            {
                text: lang('Remarks'),
                flex: 1,
                dataIndex: 'Remarks',
            },
        ],
    });
});
