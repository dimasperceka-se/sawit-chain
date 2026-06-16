/*
 * @Author: sonny.fitriawan 
 * @Date: 2017-12-07 14:19:42 
 * @Last Modified by: sonny.fitriawan
 * @Last Modified time: 2017-12-08 15:22:11
 */

/* var ContextMenuGrid = Ext.create('Ext.menu.menu',{
    items: [{
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        text: lang('View'),
        handler: function() {
            
        }
    }]
}); */ 

var storeMainGrid = Ext.create('Koltiva.store.Reference.Vehicle.MainGrid');

var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
    id: 'RowEditing',
    clicksToMoveEditor: 0,
    autoCancel: false,
    errorSummary: false,
    clicksToEdit: 2
});

var cmbStatusCode = Ext.create('Ext.data.Store', {
    fields: ['id', 'label'],
    data: [{
        "id": "active",
        "label": "ACTIVE"
    }, {
        "id": "inactive",
        "label": "INACTIVE"
    }]
});

Ext.define('Scpp.Model', {
    extend: 'Ext.data.Model',
    fields: ['BrandID', 'BrandName', 'StatusCode']
});

Ext.define('Koltiva.view.Reference.Vehicle.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Reference.Vehicle.MainGrid-MainPanel',
    renderTo: 'ext-content',
    listeners: {
        afterRender: function(){
            Ext.getCmp('view.Reference.Vehicle.MainGrid-gridMainGrid').getStore().load();
        }
    },
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;
        thisObj.items = [{
            xtype: 'grid',
            id: 'view.Reference.Vehicle.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeMainGrid,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'view.Reference.Vehicle.MainGrid-gridToolbar',
                store: storeMainGrid,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: m_act_add,
                    handler: function() {
                        RowEditing.cancelEdit();
                        var r = Ext.create('Scpp.Model', {
                            BrandID: '',
                            BrandName: '',
                            StatusCode: ''
                        });
                        storeMainGrid.insert(0, r);
                        RowEditing.startEdit(0, 0);
                    }
                },{
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: m_act_delete,
                    text: lang('Delete'),
                    scope: this,
                    handler: function() {
                        var smb = Ext.getCmp('view.Reference.Vehicle.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                        RowEditing.cancelEdit();
                        console.log(smb.raw);
                        Ext.MessageBox.confirm('Message', 'Are you sure want to delete this data ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_crud,
                                    method: 'DELETE',
                                    params: {
                                        BrandID: smb.raw.BrandID
                                    },
                                    success: function(response, opts) {
                                        console.log(response);
                                        if(response.responseText != ''){
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    storeMainGrid.load();
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
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
                dataIndex: 'BrandID',
                hidden: true
            },{
                text: 'No',
                xtype: 'rownumberer',
                width: '5%'
            },{
                text: lang('vehicle_brand_name'),
                width: '65%',
                dataIndex: 'BrandName',
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },{
                text: lang('vehicle_status_code'),
                width: '30%',
                dataIndex: 'StatusCode',
                renderer: Ext.util.Format.uppercase,
                editor: {
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
                    storeMainGrid.load();
                },
                'edit': function(editor, e) {
                    var BrandID = e.record.data.BrandID;
                    var BrandName = e.record.data.BrandName;
                    var StatusCode = e.record.data.StatusCode;
                    if (BrandID.trim() === '') {
                        console.log(m_crud);
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_crud,
                            method: 'POST',
                            params: {
                                BrandName: BrandName,
                                StatusCode: StatusCode
                            },
                            success: function(response, opts) {
                                if(response.responseText != ''){
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
                                }
                            },
                            failure: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                console.log(obj);
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                            }
                        });
                    } else {
                        Ext.MessageBox.confirm('Message', 'Do you want to update ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_crud,
                                    method: 'PUT',
                                    params: {
                                        BrandID: BrandID,
                                        BrandName: BrandName,
                                        StatusCode: StatusCode
                                    },
                                    success: function(response, opts) {
                                        if(response.responseText != ''){
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    storeMainGrid.load();
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }    
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
        }]
        this.callParent(arguments);
    }
});
 