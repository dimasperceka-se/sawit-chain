/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 15 2018
 *  File : WinFormImsAssetFarmerCardAddFarmerRec.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini    
    - RcpID    
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec',
    title: lang('IMS - Farmer ID Card (Add Farmer)'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '76%',
    height: '86%',
    overflowY: 'auto',
    style:'padding:6px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //Store ================================== (Begin)
        thisObj.store_grid_farmer_rec_add_farmer = Ext.create('Koltiva.store.IMS.ImsAssetRcpGridFarmerCardFormFarmerRecAddFarmer',{
        	storeVar: {
                RcpID: thisObj.viewVar.RcpID
            }
        });

        thisObj.combo_search_cpg = Ext.create('Koltiva.store.IMS.CmbFarmerCardFilterCpgByRcpID',{
        	storeVar: {
                RcpID: thisObj.viewVar.RcpID
            }
        });
        //Store ================================== (End)

        //items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'gridpanel',
            title: lang('Farmer List'),
            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec-Form-GridFarmerList',
            style: 'border:1px solid #CCC;',
            store: thisObj.store_grid_farmer_rec_add_farmer,
            width: '100%',
            loadMask: true,
            selType: 'checkboxmodel',
            //minHeight:425,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No Data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.store_grid_farmer_rec_add_farmer,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                items: [{
                    name: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec-Form-GridFarmerList-SearchStringParam',
                    id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec-Form-GridFarmerList-SearchStringParam',
                    xtype: 'textfield',
                    baseCls:'Sfr_TxtfieldSearchGrid',
                    width: 200,
                    emptyText: lang('ID / Name')
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec-Form-GridFarmerList-SearchCpgParam',
                    name: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec-Form-GridFarmerList-SearchCpgParam',
                    store: thisObj.combo_search_cpg,
                    baseCls:'Sfr_TxtfieldSearchGrid',
                    width: 350,
                    emptyText: lang('Filter by Farmer Group'),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                    cls:'Sfr_BtnGridBlue',
                    overCls:'Sfr_BtnGridBlue-Hover',
                    margin: '0px 0px 0px 6px',
                    text: lang('Search'),
                    handler: function() {
                        thisObj.store_grid_farmer_rec_add_farmer.storeVar = {
                            RcpID: thisObj.viewVar.RcpID,
                            SearchStringParam: Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec-Form-GridFarmerList-SearchStringParam').getValue(),
                            SearchCpgParam: Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec-Form-GridFarmerList-SearchCpgParam').getValue()                            
                        };
                        thisObj.store_grid_farmer_rec_add_farmer.load();
                    }
                }]
            }],
            columns: [{
                HeaderCheckbox: true,
                dataIndex : 'CheckData',
                width:'5%'
            },{
                text: lang('Farmer ID'),
                width: '15%',
                dataIndex: 'FarmerID'
            },{
                text: lang('Name'),
                width: '33%',
                dataIndex: 'FarmerName'
            },{
                text: lang('Gender'),
                width: '10%',
                dataIndex: 'Gender'
            },{
                text: lang('Farmer Group'),
                width: '34%',
                dataIndex: 'FarmerGroup'
            }]
        }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (End)

        thisObj.buttons = [{
                text: lang('Add Farmer'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormGreen',
                overCls: 'Sfr_BtnFormGreen-Hover',
                id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec-Form-BtnSave',
                handler: function () {
                    var gridSelected = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec-Form-GridFarmerList').getSelectionModel().getSelection();

                    var IdSelectedArr = [];
                    for (var i = gridSelected.length - 1; i >= 0; i--) {
                        IdSelectedArr.push(gridSelected[i].get('FarmerID'));
                    }

                    if (IdSelectedArr.length > 0) {
                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/ims_asset_rcp/farmer_card_farmer_rec_add_farmer',
                            method: 'POST',
                            params: {
                                FarmerIDSel: Ext.encode(IdSelectedArr),
                                RcpID: thisObj.viewVar.RcpID
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

                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp').store_grid_farmer_rec.load();
                                thisObj.store_grid_farmer_rec_add_farmer.load();
                            },
                            failure: function (rp, o) {
                                var r = Ext.decode(rp.responseText);

                                var pesanNya;
                                if (r.message != undefined) {
                                    pesanNya = r.message;
                                } else {
                                    pesanNya = lang('Connection error');
                                }
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: pesanNya,
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