/*
* @Author: nikolius
* @Date:   2017-10-10 10:30:41
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 10:13:34
*/

/*
    Param2 yg diperlukan ketika load View ini
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.DataAdm.AdcMember.PanelSetByMember' ,{
    extend: 'Ext.panel.Panel',
    frame: true,
    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember',
    title: lang('Access Set By Member'),
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

        var storeGridSetByMember = Ext.create('Koltiva.store.DataAdm.AdcMember.GridSetByMember')
        //store yg dipakai (end)

        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 0.25,
                layout: 'form',
                items:[{
                    xtype: 'textfield',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-ID',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-ID',
                    fieldLabel: 'ID',
                },{
                    xtype: 'textfield',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Name',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Name',
                    fieldLabel: lang('Name'),
                }]
            },{
                columnWidth: 0.25,
                layout: 'form',
                style: 'padding-left:10px;',
                items:[{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Province',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Province',
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-District').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-SubDistrict').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Village').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-SubDistrict',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-SubDistrict',
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Village').setValue('');
                        }
                    }
                }]
            },{
                columnWidth: 0.25,
                layout: 'form',
                style: 'padding-left:10px;',
                items:[{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-District',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-District',
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
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-SubDistrict').setValue('');
                            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Village').setValue('');
                        }
                    }
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Village',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Village',
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
                    xtype: 'combobox',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-MemberType',
                    name: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-MemberType',
                    store: cmb_member_type,
                    fieldLabel: lang('Member Type'),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                },{
                    xtype: 'button',
                    text: lang('Search Member'),
                    handler: function() {
                        //set param
                        storeGridSetByMember.setStoreVar({
                            MemberID: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-ID').getValue(),
                            MemberName: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Name').getValue(),
                            ProvinceID: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Province').getValue(),
                            DistrictID: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-District').getValue(),
                            SubDistrictID: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-SubDistrict').getValue(),
                            VillageID: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-Village').getValue(),
                            MemberType: Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-MemberType').getValue()
                        });
                        storeGridSetByMember.load()
                    }
                }]
            }]
        },{
            xtype: 'grid',
            id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-gridSetByMember',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'checkboxmodel',
            store: storeGridSetByMember,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeGridSetByMember,
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
                        var gridSelected = Ext.getCmp('Koltiva.view.DataAdm.AdcMember.PanelSetByMember-gridSetByMember').getSelectionModel().getSelection();

                        var idSelectedArr = [];
                        for (var i = gridSelected.length - 1; i >= 0; i--) {
                            idSelectedArr.push(gridSelected[i].get('MemberIDInc'));
                        }

                        if(idSelectedArr.length > 0){
                            //popup set Data Control
                            var WinDataSetControl = Ext.create('Koltiva.view.DataAdm.AdcMember.WinDataSetControl',{
                                viewVar: {
                                    MemberIDSelected: Ext.encode(idSelectedArr)
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
                id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-gridSetByMember-colId',
                dataIndex: 'MemberIDInc',
                hidden:true
            },{
                //xtype : 'checkcolumn',
                HeaderCheckbox: true,
                dataIndex : 'chdata',
                width:'3%'
            },{
                id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-gridSetByMember-colMemberDisplayId',
                text: lang('ID'),
                dataIndex: 'id',
                width:'10%'
            },{
                id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-gridSetByMember-colName',
                text: lang('Name'),
                dataIndex: 'Name',
                width:'20%'
            },{
                id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-gridSetByMember-colKecamatan',
                text: lang('Kecamatan'),
                dataIndex: 'Kecamatan',
                width:'12%'
            },{
                id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-gridSetByMember-colDesa',
                text: lang('Desa'),
                dataIndex: 'Desa',
                width:'12%'
            },{
                id: 'Koltiva.view.DataAdm.AdcMember.PanelSetByMember-gridSetByMember-colPartnerAccess',
                text: lang('Partner Access'),
                dataIndex: 'PartnerAccess',
                width:'40%'
            }]
        }]

        this.callParent(arguments);
    }
});