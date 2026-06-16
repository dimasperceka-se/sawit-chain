/*
* @Author: nikolius
* @Date:   2017-10-11 09:54:45
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 10:27:33
*/

/*
    Param2 yg diperlukan ketika load View ini
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion' ,{
    extend: 'Ext.panel.Panel',
    frame: true,
    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion',
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

        var cmb_member_type = Ext.create('Koltiva.store.ComboGeneral.CmbMemberType');
        //store yg dipakai (begin)

        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 0.35,
                layout: 'form',
                items:[{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Province',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Province',
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-District').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-SubDistrict').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Village').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-District',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-District',
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-SubDistrict').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Village').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-SubDistrict',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-SubDistrict',
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Village').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Village',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Village',
                    store: cmb_village,
                    fieldLabel: lang('Village'),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-MemberType',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-MemberType',
                    store: cmb_member_type,
                    fieldLabel: lang('Member Type'),
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
                        if(Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Province').getValue() == undefined || Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Province').getValue() == null ){
                            Ext.MessageBox.show({
                                title: 'Notifications',
                                msg: 'Minimal Filter Propinsi harus terpilih',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }else{
                            //popup set Data Control
                            var WinDataSetControlRegion = Ext.create('Koltiva.view.DataAdm.AdcMember.WinDataSetControlRegion',{
                                viewVar: {
                                    ProvinceID: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Province').getValue(),
                                    DistrictID: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-District').getValue(),
                                    SubDistrictID: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-SubDistrict').getValue(),
                                    VillageID: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-Village').getValue(),
                                    MemberType: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion-MemberType').getValue()
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