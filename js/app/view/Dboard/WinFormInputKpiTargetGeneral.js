Ext.define('Koltiva.view.Dboard.WinFormInputKpiTargetGeneral' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('KPI Target Setting Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '50%',
    height: 380,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        let labelWidth = 200;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-CountryID',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-CountryID',
                        value: thisObj.viewVar.CountryID
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-ProvinceID',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-ProvinceID',
                        value: thisObj.viewVar.ProvinceID
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-DistrictID',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-DistrictID',
                        value: thisObj.viewVar.DistrictID
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PartnerID',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PartnerID',
                        value: thisObj.viewVar.PartnerID
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-CountryLabel',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-CountryLabel',
                        value: thisObj.viewVar.CountryLabel,
                        readOnly:true,
                        fieldLabel: lang('Country'),
                        labelWidth: labelWidth
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-ProvinceLabel',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-ProvinceLabel',
                        value: thisObj.viewVar.ProvinceLabel,
                        readOnly:true,
                        fieldLabel: lang(m_label_prov),
                        labelWidth: labelWidth
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-DistrictLabel',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-DistrictLabel',
                        value: thisObj.viewVar.DistrictLabel,
                        readOnly:true,
                        fieldLabel: lang('District'),
                        labelWidth: labelWidth
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-Year',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-Year',
                        value: thisObj.viewVar.Year,
                        readOnly:true,
                        fieldLabel: lang('Year'),
                        labelWidth: labelWidth
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilFarmersRegistered',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilFarmersRegistered',
                        fieldLabel: lang('Palm Oil Farmers Registered'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.PalmOilFarmersRegistered
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilPlantationsMapped',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilPlantationsMapped',
                        fieldLabel: lang('Palm Oil Plantations Mapped'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.PalmOilPlantationsMapped
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-ConsentLettersSigned',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-ConsentLettersSigned',
                        fieldLabel: lang('Consent Letters Signed'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.ConsentLettersSigned
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilPlantationsArea',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilPlantationsArea',
                        fieldLabel: lang('Palm Oil Plantations Area'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.PalmOilPlantationsArea
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilMillsMapped',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilMillsMapped',
                        fieldLabel: lang('Palm Oil Mills Mapped'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.PalmOilMillsMapped
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilPlantationsMappedWithPolygon',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilPlantationsMappedWithPolygon',
                        fieldLabel: lang('Palm Oil Plantations Mapped With Polygon'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.PalmOilPlantationsMappedWithPolygon
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilSMEMapped',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilSMEMapped',
                        fieldLabel: lang('Palm Oil SME Mapped'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.PalmOilSMEMapped
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilPlantationsHectareMappedWithPolygon',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PalmOilPlantationsHectareMappedWithPolygon',
                        fieldLabel: lang('Palm Oil Plantations Hectare Mapped With Polygon'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.PalmOilPlantationsHectareMappedWithPolygon
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-TrainOrCoachFarmers',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-TrainOrCoachFarmers',
                        fieldLabel: lang('Farmers Trained or Coached'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.TrainOrCoachFarmers
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-RegisteredPlantation',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-RegisteredPlantation',
                        fieldLabel: lang('Farmer Plantation Registered'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.RegisteredPlantation
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-RegisteredPlantationHectares',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-RegisteredPlantationHectares',
                        fieldLabel: lang('Farmer Plantation (Ha)'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.RegisteredPlantationHectares
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-ResponSourcingFarmers',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-ResponSourcingFarmers',
                        fieldLabel: lang('Farmers active in Responsible Sourcing'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.ResponSourcingFarmers
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-TraceTransaction',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-TraceTransaction',
                        fieldLabel: lang('Traceability Transactions'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.TraceTransaction
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PlatformUsers',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-PlatformUsers',
                        fieldLabel: lang('Platform Users'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.PlatformUsers
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-RegisteredSME',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-RegisteredSME',
                        fieldLabel: lang('Small and Medium Enterprises Registered'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.RegisteredSME
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-FarmXUsers',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-FarmXUsers',
                        fieldLabel: lang('FarmXtension Users'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.FarmXUsers
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-FarmGateUsers',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-FarmGateUsers',
                        fieldLabel: lang('FarmGate Users'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.FarmGateUsers
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-FarmRetailUsers',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-FarmRetailUsers',
                        fieldLabel: lang('FarmRetail Users'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.FarmRetailUsers
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-FarmCloudUsers',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-FarmCloudUsers',
                        fieldLabel: lang('FarmCloud Users'),
                        labelWidth: labelWidth,
                        allowNegative: false,
                        minValue: 0,
                        value: thisObj.viewVar.FarmCloudUsers
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls:'Sfr_BtnFormBlue',
            overCls:'Sfr_BtnFormBlue-Hover',
            text: lang('Save'),
            id: 'Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form-BtnSave',
            handler: function () {
                var FormNya = Ext.getCmp('Koltiva.view.Dboard.WinFormInputKpiTargetGeneral-Form').getForm();
                if (FormNya.isValid()) {

                    FormNya.submit({
                        url: m_api + '/kpi_general/kpi_target',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //form reset
                            FormNya.reset();

                            thisObj.viewVar.CallerStore.load();

                            //tutup popup
                            thisObj.close();
                        },
                        failure: function(rp, o) {
                            try {
                                var r = Ext.decode(rp.responseText);
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                            catch(err) {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'Connection Error',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        }
                    });

                }else{
                    Ext.MessageBox.show({
                        title: lang('Attention'),
                        msg: lang('Form not complete yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
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
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});