/*
* @Author: nikolius
* @Date:   2017-08-04 09:59:38
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-08 12:02:57
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
    function resetAdvancedFilterLs(){
        localStorage.setItem('patchouli_farmer_group_ls', JSON.stringify({
            pSearch: '',
            ProvinceID: '',
            DistrictID: '',
            SubdistrictID: '',
            VillageID: '',
            Enumerator: ''
        }));
    }

    function setAdvancedFilterLs(){
        localStorage.setItem('patchouli_farmer_group_ls', JSON.stringify({
            pSearch: Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-textSearch').getValue(),
            ProvinceID: Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-ProvinceID').getValue(),
            DistrictID: Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-DistrictID').getValue(),
            SubdistrictID: Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-SubdistrictID').getValue(),
            VillageID: Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-VillageID').getValue(),
            Enumerator: Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-Enumerator').getValue()
        }));
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.FarmerGroup.WinAdvancedFilter' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmerGroup.WinAdvancedFilter',
    title: lang('Filter'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '60%',
    height: '53%',
    overflowY: 'auto',
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai

        var cmb_enumerator  = Ext.create('Koltiva.store.FarmerGroup.CmbEnumerator');

        var cmb_province    = Ext.create('Koltiva.store.Grower.CmbProvince');
        var cmb_district    = Ext.create('Koltiva.store.Grower.CmbDistrict');
        var cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        var cmb_village     = Ext.create('Koltiva.store.Grower.CmbVillage');

        cmb_province.load();

        //isi formnya
        thisObj.items = [{
            xtype:'panel',
            border: false,
            padding:'5 23 5 15',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 0.5,
                    layout: 'form',
                    style:'padding-right:5px',
                    items:[{
                        id: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-textSearch',
                        xtype: 'textfield',
                        width: 300,
                        labelAlign:'top',
                        fieldLabel: lang('Cari berdasar nama/ID')
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-ProvinceID',
                        name: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-ProvinceID',
                        store: cmb_province,
                        labelAlign:'top',
                        fieldLabel: lang('Province'),
                        labelAlign:'top',
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
                                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-DistrictID').setValue('');
                                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-SubdistrictID').setValue('');
                                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-VillageID').setValue('');
                            }
                        }
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-DistrictID',
                        name: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-DistrictID',
                        store: cmb_district,
                        labelAlign:'top',
                        fieldLabel: lang('District'),
                        labelAlign:'top',
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
                                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-SubdistrictID').setValue('');
                                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-VillageID').setValue('');
                            }
                        }
                    }]
                },{
                    columnWidth: 0.5,
                    layout: 'form',
                    style:'padding-left:5px',
                    items:[{
                        xtype: 'combobox',
                        id: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-SubdistrictID',
                        name: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-SubdistrictID',
                        store: cmb_subdistrict,
                        labelAlign:'top',
                        fieldLabel: lang('Subdistrict'),
                        labelAlign:'top',
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
                                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-VillageID').setValue('');
                            }
                        }
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-VillageID',
                        name: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-VillageID',
                        store: cmb_village,
                        labelAlign:'top',
                        fieldLabel: lang('Village'),
                        labelAlign:'top',
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id'
                    },{
                        id: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-Enumerator',
                        xtype: 'combobox',
                        store: cmb_enumerator,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        width: 300,
                        labelAlign:'top',
                        fieldLabel: lang('Enumerator')
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    },
    buttons: [{
        text: lang('Search'),
        icon: varjs.config.base_url + 'images/icons/new/search-white.png',
        cls: 'Sfr_BtnFormBlue',
        overCls: 'Sfr_BtnFormBlue-Hover',
        handler: function() {
            setAdvancedFilterLs();
            Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput').getStore().loadPage(1);
            Ext.getCmp('Koltiva.view.FarmerGroup.WinAdvancedFilter').close(); //tutup popup
        }
    },{
        text: lang('Reset Filter'),
        icon: varjs.config.base_url + 'images/icons/new/delete.svg',
        cls:'Sfr_BtnFormRed',
        overCls:'Sfr_BtnFormRed-Hover',
        handler: function() {
            resetAdvancedFilterLs();
            Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput').getStore().loadPage(1);
            Ext.getCmp('Koltiva.view.FarmerGroup.WinAdvancedFilter').close(); //tutup popup
        }
    },{
        text: lang('Close'),
        icon: varjs.config.base_url + 'images/icons/new/close.png',
        cls: 'Sfr_BtnFormGrey',
        overCls: 'Sfr_BtnFormGrey-Hover',
        handler: function() {
            Ext.getCmp('Koltiva.view.FarmerGroup.WinAdvancedFilter').close();
        }
    }],
    listeners: {
        afterRender: function(component, eOpts){
            var patchouli_farmer_group_ls = JSON.parse(localStorage.getItem('patchouli_farmer_group_ls'));
            var filterValue = [];

            if(patchouli_farmer_group_ls != null){
                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-textSearch').setValue(patchouli_farmer_group_ls.pSearch);
                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-ProvinceID').setValue(patchouli_farmer_group_ls.ProvinceID);
                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-DistrictID').setValue(patchouli_farmer_group_ls.DistrictID);
                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-SubdistrictID').setValue(patchouli_farmer_group_ls.SubdistrictID);
                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-VillageID').setValue(patchouli_farmer_group_ls.VillageID);
                Ext.getCmp('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput-Enumerator').setValue(patchouli_farmer_group_ls.Enumerator);

            }
        }
    }
});