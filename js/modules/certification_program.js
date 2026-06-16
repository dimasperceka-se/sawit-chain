Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

var form_data = {};
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    var selected_role = null;
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['CertProgID', 'CertProgName', 'CertProgOfficialName', 'CertProgLogo', 'CertProgAddress', 'CertProgPhone', 'CertProgEmail', 'CertProgWeb'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
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
    
    
    

    

    function displayFormWindow(editable) {
        if (editable===false) {            
            DataForm.query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(true);});
            DataForm.query('.itemselector').forEach(function(c){c.setDisabled(true);});
            Ext.getCmp('Photo').hide();
            Ext.getCmp('saveButton').hide();
        } else {
            DataForm.query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(false);});
            DataForm.query('.itemselector').forEach(function(c){c.setDisabled(false);});
            Ext.getCmp('Photo').show();
            Ext.getCmp('saveButton').show();
        }
        if (!win.isVisible()) {
            // resetForm();
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
            Ext.getCmp('CertProgID').setValue(data.CertProgID);
            Ext.getCmp('CertProgName').setValue(data.CertProgName);
            Ext.getCmp('CertProgOfficialName').setValue(data.CertProgOfficialName);
            Ext.getCmp('CertProgAddress').setValue(data.CertProgAddress);
            Ext.getCmp('CertProgPhone').setValue(data.CertProgPhone);
            Ext.getCmp('CertProgEmail').setValue(data.CertProgEmail);
            Ext.getCmp('CertProgWeb').setValue(data.CertProgWeb);
            if(data.CertProgLogo!='' && data.CertProgLogo!=null ){
                Ext.getCmp('iphoto').setSrc(data.CertProgLogo);
            }else{
                Ext.getCmp('iphoto').setSrc();
            }
            if(data.CertProgLogoPath!='' && data.CertProgLogoPath!=null ){
                Ext.getCmp('PhotoOld').setSrc(data.CertProgLogoPath);
            }else{
                Ext.getCmp('PhotoOld').setSrc();
            }
            
        } else {            
            Ext.getCmp('NationalityNm_local').setValue(true);
            Ext.getCmp('StatusCd').setValue('active');
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        id: 'dataForm',
        frame: false,
        width: 900,
        // height: 350,
        // autoScroll:true,
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
                                        xtype: 'hiddenfield',
                                        id: 'CertProgID',
                                        name: 'CertProgID',
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Program Name'),
                                        labelWidth: 120,
                                        allowBlank: false,
                                        id: 'CertProgName',
                                        name: 'CertProgName'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Official Name'),
                                        //allowBlank: false,
                                        id: 'CertProgOfficialName',
                                        name: 'CertProgOfficialName'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Address'),
                                        //allowBlank: false,
                                        id: 'CertProgAddress',
                                        name: 'CertProgAddress'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Phone'),
                                        //allowBlank: false,
                                        id: 'CertProgPhone',
                                        name: 'CertProgPhone'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Email'),
                                        allowBlank: false,
                                        vtype: 'email',
                                        id: 'CertProgEmail',
                                        name: 'CertProgEmail'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Website'),
                                        //allowBlank: false,
                                        id: 'CertProgWeb',
                                        name: 'CertProgWeb'
                                    }
                                ]
                            },
                            {
                                columnWidth: 0.49,
                                padding: '0 0 0 10',
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'image',
                                        id: 'iphoto',
                                        height: '160px'
                                    },{
                                        xtype: 'textfield',
                                        id: 'PhotoOld',
                                        name: 'PhotoOld',
                                        inputType: 'hidden'
                                    },
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
                                                var form = this.up('form').getForm();
                                                form.submit({
                                                    url: m_crud+'_logo',
                                                    clientValidation: false,
                                                    params: {
                                                        CertProgID: Ext.getCmp('CertProgID').getValue()
                                                    },
                                                    waitMsg: 'Sending Photo...',
                                                    success: function (fp, o) {
                                                        Ext.getCmp('iphoto').setSrc(o.result.file);
                                                        Ext.getCmp('PhotoOld').setValue(o.result.filepath);
                                                    }
                                                });
                                            }
                                        }
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
                if (Ext.getCmp('CertProgID').getValue() === '') methode = 'POST'; else methode = 'PUT';
                if (form.isValid()) {
                    form.submit({
                        url: m_crud,
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function (fp, o) {                            
                            if(o.result.success=='true'){
                                Ext.MessageBox.alert('Success', o.result.message);
                            }else{
                                Ext.MessageBox.alert('Error', o.result.message);
                            }
                            store.load();
                            /*if(o.result.photo!=''){
                                Ext.MessageBox.alert('Warning', o.result.photo);   
                            }else{
                                //Ext.getCmp('iphoto').setSrc(m_photo + '/' + o.result.photo_path + '?random=' + Date.now());
                            }
                            //Ext.MessageBox.alert('Success', 'Data saved.');*/
                            DataForm.getForm().reset();
                            win.hide(this, function () {
                                store.load({
                                    params: {
                                        key: Ext.getCmp('key').getValue()
                                    }
                                });
                            });
                        }
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
        title: 'Data Certification Program',
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
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
                    url: m_crud,
                    method: 'GET',
                    params: {CertProgID: sm.get('CertProgID')},
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
                            params: {CertProgID: smb.get('CertProgID')},
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case "true":
                                    Ext.MessageBox.alert('Warning', obj.message);
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
        //title: 'User List',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function(view, record, item, index, e){
               contextMenuGrid.showAt(e.getXY());
            }
            // itemdblclick: function (dv, record, item, index, e) {
            //     displayFormWindow();
            //     var sm = record;
            //     Ext.Ajax.request({
            //         url: m_crud,
            //         method: 'GET',
            //         params: {CertProgID: sm.get('CertProgID')},
            //         success: function (fp, o) {
            //             var data = Ext.decode(fp.responseText);
            //             set_form_value(data);
            //         }
            //     });
            // }
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
                        DataForm.getForm().reset();
                        Ext.getCmp('iphoto').setSrc();
                    },
                    cls: m_act_add?'':'hidden'
                }, 
                // {
                //     itemId: 'remove',
                //     icon: varjs.config.base_url + 'images/icons/new/delete.png',
                //     cls: m_act_delete,
                //     text: 'Hapus',
                //     scope: this,
                //     handler: function () {
                //         var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                //         Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                //             if (btn == 'yes') {
                //                 Ext.Ajax.request({
                //                     waitMsg: lang('Please Wait'),
                //                     url: m_crud,
                //                     method: 'DELETE',
                //                     params: {CertProgID: smb.raw.CertProgID},
                //                     success: function (response, opts) {
                //                         var obj = Ext.decode(response.responseText);
                //                         switch (obj.success) {
                //                             case true:
                //                                 store.load();
                //                                 break;
                //                             default:
                //                                 Ext.MessageBox.alert('Warning', obj.message);
                //                                 break;
                //                         }
                //                     },
                //                     failure: function (response, opts) {
                //                         var obj = Ext.decode(response.responseText);
                //                         Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                //                     }
                //                 });
                //             }
                //         });
                //     }
                // },                 
                //**Start Hapus**//
                
                {
                    xtype: 'textfield',
                    emptyText: lang('Program Name'),
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'key',
                    listeners: {
                        specialkey: submitOnEnter
                    },
                    width:400
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
                dataIndex: 'CertProgID',
                hidden: true
            },
            {
                text: 'No',
                xtype: 'rownumberer',
                align: 'center',
                width: 50,
            },
            {
                text: lang('Program Name'),
                flex: 2,
                dataIndex: 'CertProgName'
            },
            {
                text: lang('Official Name'),
                flex: 2,
                dataIndex: 'CertProgOfficialName'
            },
            {
                text: lang('Address'),
                flex: 2,
                dataIndex: 'CertProgAddress'
            },
            {
                text: lang('Phone'),
                flex: 1,
                dataIndex: 'CertProgPhone'
            },
            {
                text: lang('Email'),
                flex: 2,
                dataIndex: 'CertProgEmail'
            },
            {
                text: lang('Web'),
                flex: 2,
                dataIndex: 'CertProgWeb'
            }]
    });
});
