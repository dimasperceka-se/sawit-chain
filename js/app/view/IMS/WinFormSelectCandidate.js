/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 17-03-2020
 *  File : WinFormSelectCandidate.js
 *******************************************/
/*
 Param2 yg diperlukan ketika load View ini
 - IMSID
 - CallerStore
 */

Ext.define('Koltiva.view.IMS.WinFormSelectCandidate', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormSelectCandidate',
    title: lang('List of Farmers'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '92%',
    height: 550,
    overflowY: 'auto',
    style: 'padding:2px;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        //Store ========================= (Begin)
        thisObj.CmbFilterProvince = Ext.create('Koltiva.store.ComboGeneral.ComboProvince');
        thisObj.CmbFilterDistrict = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict', {
            storeVar: {
                ProvinceID: null
            }
        });
        thisObj.CmbFilterSubDistrict = Ext.create('Koltiva.store.ComboGeneral.ComboSubDistrict', {
            storeVar: {
                DistrictID: null
            }
        });

        thisObj.StoreGridMain = Ext.create('Koltiva.store.IMS.GridFormFarmerCandidate', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID,
                TxtSearchLabel: null,
                CmbFilterProvince: null,
                CmbFilterDistrict: null,
                CmbFilterSubDistrict: null
            }
        });
        //Store ========================= (End)


        thisObj.items = [{
                xtype: 'grid',
                id: 'Koltiva.view.IMS.WinFormSelectCandidate-MainGrid',
                style: 'border:1px solid #CCC;',
                cls: 'Sfr_GridNew',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreGridMain,
                enableColumnHide: false,
                height: 450,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreGridMain,
                        dock: 'bottom',
                        displayInfo: true,
                        style: 'padding-right:12px;'
                    }, {
                        xtype: 'toolbar',
                        items: [{
                                name: 'Koltiva.view.IMS.WinFormSelectCandidate-TxtSearchLabel',
                                id: 'Koltiva.view.IMS.WinFormSelectCandidate-TxtSearchLabel',
                                xtype: 'textfield',
                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                width: 200,
                                emptyText: lang('Cari berdasar nama / id')
                            }, {
                                store: thisObj.CmbFilterProvince,
                                editable: false,
                                xtype: 'combobox',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterProvince',
                                name: 'Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterProvince',
                                style: 'margin-left:5px;margin-top:5px;',
                                emptyText: lang('All Province'),
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        Ext.getCmp('Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterDistrict').setValue(null);
                                        Ext.getCmp('Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterSubDistrict').setValue(null);

                                        thisObj.CmbFilterDistrict.load({
                                            params: {
                                                ProvinceID: nv
                                            }
                                        });
                                    }
                                }
                            }, {
                                store: thisObj.CmbFilterDistrict,
                                editable: false,
                                xtype: 'combobox',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterDistrict',
                                name: 'Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterDistrict',
                                style: 'margin-left:5px;margin-top:5px;',
                                emptyText: lang('All District'),
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        Ext.getCmp('Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterSubDistrict').setValue(null);

                                        thisObj.CmbFilterSubDistrict.load({
                                            params: {
                                                DistrictID: nv
                                            }
                                        });
                                    }
                                }
                            }, {
                                store: thisObj.CmbFilterSubDistrict,
                                editable: false,
                                xtype: 'combobox',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterSubDistrict',
                                name: 'Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterSubDistrict',
                                style: 'margin-left:5px;margin-top:5px;',
                                emptyText: lang('All SubDistrict'),
                                listeners: {
                                    change: function (cb, nv, ov) {
                                    }
                                }
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                text: lang('Search'),
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    thisObj.StoreGridMain.storeVar.TxtSearchLabel = Ext.getCmp('Koltiva.view.IMS.WinFormSelectCandidate-TxtSearchLabel').getValue();
                                    thisObj.StoreGridMain.storeVar.CmbFilterProvince = Ext.getCmp('Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterProvince').getValue();
                                    thisObj.StoreGridMain.storeVar.CmbFilterDistrict = Ext.getCmp('Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterDistrict').getValue();
                                    thisObj.StoreGridMain.storeVar.CmbFilterSubDistrict = Ext.getCmp('Koltiva.view.IMS.WinFormSelectCandidate-CmbFilterSubDistrict').getValue();
                                    thisObj.StoreGridMain.load();
                                }
                            }]
                    }],
                columns: [{
                        dataIndex: 'FarmerID',
                        hidden: true
                    }, {
                        text: 'No',
                        width: '5%',
                        xtype: 'rownumberer'
                    }, {
                        xtype: 'checkcolumn',
                        text: '&nbsp;',
                        dataIndex: 'chdata',
                        width: '5%'
                    }, {
                        text: lang('ID'),
                        dataIndex: 'FarmerID',
                        width: '10%'
                    }, {
                        text: lang('Name'),
                        dataIndex: 'FarmerName',
                        width: '20%'
                    }, {
                        text: lang('Gender'),
                        dataIndex: 'Gender',
                        width: '10%',
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '') {
                                switch (value) {
                                    case '1':
                                        RetVal = lang('Male');
                                        break;
                                    case '2':
                                        RetVal = lang('Female');
                                        break;
                                    default:
                                        RetVal = '-';
                                        break;
                                }
                            } else {
                                RetVal = '-';
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('Age'),
                        dataIndex: 'Age',
                        width: '10%'
                    }, {
                        text: lang('Region'),
                        dataIndex: 'Region',
                        width: '20%',
                        renderer: function (t, meta, record) {
                            var RetVal;
                            RetVal = '<span class="Sfr_GridColPlaces">' + record.data.Province + ', ' + record.data.District + '</span>';
                            return RetVal;
                        }
                    }, {
                        text: lang('Location'),
                        dataIndex: 'Location',
                        width: '19%',
                        renderer: function (t, meta, record) {
                            var RetVal, labelLocation;
                            if (record.data.SubDistrict == '-' && record.data.Village == '-')
                                labelLocation = '-';
                            if (record.data.SubDistrict != '-' && record.data.Village == '-')
                                labelLocation = record.data.SubDistrict;
                            if (record.data.SubDistrict != '-' && record.data.Village != '-')
                                labelLocation = record.data.SubDistrict + ', ' + record.data.Village;
                            RetVal = '<span class="Sfr_GridColPlaces">' + labelLocation + '</span>';
                            return RetVal;
                        }
                    }]
            }];

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                text: lang('Save'),
                handler: function () {
                    var records = thisObj.StoreGridMain.queryBy(function (record) {
                        return record.get('chdata') === true;
                    });
                    var farmerIds = [];
                    records.each(function (record) {
                        farmerIds.push(record.get('FarmerID'));
                    });

                    if (farmerIds.length > 0) {
                        Ext.Ajax.request({
                            url: m_api + '/ims/ims_detail_candidate',
                            method: 'POST',
                            params: {
                                Candidate: Ext.encode(farmerIds),
                                IMSID: thisObj.viewVar.IMSID
                            },
                            success: function (rp, o) {
                                var r = Ext.decode(rp.responseText);
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                thisObj.viewVar.CallerStore.load();
                                thisObj.close();
                            },
                            failure: function (rp, o) {
                                try {
                                    var r = Ext.decode(rp.responseText);
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                } catch (err) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: 'Connection Error',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            }
                        });
                    } else {
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Choose at Least one Farmer!'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-success'
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
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});