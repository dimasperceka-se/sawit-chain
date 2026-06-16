/*
* @Author: nikolius
* @Date:   2017-12-13 10:48:15
* @Last Modified by:   nikolius
* @Last Modified time: 2017-12-13 13:50:33
*/

function displayWinFormBuyingUnitStaff(IMSMasterID,IMSID,CallerStore){

	/*============================================ Store (Begin)   ==================================================*/
	var buying_unit_type = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "Pedagang",
            "label": lang("Pedagang")
        }, {
            "id": "sce",
            "label": lang("SCE")
        }]
    });

	var province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'province',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

	var bu_district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'district',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_buying_unit_add = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['addBUSupplychainID', 'addBUOrgType', 'addBUName', 'addBUCompany', 'addBUDistrict'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'buying_unit_add_list',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

	/*============================================ Store (End)     ==================================================*/

    var winFormImsBuyingUnit = Ext.create('widget.window', {
        title: lang('Form IMS Buying Unit Input'),
        id: 'imsCertWinFormImsBuyingUnit',
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
                padding: '5 20 5 8',
                border: false,
                items: [{
                        columnWidth: 1,
                        layout: 'form',
                        border: false,
                        items: [{
                                xtype: 'combobox',
                                id: 'imsBUAOrgType',
                                name: 'imsBUAOrgType',
                                emptyText: lang('Type'),
                                store: buying_unit_type,
                                allowBlank: false,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            }]
                    }, {
                        columnWidth: 1,
                        layout: 'form',
                        border: false,
                        items: [{
                                xtype: 'combobox',
                                id: 'imsBUAProvinceID',
                                name: 'imsBUAProvinceID',
                                emptyText: lang('Province'),
                                store: province,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        Ext.getCmp('imsBUADistrictID').setValue('');
                                        bu_district.load({
                                            params: {
                                                ProvinceID: Ext.getCmp('imsBUAProvinceID').getValue()
                                            }
                                        });
                                    }
                                }
                            }]
                    }, {
                        columnWidth: 1,
                        layout: 'form',
                        border: false,
                        items: [{
                                xtype: 'combobox',
                                id: 'imsBUADistrictID',
                                name: 'imsBUADistrictID',
                                emptyText: lang('District'),
                                store: bu_district,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            }]
                    }, {
                        columnWidth: 1,
                        layout: 'form',
                        //padding: 5,
                        border: false,
                        items: [{
                                xtype: 'gridpanel',
                                id: 'ims_grid_buying_unit_add',
                                store: store_buying_unit_add,
                                cls: 'Sfr_GridNew',
                                style: 'border:1px solid #CCC;',
                                loadMask: true,
                                dockedItems: [{
                                        xtype: 'toolbar',
                                        items: [{
                                                xtype: 'textfield',
                                                name: 'imsBUAkey',
                                                id: 'imsBUAkey',
                                                emptyText: lang('Keyword'),
                                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                                width: 280,
                                                listeners: {}
                                            }, {
                                                xtype: 'button',
                                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                margin: '0px 0px 0px 6px',
                                                text: lang('Search'),
                                                cls: 'Sfr_BtnGridBlue',
                                                overCls: 'Sfr_BtnGridBlue-Hover',
                                                handler: function () {
                                                    if (Ext.getCmp('imsBUAOrgType').getValue() == "" || Ext.getCmp('imsBUAOrgType').getValue() == undefined) {
                                                        Ext.MessageBox.alert('Warning', lang('Select Buying Unit Type first.!'));
                                                    } else {
                                                        store_buying_unit_add.load({
                                                            params: {
                                                                IMSID: IMSID,
                                                                ObjType: Ext.getCmp('imsBUAOrgType').getValue(),
                                                                key: Ext.getCmp('imsBUAkey').getValue(),
                                                                ProvinceID: Ext.getCmp('imsBUAProvinceID').getValue(),
                                                                DistrictID: Ext.getCmp('imsBUADistrictID').getValue()
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
                                        width: '5%'
                                    }, {
                                        text: lang('ID'),
                                        dataIndex: 'addBUSupplychainID',
                                        hidden: true
                                    }, {
                                        text: lang('Name'),
                                        dataIndex: 'addBUName',
                                        width: '32%'
                                    }, {
                                        text: lang('Official / Company Name'),
                                        dataIndex: lang('addBUCompany'),
                                        width: '30%'
                                    }, {
                                        text: lang('District'),
                                        dataIndex: 'addBUDistrict',
                                        width: '25%'
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
                    var bunits = '';
                    Ext.each(Ext.getCmp('ims_grid_buying_unit_add').getSelectionModel().getSelection(), function (row, index, value) {
                        bunits = bunits + ',' + row.data.addBUSupplychainID;
                    });
                    if (bunits != '') {
                        Ext.Ajax.request({
                            url: m_crud + 'buying_unit_add',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                IMSMasterID: IMSMasterID,
                                IMSID: IMSID,
                                bunits: bunits
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        CallerStore.load();

                                        store_buying_unit_add.load({
                                            params: {
                                                IMSID: IMSID,
                                                ObjType: Ext.getCmp('imsBUAOrgType').getValue(),
                                                key: Ext.getCmp('imsBUAkey').getValue(),
                                                ProvinceID: Ext.getCmp('imsBUAProvinceID').getValue(),
                                                DistrictID: Ext.getCmp('imsBUADistrictID').getValue()
                                            }
                                        });
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert("Warning", "Please select Buying Unit");
                    }
                }
            }, {
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    winFormImsBuyingUnit.close();
                }
            }]
    });

	//show windows
    if (!winFormImsBuyingUnit.isVisible()) {
        winFormImsBuyingUnit.center();
        winFormImsBuyingUnit.show();
    } else {
        winFormImsBuyingUnit.close();
    }
}