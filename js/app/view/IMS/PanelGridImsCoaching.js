/* global Ext */

/******************************************
 *  Author : fikri
 *******************************************/

/*
 Param2 yg diperlukan ketika load View ini
 - UserID
 */

Ext.define('Koltiva.view.IMS.PanelGridImsCoaching', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.IMS.PanelGridImsCoaching',
    frame: false,
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.storeGridCoaching = Ext.create('Koltiva.store.IMS.GridIMSCoaching', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });
        
        thisObj.items = [{
            xtype: 'grid',
            minHeight:300,
            id: 'Koltiva.view.IMS.PanelGridImsCoaching-MainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls: 'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.storeGridCoaching,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.storeGridCoaching,
                dock: 'bottom',
                displayInfo: true,
                style: 'padding-right:12px;'
            }, {
                xtype: 'toolbar',
                items: [{
                    name: 'Koltiva.view.IMS.PanelGridImsCoaching-MainGrid-SearchStringParam',
                    id: 'Koltiva.view.IMS.PanelGridImsCoaching-MainGrid-SearchStringParam',
                    xtype: 'textfield',
                    baseCls:'Sfr_TxtfieldSearchGrid',
                    width: 200,
                    emptyText: lang('ID / Name'),
                    listeners: {
                        specialkey: function (f, e) {
                            if (e.getKey() == e.ENTER) {
                                thisObj.storeGridCoaching.storeVar.IMSID = thisObj.viewVar.IMSID;
                                thisObj.storeGridCoaching.storeVar.textSearch = Ext.getCmp('Koltiva.view.IMS.PanelGridImsCoaching-MainGrid-SearchStringParam').getValue();
                                thisObj.storeGridCoaching.load();
                            }
                        }

                    }
                }, {
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                    margin: '0px 0px 0px 6px',
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    text: lang('Search'),
                    handler: function () {
                        thisObj.storeGridCoaching.storeVar.IMSID = thisObj.viewVar.IMSID;
                        thisObj.storeGridCoaching.storeVar.textSearch = Ext.getCmp('Koltiva.view.IMS.PanelGridImsCoaching-MainGrid-SearchStringParam').getValue();
                        thisObj.storeGridCoaching.load();
                    }
                }]
            }],
            columns: [{
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colFarmeID',
                text: lang('Farmer ID'),
                flex: 1,
                dataIndex: 'FarmerID'
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colFarmerName',
                text: lang('Farmer Name'),
                dataIndex: 'FarmerName',
                flex: 3,
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colGender',
                text: lang('Gender'),
                dataIndex: 'Gender',
                flex: 1,
                align: 'center'
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colNCMajor',
                text: lang('NCMajor'),
                dataIndex: 'NCMajor',
                flex: 1,
                align: 'center'
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colNCMinor',
                text: lang('NCMinor'),
                dataIndex: 'NCMinor',
                flex: 1,
                align: 'center'
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colNCMajorAct',
                text: lang('NC Major Activity'),
                dataIndex: 'NCMajorAct',
                flex: 1,
                align: 'center'
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colNCMinorAtc',
                text: lang('NC Minor Activity'),
                dataIndex: 'NCMinorAct',
                flex: 1,
                align: 'center'
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colFarmerGroup',
                text: lang('Farmer Group'),
                dataIndex: 'FarmerGroup',
                flex: 3,
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colProvince',
                text: lang('Province'),
                dataIndex: 'Province',
                felx: 2
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colDistrict',
                text: lang('District'),
                dataIndex: 'District',
                felx: 2
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colSubdistrict',
                text: lang('Sub-District'),
                dataIndex: 'SubDistrict',
                felx: 2
            }, {
                id: 'Koltiva.view.IMS.PanelGridImsCoaching-colVillage',
                text: lang('Village'),
                dataIndex: 'Village',
                felx: 2
            }]
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;

            Ext.getCmp('Koltiva.view.IMS.PanelGridImsCoaching-MainGrid').getStore().load();
        }
    }
});