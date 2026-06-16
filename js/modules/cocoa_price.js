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
        fields: ['CocoaPriceID', 'CocoaPriceDate', 'DistrictID', 'District', 'Type', 'CocoaPrice'],
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
                store.proxy.extraParams.prov = m_prov;
                store.proxy.extraParams.key = Ext.getCmp('key').getValue();
                store.proxy.extraParams.dateStart = Ext.getCmp('dateStart').getValue();
                store.proxy.extraParams.dateEnd = Ext.getCmp('dateEnd').getValue();
            }
        }
    });

    function displayFormWindow(editable) {
        if (editable===false) {            
            DataForm.query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(true);});
            DataForm.query('.itemselector').forEach(function(c){c.setDisabled(true);});
            //Ext.getCmp('Photo').hide();
            Ext.getCmp('saveButton').hide();
        } else {
            DataForm.query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(false);});
            DataForm.query('.itemselector').forEach(function(c){c.setDisabled(false);});
            //Ext.getCmp('Photo').show();
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
            Ext.getCmp('CocoaPriceID').setValue(data.CocoaPriceID);
            Ext.getCmp('Provinsi').setValue(data.Province);
            Ext.getCmp('Kabupaten').setValue(data.District);
            Ext.getCmp('CocoaPriceDate').setValue(data.CocoaPriceDate);
            Ext.getCmp('CocoaPriceType').setValue(data.Type);
            Ext.getCmp('CocoaPrice').setValue(data.CocoaPrice);
        }
    }

    var mc_Provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Provinsi,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Kabupaten,
            extraParams: {
                prov: m_prov
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var CocoaPriceType = Ext.create('Ext.data.Store', {
        fields: ['id'],
        data: [{
            "id":"FF"
        }, {
            "id":"FAQ"
        }]
   });

    var DataForm = Ext.create('Ext.form.Panel', {
        id: 'dataForm',
        frame: false,
        width: 450,
        height: 600,
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
                                columnWidth: 1,
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'hiddenfield',
                                        id: 'CocoaPriceID',
                                        name: 'CocoaPriceID',
                                    }, {
                                        id: 'Provinsi',
                                        name: 'Provinsi',
                                        xtype: 'combo',
                                        fieldLabel: lang('Provinsi'),
                                        store: mc_Provinsi,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        allowBlank   : false,
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                //alert(Ext.getCmp('Provinsi').getValue());
                                                mc_Kabupaten.load({
                                                    params: {
                                                        key: Ext.getCmp('Provinsi').getValue()
                                                    }
                                                });
                                            }
                                        }
                                    }, {
                                        id: 'Kabupaten',
                                        name: 'Kabupaten',
                                        xtype: 'combo',
                                        fieldLabel: lang('Kabupaten'),
                                        store: mc_Kabupaten,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        readOnly: 'true',
                                        allowBlank   : false,
                                        listeners: {
                                            
                                        }
                                    },{
                                        xtype        : 'datefield',                            
                                        id           : 'CocoaPriceDate',
                                        name         : 'CocoaPriceDate',
                                        fieldLabel   : lang('Date'),
                                        format       : 'Y-m-d',
                                        altFormats   : 'Y-m-d',
                                        submitFormat : 'Y-m-d',
                                        emptyText    :  lang('Date'),
                                        allowBlank   : false,
                                        anchor       : '90%'  
                                    }, {
                                        id: 'CocoaPriceType',
                                        name: 'CocoaPriceType',
                                        xtype: 'combo',
                                        fieldLabel: lang('Type'),
                                        store: CocoaPriceType,
                                        displayField: 'id',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        allowBlank   : false,
                                        listeners: {
                                            
                                        }
                                    }, {
                                        xtype: 'numberfield',
                                        fieldLabel: lang('Cocoa Price'),
                                        labelWidth: 120,
                                        allowBlank: false,
                                        id: 'CocoaPrice',
                                        name: 'CocoaPrice',
                                        minValue: 1
                                    }
                                ]
                            }/*,
                            {
                                columnWidth: 0.5,
                                padding: '0 0 0 10',
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'image',
                                        id: 'iphoto',
                                        height: '180px'
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
                                                // do something
                                            }
                                        }
                                    }
                                ]
                            }*/
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
                if (Ext.getCmp('CocoaPriceID').getValue() === '') methode = 'POST'; else methode = 'PUT';
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
        title: 'Data Cocoa Price',
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: 450,
        minWidth: 570,
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
                key: Ext.getCmp('key').getValue()
            }
        });
    }
    var contextMenuGridCocoaPrice = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            //hidden: !m_act_update,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormWindow(true);
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {CocoaPriceID: sm.get('CocoaPriceID')},
                    success: function (fp, o) {
                        var data = Ext.decode(fp.responseText);
                        set_form_value(data);
                        Ext.getCmp('Provinsi').setDisabled(true);
                        Ext.getCmp('Kabupaten').setDisabled(true);
                    }
                });
            }
        },
        {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            //hidden: !m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_crud,
                            method: 'DELETE',
                            params: {CocoaPriceID: smb.get('CocoaPriceID')},
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
               contextMenuGridCocoaPrice.showAt(e.getXY());
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
                        mc_Provinsi.load();
                        displayFormWindow(true);
                        DataForm.getForm().reset();
                        Ext.getCmp('Kabupaten').setDisabled(false);
                        if(m_prov==''){
                            Ext.getCmp('Provinsi').setDisabled(false);
                            Ext.getCmp('Provinsi').setValue('');    
                        }else{
                            Ext.Ajax.request({
                                url: m_prov_name,
                                method: 'GET',
                                success: function(fp, o) {
                                    var r = Ext.decode(fp.responseText);
                                    Ext.getCmp('Provinsi').setValue(r.Province);
                                }
                            })
                        }
                        
                        //Ext.getCmp('iphoto').setSrc();

                    },
                    //cls: m_act_add?'':'hidden'
                },{
                    xtype: 'textfield',
                    emptyText: lang('District Name'),
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'key',
                    listeners: {
                        specialkey: submitOnEnter
                    },
                    width:400
                },{
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    fieldLabel: '',
                    emptyText: lang('Date Start'),
                    name: 'dateStart',
                    id: 'dateStart',
                    width:200
                },{
                    xtype: 'label',
                    text: lang('s.d.'),
                    width:50
                },{
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    fieldLabel: '',
                    emptyText: lang('Date End'),
                    name: 'dateEnd',
                    id: 'dateEnd',
                    width:200
                },{
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
                dataIndex: 'CocoaPriceID',
                hidden: true
            },
            {
                text: 'No',
                xtype: 'rownumberer',
                align: 'center',
                width: 50,
            },
            {
                text: lang('Date'),
                flex: 2,
                dataIndex: 'CocoaPriceDate'
            },
            {
                text: lang('District'),
                flex: 2,
                dataIndex: 'District'
            },
            {
                text: lang('Type'),
                flex: 2,
                dataIndex: 'Type'
            },
            {
                text: lang('Cocoa Price'),
                flex: 2,
                dataIndex: 'CocoaPrice'
            }]
    });

    if(m_prov!=""){
        Ext.getCmp('Provinsi').setDisabled(true);
        Ext.Ajax.request({
            url: m_prov_name,
            method: 'GET',
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('Provinsi').setValue(r.Province);
            }
        })
    }else{
        
    }

});
