Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['BroadcastID', 'message', 'total_all', 'total_new', 'total_success', 'total_failed'],
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
                store.proxy.extraParams.key = Ext.getCmp('bckey').getValue();
            }
        }
    });
    
    var store_sentto = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['BroadcastDetailID', 'BroadcastID', 'GroupDetailID', 'FarmerID', 'Name', 'to', 'BroadcastStatus', 'GroupName'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + '_to',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.BroadcastID = Ext.getCmp('bcBroadcastID').getValue();
            }
        }
    });
    
    var store_sentto_add = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [{name: 'GroupDetailID'}, {name: 'FarmerID'}, {name: 'ToName'}, {name: 'to'}, {name: 'GroupName'}],
        //pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + '_to_add_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    store_sentto_add.on('load', function(store, records) {
        if(store.data.items.length<1){
            Ext.Msg.alert('Warning', 'No data found!');
        }
    });
    
    var province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_province',
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
            url: m_crud + '_district',
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
            url: m_crud + '_subdistrict',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var village = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_village',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var smsGroup = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_sms_group',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    province.load();
    smsGroup.load();
    
    var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: m_act_update,
            handler: function(){
                Ext.getCmp('bcTKey').setValue('');
                var sm = Ext.getCmp('bcgrid').getSelectionModel().getSelection()[0];
                Ext.Ajax.request({
                    url: m_crud + '_detail',
                    method: 'GET',
                    params: {BroadcastID: sm.get('BroadcastID')},
                    success: function (fp, o) {
                        var data = Ext.decode(fp.responseText);
                        Ext.getCmp('bcBroadcastID').setValue(data.BroadcastID);
                        Ext.getCmp('bcMessage').setValue(data.message);
                        store_sentto.load({
                            params: {
                                BroadcastID: data.BroadcastID,
                                key: Ext.getCmp('bcTKey').getValue()
                            }
                        });
                        displayFormWindow();
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
            hidden: m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('bcgrid').getSelectionModel().getSelection()[0];
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_crud,
                            method: 'DELETE',
                            params: {BroadcastID: smb.raw.BroadcastID},
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                    store.load({
                                        params: {
                                            key: Ext.getCmp('bckey').getValue()
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
    
    var contextMenuGridSentTo = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: m_act_update,
            handler: function(){
                var sm = Ext.getCmp('bcSentTo').getSelectionModel().getSelection()[0];
                //displayFormWindow(true);
                Ext.Ajax.request({
                    url: m_crud + '_sent_detail',
                    method: 'GET',
                    params: {BroadcastDetailID: sm.get('BroadcastDetailID')},
                    success: function (fp, o) {
                        var data = Ext.decode(fp.responseText);
                        Ext.getCmp('bcBroadcastDetailID').setValue(data.BroadcastDetailID);
                        Ext.getCmp('bcNAName').setValue(data.ToName);
                        Ext.getCmp('bcNAPhoneNumber').setValue(data.to);
                        displayAddWindowNumber();
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
            hidden: m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('bcSentTo').getSelectionModel().getSelection()[0];
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_crud + '_sent_detail',
                            method: 'DELETE',
                            params: {BroadcastDetailID: smb.raw.BroadcastDetailID},
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                    store_sentto.load({
                                        params: {
                                            BroadcastID: Ext.getCmp('bcBroadcastID').getValue(),
                                            key: Ext.getCmp('bcTKey').getValue()
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
        id:'bcgrid',
        minHeight:250,
        style:'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function(view, record, item, index, e){
               contextMenuGrid.showAt(e.getXY());
            }
        },
        dockedItems: [
                {
                    xtype: 'pagingtoolbar',
                    store: store,   
                    dock: 'bottom',
                    displayInfo: true
                }
                ,{
                    xtype: 'toolbar',
                    items: [
                        {
                            xtype :'button',
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            text: lang('Create'),
                            handler: function() {
                                Ext.getCmp('bcMessage').setValue('');
                                Ext.getCmp('bcBroadcastID').setValue('');
                                store_sentto.clearData();
                                store_sentto.removeAll();
                                displayFormWindow();
                            }
                        }
                        ,{
                            name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                            id: 'bckey',
                            xtype:'textfield',
                            listeners: {
                                specialkey: submitOnEnter
                            }
                        }
                        ,{
                            xtype :'button',
                            icon: varjs.config.base_url+'images/icons/silk/search.png',
                            margin: '0px 0px 0px -10px',
                            text: 'Search',
                            handler: function() {
                                store.load({
                                    params: {
                                            key: Ext.getCmp('bckey').getValue()
                                    }
                                });
                            }
                        }
                    ]
                }
        ],
        columns: [
            {
                text: 'ID',
                dataIndex: 'BroadcastID',
                hidden: true
            },
            {
                text: 'No',
                xtype: 'rownumberer',
                align: 'center',
                width: 50,
            },
            {
                text: lang('Message'),
                flex: 5,
                dataIndex: 'message'
            },
            {
                text: lang('Total Sent'),
                flex: 1,
                dataIndex: 'total_all'
            },
            {
                text: lang('Pending'),
                flex: 1,
                dataIndex: 'total_new'
            },
            {
                text: lang('Success'),
                flex: 1,
                dataIndex: 'total_success'
            },
            {
                text: lang('Failed'),
                flex: 1,
                dataIndex: 'total_failed'
            },
        ]   
    });

    function submitOnEnter(field, event) {
            if (event.getKey() == event.ENTER) {
                    store.load({
                            params: {
                                    key: Ext.getCmp('bckey').getValue()
                            }});
            }
    };
    function displayFormWindow(){
            if(!win.isVisible()){
                    win.show();
            } else {
                    win.hide(this, function() {});
                    win.toFront();
            }
    }
    function displayAddWindowFarmer() {
        if (!winAddFarmer.isVisible()) {
            winAddFarmer.show();
        } else {
            winAddFarmer.hide(this, function() {
            });
            winAddFarmer.toFront();
        }
    }
    function displayAddWindowNumber() {
        if (!winAddNumber.isVisible()) {
            winAddNumber.show();
        } else {
            winAddNumber.hide(this, function() {
            });
            winAddNumber.toFront();
        }
    }
    
    var DataFormAddFarmer = Ext.create('Ext.panel.Panel', {
        height: 700,
        autoScroll: true,
        width: 900,
        bodyPadding: 5,
        id: 'bcdataFormAddFarmer',
        items: [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'bcFAProvinceID',
                    name: 'FAProvinceID',
                    emptyText: lang('Province'),
                    store: province,
                    //allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('bcFADistrictID').setValue('');
                            Ext.getCmp('bcFASubDistrictID').setValue('');
                            district.load({
                                params: {
                                    ProvinceID: Ext.getCmp('bcFAProvinceID').getValue()
                                }
                            });
                        }
                    }
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'bcFADistrictID',
                    name: 'FADistrictID',
                    emptyText: lang('District'),
                    store: district,
                    //allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('bcFASubDistrictID').setValue('');
                            subdistrict.load({
                                params: {
                                    ProvinceID: Ext.getCmp('bcFAProvinceID').getValue(),
                                    DistrictID: Ext.getCmp('bcFADistrictID').getValue()
                                }
                            });
                        }
                    }
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'bcFASubDistrictID',
                    name: 'FASubDistrictID',
                    emptyText: lang('Sub District'),
                    store: subdistrict,
                    //allowBlank: false,
                    //multiSelect: true,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('bcFAVillageID').setValue('');
                            village.load({
                                params: {
                                    SubDistrictID: Ext.getCmp('bcFASubDistrictID').getValue()
                                }
                            });
                        }
                    }
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'bcFAVillageID',
                    name: 'FAVillageID',
                    emptyText: lang('Village'),
                    store: village,
                    //allowBlank: false,
                    multiSelect: true,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'bcFAGroupID',
                    name: 'FAGroupID',
                    emptyText: lang('Group'),
                    store: smsGroup,
                    //allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'gridpanel',
                    id: 'bc_grid_farmer_add',
                    store: store_sentto_add,
                    loadMask: true,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            xtype: 'textfield',
                            name: 'bcFAkey',
                            id: 'bcFAkey',
                            emptyText: lang('Farmer ID / Farmer Name'),
                            width: 280,
                            listeners: {}
                        }, {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/silk/search.png',
                            margin: '0px 0px 0px 6px',
                            text: lang('Search'),
                            handler: function() {
                                /*if(Ext.getCmp('bcFAProvinceID').getValue()=="" || Ext.getCmp('bcFAProvinceID').getValue()==undefined){
                                    Ext.MessageBox.alert('Warning', lang('Select Province first!'));
                                }else{
                                    if(Ext.getCmp('bcFADistrictID').getValue()=="" || Ext.getCmp('bcFADistrictID').getValue()==undefined){
                                        Ext.MessageBox.alert('Warning', lang('Select DistrictID first!'));
                                    }else{
                                        if(Ext.getCmp('bcFASubDistrictID').getValue()=="" || Ext.getCmp('bcFASubDistrictID').getValue()==undefined){
                                            Ext.MessageBox.alert('Warning', lang('Select SubDistrictID first!'));
                                        }else{*/
                                            if(Ext.getCmp('bcFAVillageID').getValue()==''){
                                                var  village = "";
                                            }else{
                                                var village = Ext.getCmp('bcFAVillageID').getValue().join().replace(/,/g, '::');
                                            }
                                            store_sentto_add.load({
                                                params: {
                                                    BroadcastID: Ext.getCmp('bcBroadcastID').getValue(),
                                                    key: Ext.getCmp('bcFAkey').getValue(),
                                                    Province : Ext.getCmp('bcFAProvinceID').getValue(),
                                                    District : Ext.getCmp('bcFADistrictID').getValue(),
                                                    SubDistrict : Ext.getCmp('bcFASubDistrictID').getValue(),
                                                    Village : village,
                                                    GroupID : Ext.getCmp('bcFAGroupID').getValue()
                                                }
                                            });
                                        /*}
                                    }
                                }*/
                            }
                        }]
                    }],
                    selType: 'checkboxmodel',
                    selModel: {
                        checkOnly: true,
                        mode: "MULTI",
                        headerWidth: 50
                    },
                    columns: [{
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: 50,
                    }, {
                        text: lang('ID'),
                        dataIndex: 'GroupDetailID',
                        hidden: true
                    }, {
                        text: lang('Farmer ID'),
                        dataIndex: 'FarmerID',
                        flex: 1
                    }, {
                        text: lang('Farmer Name'),
                        dataIndex: 'ToName',
                        flex: 2
                    }, {
                        text: lang('Phone Number'),
                        dataIndex: 'to',
                        flex: 1
                    }, {
                        text: lang('Group'),
                        dataIndex: 'GroupName',
                        flex: 2
                    }]
                }]
            }]
        }],
        buttons: [{
                id: 'bc_save_par_add',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var farmers = '';
                    Ext.each(Ext.getCmp('bc_grid_farmer_add').getSelectionModel().getSelection(), function(row, index, value) {
                        farmers = farmers + ',' + row.data.GroupDetailID + '_' + row.data.ToName + '_' + row.data.to;
                    });
                    if (farmers != '') {
                        Ext.Ajax.request({
                            url: m_crud + '_farmer_add',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                BroadcastID: Ext.getCmp('bcBroadcastID').getValue(),
                                Message: Ext.getCmp('bcMessage').getValue(),
                                farmers: farmers
                            },
                            success: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        store_sentto_add.clearData();
                                        store_sentto_add.removeAll();
                                        store_sentto.load({
                                            params: {
                                                BroadcastID: Ext.getCmp('bcBroadcastID').getValue(),
                                                key: Ext.getCmp('bcTKey').getValue()
                                            }
                                        });
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert("Warning", "Please select farmer");
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
                    winAddFarmer.hide();
                }
            }]
    });
    
    var winAddFarmer = Ext.widget('window', {
        title: lang('Add Farmer'),
        id: 'bcwinAddFarmer',
        closeAction: 'hide',
        height: 750,
        autoScroll: false,
        width: 920,
        bodyPadding: 5,
        modal: true,
        layout: 'fit',
        items: [DataFormAddFarmer]
    });

    var DataFormAddNumber = Ext.create('Ext.form.Panel', {
        id: 'dataFormAddNumber',
        frame: false,
        width: 450,
        height: 200,
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
                                        hidden: true,
                                        labelWidth: 120,
                                        id: 'bcBroadcastDetailID',
                                        name: 'BroadcastDetailID'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Name'),
                                        labelWidth: 120,
                                        id: 'bcNAName',
                                        name: 'Name',
                                        allowBlank: false
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Phone Number'),
                                        labelWidth: 120,
                                        id: 'bcNAPhoneNumber',
                                        name: 'PhoneNumber',
                                        allowBlank: false
                                    }
                                ]
                            }
                        ]
                    },
                ],
            }
        ],
        buttons: [{
            id: 'bcsave_number',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('bcBroadcastDetailID').getValue() == '') methode = 'POST'; else methode = 'PUT';
                if (form.isValid()) {
                    form.submit({
                        url: m_crud + '_number_add',
                        method: methode,
                        waitMsg: lang('Sending data...'),
                        params: {
                            BroadcastDetailID: Ext.getCmp('bcBroadcastDetailID').getValue(),
                            BroadcastID: Ext.getCmp('bcBroadcastID').getValue(),
                            Message: Ext.getCmp('bcMessage').getValue(),
                        },
                        success: function(fp, o) {
                            switch (o.result.success) {
                                case true:
                                    Ext.MessageBox.alert('Success', o.result.message);
                                    store_sentto.load({
                                        params: {
                                            BroadcastID: Ext.getCmp('bcBroadcastID').getValue(),
                                            key: Ext.getCmp('bcTKey').getValue()
                                        }
                                    });
                                    winAddNumber.hide();
                                    break;
                                case "failed":
                                    Ext.MessageBox.alert('Warning', o.result.message);
                                    break;
                                default:
                                    Ext.MessageBox.alert('Warning', o.result.message);
                                    break;
                            }
                        },
                        failure: function (response, opts) {
                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
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
                winAddNumber.hide();
            }
        }]
    });

    var winAddNumber = Ext.create('widget.window', {
        title: lang('Add Number'),
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: 450,
        minWidth: 350,
        height: 220,
        layout: 'fit',
        items: [DataFormAddNumber]
    });
        
    var DataForm = Ext.create('Ext.form.Panel', {
        height: 450,
        autoScroll: true,
        width: 900,
        id: 'bcdataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 50,
            anchor: '100%'
        },  
        items: [{
            xtype: 'textfield',
            id: 'bcBroadcastID',
            name: 'BroadcastID',
            hidden: true
        }, {
            xtype: 'textareafield',
            id: 'bcMessage',
            name: 'Message',
            allowBlank: false,
            fieldLabel: lang('Text')
        }, {
            xtype: 'tabpanel',
            activeTab: 0,
            plain: true,
            items: [{
                xtype: 'gridpanel',
                title: lang('Sent To'),
                id: 'bcSentTo',
                style: 'border:1px solid #CCC;',
                store: store_sentto,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                minHeight:190,
                listeners: {
                    itemclick: function(view, record, item, index, e){
                       contextMenuGridSentTo.showAt(e.getXY());
                    }
                },
                dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        //cls: m_act_save,
                        text: lang('Add Farmer'),
                        scope: this,
                        handler: function() {
                            if(Ext.getCmp('bcBroadcastID').getValue()==''){
                                Ext.MessageBox.alert('Warning', 'Please save message first!');
                            }else{
                                displayAddWindowFarmer();
                                Ext.getCmp('bcFAProvinceID').setValue('');
                                Ext.getCmp('bcFAkey').setValue('');
                                Ext.getCmp('bcFAGroupID').setValue('');
                                store_sentto_add.clearData();
                                store_sentto_add.removeAll();
                                
                            }
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        //cls: m_act_save,
                        text: lang('Add Number'),
                        scope: this,
                        handler: function() {
                            if(Ext.getCmp('bcBroadcastID').getValue()==''){
                                Ext.MessageBox.alert('Warning', 'Please save message first!');
                            }else{
                                Ext.getCmp('bcNAName').setValue();
                                Ext.getCmp('bcNAPhoneNumber').setValue();
                                displayAddWindowNumber();
                            }
                        }
                    }, {
                        xtype: 'textfield',
                        name: 'bcTKey',
                        id: 'bcTKey',
                        emptyText: lang('Name / Phone Number'),
                        width: 280,
                        listeners: {}
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store_sentto.load({
                                params: {
                                    BroadcastID: Ext.getCmp('bcBroadcastID').getValue(),
                                    key: Ext.getCmp('bcTKey').getValue()
                                }
                            });
                        }
                    }]
                }, {
                    xtype: 'pagingtoolbar',
                    store: store_sentto,   // same store GridPanel is using
                    dock: 'bottom',
                    displayInfo: true
                }],
                columns: [{
                    text: 'ID',
                    dataIndex: 'BroadcastID',
                    hidden: true
                },
                {
                    text: 'No',
                    xtype: 'rownumberer',
                    align: 'center',
                    width: 50
                },
                {
                    text: lang('Farmer ID'),
                    flex: 1,
                    dataIndex: 'FarmerID'
                },
                {
                    text: lang('Name'),
                    flex: 2,
                    dataIndex: 'Name'
                },
                {
                    text: lang('Phone Number'),
                    flex: 2,
                    dataIndex: 'to'
                },
                {
                    text: lang('Group'),
                    flex: 2,
                    dataIndex: 'GroupName'
                },
                {
                    text: lang('Status'),
                    flex: 2,
                    dataIndex: 'BroadcastStatus'
                }]
            }]
        }],
        buttons: [{
                id: 'bcSentMessage',
                text: lang('Sent Message'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-green',
                handler: function() {
                    if(Ext.getCmp('bcBroadcastID').getValue()==''){
                        Ext.MessageBox.alert('Warning', 'Please save message first!');
                    }else{
                        if(Ext.getCmp('bcMessage').getValue() == ''){
                            Ext.MessageBox.alert('Warning', lang('Text can not be empty!'));
                        }else{
                            Ext.MessageBox.confirm('Message', lang('Do you want to sent this broadcast message?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.MessageBox.show({
                                        msg: 'Loading, please wait...',
                                        progressText: 'Sending...',
                                        width:300,
                                        wait:true,
                                        waitConfig: {interval:200},
                                        icon:'ext-mb-download', //custom class in msg-box.html
                                        iconHeight: 50,
                                        animateTarget: 'mb7'
                                    });
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_crud + '_sent_message',
                                        method: 'POST',
                                        params: {
                                            BroadcastID: Ext.getCmp('bcBroadcastID').getValue()
                                        },
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            var j = 0;
                                            var k = 0;
                                            var tot = obj.total;
                                            if(obj.success=='true'){
                                                send_message(obj.total, obj.data, 0, 0, 0);
                                            }else{
                                                Ext.MessageBox.hide();
                                                Ext.MessageBox.alert('Warning', obj.message);
                                                store_sentto.load({
                                                    params: {
                                                        BroadcastID: Ext.getCmp('bcBroadcastID').getValue(),
                                                        key: Ext.getCmp('bcTKey').getValue()
                                                    }
                                                });
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
                }
            }, {
                id: 'bcSaveMessage',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('bcBroadcastID').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'PUT';
                    
                    if(Ext.getCmp('bcMessage').getValue() == ''){
                        Ext.MessageBox.alert('Warning', lang('Text can not be empty!'));
                    }else{
                        form.submit({
                            url: m_crud + '_data',
                            method: methode,
                            waitMsg: lang('Sending data...'),
                            success: function(fp, o) {
                                if (methode == 'POST'){
                                    Ext.getCmp('bcBroadcastID').setValue(o.result.BroadcastID);
                                }
                                Ext.MessageBox.alert('Success', lang(o.result.message));
                                store.load();
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
                }
            }]
    });

	
    var win = Ext.create('widget.window', {
        title: 'Data Broadcast',
        id:'bcwin',
        closable: true,
        modal:true,
        closeAction: 'hide',
        width: 920,
        height: 500,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataForm]
    });
    
    function send_message(total, data, i, s, f){
        Ext.Ajax.request({
            url: m_crud + '_sent_message_proccess',
            method: 'POST',
            params: {
                BroadcastDetailID: data[i].id,
                Message: Ext.getCmp('bcMessage').getValue()
            },
            success: function (fp, o) {
                var res = Ext.decode(fp.responseText);
                if(res.status=="true"){
                    s++;
                }else{
                    f++;
                }
                var l = i + 1;
                Ext.MessageBox.show({
                    msg: '( '+ l +' Message Sent of '+ total+' )',
                    progressText: 'Sending...',
                    width:300,
                    wait:true,
                    waitConfig: {interval:200},
                    icon:'ext-mb-download', //custom class in msg-box.html
                    iconHeight: 50,
                    animateTarget: 'mb7'
                });
                if(l<total){
                    send_message(total,data,l,s,f);
                }else{
                    Ext.MessageBox.hide();
                    Ext.MessageBox.alert('Info', 'Broadcast messages finished. '+s+' success, '+f+' failed of '+total+' messages.');
                    store_sentto.load({
                        params: {
                            BroadcastID: Ext.getCmp('bcBroadcastID').getValue(),
                            key: Ext.getCmp('bcTKey').getValue()
                        }
                    });
                }
            },
            failure: function (response, opts) {
                return false;
                //Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
            }
        });
    }
});
