/*
* @Author: nikolius
* @Date:   2018-07-10 13:45:27
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-11 13:17:25
*/

/*
    Param2 yg diperlukan ketika load View ini
    - AuditIMSManager
    - FarmerID
    - GardenNr
    - SurveyNr
    - Certification
    - CallerStore
    - OpsiDisplay
    - ICSDate
*/
function checkImageExists(imageUrl, callBack) {
    var imageData = new Image();
    imageData.onload = function() {
        callBack(true);
    };
    imageData.onerror = function() {
        callBack(false);
    };
    imageData.src = imageUrl;
}

Ext.define('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification',
    title: lang('New Plantation'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '60%',
    height: '80%',
    cls: 'Sfr_LayoutPopupWindows',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var FormNya = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form').getForm();
            FormNya.reset();

            if(thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view'){
            	//Set Default
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DateCollection').setReadOnly(true);

            	//Btn Save
            	if(thisObj.viewVar.opsiDisplay == 'view'){
                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-BtnSave').setVisible(false);
                }

                //load formnya
                FormNya.load({
                    url: m_api + '/plot_survey/survey_certification_open',
                    method: 'GET',
                    params: {
                        SurveyID: thisObj.viewVar.SurveyID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        
                        //Proses Draw Map
                        thisObj.DrawMap();

                        checkImageExists(r.data.DocumentWritten, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWritten').setSrc(r.data.DocumentWritten);
                            } else {
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWritten').setSrc(m_api_base_url + '/images/no-image-icon.png');
                            }
                        });

                        checkImageExists(r.data.FarmPhoto, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhoto').setSrc(r.data.FarmPhoto);
                            } else {
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhoto').setSrc(m_api_base_url + '/images/no-image-icon.png');
                            }
                        });
                        
                    },
                    failure: function(form, action) {
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Failed to retrieve data',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }
        }
    },
    initComponent: function() {
        var thisObj = this;
        
        //Untuk Polygon
        thisObj.area_bounds = new google.maps.LatLngBounds();

        // STORE ================================ (Begin)
        var CmbCertProgram = Ext.create('Koltiva.store.ComboGeneral.CmbCertProgramsGeneral');
        var ComboStaffCertification = Ext.create('Koltiva.store.ComboGeneral.CmbStaffCertification');
        // STORE ================================ (End)

        //Items -------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                /*------------------------------------------------------------- Tab Garden (Begin) --------------------------------------------------------------------*/
                columnWidth: 1,
                layout:'form',
                items:[{
                    layout: 'column',
                    border: false,
                    items:[{
                        columnWidth: 0.495,
                        style:'padding-right:25px;',
                        layout:'form',
                        items:[{
                            xtype: 'hiddenfield',
                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-SurveyID',
                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-SurveyID'
                        },{
                            xtype: 'datefield',
                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DateCollection',
                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DateCollection',
                            fieldLabel: lang('Date Collection'),
                            allowBlank: false,
                            labelAlign:'top',
                            format: 'Y-m-d'
                        },{
                            fieldLabel: lang('Do you plan to acquire or establish a new palm oil plantation'),
                            xtype: 'radiogroup',
                            labelWidth: 260,
                            labelAlign:'top',
                            columns: 2,
                            allowBlank: false,
                            msgTarget: 'side',
                            items:[{
                                boxLabel: lang('Yes'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-PlanAcquireNewPalmOil',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-PlanAcquireNewPalmOil1',
                                listeners:{
                                    change: function(){
                                        if(this.checked == true){
                                            Ext.getCmp('LocalAreaPlannedPanel').setDisabled(false);
                                            Ext.getCmp('ObtainConsentPanel').setDisabled(false);
                                            Ext.getCmp('InvolveLocalPeoplePanel').setDisabled(false);                                                    
                                            Ext.getCmp('AcquireCoercingPanel').setDisabled(false);                                                    
                                            Ext.getCmp('StartDevPlantationPanel').setDisabled(false);                                                    
                                            Ext.getCmp('ProvideOwnerRelevantInformPanel').setDisabled(false);
                                            Ext.getCmp('HCVHCSApprocahPanel').setDisabled(false);
                                            Ext.getCmp('WrittenAgreementPanel').setDisabled(false);                                                    
                                            Ext.getCmp('CharacteristicEstablishmentPanel').setDisabled(false);                                                    
                                        }else{
                                            Ext.getCmp('LocalAreaPlannedPanel').setDisabled(true);
                                            Ext.getCmp('ObtainConsentPanel').setDisabled(true);
                                            Ext.getCmp('InvolveLocalPeoplePanel').setDisabled(true);
                                            Ext.getCmp('AcquireCoercingPanel').setDisabled(true);
                                            Ext.getCmp('StartDevPlantationPanel').setDisabled(true);
                                            Ext.getCmp('ProvideOwnerRelevantInformPanel').setDisabled(true);
                                            Ext.getCmp('HCVHCSApprocahPanel').setDisabled(true);
                                            Ext.getCmp('WrittenAgreementPanel').setDisabled(true);
                                            Ext.getCmp('CharacteristicEstablishmentPanel').setDisabled(true);
                                        }
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('No'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-PlanAcquireNewPalmOil',
                                inputValue: '2',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-PlanAcquireNewPalmOil2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            fieldLabel: lang('Are there local people next to the owner using the area planned for plantation'),
                            xtype: 'radiogroup',
                            labelWidth: 260,
                            labelAlign:'top',
                            disabled:true,
                            id:'LocalAreaPlannedPanel',
                            columns: 2,
                            allowBlank: false,
                            msgTarget: 'side',
                            items:[{
                                boxLabel: lang('Yes'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-LocalAreaPlanned',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-LocalAreaPlanned1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('No'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-LocalAreaPlanned',
                                inputValue: '2',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-LocalAreaPlanned2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            fieldLabel: lang('Have you obtained consent from the owner and the local people in the area  to use the land'),
                            xtype: 'radiogroup',
                            labelWidth: 260,
                            labelAlign:'top',
                            columns: 2,
                            disabled:true,
                            id:'ObtainConsentPanel',
                            allowBlank: false,
                            msgTarget: 'side',
                            items:[{
                                boxLabel: lang('Yes'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-ObtainConsent',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-ObtainConsent1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('No'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-ObtainConsent',
                                inputValue: '2',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-ObtainConsent2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            fieldLabel: lang('Have you involved the local people and owner in mapping the area to identify important places for them'),
                            xtype: 'radiogroup',
                            labelWidth: 260,
                            labelAlign:'top',
                            columns: 2,
                            allowBlank: false,
                            disabled:true,
                            id:'InvolveLocalPeoplePanel',
                            msgTarget: 'side',
                            items:[{
                                boxLabel: lang('Yes'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-InvolveLocalPeople',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-InvolveLocalPeople1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('No'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-InvolveLocalPeople',
                                inputValue: '2',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-InvolveLocalPeople2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            fieldLabel: lang('Have you acquired the area without coercing the previous owner or local people using the area'),
                            xtype: 'radiogroup',
                            labelWidth: 260,
                            labelAlign:'top',
                            columns: 2,
                            allowBlank: false,
                            disabled:true,
                            id:'AcquireCoercingPanel',
                            msgTarget: 'side',
                            items:[{
                                boxLabel: lang('Yes'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-AcquireCoercing',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-AcquireCoercing1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('No'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-AcquireCoercing',
                                inputValue: '2',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-AcquireCoercing2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            fieldLabel: lang('Did you start any development on the plantation before receiving consent from the previous owner and local people'),
                            xtype: 'radiogroup',
                            labelWidth: 260,
                            labelAlign:'top',
                            columns: 2,
                            disabled:true,
                            id:'StartDevPlantationPanel',
                            allowBlank: false,
                            msgTarget: 'side',
                            items:[{
                                boxLabel: lang('Yes'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-StartDevPlantation',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-StartDevPlantation1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('No'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-StartDevPlantation',
                                inputValue: '2',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-StartDevPlantation2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            fieldLabel: lang('Did you provide the owner and the local people in the area with all the relevant information to understand their costs and benefits for the plantation establishment'),
                            xtype: 'radiogroup',
                            labelWidth: 260,
                            labelAlign:'top',
                            columns: 2,
                            allowBlank: false,
                            disabled:true,
                            id:'ProvideOwnerRelevantInformPanel',
                            msgTarget: 'side',
                            items:[{
                                boxLabel: lang('Yes'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-ProvideOwnerRelevantInform',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-ProvideOwnerRelevantInform1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('No'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-ProvideOwnerRelevantInform',
                                inputValue: '2',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-ProvideOwnerRelevantInform2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            fieldLabel: lang('have you applied a simplifed combined hcv-hcs approach'),
                            xtype: 'radiogroup',
                            labelWidth: 260,
                            labelAlign:'top',
                            columns: 2,
                            allowBlank: false,
                            disabled:true,
                            id:'HCVHCSApprocahPanel',
                            msgTarget: 'side',
                            items:[{
                                boxLabel: lang('Yes'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-HCVHCSApprocah',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-HCVHCSApprocah1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('No'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-HCVHCSApprocah',
                                inputValue: '2',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-HCVHCSApprocah2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            xtype: 'panel',
                            id: 'map_polygon',
                            height: 300,
                            style:'border:1px solid green;'
                        }]
                    },{
                        columnWidth: 0.495,
                        style:'padding-right:25px;',
                        layout:'form',
                        items:[{
                            layout:'column',
                            border:false,
                            items:[{
                                columnWidth: 1,
                                border: false,
                                layout:{
                                    type:'hbox',
                                    pack:'end'
                                },
                                items:[{
                                    xtype: 'image',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhoto',
                                    width: '300px',
                                    height:'200px',
                                    src: m_api_base_url + '/images/no-image-icon.png'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhotoOld',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhotoOld',
                                    inputType: 'hidden'
                                }]
                            }]
                        },{
                            xtype: 'fileuploadfield',
                            fieldLabel: lang('Farm Photo'),
                            labelWidth: 230,
                            labelAlign:'top',
                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhotoInput',
                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhotoInput',
                            buttonText: 'Browse',
                            listeners: {
                                'change': function (fb, v) {
                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form').getForm().submit({
                                        url: m_api + '/plot_survey/farm_photo',
                                        clientValidation: false,
                                        params: {
                                            opsiDisplay: thisObj.viewVar.opsiDisplay,
                                            MemberID: thisObj.viewVar.MemberID,
                                            SurveyID: Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-SurveyID').getValue(),
                                        },
                                        waitMsg: 'Sending Photo...',
                                        success: function (fp, o) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhoto').setSrc(o.result.file);
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhotoOld').setValue(o.result.filepath);
                                        }
                                    });
                                }
                            }
                        },{
                            layout:'column',
                            border:false,
                            items:[{
                                columnWidth: 1,
                                border: false,
                                layout:{
                                    type:'hbox',
                                    pack:'end'
                                },
                                items:[{
                                    xtype: 'image',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWritten',
                                    width: '300px',
                                    height:'200px',
                                    src: m_api_base_url + '/images/no-image-icon.png'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWrittenOld',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWrittenOld',
                                    inputType: 'hidden'
                                }]
                            }]
                        },{
                            xtype: 'fileuploadfield',
                            fieldLabel: lang('Document written agreement with the owner and local people'),
                            labelWidth: 230,
                            labelAlign:'top',
                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWrittenInput',
                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWrittenInput',
                            buttonText: 'Browse',
                            listeners: {
                                'change': function (fb, v) {
                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form').getForm().submit({
                                        url: m_api + '/plot_survey/document_written',
                                        clientValidation: false,
                                        params: {
                                            opsiDisplay: thisObj.viewVar.opsiDisplay,
                                            MemberID: thisObj.viewVar.MemberID,
                                            SurveyID: Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-SurveyID').getValue(),
                                        },
                                        waitMsg: 'Sending Photo...',
                                        success: function (fp, o) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWritten').setSrc(o.result.file);
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWrittenOld').setValue(o.result.filepath);
                                        },
                                        failure: function(fp, o){
                                            var pesanNya;
                                            if(o.result.message != undefined){
                                                pesanNya = o.result.message;
                                            }else{
                                                pesanNya = lang('Connection error');
                                            }
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: pesanNya,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });
                                }
                            }
                        },{
                            fieldLabel: lang('Do you have a written agreement from the owner and the local people using the area'),
                            xtype: 'radiogroup',
                            labelWidth: 260,
                            columns: 2,
                            allowBlank: false,
                            labelAlign:'top',
                            disabled:true,
                            id:'WrittenAgreementPanel',
                            msgTarget: 'side',
                            items:[{
                                boxLabel: lang('Yes'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-WrittenAgreement',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-WrittenAgreement1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('No'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-WrittenAgreement',
                                inputValue: '2',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-WrittenAgreement2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            fieldLabel: lang('Characteristics of new plantation establishment'),
                            xtype: 'checkboxgroup',
                            labelWidth: 260,
                            columns: 2,
                            allowBlank: false,
                            labelAlign:'top',
                            disabled:true,
                            id:'CharacteristicEstablishmentPanel',
                            msgTarget: 'side',
                            items:[{
                                boxLabel: lang('Bare Land / Fallow'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-BareLandPlantationArea',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-BareLandPlantationArea',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('Food Crops'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FoodCropsPlantationArea',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FoodCropsPlantationArea',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('Mangrove'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-MangrovePlantationArea',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-MangrovePlantationArea',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('Other Plantaton (Rubber,Coffe,etc)'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-OtherPlantationArea',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-OtherPlantationArea',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('Oil Palm Plantation'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-OilPalmPlantationArea',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-OilPalmPlantationArea',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('Forest'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-ForestPlantationArea',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-ForestPlantationArea',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('Infrastructure'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-InfrastructurePlantationArea',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-InfrastructurePlantationArea',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('Step Slopes'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-StepSlopesPlantationArea',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-StepSlopesPlantationArea',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('I Do Not Know'),
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DontKnowPlantationArea',
                                inputValue: '1',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DontKnowPlantationArea',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            xtype: 'textfield',
                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-LandUseStatus',
                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-LandUseStatus',
                            fieldLabel: lang('Land Use Status'),
                            labelWidth: 250,
                            labelAlign:'top',
                            readOnly: true
                        }]
                    }]
                }]
            }]
        }];
        //Items -------------------------------------------------------------- (End)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls:'Sfr_BtnFormBlue',
            overCls:'Sfr_BtnFormBlue-Hover',
            text: lang('Save'),
            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-BtnSave',
            handler: function () {
                var FormNya = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form').getForm();
                var FormValidOrNot = FormNya.isValid();

                if (FormValidOrNot ==  true) {
                    FormNya.submit({
                        url: m_api + '/plot_survey/survey_certification',
                        method:'POST',
                        params: {
                            opsiDisplay: thisObj.viewVar.opsiDisplay,
                            MemberID:thisObj.viewVar.MemberID
                        },
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

                            //refresh store yg manggil
                            thisObj.viewVar.callerStore.load();

                            //tutup popup
                            thisObj.close();
                        },
                        failure: function(fp, o){
                            var pesanNya;
                            if(o.result.message != undefined){
                                pesanNya = o.result.message;
                            }else{
                                pesanNya = lang('Connection error');
                            }
                            Ext.MessageBox.show({
                                title: 'Attention',
                                msg: pesanNya,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: 'Attention',
                        msg: 'Form not valid yet',
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
    },
    DrawMap: function(){
        var thisObj = this;

        //Set Default
        $('#map_polygon').gmap3({
            map: {
                options: {
                    center: [-2.0836809794977484, 113.63967449468988],
                    zoom: 5,
                    //mapTypeControl: false,
                    panControl: true,
                    zoomControl: true,
                    //scaleControl: false,
                    streetViewControl: false,
                    rotateControl: false,
                    rotateControlOptions: false,
                    overviewMapControl: false,
                    OverviewMapControlOptions: false,
                    scrollwheel: true,
                    mapTypeId: google.maps.MapTypeId.HYBRID
                }
            }
        });

        //Call Request
        Ext.Ajax.request({
            waitMsg: 'Please Wait',
            url: m_api + '/plot_survey/survey_certification_open_polygon',
            method: 'GET',
            params: {
                SurveyID: thisObj.viewVar.SurveyID
            },
            success: function(fp,o) {
                //Clear dl sebelum gambar
                $('#map_polygon').gmap3({
                    clear: {}
                });
                var data = Ext.decode(fp.responseText);

                // Set Marker
                if ((data.Latitude !== '' && data.Longitude !== '') && (data.Latitude !== null && data.Longitude !== null)) {
                    var icon_path = m_api + 'images/map/';
                    $("#map_polygon").gmap3({
                        marker: {
                            values: [{
                                latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
                                tag: 'garden',
                                options: {
                                    icon: icon_path + "farmer.png"
                                },
                            }]
                        }
                    });
                    var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
                    thisObj.area_bounds.extend(myLatLng);
                    $('#map_polygon').gmap3("get").fitBounds(thisObj.area_bounds);
                }

                //Show Polygon
                thisObj.area = data.PolygonGeoJson;
                thisObj.ShowPolygon();

                // Ext.getCmp('btnPolygonEdit').setVisible(false);
                // Ext.getCmp('btnPolygonDownloadKML').show();
            },
            failure: function(response, o) {
                Ext.getCmp('Koltiva.view.Farmer.WinFormGardenSurvey-Form-MapSource').setValue('-');
                Ext.getCmp('Koltiva.view.Farmer.WinFormGardenSurvey-Form-GardenHaUnPolygon').setValue('-');
            }
        });

    },
    ShowPolygon: function(){
        var thisObj = this;

        var geoObject = JSON.parse(thisObj.area);
        console.log(geoObject);
        var coord = geoObject.coordinates[0];
        var area = [];
        $.each(coord, function(i, v) {
            area[i] = [v[1], v[0]];
        });

        $("#map_polygon").gmap3({
            polygon: {
                tag: 'Area',
                options: {
                    strokeColor: "yellow",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "yellow",
                    fillOpacity: 0.35,
                    paths: area
                },
            }
        });
    }
});