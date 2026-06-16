/*
    Param2 yg diperlukan ketika load View ini
    - FarmerGroupStoreGrid
*/

Ext.define('Koltiva.view.FarmerGroup.WinApplyFilter' ,{
    extend: 'Ext.window.Window',
	id: 'Koltiva.view.FarmerGroup.WinApplyFilter',
	// cls: 'Sfr_LayoutPopupWindows',
    title: lang('Apply Filter'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '60%',
    height: 350,
    overflowY: 'auto',
    initComponent: function() {
        var thisObj = this;

        //Store ========================= (Begin)
        thisObj.CmbFilterProvince = Ext.create('Koltiva.store.ComboGeneral.ComboProvince');
        thisObj.CmbFilterDistrict = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict',{
            storeVar: {
                ProvinceID: null
            }
        });
        thisObj.CmbFilterSubDistrict = Ext.create('Koltiva.store.ComboGeneral.ComboSubDistrict',{
            storeVar: {
                DistrictID: null
            }
        });
        thisObj.CmbFilterVillage = Ext.create('Koltiva.store.ComboGeneral.ComboVillage',{
            storeVar: {
                SubDistrictID: null
            }
        });
        //Store ========================= (End)

        thisObj.items = [{
            xtype:'panel',
            border: false,
			padding:'5 12 5 5',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                	columnWidth: 1,
                    items:[{
                        layout: 'column',
		                border: false,
		                items:[{
                            columnWidth: 0.2,
		                    layout: 'form',
		                    items:[{
		                        xtype: 'label',
		                        cls: 'x-form-item-label',
		                        text: lang('Region')+':',
		                    }]
                        },{
                            columnWidth: 0.8,
                            border: false,
                            layout: 'column',
                            items:[{
                                store: thisObj.CmbFilterProvince,
                                editable: false,
		                        xtype: 'combobox',
		                        queryMode: 'local',
		                        displayField: 'label',
		                        valueField: 'id',
		                        id: 'Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterProvince',
                                name: 'Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterProvince',
                                style: 'margin-left:5px;margin-top:5px;',
                                emptyText: lang('All') + ' ' + lang('Province'),
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterDistrict').setValue(null);
                                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterSubDistrict').setValue(null);
                                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterVillage').setValue(null);

                                        thisObj.CmbFilterDistrict.storeVar.ProvinceID = nv;
                                        thisObj.CmbFilterDistrict.load();
                                    }
                                }
                            },{
                                store: thisObj.CmbFilterDistrict,
                                editable: false,
		                        xtype: 'combobox',
		                        queryMode: 'local',
		                        displayField: 'label',
		                        valueField: 'id',
		                        id: 'Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterDistrict',
                                name: 'Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterDistrict',
                                style: 'margin-left:5px;margin-top:5px;',
                                emptyText: lang('All') + ' ' + lang('District'),
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterSubDistrict').setValue(null);
                                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterVillage').setValue(null);

                                        thisObj.CmbFilterSubDistrict.storeVar.DistrictID = nv;
                                        thisObj.CmbFilterSubDistrict.load();
                                    }
                                }
                            },{
                                store: thisObj.CmbFilterSubDistrict,
                                editable: false,
		                        xtype: 'combobox',
		                        queryMode: 'local',
		                        displayField: 'label',
		                        valueField: 'id',
		                        id: 'Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterSubDistrict',
                                name: 'Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterSubDistrict',
                                style: 'margin-left:5px;margin-top:5px;',
                                emptyText: lang('All') + ' ' + lang('Sub District'),
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterVillage').setValue(null);

                                        thisObj.CmbFilterVillage.storeVar.SubDistrictID = nv;
                                        thisObj.CmbFilterVillage.load();
                                    }
                                }
                            },{
                                store: thisObj.CmbFilterVillage,
                                editable: false,
		                        xtype: 'combobox',
		                        queryMode: 'local',
		                        displayField: 'label',
		                        valueField: 'id',
		                        id: 'Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterVillage',
                                name: 'Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterVillage',
                                style: 'margin-left:5px;margin-top:5px;',
                                emptyText: lang('All')  + ' ' + lang('Village')
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        style:'margin-top:12px;',
		                items:[{
                            columnWidth: 0.2,
		                    layout: 'form',
		                    items:[{
		                        xtype: 'label',
		                        cls: 'x-form-item-label',
		                        text: lang('ID')+':',
		                    }]
                        },{
                            columnWidth: 0.8,
                            border: false,
                            layout: 'column',
                            items:[{
                                xtype:'textfield',
                                width:175,
		                        id: 'Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterID',
		                        name: 'Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterID'
                            }]
                        }]
                    },{
                        layout: 'column',
		                border: false,
		                items:[{
                            columnWidth: 0.2,
		                    layout: 'form',
		                    items:[{
		                        xtype: 'label',
		                        cls: 'x-form-item-label',
		                        text: lang('Name')+':',
		                    }]
                        },{
                            columnWidth: 0.8,
                            border: false,
                            layout: 'column',
                            items:[{
                                xtype:'textfield',
                                width:375,
		                        id: 'Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterName',
		                        name: 'Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterName'
                            }]
                        }]
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
        	icon: varjs.config.base_url + 'images/icons/new/search-white.png',
			text: lang('Apply Filter'),
			cls:'Sfr_BtnFormBlue',
			overCls:'Sfr_BtnFormBlue-Hover',
	        handler: function() {
                //Cek Validasi =================== (Begin)
                thisObj.AddValidation = true;
                thisObj.MsgAddValidation = "";
                thisObj.AddValidationBasicForm();
                if(thisObj.AddValidation == true) {
                    let ArrFilter = [];
                    let ArrFilterLang = [];

                    //Cek filter apa saja yg dimasukkan ================================= (Begin)
                    if(
                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterProvince').getValue() != null ||
                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterDistrict').getValue() != null ||
                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterSubDistrict').getValue() != null ||
                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterVillage').getValue() != null
                    ) {
                        ArrFilter.push('region');
                        ArrFilterLang.push(lang('Region'));
                    }

                    if(Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterID').getValue() != "") {
                        ArrFilter.push('id');
                        ArrFilterLang.push(lang('ID'));
                    }

                    if(Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterName').getValue() != "") {
                        ArrFilter.push('name');
                        ArrFilterLang.push(lang('Name'));
                    }
                    //Cek filter apa saja yg dimasukkan ================================= (End)

                    //Set LocalStorage ================================= (Begin)
                    localStorage.setItem('cof_gridfarmergroup_params', JSON.stringify({
                        ArrFilter: ArrFilter,
                        ArrFilterLang: ArrFilterLang,
                        CmbFilterProvince: Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterProvince').getValue(),
                        CmbFilterDistrict: Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterDistrict').getValue(),
                        CmbFilterSubDistrict: Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterSubDistrict').getValue(),
                        CmbFilterVillage: Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterVillage').getValue(),

                        TextFilterID: Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterID').getValue(),
                        TextFilterName: Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterName').getValue(),
                    }));
                    //Set LocalStorage ================================= (End)

                    //reload store main grid
                    Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridMainGrid').getStore().loadPage(1);
                    thisObj.close();
                } else {
                    Ext.MessageBox.show({
                        title: lang('Information'),
                        msg: thisObj.MsgAddValidation,
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
                //Cek Validasi =================== (End)
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/delete.svg',
			text: lang('Reset'),
			cls:'Sfr_BtnFormRed',
			overCls:'Sfr_BtnFormRed-Hover',
	        handler: function() {
                Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterProvince').setValue(null);
                Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterDistrict').setValue(null);
                Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterSubDistrict').setValue(null);
                Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterVillage').setValue(null);

                Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterID').setValue('');
                Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterName').setValue('');

                localStorage.removeItem('cof_gridfarmergroup_params');
                thisObj.viewVar.FarmerGroupStoreGrid.load();

	            thisObj.close();
	        }
        },{
            icon: varjs.config.base_url + 'images/icons/new/close.png',
			text: lang('Close'),
			cls:'Sfr_BtnFormGrey',
			overCls:'Sfr_BtnFormGrey-Hover',
	        handler: function() {
	            thisObj.close();
	        }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //ngeload filter parameters
            let cof_gridfarmergroup_params = JSON.parse(localStorage.getItem('cof_gridfarmergroup_params'));
            if(cof_gridfarmergroup_params != null){
                thisObj.CmbFilterProvince.load({
                    callback: function (records, operation, success) {
                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterProvince').setValue(cof_gridfarmergroup_params.CmbFilterProvince);

                        thisObj.CmbFilterDistrict.load({
                            callback: function (records, operation, success) {
                                Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterDistrict').setValue(cof_gridfarmergroup_params.CmbFilterDistrict);

                                thisObj.CmbFilterSubDistrict.load({
                                    callback: function (records, operation, success) {
                                        Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterSubDistrict').setValue(cof_gridfarmergroup_params.CmbFilterSubDistrict);

                                        thisObj.CmbFilterVillage.load({
                                            callback: function (records, operation, success) {
                                                Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-CmbFilterVillage').setValue(cof_gridfarmergroup_params.CmbFilterVillage);
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                });

                Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterID').setValue(cof_gridfarmergroup_params.TextFilterID);
                Ext.getCmp('Koltiva.view.FarmerGroup.WinApplyFilter-TextFilterName').setValue(cof_gridfarmergroup_params.TextFilterName);
            }
        }
    },
    AddValidationBasicForm: function() {
        // sementara tidak ada validasi
    }
});