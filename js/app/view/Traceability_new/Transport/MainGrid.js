// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)
Ext.define('Koltiva.view.Traceability_new.Transport.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transport.MainGrid',
    renderTo: 'ext-content',
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridMain = Ext.create('Koltiva.store.Traceability_new.Reference.Transport.MainGrid');
        var cmb_status = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "active",
                "label": lang("Active")
            }, {
                "id": "inactive",
                "label": lang("Inactive")
            }, {
                "id": "nullified",
                "label": lang("Nullified")
            }]
        });
        //items
        var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id : 'roweditingId',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });
        
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Transport.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMain,
            height : 550,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            plugins: [RowEditing],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability_new.Transport.MainGrid-gridToolbar',
                store: storeGridMain,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    scope: this,
                    cls: m_act_add,
                    handler: function () {
                        RowEditing.cancelEdit();
                        var record = Ext.getCmp('Koltiva.view.Traceability_new.Transport.MainGrid-gridMainGrid').getStore();

                        record.insert(0, {
                            "DestTransportID" : '',
                            "DestTransportName" : '',
                            "IsDetail" : '',
                            "StatusCode" : 'active'
                        });
                        RowEditing.startEdit(0, 0);
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    scope: this,
                    cls: m_act_update,
                    handler: function () {
                        RowEditing.cancelEdit();
                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transport.MainGrid-gridMainGrid').getSelectionModel().getSelection();
                        if (sm[0]) {
                            RowEditing.startEdit(sm[0].index, 0);
                        }else{
                            Ext.MessageBox.alert('Warning', lang('Please select data on the grid !'));
                        }
                    }
                },{
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: m_act_delete,
                    text: lang('Delete'),
                    scope: this,
                    handler: function () {
                        var smb = Ext.getCmp('Koltiva.view.Traceability_new.Transport.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                        RowEditing.cancelEdit();

                        if (smb) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure will delete this data ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_api + '/traceability_api/Transport/del/' + smb.raw.DestTransportID,
                                        method: 'DELETE',
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transport.MainGrid-gridMainGrid').getStore().load();
                                                    Ext.MessageBox.alert('Warning', lang('Delete success'));
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', lang('Delete failed'));
                                                    break;
                                            }
                                        },
                                        failure: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }else{
                            Ext.MessageBox.alert('Warning', lang('Please select data on the grid !'));
                        }
                    }
                }]
            }],
            columns: [{
                text : lang('Nomor'),
                xtype : 'rownumberer',
                align : 'center',
                width : 60
            },{
                text: lang('Transport Name'),
                dataIndex: 'DestTransportName',
                flex: 1,
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },{
                text: lang('Is Detail ? (Y/N)'),
                dataIndex: 'IsDetail',
                flex: 1,
                editor: {
                    xtype: 'checkbox',
                    inputValue : 1,
                    allowBlank: false
                },
                align : 'center',
                renderer : function(value, metaData, record, row, col, store, gridView){
                    return value == "0" ? 'N' : 'Y';
                }
            },{
                text: lang('Status'),
                dataIndex: 'StatusCode',
                flex: 1,
                editor: {
                    xtype: 'combobox',
                    store : cmb_status,
                    displayField : 'label',
                    valueField : 'id',
                    allowBlank: false
                }
            },{
                text: lang('Date Updated'),
                dataIndex: 'DateUpdated',
                align:'right',
                flex: 1,
            }],
            listeners: {
                'canceledit': function (editor, e, eOpts) {
                    Ext.getCmp('Koltiva.view.Traceability_new.Transport.MainGrid-gridMainGrid').getStore().load();
                },
                'edit': function (editor, e) {
                    var DestTransportID = e.record.data.DestTransportID;
                    var DestTransportName = e.record.data.DestTransportName;
                    var IsDetail = e.record.data.IsDetail;
                    var StatusCode = e.record.data.StatusCode;

                    Ext.Ajax.request({
                        waitMsg: lang('Please wait...'),
                        url: m_api + '/reference/transport-save',
                        method: 'POST',
                        params: {
                            DestTransportID : DestTransportID,
                            DestTransportName : DestTransportName,
                            IsDetail : IsDetail,
                            StatusCode : StatusCode
                        },
                        success: function (response, opts) {
                            var obj = Ext.decode(response.responseText);
                            var message = DestTransportID != '' ? 'Update' : 'Insert';
                            switch (obj.success) {
                                case true:
                                    Ext.MessageBox.alert('Success', lang(message + ' success'));
                                    Ext.getCmp('Koltiva.view.Traceability_new.Transport.MainGrid-gridMainGrid').getStore().load();
                                    break;
                                default:
                                    Ext.MessageBox.alert('Warning', lang(obj.message));
                                    break;
                            }
                        }, 
                        failure: function (response, opts) {
                            var obj = Ext.decode(response.responseText);
                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                        }
                    });
                }
            }
        }];

        this.callParent(arguments);
    }
});