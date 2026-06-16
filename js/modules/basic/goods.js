/*
* @Author: nikolius
* @Date:   2016-12-29 15:06:57
* @Last Modified by:   nikolius
* @Last Modified time: 2016-12-30 10:50:09
*/
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    Ext.define('mainGridModel.Model', {
        extend: 'Ext.data.Model',
        fields: [`GoodsID`,`GoodsCode`,`GoodsName`,`GoodsUnitsID`,`UnitsName`,`GoodsUsage`,`GoodsUsageLabel`,`StatusCode`]
    });

    var store = Ext.create('Ext.data.Store', {
        model: 'mainGridModel.Model',
        autoLoad: true,
        pageSize: 50,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_goods/main_list',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.sNama = Ext.getCmp('sNama').getValue();
            }
        }
    });

    var cmbStatusCode = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "active",
            "label": "ACTIVE"
        }, {
            "id": "inactive",
            "label": "INACTIVE"
        },{
            "id": "nullified",
            "label": "NULLIFIED"
        }]
    });

    var cmbUsage = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": lang("Participant")
        }, {
            "id": "2",
            "label": lang("Activity")
        },{
            "id": "3",
            "label": lang("Both of them")
        }]
    });

    var cmbUnit = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_goods/ref_unit',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'RowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    page: 1,
                    start: 0,
                    limit: 50
                }
            });
        }
    }

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 250,
        id: 'grid',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            minHeight: 38,
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                scope: this,
                hidden: m_act_add,
                handler: function() {
                    RowEditing.cancelEdit();
                    var r = Ext.create('mainGridModel.Model', {
                        GoodsID: '',
                        GoodsCode: '',
                        GoodsName: '',
                        GoodsUnitsID: '',
                        UnitsName: '',
                        GoodsUsage: '',
                        GoodsUsageLabel: '',
                        StatusCode: ''
                    });
                    store.insert(0, r);
                    RowEditing.startEdit(0, 0);
                }
            },{
                itemId: 'remove',
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                hidden: m_act_delete,
                text: lang('Delete'),
                scope: this,
                handler: function() {
                    var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    RowEditing.cancelEdit();
                    Ext.MessageBox.confirm('Message', 'Are you sure want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/basic_goods/goods',
                                method: 'DELETE',
                                params: {
                                    GoodsID: smb.raw.GoodsID
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store.load();
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
            },{
                name: 'sNama',
                id: 'sNama',
                xtype: 'textfield',
                width: 300,
                emptyText: lang('Name'),
                listeners: {
                    specialkey: submitOnEnter
                }
            },{
                xtype: 'button',
                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                margin: '0px 0px 0px 6px',
                text: lang('Search'),
                handler: function() {
                    store.load({
                        params: {
                            page: 1,
                            start: 0,
                            limit: 50
                        }
                    });
                }
            }]
        }],
        columns: [{
            dataIndex: 'GoodsID',
            hidden: true
        },{
            text: 'No',
            xtype: 'rownumberer',
            width: '3%'
        },{
            text: lang('Code'),
            width: '15%',
            dataIndex: 'GoodsCode',
            editor: {
                xtype: 'textfield',
                id:'GoodsCode',
                allowBlank: false
            }
        },{
            text: lang('Name'),
            width: '40%',
            dataIndex: 'GoodsName',
            editor: {
                xtype: 'textfield',
                id:'GoodsName',
                allowBlank: false
            }
        },{
            text: lang('Unit'),
            width: '15%',
            dataIndex: 'UnitsName',
            editor: {
                allowBlank: false,
                xtype: 'combo',
                store: cmbUnit,
                id: 'GoodsUnitsID',
                queryMode: 'local',
                displayField: 'label',
                valueField: 'id',
                editable: false
            }
        },{
            text: lang('Usage'),
            width: '15%',
            dataIndex: 'GoodsUsageLabel',
            editor: {
                allowBlank: false,
                xtype: 'combo',
                store: cmbUsage,
                id: 'GoodsUsage',
                queryMode: 'local',
                displayField: 'label',
                valueField: 'id',
                editable: false
            }
        },{
            text: lang('Status'),
            width: '12%',
            dataIndex: 'StatusCode',
            renderer: Ext.util.Format.uppercase,
            editor: {
                allowBlank: false,
                xtype: 'combo',
                store: cmbStatusCode,
                id: 'StatusCode',
                queryMode: 'local',
                displayField: 'label',
                valueField: 'id',
                editable: false
            }
        }],
        plugins: [RowEditing],
        listeners: {
            'canceledit': function(editor, e, eOpts) {
                store.load();
            },
            'edit': function(editor, e) {
                //console.log(e);

                var GoodsID = e.record.data.GoodsID;
                var GoodsCode = e.record.data.GoodsCode;
                var GoodsName = e.record.data.GoodsName;
                var UnitsName = e.record.data.UnitsName;
                var GoodsUsageLabel = e.record.data.GoodsUsageLabel;
                var StatusCode = e.record.data.StatusCode;

                if (GoodsID.trim() === '') {
                    //insert
                    Ext.Ajax.request({
                        waitMsg: 'Please wait...',
                        url: m_api + '/basic_goods/goods',
                        method: 'POST',
                        params: {
                            GoodsID: GoodsID,
                            GoodsCode: GoodsCode,
                            GoodsName: GoodsName,
                            UnitsName: UnitsName,
                            GoodsUsageLabel: GoodsUsageLabel,
                            StatusCode: StatusCode
                        },
                        success: function(response, opts) {
                            var obj = Ext.decode(response.responseText);
                            switch (obj.success) {
                                case true:
                                    Ext.MessageBox.alert('Success', obj.message);
                                    store.load();
                                    break;
                                default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                            }
                        },
                        failure: function(response, opts) {
                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                        }
                    });
                }else{
                    //update
                    Ext.MessageBox.confirm('Message', 'Do you want to update ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please wait...',
                                url: m_api + '/basic_goods/goods',
                                method: 'POST',
                                params: {
                                    GoodsID: GoodsID,
                                    GoodsCode: GoodsCode,
                                    GoodsName: GoodsName,
                                    UnitsName: UnitsName,
                                    GoodsUnitsID: GoodsUnitsID,
                                    GoodsUsageLabel: GoodsUsageLabel,
                                    StatusCode: StatusCode
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    console.log(obj);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store.load();
                                            break;
                                        default:
                                            Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                    }
                                },
                                failure: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    console.log(obj);
                                    Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                }
                            });
                        }
                    });
                }

            }
        }
    });

});