/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Jan 29 2019
 *  File : WinImsIcsReinspectionAddFarmer.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini    
    - IMSID 
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer',
    title: lang('IMS - ICS Reinspection Add Farmer'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '92%',
    height: '86%',
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.StoreImsIcsReinspectionAddFarmerGrid = Ext.create('Koltiva.store.IMS.ImsIcsReinspectionAddFarmerGrid',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        //items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
                xtype: 'gridpanel',
                id: 'Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer-Form-GridAddFarmer',
                style: 'border:1px solid #CCC;',
                store: thisObj.StoreImsIcsReinspectionAddFarmerGrid,
                width: '100%',
                loadMask: true,
                selType: 'checkboxmodel',
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No Data Available')
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreImsIcsReinspectionAddFarmerGrid,
                        dock: 'bottom',
                        displayInfo: true,
                        style: 'padding-right:12px;'
                    }, {
                        xtype: 'toolbar',
                        items: [{
                                name: 'Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer-Form-GridAddFarmer-SearchStringParam',
                                id: 'Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer-Form-GridAddFarmer-SearchStringParam',
                                xtype: 'textfield',
                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                width: 200,
                                emptyText: lang('ID / Name')
                            }, {
                                name: 'Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer-Form-GridAddFarmer-SearchCpgParam',
                                id: 'Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer-Form-GridAddFarmer-SearchCpgParam',
                                xtype: 'textfield',
                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                width: 200,
                                emptyText: lang('ID / Name Farmer Group')
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                margin: '0px 0px 0px 6px',
                                text: lang('Search'),
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    thisObj.StoreImsIcsReinspectionAddFarmerGrid.storeVar = {
                                        IMSID: thisObj.viewVar.IMSID,
                                        SearchStringParam: Ext.getCmp('Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer-Form-GridAddFarmer-SearchStringParam').getValue(),
                                        SearchCpgParam: Ext.getCmp('Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer-Form-GridAddFarmer-SearchCpgParam').getValue()
                                    };
                                    thisObj.StoreImsIcsReinspectionAddFarmerGrid.load();
                                }
                            }]
                    }],
                columns: [{
                        HeaderCheckbox: true,
                        dataIndex: 'CheckData',
                        flex: 0.5
                    }, {
                        text: 'FarmerID',
                        dataIndex: 'FarmerID',
                        flex: 1.5
                    }, {
                        text: lang('Farmer Name'),
                        flex: 3,
                        dataIndex: 'FarmerName'
                    }, {
                        text: lang('CPG'),
                        flex: 3,
                        dataIndex: 'FarmerGroup'
                    }, {
                        text: lang('Location'),
                        flex: 2,
                        dataIndex: 'Village'
                    }, {
                        text: lang('Status'),
                        flex: 1,
                        dataIndex: 'AFLStatus',
                        renderer: function (value) {
                            var RetVal;

                            switch (parseInt(value)) {
                                case 1:
                                    RetVal = 'Comply';
                                    break;
                                case 2:
                                    RetVal = 'Not Comply';
                                    break;
                                case 3:
                                    RetVal = 'Comply With Condition';
                                    break;
                                default:
                                    RetVal = '-';
                                    break;
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('Garden Nr'),
                        flex: 1,
                        dataIndex: 'CertGardenNr'
                    }, {
                        text: lang('Internal Inspection Date'),
                        flex: 2,
                        dataIndex: 'ICSDate'
                    }, {
                        text: lang('Estimated Harvest Present Year (Kg)'),
                        flex: 2,
                        dataIndex: 'CertNextHarvest'
                    }, {
                        text: lang('Previous Year\'s Harvest (Kg)'),
                        flex: 2,
                        dataIndex: 'CertHarvest'
                    }, {
                        text: lang('Total Certified Crop Area (Ha)'),
                        flex: 2,
                        dataIndex: 'CertHectare'
                    }]
            }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (End)

        thisObj.buttons = [{
                text: lang('Save'),
                margin: '5 15 5 5',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer-Form-BtnSave',
                handler: function () {
                    var gridSelected = Ext.getCmp('Koltiva.view.IMS.WinImsIcsReinspectionAddFarmer-Form-GridAddFarmer').getSelectionModel().getSelection();

                    var IdSelectedArr = [];
                    for (var i = gridSelected.length - 1; i >= 0; i--) {
                        IdSelectedArr.push(gridSelected[i].get('FarmerID') + '@' + gridSelected[i].get('CertGardenNr'));
                    }

                    if (IdSelectedArr.length > 0) {
                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/ims/ims_ics_reinspection_add_farmer',
                            method: 'POST',
                            params: {
                                FarmerGardenSel: Ext.encode(IdSelectedArr),
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

                                //Store load
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
                            title: 'Notifications',
                            msg: 'No item selected',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    }
});