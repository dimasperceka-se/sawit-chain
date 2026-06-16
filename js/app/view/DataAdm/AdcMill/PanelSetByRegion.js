/*
* @Author: nikolius
* @Date:   2017-10-11 17:27:13
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 17:30:35
*/

Ext.define('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion' ,{
    extend: 'Ext.panel.Panel',
    frame: true,
    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByRegion',
    title: lang('Access Set By Region'),
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
        //store yg dipakai (begin)

        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 0.35,
                layout: 'form',
                items:[{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Province',
                    name: 'Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Province',
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-District').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-SubDistrict').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Village').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-District',
                    name: 'Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-District',
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-SubDistrict').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Village').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-SubDistrict',
                    name: 'Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-SubDistrict',
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Village').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Village',
                    name: 'Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Village',
                    store: cmb_village,
                    fieldLabel: lang('Village'),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }]
            },{
                columnWidth: 0.25,
                layout: 'form',
                style: 'padding-left:25px;',
                items:[{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/silk/cog.png',
                    text: lang('Set Data Control'),
                    handler: function() {
                        //minimal propinsi harus terpilih
                        if(Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Province').getValue() == undefined || Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Province').getValue() == null ){
                            Ext.MessageBox.show({
                                title: 'Notifications',
                                msg: 'Minimal Filter Propinsi harus terpilih',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }else{
                            //popup set Data Control
                            var WinDataSetControlRegion = Ext.create('Koltiva.view.DataAdm.AdcMill.WinDataSetControlRegion',{
                                viewVar: {
                                    ProvinceID: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Province').getValue(),
                                    DistrictID: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-District').getValue(),
                                    SubDistrictID: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-SubDistrict').getValue(),
                                    VillageID: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion-Village').getValue()
                                }
                            });
                            if (!WinDataSetControlRegion.isVisible()) {
                                WinDataSetControlRegion.center();
                                WinDataSetControlRegion.show();
                            } else {
                                WinDataSetControlRegion.close();
                            }
                        }
                    }
                }]
            }]
        }];

        this.callParent(arguments);
    }
});