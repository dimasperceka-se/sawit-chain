/*
* @Author: nikolius
* @Date:   2016-08-22 13:31:23
* @Last Modified by:   nikolius
* @Last Modified time: 2016-12-28 16:49:57
*/
Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();

    //store compost transaction
    Ext.define('compostTrans.Model', {
        extend: 'Ext.data.Model',
        fields: ['id', 'Buyer', 'Volume', 'Price', 'Total', 'DateTransaction', 'CloneTypeID'],
    });
    var store_compost_trans = Ext.create('Ext.data.Store', {
        model: 'compostTrans.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_get_compost_trans,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    store_compost_trans.on('beforeload', function() {
        var proxy = store_compost_trans.getProxy();
        proxy.setExtraParam('compost_id', Ext.getCmp('CompostID').getValue());
    });

    //store buyer
    var mc_pembeli = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            'label': 'Anggota Kelompok'
        }, {
            'label': 'Petani Lain'
        }, {
            'label': 'Traders'
        }, {
            'label': 'Dll'
        }, {
            'label': 'Pemerintah'
        }],
    });

    //ajax ambil data compost
    Ext.Ajax.request({
        url: m_get_compost,
        method: 'GET',
        waitMsg: lang('Please Wait'),
        success: function(data) {
            var jsonResp = JSON.parse(data.responseText);
            //console.log(jsonResp);
            Ext.getCmp('CompostID').setValue(jsonResp.CompostID);
            Ext.getCmp('cFarmerName').setValue(jsonResp.FarmerName);
            Ext.getCmp('Established').setValue(jsonResp.Established);
            Ext.getCmp('CompostLatitude').setValue(jsonResp.Latitude);
            Ext.getCmp('CompostLongitude').setValue(jsonResp.Longitude);
            if (jsonResp.MesinChooper == '1') Ext.getCmp('MesinChooper').setValue(true);
            if (jsonResp.MesinChooper == '2') Ext.getCmp('MesinChooper2').setValue(true);
            if (jsonResp.RumahKompos == '1') Ext.getCmp('RumahKompos').setValue(true);
            if (jsonResp.RumahKompos == '2') Ext.getCmp('RumahKompos2').setValue(true);

            //console.log(jsonResp.CompostID);
            if(jsonResp.CompostID == undefined){
                Ext.getCmp('gridCompostTransaksi').setDisabled(true);
            }else{
                Ext.getCmp('gridCompostTransaksi').setDisabled(false);

                store_compost_trans.load({
                    params: {
                        compost_id: jsonResp.CompostID
                    }
                });
            }
        },
        failure: function() {
            /*
            Ext.MessageBox.show({
                title: 'Notifications',
                msg: 'Failed to get data. No Professional Farmer selected',
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
            */
            window.location = m_base_url+'prog_sce/profile';
        }
    });

    var cRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'cRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var DataPanel = Ext.create('Ext.form.Panel', {
        title:'Farmer Compost Unit',
        padding: 0,
        margin:15,
        //height:850,
        frame: true,
        bodyStyle:{"background-color":"#F0F0F0"},
        style:'background-color:#F0F0F0;',
        bodyPadding: 5,
        id: 'mainPanel',
        items: [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .5,
                layout: 'form',
                border: false,
                padding: 10,
                defaults: {
                    labelWidth: 150
                },
                items: [{
                    xtype: 'textfield',
                    id: 'CompostID',
                    name: 'CompostID',
                    hidden: true
                },{
                    xtype: 'textfield',
                    fieldLabel: lang('Nama Farmer'),
                    id: 'cFarmerName',
                    name: 'cFarmerName',
                    readOnly: true
                },{
                    xtype: 'datefield',
                    fieldLabel: lang('Tanggal Berdiri'),
                    id: 'Established',
                    name: 'Established',
                    format: 'Y-m-d',
                    allowBlank:false
                },{
                    xtype: 'textfield',
                    fieldLabel: lang('Latitude'),
                    id: 'CompostLatitude',
                    name: 'CompostLatitude',
                    readOnly: m_hakakses_lat_short
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Longitude'),
                    id: 'CompostLongitude',
                    name: 'CompostLongitude',
                    readOnly: m_hakakses_long_short
                }]
            },{
                columnWidth: .5,
                layout: 'form',
                border: false,
                padding: 10,
                defaults: {
                    labelWidth: 200
                },
                items: [{
                    xtype: 'radiogroup',
                    fieldLabel: lang('Mesin Chooper'),
                    readOnly: true,
                    style:'margin-bottom:20px;',
                    items: [{
                        name: 'MesinChooper',
                        id: 'MesinChooper',
                        boxLabel: lang('Ya'),
                        inputValue: '1'
                    }, {
                        name: 'MesinChooper',
                        id: 'MesinChooper2',
                        boxLabel: lang('Tidak'),
                        inputValue: '2'
                    }]
                }, {
                    xtype: 'radiogroup',
                    fieldLabel: lang('Rumah Kompos'),
                    readOnly: true,
                    items: [{
                        name: 'RumahKompos',
                        id: 'RumahKompos',
                        boxLabel: lang('Ya'),
                        inputValue: '1'
                    }, {
                        name: 'RumahKompos',
                        id: 'RumahKompos2',
                        boxLabel: lang('Tidak'),
                        inputValue: '2'
                    }]
                }]
            }]
        },{
            xtype: 'gridpanel',
            id: 'gridCompostTransaksi',
            style: 'border:1px solid #CCC;',
            store: store_compost_trans,
            width: '100%',
            height:475,
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store_compost_trans, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    //cls: m_act_save,
                    hidden: m_act_add,
                    text: lang('Add'),
                    scope: this,
                    handler: function() {
                        cRowEditing.cancelEdit();
                        var r = Ext.create('compostTrans.Model', {
                            id: '',
                            Buyer: '',
                            Volume: '',
                            Price: '',
                            Total: '',
                            DateTransaction: ''
                        });
                        store_compost_trans.insert(0, r);
                        cRowEditing.startEdit(0, 0);
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    //cls: m_act_save,
                    hidden: m_act_update,
                    text: lang('Update'),
                    scope: this,
                    handler: function() {
                        cRowEditing.cancelEdit();
                        var sm = Ext.getCmp('gridCompostTransaksi').getSelectionModel().getSelection();
                        cRowEditing.startEdit(sm[0].index, 0);
                    }
                },{
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    //cls: m_act_save,
                    hidden: m_act_delete,
                    text: lang('Delete'),
                    scope: this,
                    handler: function() {
                        var smb = Ext.getCmp('gridCompostTransaksi').getSelectionModel().getSelection()[0];
                        cRowEditing.cancelEdit();
                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_input_compost_trans,
                                    method: 'DELETE',
                                    params: {
                                        id: smb.raw.id
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store_compost_trans.load({
                                                    params: {
                                                        compost_id: Ext.getCmp('CompostID').getValue()
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
                }]
            }],
            columns: [{
                text: lang('ID'),
                dataIndex: 'id',
                hidden: true
            },{
                text: lang('No'),
                xtype: 'rownumberer',
                width: '5%'
            },{
                text: lang('Pembeli'),
                dataIndex: 'Buyer',
                width: '25%',
                editor: {
                    xtype: 'combo',
                    store: mc_pembeli,
                    displayField: 'label',
                    valueField: 'label',
                    queryMode: 'local',
                    allowBlank: false
                }
            },{
                text: lang('Volume'),
                dataIndex: 'Volume',
                width: '10%',
                xtype: 'numbercolumn',
                format:'0,000',
                editor: {
                    xtype: 'textfield',
                    id: 'cvol',
                    allowBlank: false,
                    listeners: {
                        change: function() {
                            Ext.getCmp('ctot').setValue(Ext.getCmp('cvol').getValue() * Ext.getCmp('cpri').getValue());
                        }
                    }
                }
            },{
                text: lang('Harga Satuan'),
                dataIndex: 'Price',
                width: '15%',
                xtype: 'numbercolumn',
                format:'0,000',
                editor: {
                    xtype: 'textfield',
                    id: 'cpri',
                    allowBlank: false,
                    listeners: {
                        change: function() {
                            Ext.getCmp('ctot').setValue(nnumber_format(Ext.getCmp('cvol').getValue() * Ext.getCmp('cpri').getValue()));
                        }
                    }
                }
            },{
                text: lang('Total Harga'),
                dataIndex: 'Total',
                width: '15%',
                xtype: 'numbercolumn',
                format:'0,000',
                editor: {
                    xtype: 'textfield',
                    allowBlank: false,
                    id: 'ctot',
                    readOnly: true
                }
            },{
                text: lang('Tanggal Penjualan'),
                dataIndex: 'DateTransaction',
                format: 'Y-m-d',
                width: '29%',
                editor: {
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    allowBlank: false
                }
            }],
            plugins: [cRowEditing],
            listeners: {
                'canceledit': function(editor, e, eOpts) {
                    store_compost_trans.load({
                        params: {
                            compost_id: Ext.getCmp('CompostID').getValue()
                        }
                    });
                },
                'edit': function(editor, e) {
                    if (e.record.data.id == '') {
                        //tambah

                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_input_compost_trans,
                            method: 'POST',
                            params: {
                                id_compost: Ext.getCmp('CompostID').getValue(),
                                Buyer: e.record.data.Buyer,
                                Volume: e.record.data.Volume,
                                Price: e.record.data.Price,
                                Total: e.record.data.Totel,
                                DateTransaction: e.record.data.DateTransaction
                            },
                            success: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        store_compost_trans.load({
                                            params: {
                                                compost_id: Ext.getCmp('CompostID').getValue()
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

                    }else{
                        //update
                        Ext.MessageBox.confirm('Message', 'Update this data ?', function(btn) {
                            if(btn == "yes"){
                                Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_input_compost_trans,
                                    method: 'PUT',
                                    params: {
                                        id: e.record.data.id,
                                        id_compost: Ext.getCmp('CompostID').getValue(),
                                        Buyer: e.record.data.Buyer,
                                        Volume: e.record.data.Volume,
                                        Price: e.record.data.Price,
                                        Total: e.record.data.Totel,
                                        DateTransaction: e.record.data.DateTransaction
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                                store_compost_trans.load({
                                                    params: {
                                                        compost_id: Ext.getCmp('CompostID').getValue()
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
            }
        }],
        buttons: [{
            id: 'csaveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            hidden: m_act_add,
            handler: function() {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('CompostID').getValue() != '') methode = 'PUT'; else methode = 'POST';

                form.submit({
                    url: m_input_compost,
                    method: methode,
                    waitMsg: 'Sending data...',
                    success: function(fp,data) {
                        var jsonResp = data.result;

                        if(jsonResp.prosesSave == "1"){
                            Ext.MessageBox.alert('Success', 'Data saved.');
                            Ext.getCmp('CompostID').setValue(jsonResp.CompostID);
                            Ext.getCmp('gridCompostTransaksi').setDisabled(false);
                        }else{
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: 'Failed to saved data',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    },
                    failure: function(data){
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Failed to saved data, Please fill all the require input',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });

            }
        }],
        renderTo: 'ext-content'
    });

});