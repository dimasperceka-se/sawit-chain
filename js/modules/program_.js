Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux', varjs.config.base_url+'js/'+varjs.config.extjs_version+'/ux');
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*',
    'Ext.ux.grid.FiltersFeature',
    'Ext.form.Panel',
    'Ext.tab.*',
    'Ext.window.*',
    'Ext.tip.*',
    'Ext.layout.container.Border'
]);

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    var PartnerID;
    var DistrictID;
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','PartnerName','type','PartnerFullName','Photo','PartnerIndustry'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'s',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var store_districtInPartner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','district', 'province'],
        proxy: {
            type: 'ajax',
            url: m_districtInPartner,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_Province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','province'],
        proxy: {
            type: 'ajax',
            url: m_Province,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_District = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','district'],
        proxy: {
            type: 'ajax',
            url: m_District,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    function displayFormWindow(){
        if(!win.isVisible()){
            DataForm.getForm().reset();
            Ext.getCmp('ilogo').setSrc('');
            win.show();
            Ext.getCmp('PartnerName').focus(true,true);
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 350,
        width: 600,
        bodyPadding: 5,
        fileUpload: true,
        enctype:'multipart/form-data',
        id:'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
            xtype: 'textfield',
            id: 'id',
            name: 'id',
            inputType:'hidden'
        },{
            xtype: 'textfield',
            id: 'Photo_old',
            name: 'Photo_old',
            inputType:'hidden'
        },{
            xtype: 'textfield',
            fieldLabel: 'Name',
            id: 'PartnerName',
            name: 'PartnerName'
        },{
            xtype: 'radiogroup',
            fieldLabel: 'Industry',
            defaultType: 'radiofield',
            columns: 3,
            vertical: true,
            items: [
                {
                    id: 'PartnerIndustry',
                    name: 'PartnerIndustry',
                    boxLabel  : 'Implementer',
                    inputValue: '0'
                },
                {
                    id: 'PartnerIndustry1',
                    name: 'PartnerIndustry',
                    boxLabel  : 'Donor',
                    inputValue: '1'
                },
                {
                    id: 'PartnerIndustry2',
                    name: 'PartnerIndustry',
                    boxLabel  : 'Trader',
                    inputValue: '2'
                },
                {
                    id: 'PartnerIndustry3',
                    name: 'PartnerIndustry',
                    boxLabel  : 'Processer',
                    inputValue: '3'
                },
                {
                    id: 'PartnerIndustry4',
                    name: 'PartnerIndustry',
                    boxLabel  : 'Manufacturer',
                    inputValue: '4'
                },
                {
                    id: 'PartnerIndustry5',
                    name: 'PartnerIndustry',
                    boxLabel  : 'Input Supplier',
                    inputValue: '5'
                }
            ]
        },{
            xtype: 'textfield',
            fieldLabel: 'Full Name',
            id: 'PartnerFullName',
            name: 'PartnerFullName'
        },{
            layout: 'column',
            items: [{
                columnWidth: 0.48,
                items:[{
                    xtype: 'fileuploadfield',
                    fieldLabel: 'Logo',
                    id: 'Photo',
                    name: 'Photo',
                    buttonText: 'Browse',
                    listeners: {
                        'change': function(fb, v){
                            var form = this.up('form').getForm();
                            form.submit({
                                url: m_crud+'_image',
                                waitMsg: 'Sending Photo...',
                                success: function(fp, o) {
                                    Ext.getCmp('ilogo').setSrc(m_photo+o.result.file);
                                    Ext.getCmp('Photo_old').setValue(o.result.file);
                                }
                            });
                        }
                    }
                }]
            },{
                columnWidth: 0.48,
                items:[{
                    xtype:'image',
                    id:'ilogo',
                    height:'120px'
                }]
            }]
        }],
        buttons: [{
            id:'saveButton',
            text: 'Save',
            margin: '0px 0px 0px 6px',
            scale: 'medium',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var urle;
                if (Ext.getCmp('id').getValue()!='') urle = m_crud+'u'; else urle = m_crud;
                form.submit({
                    url: urle,
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                win.hide(this, function() {
                    store.load();
                });
            }
        },{
            text: 'Close',
            margin: '0px 0px 0px 6px',
            scale: 'medium',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                win.hide();
            }
        }]
    });
    var win = Ext.create('widget.window', {
        title: 'Data Program',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: 630,
        minWidth: 570,
        height: 400,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataForm]
    });

    function displayDistrictWindow(){
        if(!winDistrict.isVisible()){
            winDistrict.show();
        } else {
            winDistrict.hide(this, function() {});
            winDistrict.toFront();
        }
    }
    var DataFormDistrict = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        height: 350,
        width: 600,
        bodyPadding: 5,
        id:'dataFormAccess',
        items: [{
            xtype: 'gridpanel',
            id:'gaccess',
            store: store_districtInPartner,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    id: 'province',
                    name: 'province',
                    xtype: 'combo',
                    store: store_Province,
                    displayField: 'province',
                    valueField: 'id',
                    queryMode: 'local',
                    listeners: {
                        change: function (cb, nv, ov) {
                            store_District.load({
                                params: {
                                    id: nv
                                }});
                            Ext.getCmp('district').reset();
                        }
                    }
                },{
                    id: 'district',
                    name: 'district',
                    xtype: 'combo',
                    store: store_District,
                    displayField: 'district',
                    valueField: 'id',
                    queryMode: 'local',
                    listeners: {
                        change: function (cb, nv, ov) {
                            DistrictID = nv
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: 'Add',
                    scope: this,
                    hidden : m_act_add,
                    handler : function() {
                        store_districtInPartner.load();
                        Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_crud+'addDistrict',
                                method : 'PUT',
                                params: {PartnerID : PartnerID, DistrictID : DistrictID },
                                success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                        case true:
                                            Ext.getCmp('id').setValue('');
                                            store_districtInPartner.load({
                                                params: {
                                                    id: PartnerID
                                                }});
                                            break;
                                        default: Ext.MessageBox.alert('Warning',obj.message);
                                            break;
                                    }
                                }
                            }
                        )
                    }
                }]
            }],
            columns: [{
                text: 'No',
                xtype: 'rownumberer',
                width:'10%'
            },
                {
                    text: 'District',
                    dataIndex: 'district',
                    width:'35%'
                },{
                    text: 'Province',
                    dataIndex: 'province',
                    width:'35%'
                },{
                    text: 'Action',
                    xtype: 'actioncolumn',
                    width: '20%',
                    items: [{
                        icon: varjs.config.base_url+'images/icons/silk/delete.png',
                        tooltip: 'Delete',
                        hidden : m_act_update,
                        handler : function(grid, rowIndex, colIndex){
                            var sma = grid.getStore().getAt(rowIndex);
                            Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?' , function(btn){
                                if(btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_crud+'udist',
                                        method : 'PUT',
                                        params: {id:  sma.get('id')},
                                        success: function(response, opts){
                                            var obj = Ext.decode(response.responseText);
                                            switch(obj.success){
                                                case true:
                                                    store_districtInPartner.load({
                                                        params: {
                                                            id: PartnerID
                                                        }});
                                                    break;
                                                default: Ext.MessageBox.alert('Warning',obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts){
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                        }
                                    });
                                }
                            });
                        }
                    }]
                }]
        }]
    });
    var winDistrict = Ext.create('widget.window', {
        title: 'Data District - Partner',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: 630,
        minWidth: 570,
        height: 400,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataFormDistrict]
    });


    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 250,
        //title: 'Program List',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: 'Add',
                scope: this,
                handler : displayFormWindow,
                hidden : m_act_add
            }]
        }],
        columns: [{
            text: 'ID',
            dataIndex: 'id',
            hidden:true
        },
            {
                text: 'No',
                xtype: 'rownumberer',
                width:'5%'
            },
            {
                text: 'Name',
                width: '30%',
                dataIndex: 'PartnerName'
            },
            {
                text: 'Industry',
                width: '20%',
                dataIndex: 'type'
            },
            {
                text: 'Full Name',
                width: '30%',
                dataIndex: 'PartnerFullName'
            },{
                text: 'Action',
                xtype: 'actioncolumn',
                width: '15  %',
                items: [{
                    icon: varjs.config.base_url+'images/icons/silk/notebook--pencil.png',
                    tooltip: 'Edit',
                    hidden : m_act_update,
                    handler : function(grid, rowIndex, colIndex) {
                        displayFormWindow();
                        var sm = grid.getStore().getAt(rowIndex);
                        Ext.Ajax.request({
                            url: m_crud,
                            method: 'GET',
                            params: {id: sm.get('id')},
                            success: function(fp, o){
                                var r = Ext.decode(fp.responseText);
                                Ext.getCmp('id').setValue(sm.get('id'));
                                Ext.getCmp('PartnerName').setValue(r.PartnerName);
                                if (r.PartnerIndustry=='0') Ext.getCmp('PartnerIndustry').setValue(true);
                                if (r.PartnerIndustry=='1') Ext.getCmp('PartnerIndustry1').setValue(true);
                                if (r.PartnerIndustry=='2') Ext.getCmp('PartnerIndustry2').setValue(true);
                                if (r.PartnerIndustry=='3') Ext.getCmp('PartnerIndustry3').setValue(true);
                                if (r.PartnerIndustry=='4') Ext.getCmp('PartnerIndustry4').setValue(true);
                                if (r.PartnerIndustry=='5') Ext.getCmp('PartnerIndustry5').setValue(true);
                                Ext.getCmp('PartnerFullName').setValue(r.PartnerFullName);
                                Ext.getCmp('ilogo').setSrc(m_photo+r.Photo);
                                Ext.getCmp('Photo_old').setValue(r.Photo);
                            }
                        });
                    }
                },{
                    icon: varjs.config.base_url+'images/icons/silk/chart_organisation_add.png',
                    tooltip: 'District',
                    hidden : m_act_update,
                    handler : function(grid, rowIndex, colIndex) {
                        displayDistrictWindow();
                        var sm = grid.getStore().getAt(rowIndex);
                        store_districtInPartner.load({
                            params: {
                                id: sm.get('id')
                            }});
                        store_Province.load();
                        Ext.getCmp('id').setValue(sm.get('id'));
                        PartnerID = sm.get('id');
                    }
                },{
                    icon: varjs.config.base_url+'images/icons/silk/decline.png',
                    tooltip: 'Delete',
                    hidden : m_act_delete,
                    handler : function(grid, rowIndex, colIndex){
                        var sma = grid.getStore().getAt(rowIndex);
                        Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus program ini ?' , function(btn){
                            if(btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_crud,
                                    method : 'DELETE',
                                    params: {id:  sma.get('id')},
                                    success: function(response, opts){
                                        var obj = Ext.decode(response.responseText);
                                        switch(obj.success){
                                            case true: store.load();
                                                break;
                                            default: Ext.MessageBox.alert('Warning',obj.message);
                                                break;
                                        }
                                    },
                                    failure: function(response, opts){
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        });
                    }
                }]
            }]
    });
});
