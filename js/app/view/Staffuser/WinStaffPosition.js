/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Jul 14 2020
 *  File : WinStaffPosition.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    * StaffID
    * ObjType
*/

Ext.define('Koltiva.view.Staffuser.WinStaffPosition' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Staffuser.WinStaffPosition',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Staff Position'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '76%',
    height: '66%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        Ext.define('staffPosModel.Model', {
            extend: 'Ext.data.Model',
            fields: ['StaffPosID', 'PositionName', 'StaffPostStart', 'StaffPostEnd', 'StatusCode']
        });
    
        var store_staff_position_list = Ext.create('Ext.data.Store', {
            model: 'staffPosModel.Model',
            pageSize: 10,
            remoteSort: true,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/staff_position_main_list',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.StaffID = thisObj.viewVar.StaffID;
                }
            }
        });
    
        var comboe_position_ref = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/position_reference',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.ObjType = thisObj.viewVar.ObjType;
                }
            }
        });
    
        var comboe_position_status = Ext.create('Ext.data.Store', {
            fields: ['id','label'],
            data: [{
                'id' : 'active',
                'label': lang('Active')
            }, {
                'id' : 'inactive',
                'label': lang('Inactive')
            }],
        });
    
        var posRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'posRowEditing',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });

        thisObj.items = [{
            xtype: 'gridpanel',
            id: 'gridMainListStaffPosition',
            style: 'border:1px solid #CCC;',
            store: store_staff_position_list,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            height:325,
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store_staff_position_list, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/silk/add.png',
                    hidden: !m_act_staff_position_add,
                    text: lang('Add'),
                    scope: this,
                    handler: function() {
                        posRowEditing.cancelEdit();
                        var r = Ext.create('staffPosModel.Model', {
                            StaffPosID: '',
                            PositionName: '',
                            StaffPostStart: '',
                            StaffPostEnd: '',
                            StatusCode: ''
                        });
                        store_staff_position_list.insert(0, r);
                        posRowEditing.startEdit(0, 0);
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/silk/delete.png',
                    hidden: !m_act_staff_position_delete,
                    text: lang('Delete'),
                    scope: this,
                    handler: function() {
                        var smb = Ext.getCmp('gridMainListStaffPosition').getSelectionModel().getSelection()[0];
                        posRowEditing.cancelEdit();

                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/basic_staff/staff_position',
                                    method: 'DELETE',
                                    params: {
                                        id: smb.raw.StaffPosID
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                                store_staff_position_list.load();
                                            break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                        }
                                    },
                                    failure: function(response, opts) {
                                        Ext.MessageBox.alert('error', 'Could not connect to the API. Retry later');
                                    }
                                });
                            }
                        });
                    }
                }]
            }],
            columns: [{
                dataIndex: 'StaffPosID',
                hidden: true
            },{
                text: lang('No'),
                xtype: 'rownumberer',
                width: '4%'
            },{
                text: lang('Position'),
                dataIndex: 'PositionName',
                width: '56%',
                editor: {
                    xtype: 'combo',
                    store: comboe_position_ref,
                    displayField: 'label',
                    valueField: 'id',
                    id: 'cmbPositionRefGrid',
                    queryMode: 'local',
                    allowBlank: false
                }
            },{
                text: lang('Start'),
                dataIndex: 'StaffPostStart',
                format: 'Y-m-d',
                width: '15%',
                editor: {
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    allowBlank: false
                }
            },{
                text: lang('End'),
                dataIndex: 'StaffPostEnd',
                format: 'Y-m-d',
                width: '15%',
                editor: {
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    allowBlank: false
                }
            },{
                text: lang('Status'),
                dataIndex: 'StatusCode',
                width: '9%',
                editor: {
                    xtype: 'combo',
                    store: comboe_position_status,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    allowBlank: false
                }
            }],
            plugins: [posRowEditing],
            listeners: {
                itemdblclick: function(dv, record, item, index, e) {
                    //buat hak akses saja, tidak ada aksi apa2, updatenya otomatis detek
                    if (!m_act_staff_position_update == true) {
                        posRowEditing.cancelEdit();
                    }
                },
                'canceledit': function(editor, e, eOpts) {
                    store_staff_position_list.load();
                },
                'edit': function(editor, e) {
                    //tambah/update
                    Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_api + '/basic_staff/staff_position',
                        method : 'POST',
                        params: {
                            StaffID: thisObj.viewVar.StaffID,
                            StaffPosID:  e.record.data.StaffPosID,
                            PositionID: Ext.getCmp('cmbPositionRefGrid').getValue(),
                            StaffPostStart: e.record.data.StaffPostStart,
                            StaffPostEnd: e.record.data.StaffPostEnd,
                            StatusCode: e.record.data.StatusCode
                        },
                        success: function(response, opts){
                            var obj = Ext.decode(response.responseText);
                            switch(obj.success){
                                case true:
                                    Ext.MessageBox.alert('Success', obj.message);
                                    store_staff_position_list.load();
                                break;
                                default:
                                    Ext.MessageBox.show({
                                        title: 'Failed',
                                        msg: obj.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                    store_staff_position_list.load();
                                break;
                            }
                        },
                        failure: function(response, opts){
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: 'Failed to update data',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                            store_staff_position_list.load();
                        }
                    });
                }
            }
        }];

        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/close.png',
			text: lang('Close'),
			cls:'Sfr_BtnFormGrey',
			overCls:'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    }
});