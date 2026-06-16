/*
 * @Author: nikolius
 * @Date:   2016-08-24 13:44:43
 * @Last Modified by:   nikolius
 * @Last Modified time: 2016-08-25 15:04:02
 */
Ext.onReady(function() {

    var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'RowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['InventoryID', 'OrgType', 'OrgID', 'Name', 'NamaBarang', 'Number', 'cat', 'Cost', 'SellingPrice', 'Stock'],
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
        }
    });

    Ext.define('detail.Model', {
        extend: 'Ext.data.Model',
        fields: ['PaketID','InventoryID','ChildInventoryID','name','Qty','dUnitMeasurement','Cost','Total'],
    });
    var store_detail = Ext.create('Ext.data.Store', {
        model: 'detail.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_detail',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    store_detail.on('beforeload', function() {
        var proxy = store_detail.getProxy();
        proxy.setExtraParam('InventoryID', Ext.getCmp('InventoryID').getValue());
    });

    Ext.define("Post", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url: m_api+'/bussiness/barang_combo_autocom',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'InventoryID', mapping: 'InventoryID'},
            {name: 'label', mapping: 'label'},
            {name: 'Cost', mapping: 'Cost'},
            {name: 'UnitMeasurement', mapping: 'UnitMeasurement'}
        ]
    });
    var mc_barang = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post'
    });
    mc_barang.on('beforeload', function() {
        var proxy = mc_barang.getProxy();
        proxy.setExtraParam('InventoryID', Ext.getCmp('InventoryID').getValue());
    });

    var mc_unit = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api+'/bussiness/unit_combo',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_supplier = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api+'/bussiness/supplier_combo',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_kategori = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api+'/bussiness/kategori_combo',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/farmer/Provinsis',
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
            url: m_api+'/farmer/Kabupatens',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/farmer/Kecamatans',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Desa = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['label', 'id'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/farmer/Desas',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 250,
        id: 'grid',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemdblclick: function(dv, record, item, index, e) {
                //aksi update
                displayFormWindow();
                var sm = record;
                setUpdate(sm);
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,
            dock: 'bottom',
            displayInfo: true
        }, {
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                scope: this,
                handler: function() {
                    displayFormWindow();
                    DataForm.getForm().reset();
                    Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/Photo/sce/items/noimage.jpg');
                },
                cls: m_act_add
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                scope: this,
                handler: function() {
                    //aksi update
                    displayFormWindow();
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    setUpdate(sm);
                },
                cls: m_act_update
            }, {
                itemId: 'remove',
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                cls: m_act_delete,
                text: lang('Hapus'),
                scope: this,
                handler: function() {
                    var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm('Message', lang('Are you sure want to delete this data ?') , function(btn){
                        if(btn == 'yes'){
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_crud,
                                method : 'DELETE',
                                params: {id:  smb.raw.InventoryID},
                                success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store.load();
                                        break;
                                        default:
                                            Ext.MessageBox.alert('Warning',obj.message);
                                        break;
                                    }
                                },
                                failure: function(response, opts){
                                   var obj = Ext.decode(response.responseText);
                                   Ext.MessageBox.alert('Error',lang('Could not connect to the database. Retry later'));
                                }
                            });
                        }
                    });
                }
            }]
        }],
        columns: [{
            text: lang('No'),
            xtype: 'rownumberer',
            width: '5%'
        }, {
            text: lang('Name'),
            width: '20%',
            dataIndex: 'NamaBarang'
        }, {
            text: lang('Number'),
            width: '10%',
            dataIndex: 'Number'
        }, {
            text: lang('Kategori'),
            width: '10%',
            dataIndex: 'cat'
        }, {
            text: lang('Cost'),
            width: '10%',
            dataIndex: 'Cost',
            xtype: 'numbercolumn',
            format: '0,000.00'
        }, {
            text: lang('SellingPrice'),
            width: '10%',
            dataIndex: 'SellingPrice',
            xtype: 'numbercolumn',
            format: '0,000.00'
        }, {
            text: lang('Stock'),
            width: '35%',
            dataIndex: 'Stock'
        }]
    });

    function setUpdate(sm){
        Ext.Ajax.request({
            url: m_crud,
            method: 'GET',
            params: {InventoryID: sm.get('InventoryID')},
            success: function(fp, o){
                var r = Ext.decode(fp.responseText);

                Ext.getCmp('InventoryID').setValue(sm.get('InventoryID'));
                Ext.getCmp('Name').setValue(r.data.Name);
                Ext.getCmp('Number').setValue(r.data.Number);
                Ext.getCmp('SupplierID').setValue(r.data.SupplierID);
                Ext.getCmp('CategoryID').setValue(r.data.CategoryID);
                Ext.getCmp('UnitMeasurementID').setValue(r.data.UnitMeasurementID);
                Ext.getCmp('IsSell').setValue(r.data.IsSell);
                Ext.getCmp('IsPaket').setValue(r.data.IsPaket);
                if(r.data.IsPaket == "1"){
                    Ext.getCmp('grid_detail').show();
                }else{
                    Ext.getCmp('grid_detail').hide();
                }

                Ext.getCmp('ParentBarang').setValue(r.data.ParentName);
                Ext.getCmp('Cost').setValue(parseInt(r.data.Cost));
                Ext.getCmp('SalePrice').setValue(parseInt(r.data.SellingPrice));

                if(r.data.Images != ""){
                    Ext.getCmp('Photo_old').setValue(r.data.Images);

                    var fotoBarang = m_api_base_url + '/images/Photo/sce/items/' + r.data.Images;
                    checkImageExists(fotoBarang, function(existsImage) {
                        if (existsImage == true) {
                            Ext.getCmp('iphoto').setSrc(fotoBarang);
                        } else {
                            Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/Photo/sce/items/noimage.jpg');
                        }
                    });
                }

                store_detail.load({params: {'InventoryID': Ext.getCmp('InventoryID').getValue()}});
            },
            failure: function(fp, o){
                Ext.MessageBox.show({
                    title: 'Failed',
                    msg: 'Could not connect to the database. Retry later',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });

                win.close();
            }
        });
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        width: '100%',
        fileUpload: true,
        //enctype: 'multipart/form-data',
        bodyPadding: 5,
        autoScroll: true,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            anchor: '100%'
        },
        items: [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .48,
                layout: 'form',
                padding: 10,
                xtype: 'fieldset',
                title: 'Data',
                height: '100%',
                items:[{
                    xtype: 'textfield',
                    id: 'InventoryID',
                    name: 'InventoryID',
                    hidden:true
                },{
                    fieldLabel: lang('Name'),
                    xtype: 'textfield',
                    id: 'Name',
                    name: 'Name',
                    allowBlank:false
                },{
                    fieldLabel: lang('Number'),
                    xtype: 'textfield',
                    id: 'Number',
                    name: 'Number',
                    allowBlank:false
                },{
                    fieldLabel: lang('Unit Measurement'),
                    id: 'UnitMeasurementID',
                    name: 'UnitMeasurementID',
                    xtype: 'combo',
                    store: mc_unit,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    allowBlank:false,
                    listeners: {
                        change: function (cb, nv, ov) {
                            if (this.value=='-1') {
                                displayFormUnitMeasurement();
                            }
                        }
                    }
                },{
                    fieldLabel: lang('Supplier'),
                    id: 'SupplierID',
                    name: 'SupplierID',
                    xtype: 'combo',
                    store: mc_supplier,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    allowBlank:false,
                    listeners: {
                        change: function (cb, nv, ov) {
                            if (this.value=='-1') {
                                displayFormSupplier();
                            }
                        }
                    }
                },{
                    fieldLabel: lang('Category'),
                    id: 'CategoryID',
                    name: 'CategoryID',
                    xtype: 'combo',
                    store: mc_kategori,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    allowBlank:false,
                    listeners: {
                        change: function (cb, nv, ov) {
                            if (this.value=='-1') {
                                displayFormCat();
                            }
                        }
                    }
                },{
                    fieldLabel: lang('Dijual'),
                    xtype: 'radiogroup',
                    width: '100%',
                    items: [{
                        boxLabel: lang('Ya'),
                        name: 'IsSell',
                        inputValue: '1',
                        id: 'IsSell'
                    }, {
                        boxLabel: lang('Tidak'),
                        name: 'IsSell',
                        inputValue: '2',
                        id: 'IsSell2'
                    }]
                },{
                    fieldLabel: lang('Paket'),
                    xtype: 'radiogroup',
                    width: '100%',
                    items: [{
                        boxLabel: lang('Ya'),
                        name: 'IsPaket',
                        inputValue: '1',
                        id: 'IsPaket'
                    }, {
                        boxLabel: lang('Tidak'),
                        name: 'IsPaket',
                        inputValue: '0',
                        id: 'IsPaket2'
                    }],
                    listeners: {
                        change: function(field, newValue, oldValue) {
                            if (newValue['IsPaket']=='1')
                                Ext.getCmp('grid_detail').show();
                            else
                                Ext.getCmp('grid_detail').hide();
                        }
                    }
                }]
            },{
                columnWidth: .52,
                height: '100%',
                layout: 'form',
                xtype: 'fieldset',
                title: 'Detail',
                style:'margin-left:12px',
                items:[{
                    xtype: 'numericfield',
                    fieldLabel: lang('Harga Beli'),
                    id: 'Cost',
                    name: 'Cost'
                },{
                    xtype: 'numericfield',
                    fieldLabel: lang('Harga Jual'),
                    id: 'SalePrice',
                    name: 'SalePrice'
                },{
                    xtype: 'textfield',
                    fieldLabel: lang('Parent'),
                    id: 'ParentBarang',
                    name: 'ParentBarang',
                    anchor: '100%',
                    readOnly: true
                },{
                    layout: 'column',
                    height: 180,
                    items: [{
                        columnWidth: 0.68,
                        items: [{
                            xtype: 'fileuploadfield',
                            fieldLabel: lang('Photo'),
                            labelWidth: 145,
                            id: 'Photo',
                            padding: 5,
                            name: 'Photo',
                            buttonText: 'Browse',
                            listeners: {
                                'change': function (fb, v) {
                                    var form = Ext.getCmp('dataForm').getForm();
                                    form.submit({
                                        url: m_crud + '_image',
                                        waitMsg: 'Sending Photo...',
                                        success: function (fp, o) {
                                            Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/Photo/sce/items/' + o.result.file);
                                            Ext.getCmp('Photo_old').setValue(o.result.file);
                                        }
                                    });
                                }
                            }
                        }]
                    },{
                        columnWidth: 0.28,
                        items: [{
                            xtype: 'image',
                            id: 'iphoto',
                            height: '145px'
                        },{
                            xtype: 'textfield',
                            id: 'Photo_old',
                            name: 'Photo_old',
                            inputType: 'hidden'
                        }]
                    }]
                }]
            }]
        },{
            xtype: 'gridpanel',
            id: 'grid_detail',
            features: [{
                ftype: 'summary'
            }],
            store: store_detail,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            hidden:true,
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    cls: m_act_add,
                    scope: this,
                    handler: function () {
                        //cek apakah ada data master
                        if(Ext.getCmp('InventoryID').getValue() == ""){
                            Ext.MessageBox.show({
                                title: 'Notifications',
                                msg: 'You need to save the data first',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }else{
                            RowEditing.cancelEdit();
                            var r = Ext.create('detail.Model', {PaketID: '',InventoryID: '',ChildInventoryID:'',name: '',Qty: '',dUnitMeasurement: '',Cost:'',Total:''});
                            store_detail.insert(0, r);
                            RowEditing.startEdit(0, 0);
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    cls: m_act_update,
                    text: lang('Edit'),
                    scope: this,
                    handler: function () {
                        //cek apakah ada data master
                        if(Ext.getCmp('InventoryID').getValue() == ""){
                            Ext.MessageBox.show({
                                title: 'Notifications',
                                msg: 'You need to save the data first',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }else{

                        }
                    }
                },{
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Hapus'),
                    scope: this,
                    handler: function () {
                        //cek apakah ada data master
                        if(Ext.getCmp('InventoryID').getValue() == ""){
                            Ext.MessageBox.show({
                                title: 'Notifications',
                                msg: 'You need to save the data first',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }else{
                            var smb = Ext.getCmp('grid_detail').getSelectionModel().getSelection()[0];
                            RowEditing.cancelEdit();

                            Ext.MessageBox.confirm('Message', 'Are you sure want to delete this data ?', function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_crud+'_detail',
                                        method: 'DELETE',
                                        params: {
                                            PaketID: smb.raw.PaketID
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    store_detail.load({
                                                        params: {
                                                        InventoryID: Ext.getCmp('InventoryID').getValue()
                                                        }
                                                    });
                                                break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                        }
                                    });
                                }
                            });

                        }
                    }
                }]
            }],
            columns: [{
                text: lang('ID'),
                dataIndex: 'PaketID',
                hidden: true
            },{
                text: lang('No'),
                xtype: 'rownumberer',
                width: '5%'
            },{
                text: lang('Barang'),
                dataIndex: 'name',
                width: '30%',
                editor: {
                    xtype: 'combo',
                    store: mc_barang,
                    id:'namaBarangPaket',
                    displayField: 'name',
                    valueField: 'id',
                    typeAhead: false,
                    hideLabel: true,
                    hideTrigger:true,
                    anchor: '100%',
                    listConfig: {
                        loadingText: 'Searching...',
                        emptyText: lang('No matching data found.'),
                        getInnerTpl: function() {
                            return '<div class="search-item">' +
                                '{label}' +
                                '{excerpt}' +
                            '</div>';
                        }
                    },
                    pageSize: 10,
                    listeners: {
                        select: function(combo, selection) {
                            var post = selection[0];
                            //console.log(post);
                            if (post) {
                                Ext.getCmp('namaBarangPaket').setValue(post.get('label'))
                                Ext.getCmp('dUnitMeasurement').setValue(post.get('UnitMeasurement'))
                                Ext.getCmp('dCost').setValue(post.get('Cost'))
                                Ext.getCmp('ChildInventoryID').setValue(post.get('InventoryID'))
                            }
                        }
                    }
                }
            },{
                text: lang('ChildInventoryID'),
                dataIndex: 'ChildInventoryID',
                hidden:true,
                editor: {
                    xtype: 'textfield',
                    id:'ChildInventoryID',
                }
            },{
                text: lang('Qty'),
                dataIndex: 'Qty',
                width: '10%',
                editor: {
                    xtype: 'numericfield',
                    id:'Qty',
                    allowBlank: false,
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('dTotal').setValue(this.value * Ext.getCmp('dCost').getValue())
                        }
                    }
                }
            },{
                text: lang('Unit Measurement'),
                dataIndex: 'dUnitMeasurement',
                width: '15%',
                editor: {
                    xtype: 'textfield',
                    id:'dUnitMeasurement',
                    allowBlank: false,
                    readonly:true
                }
            },{
                text: lang('Cost'),
                dataIndex: 'Cost',
                width: '15%',
                xtype: 'numbercolumn',
                format:'0,000',
                editor: {
                    xtype: 'numericfield',
                    id:'dCost',
                    allowBlank: false,
                    readonly:true
                }
            },{
                text: lang('Total'),
                dataIndex: 'Total',
                width: '24%',
                xtype: 'numbercolumn',
                format:'0,000',
                summaryType: 'sum',
                summaryRenderer: function(value, summaryData, dataIndex) {
                    var total = value;
                    Ext.getCmp('dTotal').setValue(total);
                },
                editor: {
                    xtype: 'numericfield',
                    allowBlank: false,
                    id:'dTotal'
                }
            }],
            plugins: [RowEditing],
            listeners: {
                'canceledit': function (editor, e, eOpts) {
                    store_detail.load({
                        params: {
                            InventoryID: Ext.getCmp('InventoryID').getValue()
                        }
                    });
                },
                'edit': function (editor, e) {
                    if (e.record.data.PaketID == '') {
                        //tambah
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_crud+'_detail',
                            method: 'POST',
                            params: {
                                InventoryID: Ext.getCmp('InventoryID').getValue(),
                                ChildInventoryID: Ext.getCmp('ChildInventoryID').getValue(),
                                Qty: e.record.data.Qty
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                                }

                                store_detail.load({
                                    params: {
                                        InventoryID: Ext.getCmp('InventoryID').getValue()
                                    }
                                });
                            },
                            failure: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                            }
                        });

                    }else{
                        //edit
                        Ext.MessageBox.confirm('Message', 'Do you want to update this data ?', function (btn) {
                            if (btn == 'yes') {

                                Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_crud+'_detail',
                                    method: 'PUT',
                                    params: {
                                        PaketID: e.record.data.PaketID,
                                        name: e.record.data.name,
                                        ChildInventoryID: Ext.getCmp('ChildInventoryID').getValue(),
                                        Qty: e.record.data.Qty
                                    },
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                            break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                        }

                                        store_detail.load({
                                            params: {
                                                InventoryID: Ext.getCmp('InventoryID').getValue()
                                            }
                                        });
                                    },
                                    failure: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('Error', 'Could not connect to the database. Retry later');
                                    }
                                });

                            }
                       });

                    }
                }
            }
        }],
        buttons: [{
            id:'saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();

                form.submit({
                    url: m_crud,
                    method:'POST',
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved');
                        store.load();
                        win.close();
                    },
                    failure: function(fp, o){
                        Ext.MessageBox.alert('Failed', 'Please input all the required field');
                    }
                });
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                win.close();
            }
        }]
    });

    var win = Ext.create('widget.window', {
        title: lang('Barang'),
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '90%',
        height: '93%',
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });

    function displayFormWindow() {
        if (!win.isVisible()) {
            DataForm.getForm().reset();
            //Ext.getCmp('ilogo').setSrc('');
            win.show();
        } else {
            win.close();
        }
    }

    var DataFormUnit = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 200,
        width: '50%',
        bodyPadding: 5,
        autoScroll:false,
        id:'dataFormUnit',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
            fieldLabel: lang('Name'),
            xtype: 'textfield',
            id: 'UnitName',
            name: 'UnitName',
            allowBlank:false,
            width:'100%'
        },{
            fieldLabel: lang('Note'),
            xtype: 'textareafield',
            id: 'UnitNote',
            name: 'UnitNote',
            width:'100%'
        }],
        buttons: [{
            id:'saveButtonUnit',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                form.submit({
                    url: m_crud+'_unit_popup',
                    method:'POST',
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        winUnit.close();
                        mc_unit.load({
                            scope: this,
                            callback: function(records, operation, success) {
                                Ext.getCmp('UnitMeasurementID').setValue(o.result.UnitMeasurementID)
                            }
                        });
                    },
                    failure: function(fp, o) {
                        Ext.MessageBox.alert('Failed', 'Please input all the required field');
                    }
                });
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winUnit.close();
            }
        }]
    });

    var winUnit = Ext.create('widget.window', {
        title: lang('Unit Measurement'),
        id: 'winUnit',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '50%',
        height: 210,
        layout: {
            type: 'fit'
        },
        items: [DataFormUnit]
    });

    function displayFormUnitMeasurement(){
        if (!winUnit.isVisible()) {
            DataFormUnit.getForm().reset();
            winUnit.show();
        } else {
            winUnit.close();
        }
    }

    var DataFormSup = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 410,
        width: '50%',
        bodyPadding: 5,
        autoScroll:false,
        id:'dataFormSup',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items:[{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .48,
                layout: 'form',
                padding: 10,
                xtype: 'fieldset',
                title: 'Data',
                height: '100%',
                items:[{
                    fieldLabel: lang('Name'),
                    xtype: 'textfield',
                    id: 'SuppName',
                    name: 'SuppName',
                    allowBlank:false
                },{
                    fieldLabel: lang('Email'),
                    xtype: 'textfield',
                    id: 'SuppEmail',
                    name: 'SuppEmail'
                },{
                    fieldLabel: lang('Phone'),
                    xtype: 'textfield',
                    id: 'SuppPhone',
                    name: 'SuppPhone'
                },{
                    fieldLabel: lang('Note'),
                    xtype: 'textareafield',
                    id: 'SuppNote',
                    name: 'SuppNote',
                    height: 50,
                    width: 200,
                }]
            },{
                columnWidth: .52,
                height: '100%',
                layout: 'form',
                xtype: 'fieldset',
                title: 'Detail',
                style:'margin-left:12px',
                items:[{
                    fieldLabel: lang('Address'),
                    xtype: 'textfield',
                    id: 'SuppAddress',
                    name: 'SuppAddress'
                },{
                    id: 'Provinsi',
                    name: 'Provinsi',
                    xtype: 'combo',
                    fieldLabel: lang('Provinsi'),
                    store: mc_Provinsi,
                    displayField: 'label',
                    valueField: 'label',
                    queryMode: 'local',
                    listeners: {
                        change: function (cb, nv, ov) {
                            mc_Kabupaten.load({
                                params: {
                                    key: Ext.getCmp('Provinsi').getValue()
                                }
                            });
                            Ext.getCmp('Kabupaten').enable();
                        }
                    }
                },{
                    id: 'Kabupaten',
                    name: 'Kabupaten',
                    xtype: 'combo',
                    fieldLabel: lang('Kabupaten'),
                    store: mc_Kabupaten,
                    displayField: 'label',
                    valueField: 'label',
                    queryMode: 'local',
                    disabled: 'true',
                    listeners: {
                        change: function (cb, nv, ov) {
                            mc_Kecamatan.load({
                                params: {
                                    key: Ext.getCmp('Kabupaten').getValue()
                                }
                            });
                            Ext.getCmp('Kecamatan').enable();
                        }
                    }
                },{
                    id: 'Kecamatan',
                    name: 'Kecamatan',
                    xtype: 'combo',
                    fieldLabel: lang('Kecamatan'),
                    store: mc_Kecamatan,
                    displayField: 'label',
                    valueField: 'label',
                    queryMode: 'local',
                    disabled: 'true',
                    listeners: {
                        change: function (cb, nv, ov) {
                            mc_Desa.load({
                                params: {
                                    key: Ext.getCmp('Kecamatan').getValue()
                                }
                            });
                            Ext.getCmp('Desa').enable();
                        }
                    }
                },{
                    id: 'Desa',
                    name: 'Desa',
                    xtype: 'combo',
                    fieldLabel: lang('Desa'),
                    store: mc_Desa,
                    displayField: 'label',
                    disabled: 'true',
                    valueField: 'id',
                    queryMode: 'local'
                }]
            }]
        }],
        buttons: [{
            id:'saveButtonSup',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                form.submit({
                    url: m_crud+'_supplier_popup',
                    method:'POST',
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        winSup.close();
                        mc_supplier.load({
                            scope: this,
                            callback: function(records, operation, success) {
                                Ext.getCmp('SupplierID').setValue(o.result.SupplierID)
                            }
                        });
                    },
                    failure: function(fp, o) {
                        Ext.MessageBox.alert('Failed', 'Please input all the required field');
                    }
                });
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winSup.close();
            }
        }]
    });

    var winSup = Ext.create('widget.window', {
        title: lang('Supplier'),
        id: 'winSup',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '50%',
        height: 410,
        layout: {
            type: 'fit'
        },
        items: [DataFormSup]
    });

    function displayFormSupplier(){
        if (!winSup.isVisible()) {
            DataFormSup.getForm().reset();
            winSup.show();
        } else {
            winSup.close();
        }
    }

    var DataFormCat = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 200,
        width: '50%',
        bodyPadding: 5,
        autoScroll:false,
        id:'dataFormCat',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items:[{
            fieldLabel: lang('Name'),
            xtype: 'textfield',
            id: 'CatName',
            name: 'CatName',
            allowBlank:false
        },{
            fieldLabel: lang('Note'),
            xtype: 'textareafield',
            id: 'CatNote',
            name: 'CatNote',
            height: 50,
            width: 200
        }],
        buttons: [{
            id:'saveButtonSup',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();

                form.submit({
                    url: m_crud+'_category_popup',
                    method:'POST',
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        winCat.close();
                        mc_kategori.load({
                            scope: this,
                            callback: function(records, operation, success) {
                                Ext.getCmp('CategoryID').setValue(o.result.CategoryID)
                            }
                        });
                    },
                    failure: function(fp, o) {
                        Ext.MessageBox.alert('Failed', 'Please input all the required field');
                    }
                });

            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winCat.close();
            }
        }]
    });

    var winCat = Ext.create('widget.window', {
        title: lang('Category'),
        id: 'winCat',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '50%',
        height: 200,
        layout: {
            type: 'fit'
        },
        items: [DataFormCat]
    });

    function displayFormCat(){
        if (!winCat.isVisible()) {
            DataFormCat.getForm().reset();
            winCat.show();
        } else {
            winCat.close();
        }
    }

    function checkImageExists(imageUrl, callBack) {
        var imageData = new Image();
        imageData.onload = function() {
            callBack(true);
        };
        imageData.onerror = function() {
            callBack(false);
        };
        imageData.src = imageUrl;
    }
});