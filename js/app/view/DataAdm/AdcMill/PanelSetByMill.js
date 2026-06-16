/*
* @Author: nikolius
* @Date:   2017-10-11 16:41:32
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 17:04:41
*/

Ext.define('Koltiva.view.DataAdm.AdcMill.PanelSetByMill' ,{
    extend: 'Ext.panel.Panel',
    frame: true,
    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill',
    title: lang('Access Set By Mill'),
    style: 'padding:10px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai (begin)
        var cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        cmb_province.load();

        var cmb_district = Ext.create('Koltiva.store.Grower.CmbDistrict');
        var cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        var cmb_village = Ext.create('Koltiva.store.Grower.CmbVillage');

        var storeGridSetByMill = Ext.create('Koltiva.store.DataAdm.AdcMill.GridSetByMill')
        //store yg dipakai (end)

        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 0.25,
                layout: 'form',
                items:[{
                    xtype: 'textfield',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-MillID',
                    name: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-MillID',
                    fieldLabel: 'ID',
                },{
                    xtype: 'textfield',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-MillName',
                    name: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-MillName',
                    fieldLabel: lang('Name'),
                }]
            },{
                columnWidth: 0.25,
                layout: 'form',
                style: 'padding-left:10px;',
                items:[{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-Province',
                    name: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-Province',
                    store: cmb_province,
                    fieldLabel: lang('Province'),
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-District').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-SubDistrict').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-Village').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-SubDistrict',
                    name: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-SubDistrict',
                    store: cmb_subdistrict,
                    fieldLabel: lang('Sub District'),
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-Village').setValue('');
                        }
                    }
                }]
            },{
                columnWidth: 0.25,
                layout: 'form',
                style: 'padding-left:10px;',
                items:[{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-District',
                    name: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-District',
                    store: cmb_district,
                    fieldLabel: lang('District'),
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-SubDistrict').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-Village').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-Village',
                    name: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-Village',
                    store: cmb_village,
                    fieldLabel: lang('Village'),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }]
            },{
                columnWidth: 0.25,
                layout: 'form',
                style: 'padding-left:10px;',
                items:[{
                    xtype: 'button',
                    text: lang('Search Mill'),
                    handler: function() {
                        //set param
                        storeGridSetByMill.setStoreVar({
                            MillID: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-MillID').getValue(),
                            MillName: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-MillName').getValue(),
                            ProvinceID: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-Province').getValue(),
                            DistrictID: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-District').getValue(),
                            SubDistrictID: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-SubDistrict').getValue(),
                            VillageID: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-Village').getValue()
                        });
                        storeGridSetByMill.load()
                    }
                }]
            }]
        },{
            xtype: 'grid',
            id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByMill-gridSetByMill',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'checkboxmodel',
            store: storeGridSetByMill,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeGridSetByMill,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    xtype:'tbspacer',
                    flex:1
                },{
                    icon: varjs.config.base_url + 'images/icons/silk/cog.png',
                    text: lang('Set Data Control'),
                    handler: function() {
                        var gridSelected = Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByMill-gridSetByMill').getSelectionModel().getSelection();

                        var idSelectedArr = [];
                        for (var i = gridSelected.length - 1; i >= 0; i--) {
                            idSelectedArr.push(gridSelected[i].get('MillID'));
                        }

                        if(idSelectedArr.length > 0){
                            //popup set Data Control
                            var WinDataSetControl = Ext.create('Koltiva.view.DataAdm.AdcMill.WinDataSetControl',{
                                viewVar: {
                                    MillIDSelected: Ext.encode(idSelectedArr)
                                }
                            });
                            if (!WinDataSetControl.isVisible()) {
                                WinDataSetControl.center();
                                WinDataSetControl.show();
                            } else {
                                WinDataSetControl.close();
                            }
                        }else{
                            Ext.MessageBox.show({
                                title: 'Notifications',
                                msg: 'No item selected',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }
                    }
                }]
            }],
            columns: [{
                dataIndex: 'MillID',
                hidden:true
            },{
                //xtype : 'checkcolumn',
                HeaderCheckbox: true,
                dataIndex : 'chdata',
                width:'3%'
            },{
                text: lang('ID'),
                dataIndex: 'id',
                width:'10%'
            },{
                text: lang('Name'),
                dataIndex: 'Name',
                width:'20%'
            },{
                text: lang('Kecamatan'),
                dataIndex: 'Kecamatan',
                width:'12%'
            },{
                text: lang('Desa'),
                dataIndex: 'Desa',
                width:'12%'
            },{
                text: lang('Partner Access'),
                dataIndex: 'PartnerAccess',
                width:'40%'
            }]
        }];

        this.callParent(arguments);
    }
});