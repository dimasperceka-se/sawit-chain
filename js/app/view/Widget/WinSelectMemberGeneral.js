/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri May 03 2019
 *  File : WinSelectMemberGeneral.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini    
    - ListType
    - CompID
    - CompLabel
*/

Ext.define('Koltiva.view.Widget.WinSelectMemberGeneral' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Widget.WinSelectMemberGeneral',
    title: lang('List Grid Members'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '88%',
    height: '86%',
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.MainGrid = Ext.create('Koltiva.store.Widget.WinSelectMemberGeneralMainGrid',{
        	storeVar: {
                ListType: thisObj.viewVar.ListType,
                ExceptionID: null,
                TextSearch: null,
                ProvinceID: null,
                DistrictID: null,
                SubDistrictID: null,
                VillageID: null
            }
        });

        var cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        cmb_province.load();
        var cmb_district = Ext.create('Koltiva.store.Grower.CmbDistrict');
        var cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        var cmb_village = Ext.create('Koltiva.store.Grower.CmbVillage');

        //items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'gridpanel',
            id: 'Koltiva.view.Widget.WinSelectMemberGeneral-MainGrid',
            style: 'border:1px solid #CCC;',
            store: thisObj.MainGrid,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.MainGrid,
                dock: 'bottom',
                displayInfo: true,
                style:'padding-right:12px;'
            },{
                xtype: 'toolbar',
                items: [{
                    name: 'Koltiva.view.Widget.WinSelectMemberGeneral-SearchStringParam',
                    id: 'Koltiva.view.Widget.WinSelectMemberGeneral-SearchStringParam',
                    xtype: 'textfield',
                    width: 200,
                    emptyText: lang('ID / Name')
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.Widget.WinSelectMemberGeneral-SearchProvinceID',
                    name: 'Koltiva.view.Widget.WinSelectMemberGeneral-SearchProvinceID',
                    store: cmb_province,
                    emptyText: lang('Province'),
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

                            Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchDistrictID').setValue('');
                            Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchSubDistrictID').setValue('');
                            Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchVillageID').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.Widget.WinSelectMemberGeneral-SearchDistrictID',
                    name: 'Koltiva.view.Widget.WinSelectMemberGeneral-SearchDistrictID',
                    store: cmb_district,
                    emptyText: lang('District'),
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
                            Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchSubDistrictID').setValue('');
                            Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchVillageID').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.Widget.WinSelectMemberGeneral-SearchSubDistrictID',
                    name: 'Koltiva.view.Widget.WinSelectMemberGeneral-SearchSubDistrictID',
                    store: cmb_subdistrict,
                    emptyText: lang('Sub District'),
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
                            Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchVillageID').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.Widget.WinSelectMemberGeneral-SearchVillageID',
                    name: 'Koltiva.view.Widget.WinSelectMemberGeneral-SearchVillageID',
                    store: cmb_village,
                    emptyText: lang('Village'),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        change: function(cb, nv, ov) {
                        }
                    }
                },{
                    xtype: 'button',
                    text: lang('Search'),
                    icon: varjs.config.base_url + 'images/icons/new/search-white.png',
                    cls: 'Sfr_BtnFormBlue',
                    overCls: 'Sfr_BtnFormBlue-Hover',
                    handler: function() {
                        thisObj.MainGrid.storeVar.TextSearch = Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchStringParam').getValue();
                        thisObj.MainGrid.storeVar.ProvinceID = Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchProvinceID').getValue();
                        thisObj.MainGrid.storeVar.DistrictID = Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchDistrictID').getValue();
                        thisObj.MainGrid.storeVar.SubDistrictID = Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchSubDistrictID').getValue();
                        thisObj.MainGrid.storeVar.VillageID = Ext.getCmp('Koltiva.view.Widget.WinSelectMemberGeneral-SearchVillageID').getValue();
                        thisObj.MainGrid.load();
                    }
                }]
            }],
            columns: [{
                dataIndex: 'MemberID',
                hidden: true
            },{
                xtype: 'actioncolumn',
                width: 50,
                items: [{
                    icon: varjs.config.base_url + 'images/icons/silk/control_add_blue.png',
                    tooltip: 'Select',
                    handler: function(grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);
                        thisObj.viewVar.CompID.setValue(rec.data.MemberID);
                        thisObj.viewVar.CompLabel.setValue(rec.data.MemberDisplayID+' - '+rec.data.MemberName);
                        thisObj.close();
                    }
                }]
            },{
                text: lang('ID'),
                dataIndex: 'MemberDisplayID',
                flex: 1
            },{
                text: lang('Name'),
                dataIndex: 'MemberName',
                flex: 3
            },{
                text: lang('Gender'),
                dataIndex: 'Gender',
                flex: 1,
                renderer: function (value) {
                    var RetVal;

                    if(value != null && value != ''){
                        switch(value){
                            case 'm':
                                RetVal = lang('Male');
                            break;
                            case 'f':
                                RetVal = lang('Female');
                            break;
                            default:
                                RetVal = '-';
                            break;
                        }
                    }else{
                        RetVal = '-';
                    }

                    return RetVal;
                }
            },{
                text: lang('Age'),
                dataIndex: 'Age',
                flex: 1
            },{
                text: lang('Province'),
                dataIndex: 'Province',
                flex: 2
            },{
                text: lang('District'),
                dataIndex: 'District',
                flex: 2
            },{
                text: lang('Sub District'),
                dataIndex: 'SubDistrict',
                flex: 2
            },{
                text: lang('Village'),
                dataIndex: 'Village',
                flex: 2
            }]
        }]
        //items ---------------------------------------------------------------------------------------------------------------------------- (End)

        thisObj.buttons = [{
            text: lang('Close'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    }
});