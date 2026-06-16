/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Apr 29 2019
 *  File : WinFormPlotSurveyGar.js
 *******************************************/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
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

function calcTreeTbmTmTr(){
    var treeTBM = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTBM').getValue());
    var treeTR = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTR').getValue());
    var treeTM = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTM').getValue());
    var ha = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaHa').getValue());

    var total = treeTBM + treeTR + treeTM;
    var treeperha = total / ha;

    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').setValue(total);
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTRPerHa').setValue(treeperha);

    //validasi dengan TbmTmTr dengan planting material
//    var totPlantingMate = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').getValue());
//    if(total != totPlantingMate){
//        Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').addCls('notif-red');
//        Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').addCls('notif-red');
//    }else{
//        Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').removeCls('notif-red');
//        Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').removeCls('notif-red');
//    }
}

/*function calcTotalTreePlantingMaterial(){
    var totalTrees;
    var totalTreesMarihat = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateMarihatNr').getValue());
    var totalTreesDumpy = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDumpyNr').getValue());
    var totalTreesLonsum = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateLonsumNr').getValue());
    var totalTreesSima = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSimalungunNr').getValue());
    var totalTreesDanimas = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDanimasNr').getValue());
    var totalTreesSriwijaya = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSriwijayaNr').getValue());
    var totalTreesSocfin = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSocfinNr').getValue());
    var totalTreesOther = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateOtherNr').getValue());
    var totalTreesDoNotKnow = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDoNotKnowNr').getValue());

    if(isNaN(totalTreesMarihat)) totalTreesMarihat = 0;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateMarihatNr').isDisabled() == true) totalTreesMarihat = 0;

    if(isNaN(totalTreesDumpy)) totalTreesDumpy = 0;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDumpyNr').isDisabled() == true) totalTreesDumpy = 0;

    if(isNaN(totalTreesLonsum)) totalTreesLonsum = 0;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateLonsumNr').isDisabled() == true) totalTreesLonsum = 0;

    if(isNaN(totalTreesSima)) totalTreesSima = 0;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSimalungunNr').isDisabled() == true) totalTreesSima = 0;

    if(isNaN(totalTreesDanimas)) totalTreesDanimas = 0;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDanimasNr').isDisabled() == true) totalTreesDanimas = 0;

    if(isNaN(totalTreesSriwijaya)) totalTreesSriwijaya = 0;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSriwijayaNr').isDisabled() == true) totalTreesSriwijaya = 0;

    if(isNaN(totalTreesSocfin)) totalTreesSocfin = 0;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSocfinNr').isDisabled() == true) totalTreesSocfin = 0;

    if(isNaN(totalTreesOther)) totalTreesOther = 0;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateOtherNr').isDisabled() == true) totalTreesOther = 0;

    if(isNaN(totalTreesDoNotKnow)) totalTreesDoNotKnow = 0;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDoNotKnowNr').isDisabled() == true) totalTreesDoNotKnow = 0;

    totalTrees = totalTreesMarihat + totalTreesDumpy + totalTreesLonsum + totalTreesSima + totalTreesDanimas + totalTreesSriwijaya + totalTreesSocfin + totalTreesOther + totalTreesDoNotKnow;
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').setValue(totalTrees);

    var totTbmTmTr = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').getValue());
    if(totalTrees != totTbmTmTr){
        Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').addCls('notif-red');
        Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').addCls('notif-red');
    }else{
        Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').removeCls('notif-red');
        Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').removeCls('notif-red');
    }
}*/

function calcPalmProduction(){
    var HighSeasonProduction, LowSeasonProduction, AnnualProduction, PlantationProductivity;

    var HarvestRateDaysHighSeason = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysHighSeason').getValue());
    var HarvestRateDaysLowSeason = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysLowSeason').getValue());
    var AverageProdHighSeason = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageProdHighSeason').getValue());
    var AverageProdLowSeason = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageProdLowSeason').getValue());
    var NrHighSeasonMonths = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-NrHighSeasonMonths').getValue());
    var NrLowSeasonMonths = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-NrLowSeasonMonths').getValue());
    var AreaHa = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaHa').getValue());

    if(isNaN(HarvestRateDaysHighSeason)) HarvestRateDaysHighSeason = 0;
    if(isNaN(HarvestRateDaysLowSeason)) HarvestRateDaysLowSeason = 0;
    if(isNaN(AverageProdHighSeason)) AverageProdHighSeason = 0;
    if(isNaN(AverageProdLowSeason)) AverageProdLowSeason = 0;
    if(isNaN(NrHighSeasonMonths)) NrHighSeasonMonths = 0;
    if(isNaN(NrLowSeasonMonths)) NrLowSeasonMonths = 0;

    HighSeasonProduction = (30/HarvestRateDaysHighSeason) * NrHighSeasonMonths * AverageProdHighSeason;
    LowSeasonProduction = (30/HarvestRateDaysLowSeason) * NrLowSeasonMonths * AverageProdLowSeason;
    AnnualProduction = HighSeasonProduction + LowSeasonProduction;
    PlantationProductivity = AnnualProduction / AreaHa;

    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HighSeasonProduction').setValue(HighSeasonProduction);
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LowSeasonProduction').setValue(LowSeasonProduction);
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AnnualProduction').setValue(AnnualProduction);
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationProductivity').setValue(PlantationProductivity);
}

function calcTotalUsageHerbi(){
    var PeFreqHerbi = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqHerbi').getValue());
    var PeDoseHerbi = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseHerbi').getValue());

    var totalUsage = PeFreqHerbi * PeDoseHerbi;
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeTotalUsageHerbi').setValue(totalUsage);
}

function calcTotalUsageInsec(){
    var PeFreqInsec = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqInsec').getValue());
    var PeDoseInsec = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseInsec').getValue());

    var totalUsage = PeFreqInsec * PeDoseInsec;
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeTotalUsageInsec').setValue(totalUsage);
}

function calcTotalUsageFungi(){
    var PeFreqFungi = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqFungi').getValue());
    var PeDoseFungi = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseFungi').getValue());

    var totalUsage = PeFreqFungi * PeDoseFungi;
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeTotalUsageFungi').setValue(totalUsage);
}

function calcNumberHighLowSeason(){
    var cekMonth = 0;

    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonJan').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonFeb').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonMar').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonApr').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonMay').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonJun').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonJul').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonAug').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonSep').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonOct').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonNov').checked == true) cekMonth++;
    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonDec').checked == true) cekMonth++;

    var highSeasonMonth = parseInt(cekMonth);
    var lowSeasonMonth = 12 - highSeasonMonth;

    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-NrLowSeasonMonths').setValue(lowSeasonMonth);
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-NrHighSeasonMonths').setValue(highSeasonMonth);
}

function checkOwnerPlantationInput(){
    var landOwnership = false; var ownerOfTheGarden = true;

    if(
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType2').checked == true ||
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType3').checked == true ||
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType4').checked == true
    )
        landOwnership = true;

    if(
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden1').checked == true
    )
        ownerOfTheGarden = false;

    if(ownerOfTheGarden == true){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationInformation').setDisabled(false);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationNameText').setDisabled(false);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationLocationText').setDisabled(false);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationPhoneText').setDisabled(false);
    }else{
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationInformation').setDisabled(true);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationNameText').setDisabled(true);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationLocationText').setDisabled(true);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationPhoneText').setDisabled(true);
    }

}

function checkIfRegisFarmerChosen(){
    if(
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden1').checked == true
    ){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationNameText').setValue(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey').memberVar.MemberName);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationLocationText').setValue(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey').memberVar.MemberLocation);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationPhoneText').setValue(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey').memberVar.MemberHandphone);
    }else{
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationNameText').setValue('');
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationLocationText').setValue('');
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationPhoneText').setValue('');
    }

    if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ManagerOfTheGarden1').checked == true){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmManager').setValue(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey').memberVar.MemberName);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmManager').setDisabled(true);
    }else{
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmManager').setValue('');
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmManager').setDisabled(false);
    }
    
}

/*function autofillPalmTreeCheck(){
    if(parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateMarihatNr').getValue()) > 0){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateMarihat').setValue(true);
    }
    if(parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDumpyNr').getValue()) > 0){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDumpy').setValue(true);
    }
    if(parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateLonsumNr').getValue()) > 0){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateLonsum').setValue(true);
    }
    if(parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSimalungunNr').getValue()) > 0){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSimalungun').setValue(true);
    }
    if(parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDanimasNr').getValue()) > 0){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDanimas').setValue(true);
    }
    if(parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSriwijayaNr').getValue()) > 0){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSriwijaya').setValue(true);
    }
    if(parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSocfinNr').getValue()) > 0){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSocfin').setValue(true);
    }
    if(parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateOtherNr').getValue()) > 0){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateOther').setValue(true);
    }
    if(parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDoNotKnowNr').getValue()) > 0){
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDoNotKnow').setValue(true);
    }
}*/
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.PlotSurvey.WinFormPlotSurveyGar' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey',
    title: lang('Garden Survey Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '98%',
    height: '92%',
    intMinTreePerHa: 128,
    intMaxTreePerHa: 160,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store --------------------------------------------------------------------------------------------------------------- (begin)
        var cmb_survey_nr = Ext.create('Koltiva.store.PlotSurvey.CmbSurveyNr');

        thisObj.cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        thisObj.cmb_province.load();
        thisObj.cmb_district = Ext.create('Koltiva.store.Grower.CmbDistrict');
        thisObj.cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        thisObj.cmb_village = Ext.create('Koltiva.store.Grower.CmbVillage');
        thisObj.cmb_respondent = Ext.create('Koltiva.store.Grower.CmbRespondent');
        thisObj.cmb_inactive_reason = Ext.create('Koltiva.store.Grower.CmbInactiveReasonPlantation');
        thisObj.cmb_water_body_far = Ext.create('Koltiva.store.Grower.CmbWaterBodyFar');

        var cmb_ownership_document = Ext.create('Koltiva.store.PlotSurvey.CmbOwnershipDocument');
        var cmb_business_model = Ext.create('Koltiva.store.PlotSurvey.CmbBusinessModel');
        var cmb_plantation_condition_est = Ext.create('Koltiva.store.PlotSurvey.CmbPlantationConditionEst');
        var cmb_soil_type = Ext.create('Koltiva.store.PlotSurvey.CmbSoilType');

        var cmb_donotknow_number = Ext.create('Koltiva.store.PlotSurvey.CmbDoNotKnowNumber');
        var cmb_fertilizer = Ext.create('Koltiva.store.PlotSurvey.CmbFertilizer');
        var cmb_wage_period = Ext.create('Koltiva.store.PlotSurvey.CmbWagePeriod');

        var cmb_year_option = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption');
        cmb_year_option.setStoreVar({yearRange:100});
        cmb_year_option.load();
        //store --------------------------------------------------------------------------------------------------------------- (end)


        //items ---------------------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    style: '',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.495,
                            style:'padding-right:25px;',
                            layout:'form',
                            items:[{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberID',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberID'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberDisplayID',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberDisplayID',
                                fieldLabel: lang('Farmer ID'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberName',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberName',
                                fieldLabel: lang('Farmer Name'),
                                readOnly: true
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Respondent',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Respondent',
                                store: thisObj.cmb_respondent,
                                fieldLabel: lang('Respondent'),
                                allowBlank: false,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                xtype: 'numberfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlotNr',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlotNr',
                                fieldLabel: lang('Garden Nr'),
                                allowBlank: false,
                                minValue: 1
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SurveyNr',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SurveyNr',
                                store: cmb_survey_nr,
                                fieldLabel: lang('Survey Nr'),
                                allowBlank: false,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style:'padding-left:15px;',
                            items:[{
                                xtype: 'datefield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DateCollection',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DateCollection',
                                fieldLabel: lang('Date Collection'),
                                allowBlank: false,
                                format: 'Y-m-d H:i:s'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-CreatedByLabel',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-CreatedByLabel',
                                fieldLabel: lang('Enumerator'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ModifiedByLabel',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ModifiedByLabel',
                                fieldLabel: lang('Modified by'),
                                readOnly: true
                            }]
                        }]
                    }]
                }]
            },{
                xtype: 'tabpanel',
                flex: 1,
                margin: 2,
                activeTab: 0,
                plain: true,
                cls:'tabSce',
                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TabPanel',
                items:[{
                    xtype: 'panel',
                    title: lang('Garden'),
                    padding: '0 10 10 10',
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
                                    xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Plantation Status')+'</div>'
                                },{
                                    fieldLabel: lang('Farm Status'),
                                    xtype: 'radiogroup',
                                    labelWidth: 260,
                                    columns: 1,
                                    allowBlank: false,
                                    msgTarget: 'side',
                                    items:[{
                                        boxLabel: lang('Active'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmStatus',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmStatus1',
                                        listeners:{
                                            change: function(){                                                
                                                if(this.checked == true){
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InactiveReason').setDisabled(true);
                                                }
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Inactive'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmStatus',
                                        inputValue: '2',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmStatus2',
                                        listeners:{
                                            change: function(){                                                
                                                if(this.checked == true){
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InactiveReason').setDisabled(false);
                                                }
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Refused to be Surveyed'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmStatus',
                                        inputValue: '3',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmStatus3',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InactiveReason').setDisabled(false);
                                                }
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InactiveReason',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InactiveReason',
                                    store: thisObj.cmb_inactive_reason,
                                    fieldLabel: lang('Inactive Reason'),
                                    queryMode: 'local',
                                    disabled:true,
                                    displayField: 'label',
                                    valueField: 'id',
                                    listeners:{
                                        change:function(cb, nv, ov){
                                            if(nv == 3){
                                                Ext.getCmp('SwitchCommodityPanel').setDisabled(false);
                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwithCommodityAreaHa').setDisabled(false);
                                            }else{
                                                Ext.getCmp('SwitchCommodityPanel').setDisabled(true);
                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwithCommodityAreaHa').setDisabled(true);
                                            }
                                        }
                                    }
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:10px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        id:'SwitchCommodityPanel',
                                        disabled:true,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 1,
                                            layout:'form',
                                            style:'margin-bottom:-10px;padding-top:0px;',
                                            items:[{
                                                xtype:'label',
                                                cls: 'x-form-item-label',
                                                text: lang('Commodity')
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Corn'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityCorn',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityCorn',
                                                listeners:{
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Cocoa'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityCocoa',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityCocoa',
                                                listeners:{
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Rubber'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityRubber',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityRubber',
                                                listeners:{
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Clove'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityClove',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityClove',
                                                listeners:{
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Rice'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityRice',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityRice',
                                                listeners:{
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Fruits'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityFruits',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityFruits',
                                                listeners:{
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Timber'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityTimber',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityTimber',
                                                listeners:{
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Other'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityOther',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwitchCommodityOther',
                                                listeners:{
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwithCommodityAreaHa',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SwithCommodityAreaHa',
                                    fieldLabel: lang('Commodity Area (Ha)'),
                                    labelWidth: 250,
                                    allowNegative: false,
                                    disabled:true,
                                    minValue: 0,
                                    allowBlank: true,
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-RecipientDealer',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-RecipientDealer',
                                    fieldLabel: lang('Recipient Dealer'),
                                    hidden:true,
                                    labelWidth:200,
                                },{
                                    xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Location and Size of Plantation')+'</div>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaHa',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaHa',
                                    fieldLabel: lang('Area of Garden (Ha)'),
                                    labelWidth: 250,
                                    allowNegative: false,
                                    minValue: 0.1,
                                    allowBlank: false,
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            calcPalmProduction();
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantedAreaHa',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantedAreaHa',
                                    fieldLabel: lang('Planted Area (Ha)'),
                                    labelWidth: 250,
                                    allowNegative: false,
                                    minValue: 0.1,
                                    allowBlank: false,
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            var GardenArea = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaHa').getValue();
                                            if(nv > GardenArea){
                                                Ext.MessageBox.show({
                                                    title: 'Attention',
                                                    msg: lang('Planted area cannot be larger than Area of plantation'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-info'
                                                });
                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantedAreaHa').setValue('');
                                            }
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ProvinceID',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ProvinceID',
                                    store: thisObj.cmb_province,
                                    fieldLabel: lang('Province'),
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            thisObj.cmb_district.load({
                                                params: {
                                                    ProvinceID: nv
                                                }
                                            });
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DistrictID').setValue('');
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SubDistrictID').setValue('');
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VillageID').setValue('');
                                        }
                                    }
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DistrictID',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DistrictID',
                                    store: thisObj.cmb_district,
                                    fieldLabel: lang('District'),
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            thisObj.cmb_subdistrict.load({
                                                params: {
                                                    DistrictID: nv
                                                }
                                            });
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SubDistrictID').setValue('');
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VillageID').setValue('');
                                        }
                                    }
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SubDistrictID',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SubDistrictID',
                                    store: thisObj.cmb_subdistrict,
                                    fieldLabel: lang('Subdistrict'),
                                    allowBlank: false,
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            thisObj.cmb_village.load({
                                                params: {
                                                    SubdistrictID: nv
                                                }
                                            });
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VillageID').setValue('');
                                        }
                                    }
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VillageID',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VillageID',
                                    store: thisObj.cmb_village,
                                    fieldLabel: lang('Village'),
                                    allowBlank: false,
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Latitude',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Latitude',
                                    allowNegative: false,
                                    fieldLabel: lang('Latitude'),
                                    allowBlank: false,
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Longitude',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Longitude',
                                    allowNegative: false,
                                    fieldLabel: lang('Longitude'),
                                    allowBlank: false,
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaPolygon',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaPolygon',
                                    fieldLabel: lang('Area of Garden Polygon (Ha)'),
                                    labelWidth: 250,
                                    allowNegative: false,
                                    minValue: 0,
                                    readOnly: true
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandUseStatus',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandUseStatus',
                                    fieldLabel: lang('Land Use Status'),
                                    labelWidth: 250,
                                    allowNegative: false,
                                    minValue: 0,
                                    readOnly: true
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenLength',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenLength',
                                    fieldLabel: lang('Length'),
                                    allowNegative: false,
                                    minValue: 0,
                                    hidden: true
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenWidth',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenWidth',
                                    fieldLabel: lang('Width'),
                                    allowNegative: false,
                                    minValue: 0,
                                    hidden: true
                                },{
                                    xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Plantation Borders')+'</div>'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-North',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-North',
                                    fieldLabel: lang('North'),
                                    labelWidth:200,
                                    allowBlank: true,
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-East',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-East',
                                    fieldLabel: lang('East'),
                                    labelWidth:200,
                                    allowBlank: true,
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-South',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-South',
                                    fieldLabel: lang('South'),
                                    labelWidth:200,
                                    allowBlank: true,
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-West',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-West',
                                    fieldLabel: lang('West'),
                                    labelWidth:200,
                                    allowBlank: true,
                                },{
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationInformation',
                                    xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Owner of this plantation information')+'</div>',
                                    disabled: true
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Owner of the Plantation')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Registered Farmer'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden1',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        checkIfRegisFarmerChosen();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Other People'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden3',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Family Members'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden2',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Do Not Know'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden4',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationNameText',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationNameText',
                                    fieldLabel: lang('Name of Plantation Owner'),
                                    labelWidth: 200,
                                    disabled: true,
                                    allowBlank: false,
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationLocationText',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationLocationText',
                                    fieldLabel: lang('Location'),
                                    disabled: true,
                                    hidden: true
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationPhoneText',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationPhoneText',
                                    fieldLabel: lang('Phone'),
                                    disabled: true,
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Plantation Manager')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Registered Farmer'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ManagerOfTheGarden',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ManagerOfTheGarden1',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        checkIfRegisFarmerChosen();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Other People'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ManagerOfTheGarden',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ManagerOfTheGarden3',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Family Members'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ManagerOfTheGarden',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ManagerOfTheGarden2',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Do Not Know'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ManagerOfTheGarden',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ManagerOfTheGarden4',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-RecipientDealer',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-RecipientDealer',
                                    fieldLabel: lang('Name of Recepient Dealer'),
                                    labelWidth: 200,
                                    disabled:true,
                                    allowBlank: false,
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmManager',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmManager',
                                    fieldLabel: lang('Name of Plantation Manager'),
                                    labelWidth: 200,
                                    disabled:true,
                                    allowBlank: false,
                                },
                                {
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Land Ownership')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Owned'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType1',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Rented'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType3',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Profit Sharing'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType2',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Others'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType4',
                                                listeners:{
                                                    change: function(){
                                                        checkOwnerPlantationInput();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandLegality',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandLegality',
                                    fieldLabel: lang('Land Legality'),
                                    hidden: true,
                                },{
                                    xtype: 'radiogroup',
                                    fieldLabel: lang('Ownership Document'),
                                    labelAlign: 'top',
                                    labelWidth: 260,
                                    columns: 2,
                                    allowBlank: false,
                                    msgTarget: 'side',
                                    items:[{
                                        boxLabel: lang('No Document'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc1',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('PanelSaksiPemilikLahan').setDisabled(false);
                                                }else{
                                                    Ext.getCmp('PanelSaksiPemilikLahan').setDisabled(true);
                                                }
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('SHM (Sertifikat Hak Milik) / Certificate'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc',
                                        inputValue: '3',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc3',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('SKGR (Surat Keterangan Ganti Rugi)'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc',
                                        inputValue: '5',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc5',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('SKT (Surat Keterangan Tanah)'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc',
                                        inputValue: '2',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('HGU (Hak Guna Usaha)'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc',
                                        inputValue: '4',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc4',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Segel'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc',
                                        inputValue: '7',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc7',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('AJB (Akta Jual Beli)'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc',
                                        inputValue: '8',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc8',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        xtype:'radiofield',
                                        boxLabel: lang('Other'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc',
                                        inputValue: '6',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc6',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocText').setDisabled(false);
                                                }else{
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocText').setDisabled(true);
                                                }
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocText',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocText',
                                    disabled: true,
                                    emptyText: lang('Other Text'),
                                },{
                                    xtype: 'radiogroup',
                                    fieldLabel: lang('Are You Willing to Share The Ownership Document With Us'),
                                    labelAlign: 'top',
                                    labelWidth: 260,
                                    columns: 2,
                                    allowBlank: false,
                                    msgTarget: 'side',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocShare',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocShare1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocShare',
                                        inputValue: '2',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocShare2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocNr',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocNr',
                                    fieldLabel: lang('Document Number'),
                                    labelAlign: 'top',
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
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDoc',
                                            width: '300px',
                                            height:'200px',
                                            src: m_api_base_url + '/images/no-image-icon.png'
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDocOld',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDocOld',
                                            inputType: 'hidden'
                                        }]
                                    }]
                                },{
                                    xtype: 'fileuploadfield',
                                    fieldLabel: lang('Photo of The Ownership Document'),
                                    labelWidth: 230,
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDocInput',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDocInput',
                                    buttonText: 'Browse',
                                    listeners: {
                                        'change': function (fb, v) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form').getForm().submit({
                                                url: m_api + '/plot_survey/photo_document_ownership',
                                                clientValidation: false,
                                                params: {
                                                    opsiDisplay: thisObj.viewVar.opsiDisplay,
                                                    MemberID: thisObj.viewVar.MemberID
                                                },
                                                waitMsg: 'Sending Photo...',
                                                success: function (fp, o) {
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDoc').setSrc(o.result.file);
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDocOld').setValue(o.result.filepath);
                                                }
                                            });
                                        }
                                    }
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        id: 'PanelSaksiPemilikLahan',
                                        disabled: true,
                                        layout:'form',
                                        items:[{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 1,
                                                layout:'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Is the ownership document in the name of the current owner ?')
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            style:'margin-top:-20px;padding-top:0px;',
                                            items:[{
                                                layout:'column',
                                                columnWidth: 1,
                                                style:'margin-top:-7px;padding-top:0px;',
                                                items:[{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerDocIsOwner',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerDocIsOwner1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerDocIsOwner',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerDocIsOwner2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('Do not know'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerDocIsOwner',
                                                        inputValue: '3',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerDocIsOwner3',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            }]
                                        },{
                                            xtype: 'radiogroup',
                                            fieldLabel: lang('Do you have witnesses to prove the plot ownership'),
                                            labelAlign: 'top',
                                            labelWidth: 260,
                                            columns: 2,
                                            allowBlank: false,
                                            msgTarget: 'side',
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarWitnessProveOwnership',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarWitnessProveOwnership1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarWitnessProveOwnership',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarWitnessProveOwnership2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarNameOfWitness',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarNameOfWitness',
                                            fieldLabel: lang('Name of the witness'),
                                            labelAlign: 'top',
                                        },{
                                            fieldLabel: lang('Owners relationship with the witness'),
                                            labelAlign: 'top',
                                            columns: 2,
                                            xtype: 'radiogroup',
                                            items: [{
                                                boxLabel: lang('Family'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarOwnerRelationship',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarOwnerRelationship1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }, {
                                                boxLabel: lang('Official witness'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarOwnerRelationship',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarOwnerRelationship2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Institution leader (village, farmer group, religion etc)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarOwnerRelationship',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarOwnerRelationship3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Other'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarOwnerRelationship',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GarOwnerRelationship4',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Does the farm have a STD-B (operational / business letter) ?')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSTDB',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSTDB1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSTDB',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSTDB2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Do not know'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSTDB',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSTDB3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Does the farm have a SPPL (Environmental Management Letter) ?')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSPPL',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSPPL1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSPPL',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSPPL2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Do not know'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSPPL',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HaveSPPL3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Business Model')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Independent'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BusinessModel',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BusinessModel1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Plasma (has existing contract with plantation)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BusinessModel',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BusinessModel3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Independent - Ex Plasma'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BusinessModel',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BusinessModel2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('How did you obtain the plantation ?')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Inheritance'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantation',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantation1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Convert Existing Plantation'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantation',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantation3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Purchased'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantation',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantation2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Received From Government (Transmigrate)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantation',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantation4',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-25px 0 0 0',
                                    items:[{
                                        columnWidth: 0.2,
                                        layout: 'form',
                                        items:[{
                                            xtype:'radiofield',
                                            boxLabel: lang('Lainnya'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantation',
                                            inputValue: '5',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantation5',
                                            listeners:{
                                                change: function(){
                                                    if(this.checked == true){
                                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantationText').setDisabled(false);
                                                    }else{
                                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantationText').setDisabled(true);
                                                    }
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.775,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantationText',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowObPlantationText',
                                            disabled: true,
                                            emptyText: lang('Other Text')
                                        }]
                                    }]
                                }]
                            },{
                                columnWidth: 0.5,
                                layout:'form',
                                style:'padding-left:15px;border-left: 1px dashed gray;',
                                items:[,{
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationHistoryAndCharacteristics',
                                    xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Plantation History and Characteristics')+'</div>',
                                    disabled: true
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Condition when establishing oil palm plantation')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Secondary Veg/Fallow'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Mangrove'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Oil Palm Plantation'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst',
                                                inputValue: '5',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst5',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('I don\'t know'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst',
                                                inputValue: '7',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst7',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Food Crops'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Other Plantation (rubber, coffee, etc.)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst4',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Forest'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst',
                                                inputValue: '6',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationConditionEst6',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FirstPlantingYear',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FirstPlantingYear',
                                    fieldLabel: lang('Year of first planting palm trees'),
                                    labelWidth: 260,
                                    store: cmb_year_option,
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    allowBlank: false,
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-YearPlantingCurrent',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-YearPlantingCurrent',
                                    fieldLabel: lang('Year of planting current oil palms'),
                                    labelWidth: 260,
                                    store: cmb_year_option,
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    allowBlank: false,
                                    listeners:{
                                        change:function(bb,nv,hh){
                                            if(nv < "2005"){
                                                // Ext.getCmp('AppliedHcvApproach').setDisabled(true);
                                                
                                            }else{
                                                // Ext.getCmp('AppliedHcvApproach').setDisabled(false);
                                            }
                                            if(nv < "2019"){
                                                // Ext.getCmp('AppliedHcsApproach').setDisabled(true);
                                            }else{
                                                // Ext.getCmp('AppliedHcsApproach').setDisabled(false);
                                            }

                                            var now = new Date().getFullYear();
                                            var age = now - nv;

                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageAgeTree').setValue(age);
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageAgeTree',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageAgeTree',
                                    fieldLabel: lang('Average age of trees on plantation? (years)'),
                                    labelWidth: 260,
                                    allowNegative: false,
                                    readOnly:true,
                                    minValue: 0
                                },{
                                    fieldLabel: lang('Soil Type'),
                                    xtype: 'radiogroup',
                                    labelWidth: 260,
                                    columns: 3,
                                    allowBlank: false,
                                    msgTarget: 'side',
                                    items:[{
                                        boxLabel: lang('Mineral'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilType',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilType1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Peat'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilType',
                                        inputValue: '2',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilType2',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    // Ext.getCmp('IssuesPeatLand').setDisabled(false);
                                                    // Ext.getCmp('BmpPeatland').setDisabled(false);
                                                }else{
                                                    // Ext.getCmp('IssuesPeatLand').setDisabled(true);
                                                    // Ext.getCmp('BmpPeatland').setDisabled(true);
                                                }
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Sandy'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilType',
                                        inputValue: '3',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilType3',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    fieldLabel: lang('Type of Topography Plantation'),
                                    xtype: 'radiogroup',
                                    labelWidth: 260,
                                    columns: 3,
                                    items:[{
                                        boxLabel: lang('Flat'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TopographyType',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TopographyType1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Moderate'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TopographyType',
                                        inputValue: '2',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TopographyType2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Steep'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TopographyType',
                                        inputValue: '3',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TopographyType3',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    fieldLabel: lang('Do You See Any Trace of Soil Erosion in Your Field'),
                                    xtype: 'radiogroup',
                                    labelWidth: 260,
                                    columns: 3,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilErotion',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilErotion1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilErotion',
                                        inputValue: '2',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilErotion2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
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
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotion',
                                            width: '300px',
                                            height:'200px',
                                            src: m_api_base_url + '/images/no-image-icon.png'
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotionOld',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotionOld',
                                            inputType: 'hidden'
                                        }]
                                    }]
                                },{
                                    xtype: 'fileuploadfield',
                                    fieldLabel: lang('Photo of Soil Erotion'),
                                    labelWidth: 230,
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotionInput',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotionInput',
                                    buttonText: 'Browse',
                                    listeners: {
                                        'change': function (fb, v) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form').getForm().submit({
                                                url: m_api + '/plot_survey/photo_soil_erotion',
                                                clientValidation: false,
                                                params: {
                                                    opsiDisplay: thisObj.viewVar.opsiDisplay,
                                                    MemberID: thisObj.viewVar.MemberID
                                                },
                                                waitMsg: 'Sending Photo...',
                                                success: function (fp, o) {
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotion').setSrc(o.result.file);
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotionOld').setValue(o.result.filepath);
                                                }
                                            });
                                        }
                                    }
                                },{
                                    fieldLabel: lang('Do You See Any Trace of Soil Accumulation in Your Field'),
                                    xtype: 'radiogroup',
                                    labelWidth: 260,
                                    columns: 3,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilAccumulation',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilAccumulation1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilAccumulation',
                                        inputValue: '2',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SoilAccumulation2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
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
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulation',
                                            width: '300px',
                                            height:'200px',
                                            src: m_api_base_url + '/images/no-image-icon.png'
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulationOld',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulationOld',
                                            inputType: 'hidden'
                                        }]
                                    }]
                                },{
                                    xtype: 'fileuploadfield',
                                    fieldLabel: lang('Photo of Soil Accumulation'),
                                    labelWidth: 230,
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulationInput',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulationInput',
                                    buttonText: 'Browse',
                                    listeners: {
                                        'change': function (fb, v) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form').getForm().submit({
                                                url: m_api + '/plot_survey/photo_soil_accumulation',
                                                clientValidation: false,
                                                params: {
                                                    opsiDisplay: thisObj.viewVar.opsiDisplay,
                                                    MemberID: thisObj.viewVar.MemberID
                                                },
                                                waitMsg: 'Sending Photo...',
                                                success: function (fp, o) {
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulation').setSrc(o.result.file);
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulationOld').setValue(o.result.filepath);
                                                }
                                            });
                                        }
                                    }
                                },{
                                    fieldLabel: lang('How Do You Quality The Vegetation Understory In Your Field'),
                                    xtype: 'radiogroup',
                                    labelWidth: 260,
                                    columns: 3,
                                    items:[{
                                        boxLabel: lang('Bare'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-QualityVegetarian',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-QualityVegetarian1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Standard'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-QualityVegetarian',
                                        inputValue: '2',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-QualityVegetarian2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Enhanced'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-QualityVegetarian',
                                        inputValue: '3',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-QualityVegetarian3',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
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
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisit',
                                            width: '300px',
                                            height:'200px',
                                            src: m_api_base_url + '/images/no-image-icon.png'
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitOld',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitOld',
                                            inputType: 'hidden'
                                        }]
                                    }]
                                },{
                                    xtype: 'fileuploadfield',
                                    fieldLabel: lang('Photo of Visit'),
                                    labelWidth: 230,
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitInput',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitInput',
                                    buttonText: 'Browse',
                                    listeners: {
                                        'change': function (fb, v) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form').getForm().submit({
                                                url: m_api + '/plot_survey/photo_visit',
                                                clientValidation: false,
                                                params: {
                                                    opsiDisplay: thisObj.viewVar.opsiDisplay,
                                                    MemberID: thisObj.viewVar.MemberID
                                                },
                                                waitMsg: 'Sending Photo...',
                                                success: function (fp, o) {
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisit').setSrc(o.result.file);
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitOld').setValue(o.result.filepath);
                                                }
                                            });
                                        }
                                    }
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitDesc',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitDesc',
                                    fieldLabel: lang('Photo Description'),
                                    labelWidth: 230,
                                },{
                                    xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Palm Trees')+'</div>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTBM',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTBM',
                                    fieldLabel: lang('TBM - Plants yet to produce'),
                                    labelWidth: 260,
                                    allowNegative: false,
                                    minValue: 0,
                                    emptyText: lang('trees'),
                                    listeners:{
                                        change: function(){
                                            calcTreeTbmTmTr();
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTM',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTM',
                                    fieldLabel: lang('TM - Producing plants'),
                                    labelWidth: 260,
                                    allowNegative: false,
                                    minValue: 0,
                                    emptyText: lang('trees'),
                                    listeners:{
                                        change: function(){
                                            calcTreeTbmTmTr();
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTR',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTR',
                                    fieldLabel: lang('TR - Old/diseased'),
                                    labelWidth: 260,
                                    allowNegative: false,
                                    minValue: 0,
                                    emptyText: lang('trees'),
                                    listeners:{
                                        change: function(){
                                            calcTreeTbmTmTr();
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR',
                                    fieldLabel: lang('Total Number of Trees'),
                                    labelWidth: 260,
                                    allowNegative: false,
                                    minValue: 0,
                                    readOnly: true,
                                    emptyText: lang('trees')
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTRPerHa',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTRPerHa',
                                    fieldLabel: lang('Total number of trees per ha'),
                                    labelWidth: 260,
                                    allowNegative: false,
                                    minValue: 0,
                                    readOnly: true,
                                    emptyText: lang('trees')
                                },{
                                    html:'<div margin-top:10px;></div>'
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('What Is The Majority of Planting Material Used On The Farm? Select All That Apply')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDumpy',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDumpy',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Dumpy')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDolokSinumbah',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDolokSinumbah',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Dolok Sinumbah')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateMarihat',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateMarihat',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Marihat')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateLame',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateLame',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Lame')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateBahJambi',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateBahJambi',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Bah Jambi')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateAvros',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateAvros',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Avros')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMatePPKS540',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMatePPKS540',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('PPKS 540/PPKS 540NG')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSimalungun',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSimalungun',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Simalungun')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateYangambi',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateYangambi',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Yangambi')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMatePKS718',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMatePKS718',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('PKS 718/PKS 239')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateLangkat',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateLangkat',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Langkat')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTopaz',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTopaz',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Topaz')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateLonsum',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateLonsum',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Lonsum')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDanimas',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDanimas',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Dami Mas')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSriwijaya',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSriwijaya',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Sriwijaya')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSocfin',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateSocfin',
                                            listeners:{
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            text: lang('Socfin')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateOther',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateOther',
                                            listeners:{
                                                change: function(){
                                                    if(this.checked == true){
                                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateOtherText').setDisabled(false);
                                                    }else{
                                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateOtherText').setDisabled(true);
                                                    }
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'textfield',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateOtherText',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateOtherText',
                                            emptyText: lang('Other Planting Material'),
                                            disabled: true
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.05,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDoNotKnow',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDoNotKnow',
                                            listeners:{
                                                change: function(){
                                                    if(this.checked == true){
                                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDoNotKnowText').setDisabled(false);
                                                    }else{
                                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDoNotKnowText').setDisabled(true);
                                                    }
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            style: 'margin-top:3px;',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateDoNotKnowText',
                                            text: lang('Do Not Know')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Variety')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-12px 0 0 0',
                                    items:[{
                                        columnWidth: 0.25,
                                        border: false,
                                        defaultType: 'checkboxfield',
                                        items:[{
                                            boxLabel: lang('Dura'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VarietyDura',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VarietyDura',
                                            listeners:{
                                                change: function(){
                                                    
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.25,
                                        border: false,
                                        defaultType: 'checkboxfield',
                                        items:[{
                                            boxLabel: lang('Tenera'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VarietyTenera',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VarietyTenera',
                                            listeners:{
                                                change: function(){
                                                    
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.25,
                                        border: false,
                                        defaultType: 'checkboxfield',
                                        items:[{
                                            boxLabel: lang('Pisifera'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VarietyPisifera',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VarietyPisifera',
                                            listeners:{
                                                change: function(){
                                                    
                                                }
                                            }
                                        }]
                                    }]
                                },{
                                    xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Farm Production')+'</div>'
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('When is the high harvest season for oil palm in your area? (Select all that apply)')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('January'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonJan',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonJan',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('February'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonFeb',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonFeb',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('March'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonMar',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonMar',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('April'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonApr',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonApr',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('May'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonMay',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonMay',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('June'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonJun',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonJun',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('July'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonJul',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonJul',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('August'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonAug',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonAug',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('September'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonSep',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonSep',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.25,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('October'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonOct',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonOct',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('November'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonNov',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonNov',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('December'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonDec',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LeanHarvestSeasonDec',
                                                listeners:{
                                                    change: function(){
                                                        calcNumberHighLowSeason();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysHighSeason',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysHighSeason',
                                    fieldLabel: lang('Harvest rate (once every … Days) in high season'),
                                    labelWidth: 275,
                                    allowNegative: false,
                                    minValue: 0,
                                    listeners:{
                                        change: function(){
                                            calcPalmProduction();
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageProdHighSeason',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageProdHighSeason',
                                    fieldLabel: lang('Average production per harvest (ton) in high season'),
                                    labelWidth: 275,
                                    allowNegative: false,
                                    minValue: 0,
                                    listeners:{
                                        change: function(){
                                            calcPalmProduction();
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-NrHighSeasonMonths',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-NrHighSeasonMonths',
                                    fieldLabel: lang('Number of Months in High Season'),
                                    labelWidth: 275,
                                    allowNegative: false,
                                    minValue: 0,
                                    readOnly: true,
                                    listeners:{
                                        change: function(){
                                            calcPalmProduction();
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HighSeasonProduction',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HighSeasonProduction',
                                    fieldLabel: lang('High Season Production (ton)'),
                                    labelWidth: 275,
                                    allowNegative: false,
                                    minValue: 0,
                                    readOnly: true
                                },{
                                    html:'<br>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysLowSeason',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysLowSeason',
                                    fieldLabel: lang('Harvest rate (once every … Days) in low season'),
                                    labelWidth: 275,
                                    allowNegative: false,
                                    minValue: 0,
                                    listeners:{
                                        change: function(){
                                            calcPalmProduction();
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageProdLowSeason',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageProdLowSeason',
                                    fieldLabel: lang('Average production per harvest (ton) in low season'),
                                    labelWidth: 275,
                                    allowNegative: false,
                                    minValue: 0,
                                    listeners:{
                                        change: function(){
                                            calcPalmProduction();
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-NrLowSeasonMonths',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-NrLowSeasonMonths',
                                    fieldLabel: lang('Number of Months in Low Season'),
                                    labelWidth: 275,
                                    allowNegative: false,
                                    minValue: 0,
                                    readOnly: true,
                                    listeners:{
                                        change: function(){
                                            calcPalmProduction();
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LowSeasonProduction',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LowSeasonProduction',
                                    fieldLabel: lang('Low Season Production (ton)'),
                                    labelWidth: 275,
                                    allowNegative: false,
                                    minValue: 0,
                                    readOnly: true
                                },{
                                    html:'<br>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AnnualProduction',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AnnualProduction',
                                    fieldLabel: lang('Annual Production (TON)'),
                                    labelWidth: 275,
                                    allowNegative: false,
                                    minValue: 0,
                                    readOnly: true,
                                    allowBlank: false,
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationProductivity',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlantationProductivity',
                                    fieldLabel: lang('Plantation Productivity (ton/ha)'),
                                    labelWidth: 275,
                                    allowNegative: false,
                                    minValue: 0,
                                    readOnly: true
                                },{
                                    html:'<br>'
                                },{
                                    layout: 'column',
                                    border: false,
                                    hidden:true,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Who does the harvesting? (select all that apply)')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    hidden:true,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.45,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Respondent and/or Family member'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-WhoHarvestFamily',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-WhoHarvestFamily'
                                            }]
                                        },{
                                            columnWidth: 0.45,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Use of Hired Labor'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-WhoHarvestLabor',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-WhoHarvestLabor'
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('To how many different buyers have you sold your FFB from this plantation to within the past year ?')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('1'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffBuyerSoldLastYear',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffBuyerSoldLastYear1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: '3',
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffBuyerSoldLastYear',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffBuyerSoldLastYear3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('More than 4'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffBuyerSoldLastYear',
                                                inputValue: '5',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffBuyerSoldLastYear5',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('2'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffBuyerSoldLastYear',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffBuyerSoldLastYear2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: '4',
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffBuyerSoldLastYear',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffBuyerSoldLastYear4',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhoSellFFBLastYear',
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhoSellFFBLastYear',
                                    fieldLabel: lang('To who do you sell your FFB to within the last year ?'),
                                    labelWidth: 310,
                                    hidden: true
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('How many different palm oil mills have you sold your FFB to within the past year ?')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('1'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffMillSoldLastYear',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffMillSoldLastYear1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: '3',
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffMillSoldLastYear',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffMillSoldLastYear3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('More than 4'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffMillSoldLastYear',
                                                inputValue: '5',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffMillSoldLastYear5',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('2'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffMillSoldLastYear',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffMillSoldLastYear2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: '4',
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffMillSoldLastYear',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HowManyDiffMillSoldLastYear4',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    hidden: true,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('To which mill did you sell your FFB to in the last year ?')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    hidden: true,
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Tidak tahu'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Sumber Kencana Indo Palma (SKIP)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Bumi Inti Mekar (BIM)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '5',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear5',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Riau Agri'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '7',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear7',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Kencana Amal Tani (KAT)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '9',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear9',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Inecda'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '11',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear11',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Swakarsa Sawit Raya (SSR)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '13',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear13',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('None'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Nikmat Halona Reksa (NHR)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear4',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Berkat Sawit Sejahtera (BSS)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '6',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear6',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Soegih Riesta Jaya (SRJ)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '8',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear8',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Sumatera Makmur Lestari (SML) – Harpena'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '10',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear10',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Talang Jerinjing Sawit (TJS)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '12',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear12',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('PT. Banyu Bening Utama (BBU)'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                                inputValue: '14',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear14',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    margin:'-25px 0 0 0',
                                    hidden: true,
                                    items:[{
                                        columnWidth: 0.2,
                                        layout: 'form',
                                        items:[{
                                            xtype:'radiofield',
                                            boxLabel: lang('Lainnya'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '15',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYear15',
                                            listeners:{
                                                change: function(){
                                                    if(this.checked == true){
                                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYearText').setDisabled(false);
                                                    }else{
                                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYearText').setDisabled(true);
                                                    }
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.775,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYearText',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ToWhichMillSellFFBLastYearText',
                                            disabled: true,
                                            emptyText: lang('Other Text')
                                        }]
                                    }]
                                },{
                                    html:'<div style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px dashed gray;"></div>'
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Any comments about plantation ?')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-16px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:0px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 1,
                                            xtype:'textarea',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Comment',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Comment',
                                            width: '100%'
                                        }]
                                    }]
                                }]
                            }]
                        }]
                        /*------------------------------------------------------------- Tab Garden (End)   --------------------------------------------------------------------*/
                    }]
                },{
                    /*------------------------------------------------------------- Tab Fertilizer (Begin)   --------------------------------------------------------------------*/
                    xtype: 'panel',
                    title: lang('Fertilizer'),
                    padding: '0 10 10 10',
                    items:[{
                        columnWidth: 1,
                        layout:'form',
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 1,
                                style:'',
                                layout:'form',
                                items:[{
                                    html:'<div style="margin-top:-4px;" class="subtitleForm">'+lang('Non Organic Fertilizers')+'</div>'
                                },{
                                    fieldLabel: lang('Do you use non organic fertilizers'),
                                    xtype: 'radiogroup',
                                    labelWidth: 525,
                                    columns: 2,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNonOrganicData',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNonOrganicDataYes',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNonOrganicData',
                                        inputValue: '2',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNonOrganicDataNo',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('PanelFertChemical').setDisabled(true);
                                                }else{
                                                    Ext.getCmp('PanelFertChemical').setDisabled(false);
                                                }
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    xtype: 'panel',
                                    id: 'PanelFertChemical',
                                    items: [{
                                        layout: 'column',
                                        border: false,
                                        items:[{
                                            columnWidth: 1,
                                            layout:'form',
                                            items:[{
                                                xtype: 'numericfield',
                                                fieldLabel: lang('How much money did you spend in past 12 months on non organic fertilizers'),
                                                labelWidth: 525,
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertMoneySpentNonOrganic',
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertMoneySpentNonOrganic',
                                                emptyText: lang('in rupiah'),
                                                hidden: true,
                                                allowNegative: false,
                                                minValue: 0
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{}]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Frequency (times/year)')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Dose (kg/plot/times)')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Unit'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Annual dose (kg/plot/year)')
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Urea')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUreaTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUreaTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUreaDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUreaDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUreaDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUreaDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUreaTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUreaDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUreaDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUreaDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('SS')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertSSTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertSSTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertSSDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertSSDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertSSDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertSSDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertSSTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertSSDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertSSDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertSSDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('NPK')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('TSP')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertTSPTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertTSPTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertTSPDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertTSPDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertTSPDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertTSPDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertTSPTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertTSPDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertTSPDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertTSPDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('CU')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCUTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCUTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCUDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCUDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCUDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCUDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCUTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCUDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCUDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCUDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('KCL')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertKCLTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertKCLTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertKCLDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertKCLDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertKCLDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertKCLDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertKCLTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertKCLDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertKCLDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertKCLDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                hidden: true,
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('NPK Mutiara')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKMutiTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKMutiTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKMutiDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKMutiDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKMutiDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKMutiDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKMutiTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKMutiDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKMutiDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNPKMutiDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Borat')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertBoratTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertBoratTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertBoratDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertBoratDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertBoratDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertBoratDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertBoratTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertBoratDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertBoratDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertBoratDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Dolomite/Lime')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertDolomiteTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertDolomiteTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertDolomiteDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertDolomiteDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertDolomiteDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertDolomiteDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertDolomiteTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertDolomiteDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertDolomiteDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertDolomiteDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                fieldLabel: lang('Who Tells You The Type and Quantity of Fertilizer You Need to Apply'),
                                                xtype: 'radiogroup',
                                                labelWidth: 260,
                                                labelAlign:'top',
                                                columns: 3,
                                                items:[{
                                                    boxLabel: lang('External Adviser'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TellNeedApply',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TellNeedApply1',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('I Follow Neighbour Farmer'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TellNeedApply',
                                                    inputValue: '2',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TellNeedApply2',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('Farmer Coordinate'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TellNeedApply',
                                                    inputValue: '3',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TellNeedApply3',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('Myself'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TellNeedApply',
                                                    inputValue: '4',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TellNeedApply4',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('I Just Apply Like Last Year'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TellNeedApply',
                                                    inputValue: '5',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TellNeedApply5',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            },{
                                                fieldLabel: lang('Do You Change The Rate of Fertilizers Depending on The Price of Fruit'),
                                                xtype: 'radiogroup',
                                                labelWidth: 260,
                                                labelAlign:'top',
                                                columns: 3,
                                                items:[{
                                                    boxLabel: lang('Yes'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ChangeRateFertilizer',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ChangeRateFertilizer1',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('No'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ChangeRateFertilizer',
                                                    inputValue: '2',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ChangeRateFertilizer2',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            },{
                                                fieldLabel: lang('Do You Change The Type of Fertilizers Depending on The Price of Fruit'),
                                                xtype: 'radiogroup',
                                                labelWidth: 260,
                                                labelAlign:'top',
                                                columns: 3,
                                                items:[{
                                                    boxLabel: lang('Yes'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ChangeTypeFertilizer',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ChangeTypeFertilizer1',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('No'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ChangeTypeFertilizer',
                                                    inputValue: '2',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ChangeTypeFertilizer2',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            },{
                                                fieldLabel: lang('How Do You Decide Which Day to Apply Fertilizer'),
                                                xtype: 'radiogroup',
                                                labelWidth: 260,
                                                labelAlign:'top',
                                                columns: 3,
                                                items:[{
                                                    boxLabel: lang('Any Day When I Have Time'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DecideApplyFertilizer',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DecideApplyFertilizer1',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('Depending on Rainfall'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DecideApplyFertilizer',
                                                    inputValue: '2',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DecideApplyFertilizer2',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            }
                                            /*,{
                                                xtype: 'checkboxgroup',
                                                fieldLabel: lang('Which trees are fertilized with non organic fertilizers'),
                                                labelWidth: 525,
                                                columns: 3,
                                                items:[{
                                                    boxLabel: lang('singk TBM'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithNonOrgaTBM',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithNonOrgaTBM',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('singk TM'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithNonOrgaTM',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithNonOrgaTM',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('singk TR'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithNonOrgaTR',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithNonOrgaTR',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            }*/
                                        ]
                                        }]
                                    }]
                                },{
                                    html:'<br /><div style="margin-top:-4px;" class="subtitleForm">'+lang('Organic Fertilizers')+'</div>'
                                },{
                                    fieldLabel: lang('Do you use organic fertilizers'),
                                    xtype: 'radiogroup',
                                    labelWidth: 525,
                                    columns: 2,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUseOrganic',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUseOrganicYes',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUseOrganic',
                                        inputValue: '0',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUseOrganicNo',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('PanelFertOrganic').setDisabled(true);
                                                }else{
                                                    Ext.getCmp('PanelFertOrganic').setDisabled(false);
                                                }
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    xtype: 'panel',
                                    id: 'PanelFertOrganic',
                                    items: [{
                                        layout: 'column',
                                        border: false,
                                        items:[{
                                            columnWidth: 1,
                                            layout:'form',
                                            items:[{
                                                xtype: 'numericfield',
                                                fieldLabel: lang('How much money did you spend in past 12 months  on organic fertilizers'),
                                                labelWidth: 525,
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertMoneySpentOrganic',
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertMoneySpentOrganic',
                                                emptyText: lang('in rupiah'),
                                                hidden: true,
                                                allowNegative: false,
                                                minValue: 0
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{}]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Frequency (times/year)')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Dose (kg/plot/times)')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Unit'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Annual dose (kg/plot/year)')
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Palm Bunch Ash')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBATimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBATimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBADose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBADosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBADose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBADose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBATimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBADosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBADosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBADosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Palm Bunch')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertPBDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Compost from Palm Bunch')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCPBTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCPBTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCPBDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCPBDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCPBDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCPBDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCPBTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCPBDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCPBDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertCPBDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            },{
                                                layout: 'column',
                                                border: false,
                                                margin:'-12px 0 0 0',
                                                items:[{
                                                    columnWidth: 0.35,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Manure')
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertManureTimesYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertManureTimesYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var doseNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertManureDose').getValue());
                                                                if(isNaN(doseNya)) doseNya = 0;
            
                                                                totalDose = nv * doseNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertManureDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertManureDose',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertManureDose',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        listeners:{
                                                            change: function(cb, nv, ov) {
                                                                var totalDose;
            
                                                                var freqNya = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertManureTimesYear').getValue());
                                                                if(isNaN(freqNya)) freqNya = 0;
            
                                                                totalDose = nv * freqNya;
                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertManureDosePlotYear').setValue(totalDose);
            
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype:'label',
                                                        cls: 'x-form-item-label',
                                                        style: 'text-align:center',
                                                        text: 'Kg'
                                                    }]
                                                },{
                                                    columnWidth: 0.15,
                                                    layout: 'form',
                                                    items:[{
                                                        xtype: 'numericfield',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertManureDosePlotYear',
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertManureDosePlotYear',
                                                        allowNegative: false,
                                                        minValue: 0,
                                                        readOnly: true
                                                    }]
                                                }]
                                            }
                                            /*,{
                                                xtype: 'checkboxgroup',
                                                fieldLabel: lang('Which trees are fertilized with organic fertilizers'),
                                                labelWidth: 525,
                                                columns: 3,
                                                items:[{
                                                    boxLabel: lang('singk TBM'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithOrgaTBM',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithOrgaTBM',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('singk TM'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithOrgaTM',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithOrgaTM',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('singk TR'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithOrgaTR',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertWithOrgaTR',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            }*/
                                        ]
                                        }]
                                    }]
                                }]
                            }]
                        }]
                    }]
                    /*------------------------------------------------------------- Tab Fertilizer (End)   --------------------------------------------------------------------*/
                },{
                    /*------------------------------------------------------------- Tab Pesticide (Begin)   --------------------------------------------------------------------*/
                    xtype: 'panel',
                    title: lang('Control of HPT'),
                    padding: '0 10 10 10',
                    items:[{
                        columnWidth: 1,
                        layout:'form',
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 1,
                                style:'',
                                layout:'form',
                                items:[{
                                    layout: 'column',
                                    border: false,
                                    margin:'0',
                                    items:[{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[{
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HerbicidePanel',
                                            xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Herbicide')+'</div>',
                                            disabled: true
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 1,
                                                layout:'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Do You Use Herbicides')
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            style:'margin-top:-20px;padding-top:0px;',
                                            items:[{
                                                layout:'column',
                                                columnWidth: 1,
                                                style:'margin-top:-7px;padding-top:0px;',
                                                items:[{
                                                    columnWidth: 0.475,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingHerbicide',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingHerbicideYes',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.475,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingHerbicide',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingHerbicideNo',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    thisObj.FormFlowHerbisida('Tidak');
                                                                }else{
                                                                    thisObj.FormFlowHerbisida('Ya');
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            margin:'-12px 0 0 0',
                                            items:[{
                                                columnWidth: 0.5,
                                                style:'padding-right:10px;',
                                                layout: 'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Brand')
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Basmilang'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi2',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi2',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi2',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi2',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Bimastar'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi14',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi14',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi14',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi14',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Bravoxone'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi12',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi12',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi12',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi12',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('DMA'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi22',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi22',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi22',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi22',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Gramaxone'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi5',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi5',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi5',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi5',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Herbatop'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi25',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi25',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi25',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi25',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Kleenup'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi19',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi19',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi19',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi19',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Konup'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi24',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi24',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi24',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi24',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Mupxone'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi26',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi26',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi26',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi26',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Noxone',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi10',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi10',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi10',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi10',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Rumat'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi17',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi17',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi17',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi17',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Sapurata'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi7',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi7',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi7',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi7',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Senus'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi28',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi28',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi28',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi28',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Sun Up'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi4',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi4',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi4',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi4',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Supremo'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi6',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi6',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi6',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi6',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            },{
                                                columnWidth: 0.49,
                                                style:'padding-left:10px;',
                                                layout: 'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Brand')
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Para Special',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi9',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi9',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi9',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi9',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Paratop',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi11',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi11',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi11',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi11',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Pilar Up'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi3',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi3',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi3',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi3',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Pointer'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi27',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi27',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi27',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi27',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Polado'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi15',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi15',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi15',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi15',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Polaris'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi23',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi23',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi23',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi23',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Prima Up'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi20',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi20',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi20',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi20',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Primastar'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi16',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi16',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi16',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi16',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Primaxone'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi13',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi13',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi13',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi13',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Rambo'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi8',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi8',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi8',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi8',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Round Up'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi1',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi1',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi1',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi1',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Supretox'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi18',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi18',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi18',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi18',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Tamaxon'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi29',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi29',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi29',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi29',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Tanistar'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi21',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi21',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi21',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi21',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbiOther',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbiOther',
                                                    fieldLabel: lang('Other Brand'),
                                                    labelAlign:'top'
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbiOther',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbiOther',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 1,
                                                layout:'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('How Are You Applying Herbicides')
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            style:'margin-top:-20px;padding-top:0px;',
                                            items:[{
                                                layout:'column',
                                                columnWidth: 1,
                                                style:'margin-top:-7px;padding-top:0px;',
                                                items:[{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('Blanket on The Whole Surface'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AppliedHerbicide',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AppliedHerbicide1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('Only Circle and Harvesting Path'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AppliedHerbicide',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AppliedHerbicide2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('Selective Area'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AppliedHerbicide',
                                                        inputValue: '3',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AppliedHerbicide3',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            }]
                                        },{
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PesticideUsagePanel',
                                            xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Pesticide/Chemical Usage')+'</div>',
                                            disabled: true
                                        },{
                                            html:'<div>&nbsp;</div>'
                                        },{
                                            fieldLabel: lang('Where Do You Store The Pesticide Before and After Usage'),
                                            xtype: 'radiogroup',
                                            labelAlign:'top',
                                            labelWidth: 400,
                                            columns: 2,
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-RowPestStoreLocation',
                                            items: [{
                                                boxLabel  : lang('In the house'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation1',
                                                inputValue: '1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel  : lang('Pesticide specific place'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation2',
                                                inputValue: '2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel  : lang('Outside of the house (house area)'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation3',
                                                inputValue: '3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel  : lang('On the farm'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation4',
                                                inputValue: '4',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel  : lang('Others'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation5',
                                                inputValue: '5',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            html:'<div>&nbsp;</div>'
                                        },{
                                            fieldLabel: lang('What Do You Do With The Pesticide Packaging After Usage'),
                                            xtype: 'radiogroup',
                                            labelAlign:'top',
                                            labelWidth: 400,
                                            columns: 2,
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-RowPestPackageAfterUse',
                                            items: [{
                                                boxLabel  : lang('Random disposal (Garden or around the house)'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse1',
                                                inputValue: '1',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel  : lang('Use for something else'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse2',
                                                inputValue: '2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel  : lang('Rinse, perforate and bury'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse3',
                                                inputValue: '3',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel  : lang('Burn'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse4',
                                                inputValue: '4',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel  : lang('Recycle/return to the shop'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse5',
                                                inputValue: '5',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel  : lang('Others'),
                                                name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                                id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse6',
                                                inputValue: '6',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        items:[,{
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecticidePanel',
                                            xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Insecticide')+'</div>',
                                            disabled: true
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 1,
                                                layout:'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Do You Use Insecticide')
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            style:'margin-top:-20px;padding-top:0px;',
                                            items:[{
                                                layout:'column',
                                                columnWidth: 1,
                                                style:'margin-top:-7px;padding-top:0px;',
                                                items:[{
                                                    columnWidth: 0.475,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingInsecticide',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingInsecticideYes',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.475,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingInsecticide',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingInsecticideNo',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    thisObj.FormFlowInsektisida('Tidak');
                                                                }else{
                                                                    thisObj.FormFlowInsektisida('Ya');
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            margin:'-12px 0 0 0',
                                            items:[{
                                                columnWidth: 0.5,
                                                style:'padding-right:10px;',
                                                layout: 'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Brand')
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Alika'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec1',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec1',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec1',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec1',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Arrivo'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec15',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec15',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec15',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec15',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Bestox'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec17',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec17',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec17',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec17',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Buldok'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec20',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec20',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec20',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec20',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Capture'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec3',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec3',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec3',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec3',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Chlormite',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec9',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec9',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec9',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec9',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Dangke'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec19',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec19',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec19',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec19',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Decis',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec10',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec10',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec10',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec10',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Deicer 505'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec14',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec14',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec14',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec14',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Drusban'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec6',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec6',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec6',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec6',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Halona'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec18',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec18',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec18',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec18',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            },{
                                                columnWidth: 0.49,
                                                style:'padding-left:10px;',
                                                layout: 'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Brand')
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Klensect',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec11',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec11',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec11',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec11',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Laser'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec21',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec21',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec21',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec21',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Matador'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec2',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec2',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec2',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec2',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Nurelle'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec8',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec8',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec8',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec8',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Organic',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec23',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec23',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec23',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec23',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Penalty'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec7',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec7',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec7',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec7',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Prevaton'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec24',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec24',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec24',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec24',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Sevin'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec22',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec22',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec22',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec22',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Sidamethrin'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec16',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec16',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec16',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec16',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Unicide'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec13',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec13',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec13',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec13',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Vigor',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec12',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec12',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec12',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec12',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsecOther',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsecOther',
                                                    fieldLabel: lang('Other Brand'),
                                                    labelAlign:'top'
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsecOther',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsecOther',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 1,
                                                layout:'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Have You Appied It On')
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            style:'margin-top:-20px;padding-top:0px;',
                                            items:[{
                                                layout:'column',
                                                columnWidth: 1,
                                                style:'margin-top:-7px;padding-top:0px;',
                                                items:[{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('All Palms'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecAppliedOn',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecAppliedOn1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('Selected Palms'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecAppliedOn',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecAppliedOn2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 1,
                                                layout:'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('What was It Applied For')
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            style:'margin-top:-20px;padding-top:0px;',
                                            items:[{
                                                layout:'column',
                                                columnWidth: 1,
                                                style:'margin-top:-7px;padding-top:0px;',
                                                items:[{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    items:[{
                                                        xtype: 'checkboxfield',
                                                        boxLabel: lang('Rat Control'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecRatControl',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecRatControl',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    items:[{
                                                        xtype: 'checkboxfield',
                                                        boxLabel: lang('Caterpillar Control'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecCaterpillarControl',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecCaterpillarControl',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    items:[{
                                                        xtype: 'checkboxfield',
                                                        boxLabel: lang('Oryctes Control'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecOryctesControl',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecOryctesControl',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            }]
                                        },{
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FungicidePanel',
                                            xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Fungicide')+'</div>',
                                            disabled: true
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 1,
                                                layout:'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Do You Use Fungicide')
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            style:'margin-top:-20px;padding-top:0px;',
                                            items:[{
                                                layout:'column',
                                                columnWidth: 1,
                                                style:'margin-top:-7px;padding-top:0px;',
                                                items:[{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingFungicide',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingFungicideYes',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingFungicide',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingFungicideNo',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    thisObj.FormFlowFungisida('Tidak');
                                                                }else{
                                                                    thisObj.FormFlowFungisida('Ya');
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            margin:'-12px 0 0 0',
                                            items:[{
                                                columnWidth: 0.5,
                                                style:'padding-right:10px;',
                                                layout: 'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Brand')
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Amistartop'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi3',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi3',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi3',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi3',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Antila'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi6',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi6',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi6',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi6',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Antracol'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi7',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi7',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi7',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi7',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Benhasil',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi11',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi11',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi11',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi11',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Cozeb',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi9',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi9',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi9',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi9',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Dithane'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi2',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi2',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi2',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi2',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Nordox'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi1',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi1',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi1',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi1',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            },{
                                                columnWidth: 0.4,
                                                style:'padding-left:10px;',
                                                layout: 'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Brand')
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Organic',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi12',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi12',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi12',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi12',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Polydor'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi8',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi8',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi8',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi8',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: 'Rabbat',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi10',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi10',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi10',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi10',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Rhidomill'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi5',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi5',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi5',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi5',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'checkboxfield',
                                                    boxLabel: lang('Scorpio'),
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi4',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi4',
                                                    listeners:{
                                                        change: function(){
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi4',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi4',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungiOther',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungiOther',
                                                    fieldLabel: lang('Other Brand'),
                                                    labelAlign:'top'
                                                },{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Frequency (times/year)')
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungiOther',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungiOther',
                                                    allowNegative: false,
                                                    minValue: 0,
                                                    listeners:{
                                                        change: function(cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 1,
                                                layout:'form',
                                                items:[{
                                                    xtype:'label',
                                                    cls: 'x-form-item-label',
                                                    text: lang('Have You Applied It On')
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            style:'margin-top:-20px;padding-top:0px;',
                                            items:[{
                                                layout:'column',
                                                columnWidth: 1,
                                                style:'margin-top:-7px;padding-top:0px;',
                                                items:[{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('All Palms'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FungiAppliedOn',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FungiAppliedOn1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    columnWidth: 0.3,
                                                    border: false,
                                                    defaultType: 'radiofield',
                                                    items:[{
                                                        boxLabel: lang('Selected Palms'),
                                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FungiAppliedOn',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FungiAppliedOn2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            }]
                                        }]
                                    }]
                                }]
                            }]
                        }]
                    }]
                    /*------------------------------------------------------------- Tab Pesticide (End)   --------------------------------------------------------------------*/
                },{
                    xtype: 'panel',
                    title: lang('Handling K3'),
                    padding: '0 10 10 10',
                    hidden: true,
                    items:[{
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
                                    fieldLabel: lang('Menggunakan Alat Perlindungan Diri (APD)'),
                                    xtype: 'radiogroup',
                                    labelWidth: 260,
                                    columns: 3,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UseProtectiveGear',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UseProtectiveGearYes',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UseProtectiveGear',
                                        inputValue: '2',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UseProtectiveGearNo',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    html:'<div>&nbsp;</div>'
                                },{
                                    fieldLabel: lang('Completeness of personal protective equipment used during harvest or garden maintenance'),
                                    xtype: 'checkboxgroup',
                                    labelAlign: 'top',
                                    columns: 2,
                                    items: [{
                                        boxLabel  : lang('Helm'),
                                        name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipHelm',
                                        id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipHelm',
                                        inputValue: '1'
                                    },{
                                        boxLabel  : lang('Boots'),
                                        name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipBoots',
                                        id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipBoots',
                                        inputValue: '1'
                                    },{
                                        boxLabel  : lang('Dodos Protector'),
                                        name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipDodosProtector',
                                        id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipDodosProtector',
                                        inputValue: '1'
                                    },{
                                        boxLabel  : lang('Mask'),
                                        name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipMask',
                                        id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipMask',
                                        inputValue: '1'
                                    },{
                                        boxLabel  : lang('Gloves'),
                                        name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipGloves',
                                        id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipGloves',
                                        inputValue: '1'
                                    },{
                                        boxLabel  : lang('Spray Glasses'),
                                        name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipSprayGlasses',
                                        id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipSprayGlasses',
                                        inputValue: '1'
                                    },{
                                        boxLabel  : lang('Egrek Protector'),
                                        name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipEgrekProtector',
                                        id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipEgrekProtector',
                                        inputValue: '1'
                                    },{
                                        boxLabel  : lang('Protective Clothing'),
                                        name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipProtectiveClothing',
                                        id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-EquipProtectiveClothing',
                                        inputValue: '1'
                                    }]
                                }]
                            },{
                                columnWidth: 0.495,
                                layout:'form',
                                style:'padding-left:15px;border-left:1px dashed grey;',
                                items:[]
                            }]
                        }]
                    }]
                },{
                    xtype: 'panel',
                    title: lang('Pest & Disease'),
                    padding: '0 10 10 10',
                    items:[{
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
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Have You Installed Barn Owl Housing on Your Plantation')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InstallBarnOwl',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InstallBarnOwlYes',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InstallBarnOwl',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InstallBarnOwlNo',
                                                listeners:{
                                                    change: function(){
                                                        if(this.checked == true){
                                                            thisObj.FormFlowHerbisida('Tidak');
                                                        }else{
                                                            thisObj.FormFlowHerbisida('Ya');
                                                        }
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Have You Planted Beneficial Plants Hosting Natural Predators Such as Antigonon, Urnera, Senna or Euphorbia')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BeneficialPlants',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BeneficialPlantsYes',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BeneficialPlants',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BeneficialPlantsNo',
                                                listeners:{
                                                    change: function(){
                                                        if(this.checked == true){
                                                            thisObj.FormFlowHerbisida('Tidak');
                                                        }else{
                                                            thisObj.FormFlowHerbisida('Ya');
                                                        }
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Main Pest on Plantation')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Rats'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain1',
                                                listeners:{
                                                    change:function(){
                                                        if(this.checked == true){
                                                            // Ext.getCmp('SevirityPestPlantation').setDisabled(false);
                                                        }else{
                                                            // Ext.getCmp('SevirityPestPlantation').setDisabled(true);
                                                        }
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Satora Nitens'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain2'
                                            },{
                                                boxLabel: lang('Rinocheros Beetle'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain3'
                                            },{
                                                boxLabel: lang('Pig'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain4'
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Olygonichus'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain',
                                                inputValue: '5',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain5'
                                            },{
                                                boxLabel: lang('Tirathaba Mundella'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain',
                                                inputValue: '6',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain6'
                                            },{
                                                boxLabel: lang('Elephant'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain',
                                                inputValue: '7',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain7'
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            layout: 'form',
                                            items:[{
                                                xtype:'radiofield',
                                                boxLabel: lang('Others'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain',
                                                inputValue: '8',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMain8',
                                                listeners:{
                                                    change: function(){
                                                        if(this.checked == true){
                                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOtherText').setDisabled(false);
                                                        }else{
                                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOtherText').setDisabled(true);
                                                        }
                                                        return false;
                                                    }
                                                }
                                            },{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOtherText',
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOtherText',
                                                disabled: true,
                                                emptyText: lang('Other Text')
                                            }]
                                        }]
                                    }]
                                }]
                            },{
                                columnWidth: 0.495,
                                layout:'form',
                                style:'padding-left:15px;border-left:1px dashed grey;',
                                items:[{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Main Disease on Plantation')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Blast Disease'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain1',
                                                listeners:{
                                                    change:function(){
                                                        if(this.checked == true){
                                                            // Ext.getCmp('SeverityDiseasePlantation').setDisabled(false);
                                                        }else{
                                                            // Ext.getCmp('SeverityDiseasePlantation').setDisabled(true);
                                                        }
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Upper Steam Rot'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain2'
                                            },{
                                                boxLabel: lang('Spear Rot'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain',
                                                inputValue: '3',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain3'
                                            },{
                                                boxLabel: lang('Anthracnose'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain',
                                                inputValue: '4',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain4'
                                            },{
                                                boxLabel: lang('Viscular Wilt'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain5',
                                                inputValue: '5',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain'
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Basal Steam Rot / Genoderma'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain',
                                                inputValue: '6',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain6'
                                            },{
                                                boxLabel: lang('Bud Rot'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain',
                                                inputValue: '7',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain7'
                                            },{
                                                boxLabel: lang('Patch Yellow'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain',
                                                inputValue: '8',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain8'
                                            },{
                                                boxLabel: lang('Crown disease'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain',
                                                inputValue: '9',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain9'
                                            },{
                                                boxLabel: lang('Bunch Rot'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain',
                                                inputValue: '10',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMain10'
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            layout: 'form',
                                            items:[{
                                                xtype:'radiofield',
                                                boxLabel: lang('Lainnya'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainOther',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainOther',
                                                listeners:{
                                                    change: function(){
                                                        if(this.checked == true){
                                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainOtherText').setDisabled(false);
                                                        }else{
                                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainOtherText').setDisabled(true);
                                                        }
                                                        return false;
                                                    }
                                                }
                                            },{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainOtherText',
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainOtherText',
                                                disabled: true,
                                                emptyText: lang('Other Text')
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Have You Noticed Palms Dying From Unknown Reasons')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDying',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDying1',
                                                listeners:{
                                                    change: function(){
                                                        if(this.checked == true){
                                                            Ext.getCmp('WhatSymptomsNotice').setDisabled(false);
                                                        }else{
                                                            Ext.getCmp('WhatSymptomsNotice').setDisabled(true);
                                                        }
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDying',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDying2',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:0px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        id:'WhatSymptomsNotice',
                                        disabled:true,
                                        style:'margin-top:10px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 1,
                                            border: false,
                                            items:[{
                                                xtype:'label',
                                                cls: 'x-form-item-label',
                                                text: lang('If Yes, What Symptoms Have You Notice')
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Rotten Spear'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDyingSpear',
                                                inputValue: '1',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDyingSpear',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Rotten Trunk'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDyingTrunk',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDyingTrunk',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.3,
                                            border: false,
                                            defaultType: 'checkboxfield',
                                            items:[{
                                                boxLabel: lang('Others'),
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDyingOther',
                                                inputValue: '2',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDyingOther',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDyingOtherText',
                                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-UnknownReasonDyingOtherText',
                                                disabled: true,
                                                emptyText: lang('Other Text'),
                                            }]
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:10px;padding-top:0px;',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 0.6,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            xtype: 'numericfield',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PalmsDiedLastTwoYears',
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PalmsDiedLastTwoYears',
                                            fieldLabel: lang('How Many Palms Died During Last 2 Years'),
                                            labelAlign:'top'
                                        }]
                                    }]
                                }]
                            }]
                        }]
                    }]
                },{
                    xtype: 'panel',
                    title: lang('Farm Photo'),
                    padding: '0 10 10 10',
                    items: [{
                            columnWidth: 1,
                            layout:'form',
                            items:[{
                                    layout: 'column',
                                    border: false,
                                    items: [{
                                            columnWidth: 0.5,
                                            style:'',
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
                                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarm',
                                                            width: '300px',
                                                            height:'200px',
                                                            src: m_api_base_url + '/images/no-image-icon.png'
                                                        },{
                                                            xtype: 'textfield',
                                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarmOld',
                                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarmOld',
                                                            inputType: 'hidden'
                                                        }]
                                                    }]
                                            },{
                                                    xtype: 'fileuploadfield',
                                                    fieldLabel: lang('Photo of the plantation'),
                                                    labelWidth: 230,
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarmInput',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarmInput',
                                                    buttonText: 'Browse',
                                                    listeners: {
                                                        'change': function (fb, v) {
                                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form').getForm().submit({
                                                                url: m_api + '/plot_survey/photo_farm',
                                                                clientValidation: false,
                                                                params: {
                                                                    opsiDisplay: thisObj.viewVar.opsiDisplay,
                                                                    MemberID: thisObj.viewVar.MemberID
                                                                },
                                                                waitMsg: 'Sending Photo...',
                                                                success: function (fp, o) {
                                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarm').setSrc(o.result.file);
                                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarmOld').setValue(o.result.filepath);
                                                                }
                                                            });
                                                        }
                                                    }
                                            },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmPhotoDesc',
                                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmPhotoDesc',
                                                    fieldLabel: lang('Photo Description'),
                                                    labelWidth: 230,
                                            }]
                                    },{
                                            columnWidth: 0.45,
                                            style:'',
                                            layout:'form',
                                            items:[]
                                    }]
                            }]
                    }]
                }]
            }]
        }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (end)


        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var formNya = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form').getForm();
                var formValidOrNot = formNya.isValid();
                var labelNotValid = lang('Form not complete yet');

                //cek validasi manual (begin)
//                var totalPlantMateTree = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').getValue());
//                if(totalPlantMateTree == null || isNaN(totalPlantMateTree)) totalPlantMateTree = 0;

                var totalTMTBMTRTree = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').getValue());
                if(totalTMTBMTRTree == null || isNaN(totalTMTBMTRTree)) totalTMTBMTRTree = 0;

                //Total Pohon Sawit
//                if(Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').hasCls('notif-red')){
//                    if(totalTMTBMTRTree == 0){
//                        //console.log('Manual Valid');
//                    }else{
//                        formValidOrNot = false;
//                        labelNotValid = labelNotValid+'<br />* '+lang('Total palm oil trees must match with total planting material trees');
//                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TabPanel').setActiveTab(0);
//                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').focus();
//                    }
//                }


                //Jumlah Minimal, Maksimal Pohon Sawit per Ha
                var MinTreeByHa = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaHa').getValue()) * thisObj.intMinTreePerHa;
                var MaxTreeByHa = parseFloat(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaHa').getValue()) * thisObj.intMaxTreePerHa;
                //console.log(totalTMTBMTRTree);
                //console.log(MinTreeByHa);
                //console.log(MaxTreeByHa);

                if( (totalTMTBMTRTree < MinTreeByHa) || (totalTMTBMTRTree > MaxTreeByHa) ){
                    formValidOrNot = false;
                    labelNotValid = labelNotValid+'<br /><br />* '+lang('Total palmoil trees for this survey is')+'<br />';
                    labelNotValid = labelNotValid+'Minimal : '+MinTreeByHa+' '+lang('trees')+'<br />';
                    labelNotValid = labelNotValid+'Maximal : '+MaxTreeByHa+' '+lang('trees');

                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TabPanel').setActiveTab(0);
                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').addCls('notif-red');
//                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').addCls('notif-red');
                }else{
                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').removeCls('notif-red');
//                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').removeCls('notif-red');
                }

                //Cek Harvest Rate
                var HarvestRateDaysHighSeason = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysHighSeason').getValue());
                var HarvestRateDaysLowSeason = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysLowSeason').getValue());
                if(HarvestRateDaysHighSeason < 7 || HarvestRateDaysHighSeason > 30) {
                    formValidOrNot = false;
                    labelNotValid = labelNotValid+'<br /><br />* '+lang('Harvest rate (once every … Days) in high season')+':'+lang('min: 7, max: 30')+'<br />';

                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TabPanel').setActiveTab(0);
                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysHighSeason').addCls('notif-red');
                } else {
                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysHighSeason').removeCls('notif-red');
                }
                if(HarvestRateDaysLowSeason < 7 || HarvestRateDaysLowSeason > 30) {
                    formValidOrNot = false;
                    labelNotValid = labelNotValid+'<br /><br />* '+lang('Harvest rate (once every … Days) in low season')+':'+lang('min: 7, max: 30')+'<br />';

                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TabPanel').setActiveTab(0);
                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysLowSeason').addCls('notif-red');
                } else {
                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HarvestRateDaysLowSeason').removeCls('notif-red');
                }
                //cek validasi manual (end)

                if (formValidOrNot ==  true) {
                    formNya.submit({
                        url: m_api + '/plot_survey/survey',
                        method:'POST',
                        params: {
                            opsiDisplay: thisObj.viewVar.opsiDisplay
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
                            formNya.reset();

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
                                title: 'Error',
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
                        msg: labelNotValid,
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            text: lang('Close'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form');
            formNya.getForm().reset();

            //set MemberID default value
            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberID').setValue(thisObj.viewVar.MemberID);
            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingHerbicideNo').setValue(true);
            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingInsecticideNo').setValue(true);
            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingFungicideNo').setValue(true);
            // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertNonOrganicDataNo').setValue(true);
            // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertUseOrganicNo').setValue(true);

            if(thisObj.viewVar.opsiDisplay == 'insert'){
                //insert

                //get var yg diperlukan
                Ext.Ajax.request({
                    waitMsg: lang('Please Wait'),
                    url: m_api + '/grower/member_data_detail',
                    method : 'GET',
                    params: {MemberID:  thisObj.viewVar.MemberID},
                    success: function(response, opts){
                        var r = Ext.decode(response.responseText);
                        //console.log(r);

                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberDisplayID').setValue(r.data.MemberDisplayID);
                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberName').setValue(r.data.MemberName);

                        //load subdistrict
                        thisObj.cmb_subdistrict.load({
                            params: {
                                DistrictID: r.data.DistrictID
                            }
                        });

                        //assign data Member ke Variabel
                        thisObj.memberVar = {
                            MemberName : r.data.MemberName,
                            MemberLocation: r.data.MemberLocation,
                            MemberHandphone: r.data.Handphone
                        }
                    },
                    failure: function(response, opts){
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
            
            if(thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view'){
                //update | view


                //get data Farmer (Begin)
                Ext.Ajax.request({
                    waitMsg: lang('Please Wait'),
                    url: m_api + '/grower/member_data_detail',
                    method : 'GET',
                    params: {MemberID:  thisObj.viewVar.MemberID},
                    success: function(response, opts){
                        console.log("gar");
                        var r = Ext.decode(response.responseText);

                        //assign data Member ke Variabel
                        thisObj.memberVar = {
                            MemberName : r.data.MemberName,
                            MemberLocation: r.data.MemberLocation,
                            MemberHandphone: r.data.Handphone
                        }

                        //load formnya
                        formNya.getForm().load({
                            url: m_api + '/plot_survey/plot_survey_form_data',
                            method: 'GET',
                            params: {
                                MemberID: thisObj.viewVar.MemberID,
                                PlotNr: thisObj.viewVar.PlotNr,
                                SurveyNr: thisObj.viewVar.SurveyNr,
                                DateCollection: thisObj.viewVar.DateCollection
                            },
                            success: function(form, action) {
                                var r = Ext.decode(action.response.responseText);
                                //console.log(r);

                                //load prov,district,subdistrict & village
                                thisObj.cmb_province.load({
                                    callback: function(records, operation, success){
                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ProvinceID').setValue(r.data.ProvinceID);
                                        if (success == true) {
                                            thisObj.cmb_district.load({
                                                params: {
                                                    ProvinceID: r.data.ProvinceID
                                                },
                                                callback: function(records, operation, success){
                                                    if (success == true) {
                                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DistrictID').setValue(r.data.DistrictID);
                                                        thisObj.cmb_subdistrict.load({
                                                            params: {
                                                                DistrictID: r.data.DistrictID
                                                            },
                                                            callback: function(records, operation, success){
                                                                if (success == true) {
                                                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SubDistrictID').setValue(r.data.SubDistrictID);
                                                                    thisObj.cmb_village.load({
                                                                        params: {
                                                                            SubdistrictID: r.data.SubDistrictID
                                                                        },
                                                                        callback: function(records, operation, success){
                                                                            if (success == true) {
                                                                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VillageID').setValue(r.data.VillageID);
                                                                            }
                                                                        }
                                                                    });
                                                                }
                                                            }
                                                        });
                                                    }
                                                }
                                            });
                                        }
                                    }
                                });

                                //photo
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitOld').setValue(r.data.PhotoOfVisitPath);
                                if(r.data.PhotoOfVisit != ""){
                                    var fotoUser = r.data.PhotoOfVisit;
                                    //console.log(fotoUser);
                                    checkImageExists(fotoUser, function(existsImage) {
                                        if (existsImage == true) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisit').setSrc(fotoUser);
                                        } else {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisit').setSrc(m_api_base_url + '/images/no-image-icon.png');
                                        }
                                    });
                                }

                                //photo of fire
                                // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoofFireOld').setValue(r.data.PhotoOfFirePath);
                                // if(r.data.PhotoOfFire != ""){
                                //     var fotoFire = r.data.PhotoOfFire;
                                //     //console.log(fotoFire);
                                //     checkImageExists(fotoFire, function(existsImage) {
                                //         if (existsImage == true) {
                                //             Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoofFire').setSrc(fotoFire);
                                //         } else {
                                //             Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoofFire').setSrc(m_api_base_url + '/images/no-image-icon.png');
                                //         }
                                //     });
                                // }

                                //photo of soil erotaion
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotionOld').setValue(r.data.SoilErotionFilePath);
                                if(r.data.SoilErotionFile != ""){
                                    var fotoSoilErotion = r.data.SoilErotionFile;
                                    //console.log(fotoSoilErotion);
                                    checkImageExists(fotoSoilErotion, function(existsImage) {
                                        if (existsImage == true) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotion').setSrc(fotoSoilErotion);
                                        } else {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotion').setSrc(m_api_base_url + '/images/no-image-icon.png');
                                        }
                                    });
                                }

                                //photo of soil acc
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulationOld').setValue(r.data.SoilAccumulationFilePath);
                                if(r.data.SoilAccumulationFile != ""){
                                    var fotoSoilAcc = r.data.SoilAccumulationFile;
                                    //console.log(fotoSoilAcc);
                                    checkImageExists(fotoSoilAcc, function(existsImage) {
                                        if (existsImage == true) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulation').setSrc(fotoSoilAcc);
                                        } else {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulation').setSrc(m_api_base_url + '/images/no-image-icon.png');
                                        }
                                    });
                                }

                                //photo of Own Doc
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDocOld').setValue(r.data.OwnerDocPhotoPath);
                                if(r.data.OwnerDocPhoto != ""){
                                    var fotoDocOwn = r.data.OwnerDocPhoto;
                                    //console.log(fotoDocOwn);
                                    checkImageExists(fotoDocOwn, function(existsImage) {
                                        if (existsImage == true) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDoc').setSrc(fotoDocOwn);
                                        } else {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDoc').setSrc(m_api_base_url + '/images/no-image-icon.png');
                                        }
                                    });
                                }

                                //Farm photo
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarmOld').setValue(r.data.FarmPhotoPath);
                                if(r.data.FarmPhoto != ""){
                                    var fotoUser2 = r.data.FarmPhoto;
                                    //console.log(fotoUser);
                                    checkImageExists(fotoUser2, function(existsImage) {
                                        if (existsImage == true) {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarm').setSrc(fotoUser2);
                                        } else {
                                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarm').setSrc(m_api_base_url + '/images/no-image-icon.png');
                                        }
                                    });
                                }

                                //kasih readonly untuk field yg tak boleh ubah
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlotNr').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SurveyNr').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DateCollection').setReadOnly(true);
                                checkIfRegisFarmerChosen();

                                if(thisObj.viewVar.opsiDisplay == 'view'){
                                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-BtnSave').setVisible(false);
                                }

                                //autofill some form value (begin)
                                /*setTimeout(function(){ //akan jalan setelah 1,5 detik
                                    autofillPalmTreeCheck();
                                }, 1500);*/
                                //autofill some form value (end)
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
                //get data Farmer (End)

            }
        }
    },
    FormFlowHerbisida: function(OpsiChange){
        var DisableValue;
        var thisObj = this;

        if(OpsiChange == "Ya"){
            DisableValue = false;
        }else{
            DisableValue = true;
        }

        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseHerbi').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqHerbi').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeMoneySpentHerbi').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi1').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi1').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi2').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi2').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi3').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi3').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi4').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi4').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi5').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi5').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi6').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi6').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi7').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi7').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi8').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi8').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi9').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi9').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi10').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi10').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi11').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi11').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi12').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi12').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi13').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi13').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi14').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi14').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi15').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi15').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi16').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi16').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi17').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi17').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi18').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi18').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi19').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi19').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi20').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi20').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi21').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi21').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi22').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi22').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi23').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi23').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi24').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi24').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi25').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi25').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi26').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi26').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi27').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi27').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi28').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi28').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi29').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbi29').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbiOther').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeHerbiOther').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AppliedHerbicide1').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AppliedHerbicide2').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AppliedHerbicide3').setDisabled(DisableValue);

        

        //Cek pertanyaan pestisida
        thisObj.FormFlowGeneralPestisida('herbi',OpsiChange);
    },
    FormFlowInsektisida: function(OpsiChange){
        var DisableValue;
        var thisObj = this;

        if(OpsiChange == "Ya"){
            DisableValue = false;
        }else{
            DisableValue = true;
        }

        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseInsec').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqInsec').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeMoneySpentInsec').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec1').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec1').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec2').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec2').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec3').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec3').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec4').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec4').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec5').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec5').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec6').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec6').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec7').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec7').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec8').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec8').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec9').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec9').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec10').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec10').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec11').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec11').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec12').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec12').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec13').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec13').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec14').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec14').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec15').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec15').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec16').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec16').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec17').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec17').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec18').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec18').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec19').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec19').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec20').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec20').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec21').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec21').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec22').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec22').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec23').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec23').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec24').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsec24').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsecOther').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeInsecOther').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecAppliedOn1').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecAppliedOn2').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecRatControl').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecCaterpillarControl').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-InsecOryctesControl').setDisabled(DisableValue);

        //Cek pertanyaan pestisida
        thisObj.FormFlowGeneralPestisida('insect',OpsiChange);
    },
    FormFlowFungisida: function(OpsiChange){
        var DisableValue;
        var thisObj = this;

        if(OpsiChange == "Ya"){
            DisableValue = false;
        }else{
            DisableValue = true;
        }

        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseFungi').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqFungi').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeMoneySpentFungi').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi1').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi1').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi2').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi2').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi3').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi3').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi4').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi4').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi5').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi5').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi6').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi6').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi7').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi7').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi8').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi8').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi9').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi9').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi10').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi10').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi11').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi11').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungi12').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungi12').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungiOther').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FrequencyPeFungiOther').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FungiAppliedOn1').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FungiAppliedOn2').setDisabled(DisableValue);
        

        //Cek pertanyaan pestisida
        thisObj.FormFlowGeneralPestisida('fungi',OpsiChange);
    },
    FormFlowGeneralPestisida: function(OpsiCall,OpsiChange){
        var Herbisida = parseInt(Ext.ComponentQuery.query('[name=Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingHerbicide]')[0].getGroupValue());
        var Insectisida = parseInt(Ext.ComponentQuery.query('[name=Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingInsecticide]')[0].getGroupValue());
        var Fungisida = parseInt(Ext.ComponentQuery.query('[name=Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingFungicide]')[0].getGroupValue());
        var DisableValue = false;

        switch(OpsiCall){
            case 'herbi':
                if(OpsiChange == "Ya")
                    Herbisida = 1;
                else
                    Herbisida = 2;
            break;
            case 'insect':
                if(OpsiChange == "Ya")
                    Insectisida = 1;
                else
                    Insectisida = 2;
            break;
            case 'fungi':
                if(OpsiChange == "Ya")
                    Fungisida = 1;
                else
                    Fungisida = 2;
            break;
        }

        if(Herbisida == 2 && Insectisida == 2 && Fungisida == 2){
            DisableValue = true;
        }

        //console.log(Herbisida);
        //console.log(Insectisida);
        //console.log(Fungisida);

        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-RowPestPackageAfterUse').setDisabled(DisableValue);
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-RowPestStoreLocation').setDisabled(DisableValue);
        // Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-RowPestApplies').setDisabled(DisableValue);
        
    }
});