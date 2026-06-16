/*
    Param2 yg diperlukan ketika load View ini
    - MainGrid
*/

Ext.define('Koltiva.view.Tph.WinApplyFilter' ,{
    extend: 'Ext.window.Window',
	id: 'Koltiva.view.Tph.WinApplyFilter',
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
		                        id: 'Koltiva.view.Tph.WinApplyFilter-CmbFilterProvince',
                                name: 'Koltiva.view.Tph.WinApplyFilter-CmbFilterProvince',
                                style: 'margin-left:5px;margin-top:5px;',
                                emptyText: lang('All') + ' ' + lang('Province'),
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterDistrict').setValue(null);
                                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterSubDistrict').setValue(null);
                                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterVillage').setValue(null);

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
		                        id: 'Koltiva.view.Tph.WinApplyFilter-CmbFilterDistrict',
                                name: 'Koltiva.view.Tph.WinApplyFilter-CmbFilterDistrict',
                                style: 'margin-left:5px;margin-top:5px;',
                                emptyText: lang('All') + ' ' + lang('District'),
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterSubDistrict').setValue(null);
                                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterVillage').setValue(null);

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
		                        id: 'Koltiva.view.Tph.WinApplyFilter-CmbFilterSubDistrict',
                                name: 'Koltiva.view.Tph.WinApplyFilter-CmbFilterSubDistrict',
                                style: 'margin-left:5px;margin-top:5px;',
                                emptyText: lang('All') + ' ' + lang('Sub District'),
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterVillage').setValue(null);

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
		                        id: 'Koltiva.view.Tph.WinApplyFilter-CmbFilterVillage',
                                name: 'Koltiva.view.Tph.WinApplyFilter-CmbFilterVillage',
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
		                        id: 'Koltiva.view.Tph.WinApplyFilter-TextFilterID',
		                        name: 'Koltiva.view.Tph.WinApplyFilter-TextFilterID'
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
		                        id: 'Koltiva.view.Tph.WinApplyFilter-TextFilterName',
		                        name: 'Koltiva.view.Tph.WinApplyFilter-TextFilterName'
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
                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterProvince').getValue() != null ||
                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterDistrict').getValue() != null ||
                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterSubDistrict').getValue() != null ||
                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterVillage').getValue() != null
                    ) {
                        ArrFilter.push('region');
                        ArrFilterLang.push(lang('Region'));
                    }

                    if(Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-TextFilterID').getValue() != "") {
                        ArrFilter.push('id');
                        ArrFilterLang.push(lang('ID'));
                    }

                    if(Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-TextFilterName').getValue() != "") {
                        ArrFilter.push('name');
                        ArrFilterLang.push(lang('Name'));
                    }
                    //Cek filter apa saja yg dimasukkan ================================= (End)

                    //Set LocalStorage ================================= (Begin)
                    localStorage.setItem('cof_gridtph_params', JSON.stringify({
                        ArrFilter: ArrFilter,
                        ArrFilterLang: ArrFilterLang,
                        CmbFilterProvince: Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterProvince').getValue(),
                        CmbFilterDistrict: Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterDistrict').getValue(),
                        CmbFilterSubDistrict: Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterSubDistrict').getValue(),
                        CmbFilterVillage: Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterVillage').getValue(),

                        TextFilterID: Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-TextFilterID').getValue(),
                        TextFilterName: Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-TextFilterName').getValue(),
                    }));
                    //Set LocalStorage ================================= (End)

                    //reload store main grid
                    Ext.getCmp('Koltiva.view.Tph.GridMainTph-GridMain').getStore().loadPage(1);
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
                Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterProvince').setValue(null);
                Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterDistrict').setValue(null);
                Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterSubDistrict').setValue(null);
                Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterVillage').setValue(null);

                Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-TextFilterID').setValue('');
                Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-TextFilterName').setValue('');

                localStorage.removeItem('cof_gridtph_params');
                Ext.getCmp('Koltiva.view.Tph.GridMainTph-GridMain').getStore().loadPage(1);

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
            let cof_gridtph_params = JSON.parse(localStorage.getItem('cof_gridtph_params'));
            if(cof_gridtph_params != null){
                thisObj.CmbFilterProvince.load({
                    callback: function (records, operation, success) {
                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterProvince').setValue(cof_gridtph_params.CmbFilterProvince);

                        thisObj.CmbFilterDistrict.load({
                            callback: function (records, operation, success) {
                                Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterDistrict').setValue(cof_gridtph_params.CmbFilterDistrict);

                                thisObj.CmbFilterSubDistrict.load({
                                    callback: function (records, operation, success) {
                                        Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterSubDistrict').setValue(cof_gridtph_params.CmbFilterSubDistrict);

                                        thisObj.CmbFilterVillage.load({
                                            callback: function (records, operation, success) {
                                                Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-CmbFilterVillage').setValue(cof_gridtph_params.CmbFilterVillage);
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                });

                Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-TextFilterID').setValue(cof_gridtph_params.TextFilterID);
                Ext.getCmp('Koltiva.view.Tph.WinApplyFilter-TextFilterName').setValue(cof_gridtph_params.TextFilterName);
            }
        }
    },
    AddValidationBasicForm: function() {
        // sementara tidak ada validasi
    }
});