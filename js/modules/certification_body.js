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
        fields: ['CertBodyID', 'CertBodyName', 'CertBodyAddress', 'CertBodyPhone', 'CertBodyEmail'],
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
    
    var store_contact = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['CertBodyContactID', 'CertBodyID', 'ContactName', 'ContactGender', 'ContactEmail', 'ContactPhone', 'ContactAddress', 'ContactPosition', 'StatusCode'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 'contacts',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.CertBodyID = Ext.getCmp('CertBodyID').getValue();
                store.proxy.extraParams.key = Ext.getCmp('Ckey').getValue();
                //store.proxy.extraParams.holderType = Ext.getCmp('holderType').getValue();
            }
        }
    });
    
    var storee_contact = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [{name: 'addFarmerID'}, {name: 'addFarmerName'}, {name: 'addGardenNr'}],
        //pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'contact_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
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
    
    var cert_body = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'cert_body',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var cert_body_contact = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'cert_body_contact',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var first_buyer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'first_buyer',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var surveys = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'surveys',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'province',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'district',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var subdistrict = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'subdistrict',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function displayFormWindow(editable) {
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
        if(data) {
            Ext.getCmp('CertBodyID').setValue(data.CertBodyID);
            Ext.getCmp('ContactCertBodyID').setValue(data.CertBodyID);
            Ext.getCmp('CertBodyName').setValue(data.CertBodyName);
            Ext.getCmp('CertBodyAddress').setValue(data.CertBodyAddress);
            Ext.getCmp('CertBodyPhone').setValue(data.CertBodyPhone);
            Ext.getCmp('CertBodyEmail').setValue(data.CertBodyEmail);
            Ext.getCmp('iphoto').setSrc(data.CertBodyLogo);
            Ext.getCmp('PhotoOld').setSrc(data.CertBodyLogoPath);
            store_contact.load({
                params: {
                    CertBodyID: data.CertBodyID,
                    key: Ext.getCmp('Ckey').getValue()
                }
            });
        } else {            
            Ext.getCmp('iphoto').setSrc();
            Ext.getCmp('PhotoOld').setSrc();
        }
    }
    
    holders.load();
    cert_body.load();
    first_buyer.load();
    surveys.load();
    province.load();
    
    var DataForm = Ext.create('Ext.form.Panel', {
        // height: 500,
        // autoScroll: true,
        width: 900,
        id: 'dataForm',
        fileUpload: true,
        enctype:'multipart/form-data',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 130,
            anchor: '100%'
        },
        items: [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .5,
                layout: 'form',
                padding: 5,
                border: false,
                items: [{
                        xtype: 'hiddenfield',
                        id: 'CertBodyID',
                        name: 'CertBodyID',
                    }, {
                        xtype: 'textfield',
                        id: 'CertBodyName',
                        name: 'CertBodyName',
                        fieldLabel: lang('Name'),
                        allowBlank: false,
                    }, {
                        xtype: 'textfield',
                        id: 'CertBodyAddress',
                        name: 'CertBodyAddress',
                        fieldLabel: lang('Address'),
                    }, {
                        xtype: 'textfield',
                        id: 'CertBodyPhone',
                        name: 'CertBodyPhone',
                        fieldLabel: lang('Phone'),
                    }, {
                        xtype: 'textfield',
                        id: 'CertBodyEmail',
                        name: 'CertBodyEmail',
                        vtype: 'email',
                        fieldLabel: lang('Email'),
                    }]
            }, {
                columnWidth: .49,
                layout: 'form',
                padding: 5,
                border: false,
                items: [{
                    xtype: 'image',
                    id: 'iphoto',
                    height: '100px'
                },{
                    xtype: 'textfield',
                    id: 'PhotoOld',
                    name: 'PhotoOld',
                    inputType: 'hidden'
                },{
                    xtype: 'fileuploadfield',
                    fieldLabel: lang('Logo'),
                    labelWidth: 100,
                    id: 'Photo',
                    padding: 5,
                    name: 'Photo',
                    buttonText: 'Browse',
                    listeners: {
                        'change': function (fb, v) {
                            var form = this.up('form').getForm();
                            form.submit({
                                url: m_crud+'data_logo',
                                clientValidation: false,
                                params: {
                                    CertBodyID: Ext.getCmp('CertBodyID').getValue()
                                },
                                waitMsg: 'Sending Photo...',
                                success: function (fp, o) {
                                    Ext.getCmp('iphoto').setSrc(o.result.file);
                                    Ext.getCmp('PhotoOld').setValue(o.result.filepath);
                                }
                            });
                        }
                    }
                }]
            }]
        }, {
            xtype: 'tabpanel',
            flex: 1,
            margin:2,
            activeTab: 0,
            plain: true,
            items: [{ // grid nursery penjualan
                xtype: 'gridpanel',
                title: lang('Contact / Staff'),
                id: 'gContact',
                style: 'border:1px solid #CCC;',
                store: store_contact,
                width: '100%',
                height: 250,
                loadMask: true,
                selType: 'rowmodel',
                minHeight:190,
                listeners: {
                    itemclick: function(view, record, item, index, e){
                       contextMenuContactGrid.showAt(e.getXY());
                    }
                },
                dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        //cls: m_act_save,
                        text: lang('Add'),
                        scope: this,
                        handler: function() {
                            if(Ext.getCmp('CertBodyID').getValue()==''){
                                Ext.MessageBox.alert('Warning', 'Please save certification first!');
                            }else{
                                Ext.getCmp('dataFormAddContact').getForm().reset();
                                Ext.getCmp('ContactCertBodyID').setValue(Ext.getCmp('CertBodyID').getValue());
                                displayAddWindowContact();
                            }
                        }
                    }, {
                        xtype: 'textfield',
                        name: 'Ckey',
                        id: 'Ckey',
                        emptyText: lang('Keyword'),
                        width: 280,
                        listeners: {}
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store_contact.load({
                                params: {
                                    CertBodyID: Ext.getCmp('CertBodyID').getValue(),
                                    key: Ext.getCmp('Ckey').getValue()
                                }
                            });
                        }
                    }]
                }, {
                    xtype: 'pagingtoolbar',
                    store: store_contact,   // same store GridPanel is using
                    dock: 'bottom',
                    displayInfo: true
                }],
                columns: [{
                    text: 'ID',
                    dataIndex: 'CertBodyContactID',
                    hidden: true
                },
                {
                    text: 'ID2',
                    dataIndex: 'CertBodyID',
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
                    dataIndex: 'ContactName'
                },
                {
                    text: lang('Gender'),
                    flex: 2,
                    dataIndex: lang('ContactGender')
                },
                {
                    text: lang('Email'),
                    flex: 2,
                    dataIndex: 'ContactEmail'
                },
                {
                    text: lang('Phone'),
                    flex: 2,
                    dataIndex: 'ContactPhone'
                },
                {
                    text: lang('Address'),
                    flex: 2,
                    dataIndex: 'ContactAddress'
                },
                {
                    text: lang('Position'),
                    flex: 2,
                    dataIndex: 'ContactPosition'
                },
                {
                    text: lang('Status'),
                    flex: 2,
                    dataIndex: 'StatusCode'
                }]
            }]
        }],
        buttons: [{
                id: 'save_par',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var form = this.up('form').getForm();
                    if(Ext.getCmp('CertBodyName').getValue() == ''){
                        Ext.MessageBox.alert('Warning', lang('Please insert name!'));
                    }else{
                        form.submit({
                            url: m_crud + 'data',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            success: function(fp, o) {
                                Ext.getCmp('CertBodyID').setValue(o.result.CertBodyID);

                                Ext.MessageBox.alert('Success', o.result.message);

                                win.hide(this, function () {                                    
                                    store.load();
                                });
                            },
                            failure: function (response, opts) {
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
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
                    win.hide();
                    /*store_training.load({
                        params: {
                            cpg_id: Ext.getCmp('id').getValue()
                        }
                    });*/
                }
            }]
    });
    
    var win = Ext.create('widget.window', {
        title: lang('Data Certification Body'),
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        // width: '70%',
        // height: '70%',
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
                key: Ext.getCmp('key').getValue()
            }
        });
    }
    
    function displayAddWindowContact() {
        if (!winAddContact.isVisible()) {
            /*storee_contact.load({
                params: {
                    CertBodyID: Ext.getCmp('CertBodyID').getValue(),
                    //CpgBatchTrainingID: Ext.getCmp('idt').getValue(),
                    //cpgID: Ext.getCmp('idd').getValue()
                }
            });*/
            winAddContact.show();
        } else {
            winAddContact.hide(this, function() {
            });
            winAddContact.toFront();
        }
    }
    
    var statusCode = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "active",
            "label": lang("Active")
        }, {
            "id": "inactive",
            "label": lang("Not Active")
        }]
    });
    
    var contactGender = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "m",
            "label": lang("Male")
        }, {
            "id": "f",
            "label": lang("Female")
        }]
    });
    
    var DataFormAddContact = Ext.create('Ext.form.Panel', {
        id: 'dataFormAddContact',
        frame: false,
        width: 500,
        height: 400,
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
                                        xtype: 'textfield',
                                        fieldLabel: lang('Name'),
                                        labelWidth: 120,
                                        id: 'CertBodyContactID',
                                        name: 'CertBodyContactID',
                                        hidden: true
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Name'),
                                        labelWidth: 120,
                                        id: 'ContactCertBodyID',
                                        name: 'ContactCertBodyID',
                                        hidden: true
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Name'),
                                        labelWidth: 120,
                                        id: 'ContactName',
                                        name: 'ContactName',
                                        allowBlank: false
                                    }, {
                                        xtype: 'combobox',
                                        fieldLabel: lang('Gender'),
                                        id: 'ContactGender',
                                        name: 'ContactGender',
                                        store: contactGender,
                                        queryMode: 'local',
                                        allowBlank : false,
                                        displayField: 'label',
                                        valueField: 'id'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Email'),
                                        labelWidth: 120,
                                        id: 'ContactEmail',
                                        vtype: 'email',
                                        name: 'ContactEmail'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Phone'),
                                        labelWidth: 120,
                                        id: 'ContactPhone',
                                        name: 'ContactPhone'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Address'),
                                        labelWidth: 120,
                                        id: 'ContactAddress',
                                        name: 'ContactAddress'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Position'),
                                        labelWidth: 120,
                                        id: 'ContactPosition',
                                        name: 'ContactPosition'
                                    }, {
                                        xtype: 'combobox',
                                        fieldLabel: lang('Status'),
                                        id: 'CertBodyStatusCode',
                                        name: 'StatusCode',
                                        store: statusCode,
                                        queryMode: 'local',
                                        allowBlank : false,
                                        displayField: 'label',
                                        valueField: 'id'
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
                if (Ext.getCmp('CertBodyContactID').getValue() === '') methode = 'POST'; else methode = 'PUT';
                if (form.isValid()) {
                    form.submit({
                        url: m_crud + 'contact',
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function (fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                        },
                        failure: function (response, opts) {
                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                        }
                    });
                    winAddContact.hide(this, function () {
                        store_contact.load({
                            params: {
                                CertBodyID: Ext.getCmp('CertBodyID').getValue(),
                                key: Ext.getCmp('Ckey').getValue()
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
                winAddContact.hide();
            }
        }]
    });
    
    var winAddContact = Ext.widget('window', {
        title: lang('Add Contact / Staff'),
        id: 'winAddContact',
        closeAction: 'hide',
        height: 400,
        autoScroll: true,
        width: 500,
        bodyPadding: 5,
        modal: true,
        layout: 'fit',
        items: [DataFormAddContact]
    });
    
    var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: !m_act_update,
            handler: function(){
                Ext.getCmp('iphoto').setSrc('');
                Ext.getCmp('dataForm').getForm().reset();
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormWindow(true);
                Ext.Ajax.request({
                    url: m_crud + 'detail',
                    method: 'GET',
                    params: {CertBodyID: sm.get('CertBodyID')},
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
                            params: {CertBodyID: smb.raw.CertBodyID},
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
    
    var contextMenuContactGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: !m_act_update,
            handler: function(){
                var sm = Ext.getCmp('gContact').getSelectionModel().getSelection()[0];
                Ext.Ajax.request({
                    url: m_crud + 'contact_detail',
                    method: 'GET',
                    params: {CertBodyContactID: sm.get('CertBodyContactID')},
                    success: function (fp, o) {
                        var data = Ext.decode(fp.responseText);
                        Ext.getCmp('ContactName').setValue(data.ContactName);
                        Ext.getCmp('ContactEmail').setValue(data.ContactEmail);
                        Ext.getCmp('ContactPhone').setValue(data.ContactPhone);
                        Ext.getCmp('ContactAddress').setValue(data.ContactAddress);
                        Ext.getCmp('ContactPosition').setValue(data.ContactPosition);
                        Ext.getCmp('CertBodyID').setValue(data.CertBodyID);
                        Ext.getCmp('CertBodyContactID').setValue(data.CertBodyContactID);
                        Ext.getCmp('CertBodyStatusCode').setValue(data.StatusCode);
                        Ext.getCmp('ContactGender').setValue(data.ContactGender);
                        displayAddWindowContact();
                    },
                    failure: function (response, opts) {
                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                    }
                });
            }
        }, {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: !m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('gContact').getSelectionModel().getSelection()[0];
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_crud + 'contact',
                            method: 'DELETE',
                            params: {
                                CertBodyContactID: smb.raw.CertBodyContactID
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                    store_contact.load({
                                        params: {
                                            CertBodyID: Ext.getCmp('Ckey').getValue(),
                                            key: Ext.getCmp('Ckey').getValue()
                                        }
                                    });
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
                dataIndex: 'CertBodyID',
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
                flex: 3,
                dataIndex: 'CertBodyName'
            },
            {
                text: lang('Address'),
                flex: 3,
                dataIndex: 'CertBodyAddress'
            },
            {
                text: lang('Phone'),
                flex: 2,
                dataIndex: 'CertBodyPhone'
            },
            {
                text: lang('Email'),
                flex: 2,
                dataIndex: 'CertBodyEmail'
            }]
    });
});
