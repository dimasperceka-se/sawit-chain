/*
* @Author: nikolius
* @Date:   2016-09-27 16:01:20
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 11:37:04
*/
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();

    Ext.define('mainGridModel.Model', {
        extend: 'Ext.data.Model',
        fields: ['StaffID','StaffRegisteredNumber','PersonNm','Role','Position','Status','DutyStation','UserAcc','UserApp','UserUsername','GroupName','UserStatus','ObjType','UserAccountStatus']
    });

    var store = Ext.create('Ext.data.Store', {
        model: 'mainGridModel.Model',
        autoLoad: true,
        pageSize: 50,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/main_list',
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
                store.proxy.extraParams.ObjType = Ext.getCmp('sObjType').getValue();
                store.proxy.extraParams.PersonNm = Ext.getCmp('sPersonNm').getValue();
            }
        }
    });

    /*
    var cmb_objtype = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        data: [{
            "id": "bank",
            "label": "Bank"
        }, {
            "id": "cooperative",
            "label": "Cooperative"
        }, {
            "id": "extension",
            "label": "Extension"
        }, {
            "id": "farmergroup",
            "label": "Farmer Group"
        },{
            "id": "private",
            "label": "Private"
        },{
            "id": "program",
            "label": "Program"
        },{
            "id": "sce",
            "label": "SCE"
        },{
            "id": "trader",
            "label": "Trader"
        },{
            "id": "warehouse",
            "label": "Warehouse"
        }]
    });
    */

    var cmb_objtype = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/objtype_list',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items:[{
            icon: varjs.config.base_url + 'images/icons/new/view.png',
            text: lang('View'),
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormStaff('update',cmb_objtype,store,sm.get('StaffID'),true);
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: m_act_update,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormStaff('update',cmb_objtype,store,sm.get('StaffID'),false);
            }
        },{
            icon: varjs.config.base_url + 'images/icons/silk/page_portrait_shot.png',
            text: lang('Staff Position'),
            hidden: !m_act_staff_position,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormPosition(store,sm.get('StaffID'),sm.get('ObjType'));
            }
        },{
            icon: varjs.config.base_url + 'images/icons/silk/user.png',
            text: lang('Management User'),
            hidden: !m_act_staff_user_management,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormUser(sm.get('StaffID'),store);
            }
        },{
            icon: varjs.config.base_url + 'images/icons/silk/user.png',
            text: lang('Management User App'),
            hidden: !m_act_staff_user_app_management,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                if(sm.get('UserAcc') == "No"){
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: lang('Must create user first'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }else{
                    displayFormUserApp(sm.get('StaffID'),store);
                }
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: true,
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];

                //hapus data staff (begin)===============
                Ext.MessageBox.confirm('Message', lang('Are you sure want to delete this data ?') , function(btn){
                    if(btn == 'yes'){
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_api + '/basic_staff_sta/staff',
                            method : 'DELETE',
                            params: {StaffID:  sm.get('StaffID')},
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        store.load();
                                    break;
                                    default:
                                        Ext.MessageBox.show({
                                            title: 'Failed',
                                            msg: obj.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    break;
                                }
                            },
                            failure: function(response, opts){
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: 'Failed to delete data',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        })
                    }
                });
                //hapus data staff (end)===============
            }
        }]
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
            store.loadPage(1);
        }
    }

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 250,
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function(view, record, item, index, e){
               contextMenuGrid.showAt(e.getXY());
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                scope: this,
                hidden: true,
                handler: function() {
                    displayFormStaff('add',cmb_objtype,store,null,false);
                }
            },{
                id: 'sObjType',
                name: 'sObjType',
                xtype: 'combo',
                width: 190,
                store: cmb_objtype,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                selectOnFocus: true,
                emptyText: lang('Staff Role'),
                listeners: {
                    specialkey: submitOnEnter
                }
            },{
                name: 'sPersonNm',
                id: 'sPersonNm',
                xtype: 'textfield',
                width: 300,
                emptyText: lang('Staff Name'),
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
                    store.loadPage(1);
                }
            }]
        }],
        columns: [{
            text: 'ID',
            dataIndex: 'StaffID',
            hidden: true
        },{
            text: 'ObjType',
            dataIndex: 'ObjType',
            hidden: true
        },{
            text: 'No',
            xtype: 'rownumberer',
            width: '3%'
        }, {
            text: lang('Staff Name'),
            dataIndex: 'PersonNm',
            width: '15%'
        },{
            text: lang('Role'),
            dataIndex: 'Role',
            width: '10%'
        },{
            text: lang('Position'),
            dataIndex: 'Position',
            width: '10%'
        },{
            text: lang('Staff Status'),
            dataIndex: 'Status',
            width: '8%'
        },{
            text: lang('Duty Station'),
            dataIndex: 'DutyStation',
            width: '10%'
        },{
            text: lang('Username'),
            dataIndex: 'UserUsername',
            width: '10%'
        },{
            text: lang('Group'),
            dataIndex: 'GroupName',
            width: '10%'
        },{
            text: lang('User Account'),
            dataIndex: 'UserStatus',
            width: '10%'
        },{
            text: lang('User Account Status'),
            dataIndex: 'UserAccountStatus',
            width: '10%'
        },{
            text: lang('User FarmXtension'),
            dataIndex: 'UserApp',
            width: '10%'
        }]
    });

});

function displayFormPosition(store,StaffID,ObjType) {

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
            url: m_api + '/basic_staff_sta/staff_position_main_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.StaffID = StaffID;
            }
        }
    });

    var comboe_position_ref = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/position_reference',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.ObjType = ObjType;
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

    var winStaffPosition = Ext.create('widget.window', {
        title: lang('Staff Position'),
        id: 'winStaffPosition',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '90%',
        height: '81%',
        overflowY: 'auto',
        bodyStyle:{"background-color":"#F0F0F0"},
        style:'background-color:#F0F0F0;padding:4px;',
        items:[{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1,
                layout:'form',
                items: [{
                    xtype: 'gridpanel',
                    title: lang('Data List'),
                    id: 'gridMainListStaffPosition',
                    style: 'border:1px solid #CCC;',
                    store: store_staff_position_list,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    height:510,
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: store_staff_position_list, // same store GridPanel is using
                        dock: 'bottom',
                        displayInfo: true
                    },{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
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
                            icon: varjs.config.base_url + 'images/icons/new/delete.png',
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
                                            url: m_api + '/basic_staff_sta/staff_position',
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
                                url: m_api + '/basic_staff_sta/staff_position',
                                method : 'POST',
                                params: {
                                    StaffID: StaffID,
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
                }]
            }]
        }],
        buttons:[{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winStaffPosition.close();
            }
        }]
    });

    //show windows
    if (!winStaffPosition.isVisible()) {
        winStaffPosition.center();
        winStaffPosition.show();
    } else {
        winStaffPosition.close();
    }
}

function displayFormStaff(displayMethod,cmb_objtype,store,StaffID,viewOnly) {

    var cmb_gender = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [{
            "id": "m",
            "name": lang("Laki-laki")
        }, {
            "id": "f",
            "name": lang("Perempuan")
        }]
    });

    var cmb_status = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "active",
            "label": "Active"
        }, {
            "id": "inactive",
            "label": "Inactive"
        },{
            "id": "nullified",
            "label": "Nullified"
        }]
    });

    var cmb_marital_status = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [{
            "id": "2",
            "name": lang("Single")
        }, {
            "id": "1",
            "name": lang("Menikah")
        },{
            "id": "3",
            "name": lang("Janda/Duda")
        }]
    });

    var cmb_propinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/propinsi',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var cmb_kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/farmer/Kabupatens',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.prov = Ext.getCmp('ProvinceID').getValue();
            }
        }
    });

    var cmb_farmer_district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/farmer/Kabupatens',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.prov = Ext.getCmp('FarmerProvinceID').getValue();
            }
        }
    });

    var cmb_role_district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/farmer/Kabupatens_staff',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.prov = Ext.getCmp('RoleProvinceID').getValue();
            }
        }
    });

    var cmb_workarea = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/workarea',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.prov = Ext.getCmp('WorkAreaProvinceID').getValue();
            }
        }
    });

    var cmb_kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/kecamatan',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.DistrictID = Ext.getCmp('DistrictID').getValue();
            }
        }
    });

    var cmb_desa = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/desa',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.SubDistrictID = Ext.getCmp('SubDistrictID').getValue();
            }
        }
    });

    var cmb_farmer_cpg = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/cpg',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.DistrictID = Ext.getCmp('FarmerDistrictID').getValue();
            }
        }
    });

    var cmb_farmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/farmer',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.CPGid = Ext.getCmp('FarmerCpgID').getValue();
            }
        }
    });

    var cmb_role_obj_id = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/objectid',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.ObjType = Ext.getCmp('ObjType').getValue();
                store.proxy.extraParams.DistrictID = Ext.getCmp('RoleDistrictID').getValue();
            }
        }
    });

    var cmb_position = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/position',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.ObjType = Ext.getCmp('ObjType').getValue();
            }
        }
    });

    var store_view_group_user = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['nama', 'deksripsi', 'unit'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/user_info_group_user',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.userInfoUserId = Ext.getCmp('userInfoUserId').getValue();
            }
        }
    });

    var store_view_district_access = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['provinsi', 'kabupaten'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/user_info_district_access',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.userInfoUserId = Ext.getCmp('userInfoUserId').getValue();
            }
        }
    });

    var winFormStaff = Ext.create('widget.window', {
        title: lang('Form Staff'),
        id: 'winFormStaff',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '65%',
        height: '90%',
        overflowY: 'auto',
        bodyStyle:{"background-color":"#F0F0F0"},
        style:'background-color:#F0F0F0;',
        padding:6,
        scrollOffset: 20,
        items:[{
            xtype: 'form',
            id: 'winFormDataStaff',
            fileUpload: true,
            padding:'5 20 5 8',
            items:[{
                xtype:'panel',
                title: 'A. '+lang('Basic Data'),
                frame:true,
                bodyStyle:{"background-color":"#F0F0F0"},
                style:'background-color:#F0F0F0;margin-bottom:13px;',
                padding:2,
                //height:570,
                items:[{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.45,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'hiddenfield',
                            id: 'StaffID',
                            name: 'StaffID'
                        },{
                            xtype: 'hiddenfield',
                            id: 'userInfoUserId',
                            name: 'userInfoUserId'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Full Name'),
                            labelWidth: 150,
                            allowBlank: false,
                            id: 'PersonNm',
                            name: 'PersonNm'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('SSN'),
                            id: 'Ssn',
                            name: 'Ssn',
                            hidden: true
                        },{
                            xtype: 'datefield',
                            fieldLabel: lang('Birth Date'),
                            allowBlank: false,
                            id: 'BirthDate',
                            name: 'BirthDate',
                            format: 'Y-m-d'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Birth Place'),
                            id: 'BirthPlace',
                            name: 'BirthPlace',
                            hidden: true
                        },{
                            fieldLabel: lang('Gender'),
                            allowBlank: false,
                            msgTarget: 'side',
                            xtype: 'radiogroup',
                            width: '100%',
                            items:[{
                                boxLabel: lang('Laki-laki'),
                                id: 'Gender1',
                                name: 'Gender',
                                inputValue: 'm',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('Perempuan'),
                                id: 'Gender2',
                                name: 'Gender',
                                inputValue: 'f',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Marital Status'),
                            allowBlank: false,
                            store: cmb_marital_status,
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'id',
                            id: 'MaritalSt',
                            name: 'MaritalSt'
                        },{
                            fieldLabel: lang('Nationality'),
                            xtype: 'radiogroup',
                            allowBlank: false,
                            msgTarget: 'side',
                            width: '100%',
                            columns: 2,
                            items: [{
                                boxLabel: lang('Local'),
                                name: 'NationalityNm',
                                inputValue: 'local',
                                id: 'NationalityNm_local',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }, {
                                boxLabel: lang('Expat'),
                                name: 'NationalityNm',
                                inputValue: 'expat',
                                id: 'NationalityNm_expat',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        }]
                    },{
                        columnWidth: 0.05,
                        padding: 4,
                        layout:'form',
                        items:[{}]
                    },{
                        columnWidth: 0.5,
                        padding: 4,
                        layout:'form',
                        items:[{
                            layout:'column',
                            border:false,
                            style:'margin-bottom:5px;margin-right:-5px;',
                            items:[{
                                columnWidth: 1,
                                border: false,
                                layout:{
                                    type:'hbox',
                                    pack:'end'
                                },
                                items:[{
                                    xtype: 'image',
                                    id: 'iphoto',
                                    width: '100px',
                                    height:'100px',
                                    src: m_api_base_url + '/images/Photo/no-user.jpg'
                                },{
                                    xtype: 'textfield',
                                    id: 'Photo_old',
                                    name: 'Photo_old',
                                    inputType: 'hidden'
                                }]
                            }]
                        },{
                            xtype: 'fileuploadfield',
                            fieldLabel: lang('Photo'),
                            labelWidth: 130,
                            id: 'Photo',
                            name: 'Photo',
                            buttonText: 'Browse',
                            listeners: {
                                'change': function (fb, v) {
                                    var form = Ext.getCmp('winFormDataStaff').getForm();
                                    form.submit({
                                        url: m_api + '/basic_staff_sta/image_staff',
                                        clientValidation: false,
                                        waitMsg: 'Sending Photo...',
                                        success: function (fp, o) {
                                            Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/staff/' + o.result.file);
                                            Ext.getCmp('Photo_old').setValue(o.result.file);
                                        }
                                    });
                                }
                            }
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Address'),
                            labelWidth: 130,
                            id: 'Address',
                            name: 'Address'
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Province'),
                            store: cmb_propinsi,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'ProvinceID',
                            name: 'ProvinceID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    cmb_kabupaten.load();
                                    //Ext.getCmp('DistrictID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('District'),
                            store: cmb_kabupaten,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'DistrictID',
                            name: 'DistrictID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    cmb_kecamatan.load();
                                    //Ext.getCmp('SubDistrictID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Sub District'),
                            store: cmb_kecamatan,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'SubDistrictID',
                            name: 'SubDistrictID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    cmb_desa.load();
                                    //Ext.getCmp('VillageID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Village'),
                            store: cmb_desa,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'VillageID',
                            name: 'VillageID',
                            queryMode: 'local'
                        }]
                    }]
                }]
            },{
                xtype:'panel',
                title: 'B. '+lang('Staff Data'),
                frame:true,
                bodyStyle:{"background-color":"#F0F0F0"},
                style:'background-color:#F0F0F0;',
                padding:2,
                items:[{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.45,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'textfield',
                            fieldLabel: lang('Staff Number'),
                            labelWidth: 150,
                            allowBlank: false,
                            id: 'StaffRegisteredNumber',
                            name: 'StaffRegisteredNumber'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Old Staff ID'),
                            labelWidth: 150,
                            id: 'OldStaffID',
                            name: 'OldStaffID',
                            hidden: true
                        },{
                            fieldLabel: lang('Is Farmer'),
                            xtype: 'radiogroup',
                            allowBlank: false,
                            width: '100%',
                            columns: 2,
                            items: [{
                                boxLabel: lang('Yes'),
                                name: 'isFarmer',
                                inputValue: '1',
                                id: 'isFarmer1',
                                listeners:{
                                    change: function(){
                                        /*Ext.getCmp('winFormStaff').scrollBy(0,500,false);*/
                                        return false;
                                    }
                                }
                            }, {
                                boxLabel: lang('No'),
                                name: 'isFarmer',
                                inputValue: '2',
                                id: 'isFarmer2',
                                listeners:{
                                    change: function(){
                                        /*
                                        if(this.checked == true){
                                            Ext.getCmp('FarmerProvinceID').hide();
                                            Ext.getCmp('FarmerDistrictID').hide();
                                            Ext.getCmp('FarmerCpgID').hide();
                                            Ext.getCmp('FarmerID').hide();
                                        }else{
                                            Ext.getCmp('FarmerProvinceID').show();
                                            Ext.getCmp('FarmerDistrictID').show();
                                            Ext.getCmp('FarmerCpgID').show();
                                            Ext.getCmp('FarmerID').show();
                                        }

                                        Ext.getCmp('winFormStaff').scrollBy(0,500,false);
                                        */
                                        return false;
                                    }
                                }
                            }]
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Farmer Province'),
                            labelWidth: 150,
                            hidden:true,
                            store: cmb_propinsi,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'FarmerProvinceID',
                            name: 'FarmerProvinceID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    cmb_farmer_district.load();
                                    //Ext.getCmp('FarmerDistrictID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Farmer District'),
                            hidden:true,
                            labelWidth: 150,
                            store: cmb_farmer_district,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'FarmerDistrictID',
                            name: 'FarmerDistrictID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    cmb_farmer_cpg.load();
                                    //Ext.getCmp('FarmerCpgID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Farmer CPG'),
                            hidden:true,
                            labelWidth: 150,
                            store: cmb_farmer_cpg,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'FarmerCpgID',
                            name: 'FarmerCpgID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    cmb_farmer.load();
                                    //Ext.getCmp('FarmerID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Farmer'),
                            hidden:true,
                            labelWidth: 150,
                            store: cmb_farmer,
                            anyMatch: true,
                            typeAhead: true,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'FarmerID',
                            name: 'FarmerID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Work Area Province'),
                            labelWidth: 150,
                            store: cmb_propinsi,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'WorkAreaProvinceID',
                            name: 'WorkAreaProvinceID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    cmb_workarea.load();
                                    //Ext.getCmp('WorkAreaID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Work Area'),
                            labelWidth: 150,
                            store: cmb_workarea,
                            allowBlank: false,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'WorkAreaID',
                            name: 'WorkAreaID',
                            queryMode: 'local'
                        }]
                    },{
                        columnWidth: 0.05,
                        padding: 4,
                        layout:'form',
                        items:[{}]
                    },{
                        columnWidth: 0.5,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'textfield',
                            fieldLabel: lang('Private Cell Phone'),
                            labelWidth: 150,
                            id: 'PrivateCellPhone',
                            name: 'PrivateCellPhone',
                            hidden: true
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Official Cell Phone'),
                            labelWidth: 150,
                            allowBlank: false,
                            id: 'OfficialCellPhone',
                            name: 'OfficialCellPhone'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Private Email'),
                            labelWidth: 150,
                            vtype: 'email',
                            id: 'PrivateEmail',
                            name: 'PrivateEmail',
                            hidden: true
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Official Email'),
                            labelWidth: 150,
                            vtype: 'email',
                            allowBlank: false,
                            id: 'OfficialEmail',
                            name: 'OfficialEmail'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Cc Email'),
                            labelWidth: 150,
                            id: 'CcEmail',
                            name: 'CcEmail',
                            emptyText: 'Comma separated value'
                        },{
                            fieldLabel: lang('Work Period'),
                            xtype: 'radiogroup',
                            allowBlank: false,
                            msgTarget: 'side',
                            width: '100%',
                            columns: 2,
                            items: [{
                                boxLabel: lang('Full-time'),
                                name: 'WorkPeriod',
                                inputValue: '1',
                                id: 'WorkPeriod1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }, {
                                boxLabel: lang('Part-time'),
                                name: 'WorkPeriod',
                                inputValue: '2',
                                id: 'WorkPeriod2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Status'),
                            allowBlank: false,
                            store: cmb_status,
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'id',
                            id: 'StatusCode',
                            name: 'StatusCode'
                        }]
                    }]
                },{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.6,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'combobox',
                            fieldLabel: lang('Role'),
                            labelWidth: 150,
                            allowBlank: false,
                            store: cmb_objtype,
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'id',
                            id: 'ObjType',
                            name: 'ObjType',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    //cmb_workarea.load();
                                    //Ext.getCmp('WorkAreaID').setValue('');
                                    switch (nv) {
                                        case 'extension':
                                        case 'private':
                                        case 'program':
                                        case 'service':
                                        case 'mill':
                                            Ext.getCmp('RoleProvinceID').setDisabled(true);
                                            Ext.getCmp('RoleDistrictID').setDisabled(true);

                                            cmb_role_obj_id.load();
                                        break;

                                        case 'bank':
                                        case 'cooperative':
                                        case 'farmergroup':
                                        case 'sce':
                                        case 'trader':
                                        case 'warehouse':
                                        case 'agent':
                                            Ext.getCmp('RoleProvinceID').setDisabled(false);
                                            Ext.getCmp('RoleDistrictID').setDisabled(false);
                                            cmb_role_obj_id.load();
                                            //Ext.getCmp('RoleProvinceID').setValue('');
                                        break;
                                    }

                                    cmb_position.load();
                                    //Ext.getCmp('ObjID').setValue('');
                                    //Ext.getCmp('PositionID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Province'),
                            store: cmb_propinsi,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'RoleProvinceID',
                            name: 'RoleProvinceID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    cmb_role_district.load();
                                    //Ext.getCmp('RoleDistrictID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('District'),
                            store: cmb_role_district,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'RoleDistrictID',
                            name: 'RoleDistrictID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    cmb_role_obj_id.load();
                                    //Ext.getCmp('ObjID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Object ID'),
                            store: cmb_role_obj_id,
                            allowBlank:false,
                            anyMatch: true,
                            typeAhead: true,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'ObjID',
                            name: 'ObjID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Position'),
                            store: cmb_position,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'PositionID',
                            name: 'PositionID',
                            queryMode: 'local',
                            allowBlank: false
                        }]
                    },{
                        columnWidth: 0.4,
                        padding: 4,
                        layout:'form',
                        items:[{}]
                    }]
                }]
            },{
                xtype:'panel',
                title: 'C. '+lang('User Information'),
                frame:true,
                hidden: !viewOnly,
                bodyStyle:{"background-color":"#F0F0F0"},
                style:'background-color:#F0F0F0;margin-top:15px;',
                padding:2,
                items:[{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 1,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'textfield',
                            fieldLabel: lang('Username'),
                            labelWidth: 150,
                            readOnly: true,
                            id: 'userInfoUsername',
                            name: 'userInfoUsername'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('User Status'),
                            labelWidth: 150,
                            readOnly: true,
                            id: 'userInfoStatus',
                            name: 'userInfoStatus'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Default Group'),
                            labelWidth: 150,
                            readOnly: true,
                            id: 'userInfoDefaultGroup',
                            name: 'userInfoDefaultGroup'
                        },{
                            layout: 'fit',
                            items:[{
                                xtype:'grid',
                                store: store_view_group_user,
                                width: '98%',
                                id: 'grid_view_group_user',
                                style: 'border:1px solid #CCC;margin-top:12px;',
                                loadMask: true,
                                title:lang('Group Access'),
                                selType: 'rowmodel',
                                columns: [{
                                    text: lang('No'),
                                    xtype: 'rownumberer',
                                    width: '5%'
                                },{
                                    text: lang('Name'),
                                    width: '30%',
                                    dataIndex: 'nama'
                                },{
                                    text: lang('Description'),
                                    width: '40%',
                                    dataIndex: 'deksripsi'
                                },{
                                    text: lang('Unit'),
                                    width: '24%',
                                    dataIndex: 'unit'
                                }]
                            }]
                        },{
                            layout: 'fit',
                            items:[{
                                xtype:'grid',
                                store: store_view_district_access,
                                width: '98%',
                                id: 'grid_view_district_user',
                                style: 'border:1px solid #CCC;margin-top:12px;',
                                loadMask: true,
                                title:lang('District Access'),
                                selType: 'rowmodel',
                                columns: [{
                                    text: lang('No'),
                                    xtype: 'rownumberer',
                                    width: '5%'
                                },{
                                    text: lang('Province'),
                                    width: '45%',
                                    dataIndex: 'provinsi'
                                },{
                                    text: lang('District'),
                                    width: '49%',
                                    dataIndex: 'kabupaten'
                                }]
                            }]
                        }]
                    }]
                }]
            }]
        }],
        buttons:[{
            id: 'saveButton',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            hidden:viewOnly,
            handler: function () {
                var form = Ext.getCmp('winFormDataStaff').getForm();

                //prep validation =============================== (begin)
                if(Ext.getCmp('isFarmer1').value == false){
                    Ext.getCmp('FarmerID').allowBlank = true;
                }else{
                    Ext.getCmp('FarmerID').allowBlank = false;
                }
                //prep validation =============================== (end)

                if (form.isValid()) {
                    form.submit({
                        url: m_api + '/basic_staff_sta/staff',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved');
                            winFormStaff.close();
                            store.load();
                        },
                        failure: function(fp, o){
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: 'Failed to save data',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Please fill the required field',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            }
        },{
            text: lang('Close'),
            margin: '5px',
            id: 'winBtnClose',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winFormStaff.close();
            }
        }]
    });

    if(displayMethod == 'add'){
        Ext.getCmp('isFarmer2').setValue(true); //is farmer = no
        Ext.getCmp('RoleProvinceID').setDisabled(true);
        Ext.getCmp('RoleDistrictID').setDisabled(true);
    } else if(displayMethod == 'update') {
        console.log(StaffID);

        Ext.getCmp('winFormDataStaff').getForm().load({
            url: m_api + '/basic_staff_sta/form_staff',
            method: 'GET',
            params: {
                StaffID: StaffID
            },
            success: function(form, action) {
                var r = Ext.decode(action.response.responseText);

                //photo===========================================
                if(r.data.Photo_old != ""){
                    var fotoUser = m_api_base_url + '/images/staff/' + r.data.Photo_old;
                    checkImageExists(fotoUser, function(existsImage) {
                        if (existsImage == true) {
                            Ext.getCmp('iphoto').setSrc(fotoUser);
                        } else {
                            Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                        }
                    });
                }

                //region=================================================
                setTimeout(function() {
                    if(r.data.ProvinceID == "") Ext.getCmp('ProvinceID').setValue(null);
                    if(r.data.DistrictID == "0") Ext.getCmp('DistrictID').setValue(null);
                    if(r.data.SubDistrictID == "") Ext.getCmp('SubDistrictID').setValue(null);
                    if(r.data.VillageID == "") Ext.getCmp('VillageID').setValue(null);
                }, 1000);

                //Is Farmer=============================================
                if(r.data.isFarmerValue == "1"){ //farmer
                    Ext.getCmp('isFarmer1').setValue(true);
                    Ext.getCmp('FarmerProvinceID').show();
                    Ext.getCmp('FarmerDistrictID').show();
                    Ext.getCmp('FarmerCpgID').show();
                    Ext.getCmp('FarmerID').show();
                } else if(r.data.isFarmerValue == "2"){ //bukan farmer
                    Ext.getCmp('isFarmer2').setValue(true);
                }

                //Role (begin) ============================================================
                Ext.getCmp('ObjType').setValue(r.data.ObjTypeValue);
                switch (r.data.ObjTypeValue) {
                    case 'bank':
                    case 'cooperative':
                    case 'farmergroup':
                    case 'sce':
                    case 'trader':
                    case 'warehouse':
                    case 'agent':
                        Ext.getCmp('RoleProvinceID').setValue(r.data.RoleProvinceID);
                        Ext.getCmp('RoleDistrictID').setValue(r.data.RoleDistrictID);
                    break;
                }
                Ext.getCmp('ObjID').setValue(r.data.ObjIDValue);
                //Role (end) ============================================================

                //focus keatas
                Ext.getCmp('winFormStaff').scrollBy(0,-500,false);

                Ext.getCmp('ObjType').setReadOnly(true);
                Ext.getCmp('PositionID').setReadOnly(true);

                //tab user information ================================= (begin)
                if(viewOnly == true){
                    store_view_group_user.load();
                    store_view_district_access.load();
                }
                //tab user information ================================= (end)
            },
            failure: function(form, action) {
                Ext.MessageBox.show({
                    title: 'Failed',
                    msg: 'Failed to retrieve data',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });
            }
        });

    }

    //show windows
    if (!winFormStaff.isVisible()) {
        winFormStaff.show();
    } else {
        winFormStaff.close();
    }
}

function displayFormUser(StaffID,store){

    var cmb_langs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/system/lang_list',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var user_groups = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['GroupId', 'GroupName'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/system/grouplist',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var user_projects = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['ProjID', 'ProjLabel'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/user_project',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var selected_groups = Ext.create('Ext.data.ArrayStore', {
        fields: ['GroupId', 'GroupName'],
        autoLoad: false
    });

    var selected_projects = Ext.create('Ext.data.ArrayStore', {
        fields: ['ProjID', 'ProjLabel'],
        autoLoad: false
    });

    var access_staffs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/farmer/access_staffs',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var access_role = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/access_role',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var selected_access_object = Ext.create('Ext.data.ArrayStore', {
        fields: ['id', 'label'],
        autoLoad: false
    });

    var winFormUser = Ext.create('widget.window', {
        title: lang('Management User'),
        id: 'winFormUser',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '65%',
        height: '90%',
        overflowY: 'auto',
        bodyStyle:{"background-color":"#F0F0F0"},
        style:'background-color:#F0F0F0;',
        padding:6,
        scrollOffset: 20,
        items:[{
            xtype: 'form',
            id: 'winFormDataUser',
            padding:'5 20 0 8',
            items:[{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 0.45,
                    padding: 4,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'UserId',
                        name: 'UserId'
                    },{
                        xtype: 'hiddenfield',
                        id: 'UserExtId',
                        name: 'UserExtId'
                    },{
                        xtype: 'hiddenfield',
                        id: 'StaffID',
                        name: 'StaffID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'RoleId',
                        name: 'RoleId'
                    },{
                        xtype: 'textfield',
                        fieldLabel: lang('Staff Name'),
                        labelWidth: 150,
                        id: 'PersonNm',
                        name: 'PersonNm',
                        readOnly:true
                    },{
                        xtype: 'textfield',
                        fieldLabel: lang('Role'),
                        labelWidth: 150,
                        id: 'RoleLabel',
                        name: 'RoleLabel',
                        readOnly:true
                    },{
                        fieldLabel: lang('Status'),
                        xtype: 'radiogroup',
                        allowBlank: false,
                        msgTarget: 'side',
                        width: '100%',
                        columns: 2,
                        items: [{
                            boxLabel: lang('Active'),
                            name: 'StatusCode',
                            inputValue: 'active',
                            id: 'StatusCode_active',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }, {
                            boxLabel: lang('Inactive'),
                            name: 'StatusCode',
                            inputValue: 'inactive',
                            id: 'StatusCode_inactive',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    }]
                },{
                    columnWidth: 0.05,
                    padding: 4,
                    layout:'form',
                    items:[{}]
                },{
                    columnWidth: 0.5,
                    padding: 4,
                    layout:'form',
                    items:[{
                        xtype: 'textfield',
                        fieldLabel: lang('User Name'),
                        labelWidth: 200,
                        allowBlank: false,
                        id: 'UserName',
                        name: 'UserName'
                    },{
                        xtype: 'textfield',
                        labelWidth: 200,
                        inputType: 'password',
                        fieldLabel: lang('Password'),
                        id: 'UserPassword',
                        name: 'UserPassword',
                        validator: function(value){
                            if (!Ext.getCmp('UserId').getValue() && value === '') {
                                return lang('Please input password');
                            }
                            return true;
                        }
                    },{
                        xtype: 'textfield',
                        labelWidth: 200,
                        inputType: 'password',
                        fieldLabel: lang('Re Type Password'),
                        id: 'UserPasswordRe',
                        name: 'UserPasswordRe',
                        validator: function(value){
                            if (Ext.getCmp('UserPassword').getValue() !== value) {
                                return lang('Password confirmation doesn\'t match');
                            }
                            return true;
                        }
                    },{
                        xtype: 'combobox',
                        labelWidth: 200,
                        fieldLabel: lang('Interface Language'),
                        store: cmb_langs,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        allowBlank: false,
                        id: 'UserLanguage',
                        name: 'UserLanguage'
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    margin:'5 5 10 5',
                    padding:3,
                    layout:{
                        type:'vbox',
                        align:'stretch'
                    },
                    items:[{
                        xtype: 'itemselector',
                        flex:true,
                        id: 'GroupIds',
                        name: 'GroupIds',
                        fieldLabel: lang('Group'),
                        fromTitle: lang('Available'),
                        toTitle: lang('Selected'),
                        anchor: '100%',
                        height:350,
                        store: user_groups,
                        displayField: 'GroupName',
                        valueField: 'GroupId',
                        value: [],
                        allowBlank: false,
                        msgTarget: 'side',
                        hidden:true,
                        listeners: {
                            change: function() {
                                set_selected_groups();
                            }
                        }
                    },{
                        html:'<br />'
                    },{
                        id: 'UserGroupIsDefault',
                        name: 'UserGroupIsDefault',
                        xtype: 'combobox',
                        allowBlank:false,
                        fieldLabel: lang('Default Group'),
                        hidden:true,
                        store: selected_groups,
                        displayField: 'GroupName',
                        valueField: 'GroupId',
                        queryMode:'local'
                    },{
                        html:'<br />'
                    },{
                        xtype: 'itemselector',
                        flex:true,
                        id: 'AccessStaff',
                        name: 'AccessStaff',
                        allowBlank:false,
                        msgTarget: 'side',
                        fieldLabel: lang('Access Area'),
                        fromTitle: lang('Available'),
                        toTitle: lang('Selected'),
                        anchor: '100%',
                        height:320,
                        store: access_staffs,
                        displayField: 'name',
                        valueField: 'id',
                        value: []
                    },{
                        html:'<br /><hr style="border-bottom:0.5px dashed gray;"><br />'
                    },{
                        xtype: 'itemselector',
                        flex:true,
                        id: 'ProjIDs',
                        name: 'ProjIDs',
                        fieldLabel: lang('Project'),
                        fromTitle: lang('Available'),
                        toTitle: lang('Selected'),
                        anchor: '100%',
                        height:350,
                        store: user_projects,
                        displayField: 'ProjLabel',
                        valueField: 'ProjID',
                        value: [],
                        allowBlank: false,
                        hidden:true,
                        msgTarget: 'side',
                        listeners: {
                            change: function() {
                                set_selected_projects();
                            }
                        }
                    },{
                        id: 'UserProjectIsDefault',
                        name: 'UserProjectIsDefault',
                        xtype: 'combobox',
                        allowBlank:false,
                        fieldLabel: lang('Default Project'),
                        hidden:true,
                        store: selected_projects,
                        displayField: 'ProjLabel',
                        valueField: 'ProjID',
                        queryMode:'local'
                    },{
                        xtype: 'itemselector',
                        flex:true,
                        hidden: true,
                        id: 'AccessRoleId',
                        name: 'AccessRoleId',
                        //allowBlank:false,
                        //msgTarget: 'side',
                        fieldLabel: lang('Access Role'),
                        fromTitle: lang('Available'),
                        toTitle: lang('Selected'),
                        anchor: '100%',
                        height:250,
                        store: access_role,
                        displayField: 'name',
                        valueField: 'id',
                        value: [],
                        listeners: {
                            change: function() {
                                set_selector_access_object(Ext.getCmp('UserId').getValue());
                            }
                        }
                    },{
                        html:'<br />'
                    },{
                        xtype: 'itemselector',
                        flex:true,
                        hidden: true,
                        id: 'AccessObjId',
                        name: 'AccessObjId',
                        //allowBlank:false,
                        //msgTarget: 'side',
                        fieldLabel: lang('Access Object List'),
                        fromTitle: lang('Available'),
                        toTitle: lang('Selected'),
                        anchor: '100%',
                        height:500,
                        store: selected_access_object,
                        displayField: 'label',
                        valueField: 'id',
                        value: []
                    }]
                }]
            }]
        }],
        buttons:[{
            id: 'saveButton',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = Ext.getCmp('winFormDataUser').getForm();

                if (form.isValid()) {
                    form.submit({
                        url: m_api + '/basic_staff_sta/user',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved');
                            winFormUser.close();
                            store.load();
                        },
                        failure: function(fp, o){
                            var jsonResp = o.result;
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: jsonResp.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Form is not complete yet',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            }
        },{
            text: lang('Delete'),
            margin: '5px',
            id: 'winBtnDelete',
            scale: 'large',
            ui: 's-button',
            cls: 's-red',
            disabled: false,
            hidden:true,
            handler: function() {
                //hapus data staff (begin)===============
                Ext.MessageBox.confirm('Message', lang('Are you sure want to delete this data ?')+'<br />'+lang('Data will be lost permanently') , function(btn){
                    if(btn == 'yes'){
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_api + '/basic_staff_sta/user',
                            method : 'DELETE',
                            params: {UserId:  Ext.getCmp('UserId').getValue(),StaffID: Ext.getCmp('StaffID').getValue()},
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        winFormUser.close();
                                        store.load();
                                    break;
                                    default:
                                        Ext.MessageBox.show({
                                            title: 'Failed',
                                            msg: obj.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    break;
                                }
                            },
                            failure: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: obj.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        })
                    }
                });
            }
        },{
            text: lang('Close'),
            margin: '5px',
            id: 'winBtnClose',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winFormUser.close();
            }
        }]
    });

    //isikan form
    Ext.getCmp('winFormDataUser').getForm().load({
        url: m_api + '/basic_staff_sta/form_user',
        method: 'GET',
        params: {
            StaffID: StaffID
        },
        success: function(form, action) {
            var r = Ext.decode(action.response.responseText);
            user_groups.load({
                params: {
                    RoleId: r.data.RoleId
                },
                callback: function(records, operation, success) {
                    Ext.getCmp('GroupIds').reset();
                    Ext.getCmp('UserGroupIsDefault').reset();
                    selected_groups.removeAll();
                }
            });

            setTimeout(function() {
                //groups
                Ext.getCmp('GroupIds').setValue(r.data.groups);
                set_selected_groups();
                Ext.getCmp('UserGroupIsDefault').setValue(r.data.UserGroupIsDefault);

                //projects
                Ext.getCmp('ProjIDs').setValue(r.data.projects);
                set_selected_projects();
                Ext.getCmp('UserProjectIsDefault').setValue(r.data.UserProjectIsDefault);
            }, 1500);

            Ext.getCmp('AccessStaff').setValue(r.data.access);

            Ext.getCmp('AccessRoleId').setValue(r.data.accessRoleId);
            setTimeout(function() {
                set_selector_access_object(r.data.UserId);
            }, 1500);

            //btn delete
            if(r.data.UserId == null){
                Ext.getCmp('winBtnDelete').setVisible(false);
            }else{
                Ext.getCmp('winBtnDelete').setVisible(false);
            }
        },
        failure: function(form, action) {
            Ext.MessageBox.show({
                title: 'Failed',
                msg: 'Failed to retrieve data',
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-error'
            });
        }
    });

    //show windows
    if (!winFormUser.isVisible()) {
        winFormUser.show();
    } else {
        winFormUser.close();
    }

    function set_selected_projects(){
        var itemSelectorField   = Ext.getCmp('ProjIDs');
        var fieldList           = itemSelectorField.toField.store.getRange();
        var value = Ext.getCmp('UserProjectIsDefault').getValue();
        var exist = false;

        selected_projects.removeAll();

        $.each(fieldList, function(index, val) {
            if (value == val.data.ProjID) {
                exist = true;
            }
            selected_projects.add({
                ProjID: val.data.ProjID,
                ProjLabel: val.data.ProjLabel,
            });
        });

        if (!exist) {
            Ext.getCmp('UserProjectIsDefault').setValue('');
        }
    }

    function set_selected_groups () {
        var itemSelectorField   = Ext.getCmp('GroupIds');
        var fieldList           = itemSelectorField.toField.store.getRange();
        var value = Ext.getCmp('UserGroupIsDefault').getValue();
        var exist = false;
        selected_groups.removeAll();
        $.each(fieldList, function(index, val) {
            if (value == val.data.GroupId) {
                exist = true;
            }
            selected_groups.add({
                GroupId: val.data.GroupId,
                GroupName: val.data.GroupName,
            });
        });
        if (!exist) {
            Ext.getCmp('UserGroupIsDefault').setValue('');
        }
    }

    function set_selector_access_object(UserId = null) {
        var itemSelectorAccessRole = Ext.getCmp('AccessRoleId');
        var varSetValueObjList = [];
        var fieldListAccessRole = itemSelectorAccessRole.toField.store.getRange();
        selected_access_object.removeAll();

        if(fieldListAccessRole.length > 0){
            var p = Ext.MessageBox.show({
                title: 'Please wait',
                msg: 'Fetching Object List...',
                closable: true
            });

            $.each(fieldListAccessRole, function(index, val) {
                //console.log(val.data.id);
                Ext.Ajax.request({
                    url: m_api + '/basic_staff_sta/access_object_list',
                    method: 'POST',
                    async: false,
                    timeout: 3600,
                    params: {
                        RoleId: val.data.id,
                        UserId: UserId
                    },
                    success: function(response) {
                        var text = response.responseText;
                        var objReturn = Ext.JSON.decode(text);
                        //console.log(objReturn);

                        $.each(objReturn.data, function(index, val) {
                            selected_access_object.add({
                                id: val.id,
                                label: val.label
                            });
                        });
                        Ext.getCmp('AccessObjId').bindStore(selected_access_object);

                        //array selected
                        varSetValueObjList = varSetValueObjList.concat(objReturn.dataSelected);
                    },
                    failure: function(){
                        p.close();
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Fetching Object List Failed',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                        return false;
                    }
                });

            });

            //set valuenya ketika sudah selesai looping
            Ext.getCmp('AccessObjId').setValue(varSetValueObjList);
            //console.log(varSetValueObjList);

            //tutup msg box waiting
            p.close();
            //console.log(selected_access_object);
        }else{
            //kosong pilihannya
            Ext.getCmp('AccessObjId').bindStore(selected_access_object);
        }
    }

}

function displayFormUserApp(StaffID,store){

    var cmb_app_role = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/app_ref_role_cmb',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var cmb_app_group = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/app_ref_group_cmb',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    Ext.define('accessStaffGridModel.Model', {
        extend: 'Ext.data.Model',
        fields: ['District']
    });

    var store_app_access_staff = Ext.create('Ext.data.Store', {
        model: 'accessStaffGridModel.Model',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff_sta/list_access_staff_app',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.UserId = Ext.getCmp('UserId').getValue();
            }
        }
    });

    var winFormUserApp = Ext.create('widget.window', {
        title: lang('Management User App'),
        id: 'winFormUserApp',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '50%',
        height: '70%',
        overflowY: 'auto',
        bodyStyle:{"background-color":"#F0F0F0"},
        style:'background-color:#F0F0F0;',
        padding:6,
        scrollOffset: 20,
        items:[{
            xtype: 'form',
            id: 'winFormDataUserApp',
            padding:'5 20 0 8',
            items:[{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 0.45,
                    padding: 2,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'UserId',
                        name: 'UserId'
                    },{
                        xtype: 'hiddenfield',
                        id: 'StaffID',
                        name: 'StaffID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'UserExtId',
                        name: 'UserExtId'
                    },{
                        xtype: 'textfield',
                        fieldLabel: lang('User Name'),
                        labelWidth: 125,
                        id: 'UserName',
                        name: 'UserName',
                        readOnly:true
                    },{
                        xtype: 'textfield',
                        labelWidth: 125,
                        inputType: 'password',
                        fieldLabel: lang('Password'),
                        id: 'UserPassword',
                        name: 'UserPassword',
                        validator: function(value){
                            if (!Ext.getCmp('UserExtId').getValue() && value === '') {
                                return lang('Please input password');
                            }
                            return true;
                        }
                    },{
                        xtype: 'textfield',
                        labelWidth: 125,
                        inputType: 'password',
                        fieldLabel: lang('Re Type Password'),
                        id: 'UserPasswordRe',
                        name: 'UserPasswordRe',
                        validator: function(value){
                            if (Ext.getCmp('UserPassword').getValue() !== value) {
                                return lang('Password did not match');
                            }
                            return true;
                        }
                    }]
                },{
                    columnWidth: 0.05,
                    padding: 2,
                    layout:'form',
                    items:[{}]
                },{
                    columnWidth: 0.5,
                    padding: 2,
                    layout:'form',
                    hidden:true,
                    items:[/*{
                        xtype: 'combobox',
                        labelWidth: 125,
                        fieldLabel: lang('App Role'),
                        store: cmb_app_role,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        allowBlank: false,
                        id: 'AppRoleUid',
                        name: 'AppRoleUid'
                    },{
                        xtype: 'combobox',
                        labelWidth: 125,
                        fieldLabel: lang('App Group'),
                        store: cmb_app_group,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        allowBlank: false,
                        id: 'AppGroupUid',
                        name: 'AppGroupUid'
                    }*/]
                }]
            },{
                xtype: 'itemselector',
                flex:true,
                id: 'AppRoleUid',
                name: 'AppRoleUid',
                fieldLabel: lang('App Role'),
                labelWidth: 125,
                fromTitle: lang('Available'),
                toTitle: lang('Selected'),
                anchor: '100%',
                height:240,
                store: cmb_app_role,
                displayField: 'name',
                valueField: 'id'
            },{
                xtype: 'itemselector',
                flex:true,
                id: 'AppGroupUid',
                name: 'AppGroupUid',
                fieldLabel: lang('App Group'),
                labelWidth: 125,
                fromTitle: lang('Available'),
                toTitle: lang('Selected'),
                anchor: '100%',
                height:240,
                store: cmb_app_group,
                displayField: 'name',
                valueField: 'id'
            },{
                html:'<br />'
            },{
                xtype:'grid',
                title: lang('Access Area'),
                store: store_app_access_staff,
                height: 200,
                columns: [
                    { text: lang('District'),  dataIndex: 'District', width: '95%' }
                ]
            }]
        }],
        buttons:[{
            id: 'saveButton',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = Ext.getCmp('winFormDataUserApp').getForm();

                if (form.isValid()) {
                    if(Ext.getCmp('AppGroupUid').getValue()[0] == null || Ext.getCmp('AppRoleUid').getValue()[0] == null){
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('App Group and App Role is required'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }else{
                        form.submit({
                            url: m_api + '/basic_staff_sta/user_app',
                            method:'POST',
                            waitMsg: 'Saving data...',
                            success: function(fp, o) {
                                var jsonResp = o.result;
                                Ext.MessageBox.alert('Success', jsonResp.message);
                                winFormUserApp.close();
                                store.load();
                            },
                            failure: function(fp, o){
                                var jsonResp = o.result;
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: jsonResp.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    }
                }else{
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Form is not complete yet',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            }
        },{
            text: lang('Close'),
            margin: '5px',
            id: 'winBtnClose',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winFormUserApp.close();
            }
        }]
    });

    //isikan form
    Ext.getCmp('winFormDataUserApp').getForm().load({
        url: m_api + '/basic_staff_sta/form_user_app',
        method: 'GET',
        params: {
            StaffID: StaffID
        },
        success: function(form, action) {
            var r = Ext.decode(action.response.responseText);
            store_app_access_staff.load();
        },
        failure: function(form, action) {
            Ext.MessageBox.show({
                title: 'Failed',
                msg: 'Failed to retrieve data',
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-error'
            });
        }
    });

    //show windows
    if (!winFormUserApp.isVisible()) {
        winFormUserApp.show();
    } else {
        winFormUserApp.close();
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