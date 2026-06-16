Ext.define('Koltiva.view.Dboard.MainGridKpiTargetGeneral' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral',
    renderTo: 'ext-content',
    style:'padding:0 7px 7px 7px;margin-top:10px;',
    listeners: {
        afterRender: function(component, eOpts){
            var thisObj = this;
            document.getElementById('divCommonContentRegion').style.display = 'none';

            //ngeload main grid pertama kali
            Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterYear').getStore().load({
                callback: function(records, operation, success){
                    var combo = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterYear');
                    combo.select(combo.getStore().getAt(0));

                    thisObj.StoreGridMain.storeVar.FilterYear = combo.getValue();
                    thisObj.StoreGridMain.storeVar.FilterPartnerID = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterPartner').getValue();
                    thisObj.StoreGridMain.load();
                }
            });
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.StoreGridMain = Ext.create('Koltiva.store.Dboard.MainGridKpiTargetGeneral',{
            storeVar: {
                FilterYear: null,
                FilterCountry: '',
                FilterProvince: '',
                FilterPartnerID: null
            }
        });
        thisObj.CmbFilterCountry = Ext.create('Koltiva.store.ComboGeneral.CmbFilterCountry');
        thisObj.CmbFilterProvince = Ext.create('Koltiva.store.ComboGeneral.CmbFilterProvince',{
            storeVar: {
                CountryID: null
            }
        });
        thisObj.CmbFilterDistrict = Ext.create('Koltiva.store.Dboard.CmbFilterDistrictKPI',{
            storeVar: {
                ProvinceID: null
            }
        });
        thisObj.CmbFilterYear = Ext.create('Koltiva.store.Dboard.CmbFilterYearKpiTarget');
        thisObj.CmbPartner = Ext.create('Koltiva.store.ComboGeneral.CmbPartner');

        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu',{
            cls:'Sfr_ConMenu',
	        items:[{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls:'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-Grid').getSelectionModel().getSelection()[0];

                    var WinFormInputKpiTargetGeneral = Ext.create('Koltiva.view.Dboard.WinFormInputKpiTargetGeneral',{
                        viewVar : {
                            CallerStore: thisObj.StoreGridMain,
                            CountryID: sm.get('CountryID'),
                            ProvinceID: sm.get('ProvinceID'),
                            DistrictID: sm.get('DistrictID'),
                            DistrictLabel: sm.get('District'),
                            ProvinceLabel: sm.get('Province'),
                            CountryLabel: sm.get('CountryName'),
                            PartnerID: sm.get('PartnerID'),
                            Year: sm.get('Year'),
                            PalmOilFarmersRegistered: sm.get('PalmOilFarmersRegistered'),
                            PalmOilPlantationsMapped: sm.get('PalmOilPlantationsMapped'),
                            ConsentLettersSigned: sm.get('ConsentLettersSigned'),
                            PalmOilPlantationsArea: sm.get('PalmOilPlantationsArea'),
                            PalmOilMillsMapped: sm.get('PalmOilMillsMapped'),
                            PalmOilPlantationsMappedWithPolygon: sm.get('PalmOilPlantationsMappedWithPolygon'),
                            PalmOilSMEMapped: sm.get('PalmOilSMEMapped'),
                            PalmOilPlantationsHectareMappedWithPolygon: sm.get('PalmOilPlantationsHectareMappedWithPolygon'),
                            TrainOrCoachFarmers : sm.get('TrainOrCoachFarmers'),
                            RegisteredPlantation : sm.get('RegisteredPlantation'),
                            RegisteredPlantationHectares : sm.get('RegisteredPlantationHectares'),
                            ResponSourcingFarmers : sm.get('ResponSourcingFarmers'),
                            TraceTransaction : sm.get('TraceTransaction'),
                            PlatformUsers : sm.get('PlatformUsers'),
                            RegisteredSME : sm.get('RegisteredSME'),
                            FarmXUsers : sm.get('FarmXUsers'),
                            FarmGateUsers : sm.get('FarmGateUsers'),
                            FarmRetailUsers : sm.get('FarmRetailUsers'),
                            FarmCloudUsers : sm.get('FarmCloudUsers')
                        }
                    });
                    if (!WinFormInputKpiTargetGeneral.isVisible()) {
                        WinFormInputKpiTargetGeneral.center();
                        WinFormInputKpiTargetGeneral.show();
                    } else {
                        WinFormInputKpiTargetGeneral.close();
                    }
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-Grid',
            style: 'border:1px solid #CCC;margin-top:2px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            features: [{
                ftype: 'summary'
            }],
            dockedItems: [{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    store: thisObj.CmbFilterCountry,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterCountry',
                    name: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterCountry',
                    emptyText: lang('All Country'),
                    style: 'margin-top:5px;',
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterProvince').setValue(null);

                            thisObj.CmbFilterProvince.storeVar.CountryID = nv;
                            thisObj.CmbFilterProvince.load();
                        }
                    }
                },{
                    store: thisObj.CmbFilterProvince,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterProvince',
                    name: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterProvince',
                    style: 'margin-left:5px;margin-top:5px;',
                    emptyText: lang('All')+' '+lang(m_label_prov),
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterDistrict').setValue(null);

                            thisObj.CmbFilterDistrict.storeVar.ProvinceID = nv;
                            thisObj.CmbFilterDistrict.load();
                        }
                    }
                },{
                    store: thisObj.CmbFilterDistrict,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterDistrict',
                    name: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterDistrict',
                    style: 'margin-left:5px;margin-top:5px;',
                    emptyText: lang('All')+' '+lang('District'),
                    listeners: {
                        change: function (cb, nv, ov) {
                        }
                    }
                },{
                    store: thisObj.CmbFilterYear,
                    editable: false,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    editable: false,
                    id: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterYear',
                    name: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterYear',
                    style: 'margin-left:5px;margin-top:5px;',
                    listeners: {
                        change: function (cb, nv, ov) {
                        }
                    }
                },{
                    store: thisObj.CmbPartner,
                    editable: false,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    editable: false,
                    id: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterPartner',
                    name: 'Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterPartner',
                    style: 'margin-left:5px;margin-top:5px;',
                    listeners: {
                        afterRender: function () {
                            Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterPartner').setValue(m_partner_id);
                        }
                    }
                },{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    text: lang('Search'),
                    cls:'Sfr_BtnGridBlue',
                    overCls:'Sfr_BtnGridBlue-Hover',
                    handler: function() {
                        thisObj.StoreGridMain.storeVar.FilterYear = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterYear').getValue();
                        thisObj.StoreGridMain.storeVar.FilterCountry = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterCountry').getValue();
                        thisObj.StoreGridMain.storeVar.FilterProvince = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterProvince').getValue();
                        thisObj.StoreGridMain.storeVar.FilterDistrictID = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterDistrict').getValue();
                        thisObj.StoreGridMain.storeVar.FilterPartnerID = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral-CmbFilterPartner').getValue();
                        thisObj.StoreGridMain.load();
                    }
                }]
            }],
            columns: [{
                text: '',
                xtype:'actioncolumn',
                width: '5%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuGrid.showAt(e.getXY());
                    }
                }]
            },{
                dataIndex: 'CountryID',
                hidden:true
            },{
                dataIndex: 'ProvinceID',
                hidden:true
            },{
                dataIndex: 'PartnerID',
                hidden:true
            },{
                text: lang('Country'),
                dataIndex: 'CountryName',
                width:'10%'
            },{
                text: lang(m_label_prov),
                dataIndex: 'Province',
                width:'10%'
            },{
                text: lang('District'),
                dataIndex: 'District',
                width:'10%'
            },{
                text: lang('Year'),
                dataIndex: 'Year',
                width:'10%',
                summaryRenderer: function(value, summaryData, dataIndex) {
                    return 'Total';
                }
            },{
                xtype: 'numbercolumn',
                text: lang('Palm Oil Farmers Registered'),
                dataIndex: 'PalmOilFarmersRegistered',
                width:'10%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Palm Oil Plantations Mapped'),
                dataIndex: 'PalmOilPlantationsMapped',
                width:'15%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Consent Letters Signed'),
                dataIndex: 'ConsentLettersSigned',
                width:'10%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Palm Oil Plantations Area'),
                dataIndex: 'PalmOilPlantationsArea',
                width:'10%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Palm Oil Mills Mapped'),
                dataIndex: 'PalmOilMillsMapped',
                width:'10%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Palm Oil Plantations Mapped With Polygon'),
                dataIndex: 'PalmOilPlantationsMappedWithPolygon',
                width:'14%',
                format:'0,000',
                summaryType: 'sum'
            }, {
                xtype: 'numbercolumn',
                text: lang('Palm Oil SME Mapped'),
                dataIndex: 'PalmOilSMEMapped',
                width:'7%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Palm Oil Plantations Hectare Mapped With Polygon'),
                dataIndex: 'PalmOilPlantationsHectareMappedWithPolygon',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Farmers Trained or Coached'),
                dataIndex: 'TrainOrCoachFarmers',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Farmer Plantation Registered'),
                dataIndex: 'RegisteredPlantation',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Farmer Plantation (Ha)'),
                dataIndex: 'RegisteredPlantationHectares',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Farmers active in Responsible Sourcing'),
                dataIndex: 'ResponSourcingFarmers',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Traceability Transactions'),
                dataIndex: 'TraceTransaction',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Platform Users'),
                dataIndex: 'PlatformUsers',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Small and Medium Enterprises Registered'),
                dataIndex: 'RegisteredSME',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('FarmXtension Users'),
                dataIndex: 'FarmXUsers',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('FarmGate Users'),
                dataIndex: 'FarmGateUsers',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('FarmRetail Users'),
                dataIndex: 'FarmRetailUsers',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('FarmCloud Users'),
                dataIndex: 'FarmCloudUsers',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            }]
        }];

        this.callParent(arguments);
    }
});