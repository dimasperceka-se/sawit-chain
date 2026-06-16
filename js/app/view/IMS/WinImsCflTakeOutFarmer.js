/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jan 28 2019
 *  File : WinImsCflTakeOutFarmer.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini    
    - IMSID 
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinImsCflTakeOutFarmer' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsCflTakeOutFarmer',
    title: lang('IMS - Take Out CFL Farmer'),
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

        thisObj.StoreCflFarmerListAddTakeOut = Ext.create('Koltiva.store.IMS.ImsCflTakeOutFarmerGrid',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        //items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'gridpanel',
            title: lang('CFL Farmer List'),
            id: 'Koltiva.view.IMS.WinImsCflTakeOutFarmer-Form-GridCflTakeOutList',
            style: 'border:1px solid #CCC;',
            store: thisObj.StoreCflFarmerListAddTakeOut,
            width: '100%',
            loadMask: true,
            selType: 'checkboxmodel',
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No Data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.StoreCflFarmerListAddTakeOut,
                dock: 'bottom',
                displayInfo: true,
                style:'padding-right:12px;'
            },{
                xtype: 'toolbar',
                items: [{
                    name: 'Koltiva.view.IMS.WinImsCflTakeOutFarmer-Form-GridCflTakeOutList-SearchStringParam',
                    id: 'Koltiva.view.IMS.WinImsCflTakeOutFarmer-Form-GridCflTakeOutList-SearchStringParam',
                    xtype: 'textfield',
                    baseCls: 'Sfr_TxtfieldSearchGrid',
                    width: 200,
                    emptyText: lang('ID / Name')
                },{
                    name: 'Koltiva.view.IMS.WinImsCflTakeOutFarmer-Form-GridCflTakeOutList-SearchCpgParam',
                    id: 'Koltiva.view.IMS.WinImsCflTakeOutFarmer-Form-GridCflTakeOutList-SearchCpgParam',
                    xtype: 'textfield',
                    baseCls: 'Sfr_TxtfieldSearchGrid',
                    width: 200,
                    emptyText: lang('ID / Name Farmer Group')
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                    margin: '0px 0px 0px 6px',
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    text: lang('Search'),
                    handler: function() {
                        thisObj.StoreCflFarmerListAddTakeOut.storeVar = {
                            IMSID: thisObj.viewVar.IMSID,
                            SearchStringParam: Ext.getCmp('Koltiva.view.IMS.WinImsCflTakeOutFarmer-Form-GridCflTakeOutList-SearchStringParam').getValue(),
                            SearchCpgParam: Ext.getCmp('Koltiva.view.IMS.WinImsCflTakeOutFarmer-Form-GridCflTakeOutList-SearchCpgParam').getValue()
                        };
                        thisObj.StoreCflFarmerListAddTakeOut.load();
                    }
                }]
            }],
            columns: [{
                HeaderCheckbox: true,
                dataIndex : 'CheckData',
                flex:0.5
            },{
                text: 'FarmerID',
                dataIndex: 'FarmerID',
                flex:1
            },{
                text: lang('Farmer Name'),
                dataIndex: 'FarmerName',
                flex:2
            },{
                text: lang('CPG'),
                dataIndex: 'FarmerGroup',
                flex:2
            },{
                text: lang('Location'),
                dataIndex: 'Village',
                flex:2
            },{
                text: lang('First Year of Certification'),
                dataIndex: 'CertFirstYear',
                flex:2
            },{
                text: lang('Internal Inspection Date'),
                dataIndex: 'ICSDate',
                flex:1
            },{
                text: lang('Estimated Harvest Present Year (Kg)'),
                dataIndex: 'CertNextHarvest',
                flex:1
            },{
                text: lang('Previous Year\'s Harvest (Kg)'),
                dataIndex: 'CertHarvest',
                flex:1
            },{
                text: lang('Sales Quota'),
                dataIndex: 'SalesQuota',
                flex:1
            },{
                text: lang('Total Certified Crop Area (Ha)'),
                dataIndex: 'CertHectare',
                flex:1
            },{
                text: lang('Nr of Cert Plots'),
                dataIndex: 'CertFarmNr',
                flex:1
            },{
                text: lang('Total Cocoa Farm'),
                dataIndex: 'TotalCocoaFarm',
                flex:1
            },{
                text: lang('Total Farm Area (Ha)'),
                dataIndex: 'TotalHa',
                flex:1
            }]
        }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (End)

        thisObj.buttons = [{
                text: lang('Take Out'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinImsCflTakeOutFarmer-Form-BtnSave',
                handler: function () {
                    var gridSelected = Ext.getCmp('Koltiva.view.IMS.WinImsCflTakeOutFarmer-Form-GridCflTakeOutList').getSelectionModel().getSelection();

                    var IdSelectedArr = [];
                    for (var i = gridSelected.length - 1; i >= 0; i--) {
                        IdSelectedArr.push(gridSelected[i].get('FarmerID'));
                    }

                    if (IdSelectedArr.length > 0) {
                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/ims/ims_cfl_takeout_farmer',
                            method: 'POST',
                            params: {
                                FarmerIDSel: Ext.encode(IdSelectedArr),
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
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];


        this.callParent(arguments);
    }
});