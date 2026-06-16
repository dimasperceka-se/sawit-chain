/*
* @Author: nikolius
* @Date:   2017-11-13 10:44:18
* @Last Modified by:   nikolius
* @Last Modified time: 2017-12-13 13:39:27
*/

function displayWinFormImsStaff(IMSMasterID,CallerStore){

    /*============================================ Function & Other Var (Begin) ==================================================*/


    /*============================================ Function & Other Var (End)   ==================================================*/

    /*============================================ Store (Begin)   ==================================================*/
    var staff_type = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "program",
            "label": "Program Staff"
        }, {
            "id": "private",
            "label": "Private Staff"
        }, {
            "id": "extension",
            "label": "Extension Staff"
        }, {
            "id": "sce",
            "label": "SCE Staff"
        }, {
            "id": "trader",
            "label": "Trader Staff"
        }, {
            "id": "cooperative",
            "label": "Cooperative Staff"
        }, {
            "id": "warehouse",
            "label": "Warehouse Staff"
        }, {
            "id": "bank",
            "label": "Bank Staff"
        }, {
            "id": "farmergroup",
            "label": "Farmer Group Staff"
        }]
    });

    var staff_province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_crud + 'staff_province',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var work_area = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'work_area',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_staff_add = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['addStaffID', 'addStaffName', 'addStaffEmail', 'addStaffWorkArea', 'addGender'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'staff_master_add_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    /*============================================ Store (End)     ==================================================*/

    var winFormImsStaff = Ext.create('widget.window', {
        title: lang('Form IMS Staff Input'),
        id: 'imsCertWinFormImsStaff',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '60%',
        height: '90%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
            xtype:'panel',
            layout: 'column',
            padding: '5 20 5 8',
            border: false,
            items: [{
                columnWidth: 1,
                layout: 'form',
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'imsSAObjType',
                    name: 'imsSAObjType',
                    emptyText: lang('Type'),
                    store: staff_type,
                    allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                },{
                    xtype: 'combobox',
                    id: 'imsSAProvinceID',
                    name: 'imsSAProvinceID',
                    emptyText: lang('Province'),
                    store: staff_province,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('imsSAWorkAreaID').setValue('');
                            work_area.load({
                                params: {
                                    ProvinceID: Ext.getCmp('imsSAProvinceID').getValue()
                                }
                            });
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'imsSAWorkAreaID',
                    name: 'imsSAWorkAreaID',
                    emptyText: lang('Work Area'),
                    store: work_area,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                },{
                    xtype: 'gridpanel',
                    id: 'ims_grid_staff_add',
                    store: store_staff_add,
                    cls: 'Sfr_GridNew',
                    minHeight:300,
                    style: 'border:1px solid #CCC;',
                    loadMask: true,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            xtype: 'textfield',
                            name: 'imsSAkey',
                            baseCls:'Sfr_TxtfieldSearchGrid',
                            id: 'imsSAkey',
                            emptyText: lang('Keyword'),
                            width: 280
                        },{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                            cls:'Sfr_BtnGridBlue',
                            overCls:'Sfr_BtnGridBlue-Hover',
                            margin: '0px 0px 0px 6px',
                            text: lang('Search'),
                            handler: function() {
                                if(Ext.getCmp('imsSAObjType').getValue()=="" || Ext.getCmp('imsSAObjType').getValue()==undefined){
                                    Ext.MessageBox.alert('Warning', lang('Select Staff Type first!'));
                                }else{
                                    store_staff_add.load({
                                        params: {
                                            IMSMasterID: IMSMasterID,
                                            key: Ext.getCmp('imsSAkey').getValue(),
                                            ProvinceID : Ext.getCmp('imsSAProvinceID').getValue(),
                                            WorkAreaID : Ext.getCmp('imsSAWorkAreaID').getValue()
                                        }
                                    });

                                }
                            }
                        }]
                    }],
                    selType: 'checkboxmodel',
                    selModel: {
                        checkOnly: true,
                        mode: "MULTI",
                        headerWidth: '5%'
                    },
                    columns: [{
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '5%',
                    }, {
                        text: lang('ID'),
                        dataIndex: 'addStaffID',
                        hidden:true
                    }, {
                        text: lang('Name'),
                        dataIndex: 'addStaffName',
                        flex: 2
                    }, {
                        text: lang('Gender'),
                        dataIndex: lang('addGender'),
                        flex: 1
                    }, {
                        text: lang('Email'),
                        dataIndex: 'addStaffEmail',
                        flex: 2
                    }, {
                        text: lang('Work Area'),
                        dataIndex: 'addStaffWorkArea',
                        flex: 1
                    }]
                }]
            }]
        }],
        buttons: [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                margin: '5px',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    var staffs = '';
                    Ext.each(Ext.getCmp('ims_grid_staff_add').getSelectionModel().getSelection(), function (row, index, value) {
                        staffs = staffs + ',' + row.data.addStaffID;
                    });
                    if (staffs != '') {
                        Ext.Ajax.request({
                            url: m_crud + 'staff_master_add',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                IMSMasterID: IMSMasterID,
                                staffs: staffs
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        CallerStore.load();
                                        winFormImsStaff.close();
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert("Warning", "Please select staff");
                    }
                }
            }, {
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    winFormImsStaff.close();
                }
            }]
    });

    //show windows
    if (!winFormImsStaff.isVisible()) {
        winFormImsStaff.center();
        winFormImsStaff.show();
    } else {
        winFormImsStaff.close();
    }
}

/* =========================================================== */

function displayWinFormImsEventDetailStaff(IMSMasterID,IMSID,CallerStore){

    /*============================================ Function & Other Var (Begin) ==================================================*/

    /*============================================ Function & Other Var (End)   ==================================================*/

    /*============================================ Store (Begin) ==================================================*/
    var store_staff_ims_event_add = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSStaffID','StaffID', 'StaffName', 'StaffRoleType', 'Gender', 'Email', 'WorkAreaLabel'],
        autoLoad: true,
        pageSize: 20,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_event_grid_staff_input',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSMasterID = IMSMasterID;
                store.proxy.extraParams.IMSID = IMSID;
            }
        }
    });
    /*============================================ Store (End)   ==================================================*/

    var winFormImsEventStaff = Ext.create('widget.window', {
        title: lang('Form IMS Event Staff Input'),
        id: 'imsCertWinFormImsEventStaff',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '60%',
        height: '90%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'panel',
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: 1,
                        layout: 'form',
                        border: false,
                        items: [{
                                xtype: 'gridpanel',
                                id: 'ims_grid_staff_ims_event_add',
                                store: store_staff_ims_event_add,
                                cls: 'Sfr_GridNew',
                                style: 'border:1px solid #CCC;',
                                loadMask: true,
                                minHeight:300,
                                selType: 'checkboxmodel',
                                selModel: {
                                    checkOnly: true,
                                    mode: "MULTI",
                                    headerWidth: '5%'
                                },
                                viewConfig: {
                                    deferEmptyText: false,
                                    emptyText: lang('No data Available')
                                },
                                dockedItems: [{
                                        xtype: 'pagingtoolbar',
                                        store: store_staff_ims_event_add,
                                        dock: 'bottom',
                                        displayInfo: true
                                    }],
                                columns: [{
                                        text: 'No',
                                        xtype: 'rownumberer',
                                        align: 'center',
                                        width: '5%',
                                    }, {
                                        text: lang('ID'),
                                        dataIndex: 'IMSStaffID',
                                        hidden: true
                                    }, {
                                        dataIndex: 'StaffID',
                                        hidden: true
                                    }, {
                                        text: lang('Name'),
                                        dataIndex: 'StaffName',
                                        flex: 1
                                    }, {
                                        text: lang('Type'),
                                        dataIndex: 'StaffRoleType',
                                        width: '14%'
                                    }, {
                                        text: lang('Gender'),
                                        dataIndex: lang('Gender'),
                                        width: '10%'
                                    }, {
                                        text: lang('Email'),
                                        dataIndex: 'Email',
                                        flex: 1
                                    }, {
                                        text: lang('Work Area'),
                                        dataIndex: 'WorkAreaLabel',
                                        width: '20%'
                                    }]
                            }]
                    }]
            }],
        buttons: [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                margin: '5px',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    var staffs = '';
                    Ext.each(Ext.getCmp('ims_grid_staff_ims_event_add').getSelectionModel().getSelection(), function (row, index, value) {
                        staffs = staffs + ',' + row.data.StaffID;
                    });
                    if (staffs != '') {
                        Ext.Ajax.request({
                            url: m_crud + 'staff_ims_event_add',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                IMSMasterID: IMSMasterID,
                                IMSID: IMSID,
                                staffs: staffs
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        CallerStore.load();
                                        winFormImsEventStaff.close();
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert("Warning", "Please select staff");
                    }
                }
            }, {
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    winFormImsEventStaff.close();
                }
            }]
    });

    //show windows
    if (!winFormImsEventStaff.isVisible()) {
        winFormImsEventStaff.center();
        winFormImsEventStaff.show();
    } else {
        winFormImsEventStaff.close();
    }
}