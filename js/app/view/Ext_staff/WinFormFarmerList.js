/*
 Param2 yg diperlukan ketika load View ini
 - OpsiDisplay
 - Store yg panggil
 - SMESupplierID
 */

 Ext.define('Koltiva.view.Ext_staff.WinFormFarmerList', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Ext_staff.WinFormFarmerList',
    title: lang('Farmer Input'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '78%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        var storeFarmerAssignMemberAdd = Ext.create('Koltiva.store.Ext_staff.MemberGridAdd');

        var cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        cmb_province.load();

        var cmb_district = Ext.create('Koltiva.store.Grower.CmbDistrict');
        var cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        var cmb_village = Ext.create('Koltiva.store.Grower.CmbVillage');

        thisObj.items = [{
                xtype: 'grid',
                id: 'Koltiva.view.Ext_staff.WinFormFarmerList-GridInput',
                style: 'border:1px solid #CCC;margin:5px;',
                loadMask: true,
                selType: 'rowmodel',
                store: storeFarmerAssignMemberAdd,
                cls: 'Sfr_GridNew',
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: storeFarmerAssignMemberAdd,
                        dock: 'bottom',
                        displayInfo: true,
                        style: 'padding:4px 12px 4px 4px;'
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-textSearch',
                                xtype: 'textfield',
                                width: 300,
                                emptyText: lang('Cari berdasar nama/ID')
                            }, {
                                xtype: 'combobox',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-ProvinceID',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-ProvinceID',
                                store: cmb_province,
                                emptyText: lang('Province'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        cmb_district.load({
                                            params: {
                                                ProvinceID: nv
                                            }
                                        });
                                        Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-DistrictID').setValue('');
                                        Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-SubdistrictID').setValue('');
                                        Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-VillageID').setValue('');
                                    }
                                }
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-DistrictID',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-DistrictID',
                                store: cmb_district,
                                emptyText: lang('District'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        cmb_subdistrict.load({
                                            params: {
                                                DistrictID: nv
                                            }
                                        });
                                        Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-SubdistrictID').setValue('');
                                        Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-VillageID').setValue('');
                                    }
                                }
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-SubdistrictID',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-SubdistrictID',
                                store: cmb_subdistrict,
                                emptyText: lang('Subdistrict'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        cmb_village.load({
                                            params: {
                                                SubdistrictID: nv
                                            }
                                        });
                                        Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-VillageID').setValue('');
                                    }
                                }
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-VillageID',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-VillageID',
                                store: cmb_village,
                                emptyText: lang('Village'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                margin: '0px 0px 0px 6px',
                                text: lang('Search'),
                                cls: 'Sfr_BtnGridGreen',
                                overCls: 'Sfr_BtnGridGreen-Hover',
                                handler: function () {
                                    storeFarmerAssignMemberAdd.setStoreVar({
                                        SMEID: thisObj.viewVar.SMEID,
                                        StaffID:thisObj.viewVar.StaffID,
                                        StaffAssignmentID: thisObj.viewVar.StaffAssignmentID,
                                        textSearch: Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-textSearch').getValue(),
                                        ProvinceID: Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-ProvinceID').getValue(),
                                        DistrictID: Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-DistrictID').getValue(),
                                        SubdistrictID: Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-SubdistrictID').getValue(),
                                        VillageID: Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerList-GridInput-VillageID').getValue()
                                    });
                                    storeFarmerAssignMemberAdd.load();
                                }
                            }]
                    }],
                columns: [{
                        dataIndex: 'MemberID',
                        hidden: true
                    }, {
                        xtype: 'checkcolumn',
                        text: '&nbsp;',
                        dataIndex: 'chdata',
                    }, {
                        text: lang('Farmer ID'),
                        dataIndex: 'MemberDisplayID',
                        flex: 1,
                    }, {
                        text: lang('Farmer Name'),
                        dataIndex: 'FarmerName',
                        flex: 1,
                    }, {
                        text: lang('SubDistrict'),
                        dataIndex: 'SubDistrict',
                        flex: 1,
                    }, {
                        text: lang('Village'),
                        dataIndex: 'Village',
                        flex: 1,
                    }]
            }];

        thisObj.buttons = [{
                text: lang('Add Member'),
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                id: 'Koltiva.view.Ext_staff.WinFormFarmerList-Form-BtnSave',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    var records = storeFarmerAssignMemberAdd.queryBy(function (record) {
                        return record.get('chdata') === true;
                    });
                    var ids = [];
                    records.each(function (record) {
                        ids.push(record.get('MemberID'));
                    });

                    if (ids.length > 0) {
                        //insert kan ke tabel
                        Ext.Ajax.request({
                            url: m_api + '/ext_staff/member',
                            method: 'POST',
                            params: {
                                MemberID: Ext.encode(ids),
                                StaffAssignmentID: thisObj.viewVar.StaffAssignmentID,
                                StaffID:thisObj.viewVar.StaffID
                            },
                            success: function (response, o) {
                                var obj = Ext.decode(response.responseText);

                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: lang('Data saved'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                thisObj.viewVar.CallerStore.load();

                                thisObj.close();
                            },
                            failure: function (response, o) {
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: Ext.decode(response.responseText),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });

                    } else {
                        Ext.MessageBox.show({
                            title: 'Notifications',
                            msg: 'No item selected',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;

            //load store gridnya
            var store_grid = Ext.data.StoreManager.lookup('Koltiva.store.Ext_staff.MemberGridAdd');
            store_grid.setStoreVar(
                {
                    StaffAssignmentID: thisObj.viewVar.StaffAssignmentID,
                    StaffID: thisObj.viewVar.StaffID
                }
            );
            store_grid.load();
        }
    }
});