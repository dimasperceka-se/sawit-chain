/*
* @Author: nikolius
* @Date:   2017-05-31 12:06:31
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-21 11:05:59
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. Store yg panggil
    3. MemberID
    4. PlotNr
    5. SurveyNr
    6. DateCollection
    7. store.Grower.GridPlotStatus (spesial)
*/

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

    var total = treeTBM + treeTR + treeTM;
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').setValue(total);

    //validasi dengan TbmTmTr dengan planting material
//        var totPlantingMate = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').getValue());
//        if(total != totPlantingMate){
//            Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').addCls('notif-red');
//            Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').addCls('notif-red');
//        }else{
//            Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').removeCls('notif-red');
//            Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').removeCls('notif-red');
//        }
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
    var landOwnership = false; var ownerOfTheGarden = false;

    if(
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType2').checked == true ||
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType3').checked == true ||
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-LandOwnershipType4').checked == true
    )
        landOwnership = true;

    if(
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden2').checked == true ||
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfTheGarden3').checked == true
    )
        ownerOfTheGarden = true;

    if(landOwnership == true && ownerOfTheGarden == true){
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

Ext.define('Koltiva.view.PlotSurvey.WinFormPlotSurvey' ,{
extend: 'Ext.window.Window',
id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey',
title: lang('Garden Survey Form'),
closable: true,
modal: true,
closeAction: 'destroy',
width: '92%',
height: '88%',
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
                                xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Garden Information')+'</div>'
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaHa',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaHa',
                                fieldLabel: lang('Area of Garden (Ha)'),
                                labelWidth: 175,
                                allowNegative: false,
                                minValue: 0,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        calcPalmProduction();
                                        return false;
                                    }
                                }
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaPolygon',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-GardenAreaPolygon',
                                fieldLabel: lang('Area of Garden Polygon (Ha)'),
                                labelWidth: 175,
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
                                xtype: 'textfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Latitude',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Latitude',
                                allowNegative: false,
                                fieldLabel: lang('Latitude')
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Longitude',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Longitude',
                                allowNegative: false,
                                fieldLabel: lang('Longitude')
                            },{
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
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 1,
                                    layout:'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('Owner of the Garden')
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
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationInformation',
                                xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Owner of this plantation information')+'</div>',
                                disabled: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationNameText',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationNameText',
                                fieldLabel: lang('Name'),
                                disabled: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationLocationText',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationLocationText',
                                fieldLabel: lang('Location'),
                                disabled: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationPhoneText',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnerOfPlantationPhoneText',
                                fieldLabel: lang('Phone'),
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
                                        text: lang('Ownership Document')
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
                                            boxLabel: lang('No Document'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDoc1',
                                            listeners:{
                                                change: function(){
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
                                        }]
                                    },{
                                        columnWidth: 0.475,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
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
                                    columnWidth: 0.775,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocText',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-OwnershipDocText',
                                        disabled: true,
                                        emptyText: lang('Other Text')
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
                                xtype: 'numericfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageAgeTree',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AverageAgeTree',
                                fieldLabel: lang('Average age of trees on plantation? (years)'),
                                labelWidth: 260,
                                allowNegative: false,
                                minValue: 0
                            },{
                                fieldLabel: lang('Soil Type'),
                                xtype: 'radiogroup',
                                labelWidth: 260,
                                columns: 3,
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
                                    boxLabel: lang('Hilly'),
                                    name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TopographyType',
                                    inputValue: '2',
                                    id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TopographyType2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('Mountainous'),
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
                                xtype: 'combobox',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FirstPlantingYear',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FirstPlantingYear',
                                fieldLabel: lang('Year of first planting palm trees'),
                                labelWidth: 190,
                                store: cmb_year_option,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style:'padding-left:15px;border-left: 1px dashed gray;',
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
                                        text: lang('Type of Planting Material (select all that apply)')
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
                                },{
                                    columnWidth: 0.05,
                                    layout: 'form',
                                    items:[{}]
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
                                },{
                                    columnWidth: 0.05,
                                    layout: 'form',
                                    items:[{}]
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
                                },{
                                    columnWidth: 0.05,
                                    layout: 'form',
                                    items:[{}]
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
                                xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Farm Production')+'</div>'
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
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HighSeasonProduction',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-HighSeasonProduction',
                                fieldLabel: lang('High Season Production (ton)'),
                                labelWidth: 275,
                                allowNegative: false,
                                minValue: 0,
                                readOnly: true
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
                                xtype: 'numericfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AnnualProduction',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-AnnualProduction',
                                fieldLabel: lang('Annual Production (TON)'),
                                labelWidth: 275,
                                allowNegative: false,
                                minValue: 0,
                                readOnly: true
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
                                html:'<div style="margin-top:-4px;" class="subtitleForm">'+lang('Fertilizing using non organic/chemical')+'</div>'
                            },{
                                fieldLabel: lang('Non Organic / Chemical Fertilizing Data'),
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
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'numericfield',
                                fieldLabel: lang('How much money did you spend in the past 24 months on Chemical Fertilizers'),
                                labelWidth: 525,
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertMoneySpentNonOrganic',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertMoneySpentNonOrganic',
                                emptyText: lang('in rupiah'),
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
                                        text: lang('Times/Year')
                                    }]
                                },{
                                    columnWidth: 0.15,
                                    layout: 'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('Dose/Plot/Times per Year')
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
                                        text: lang('Dose/Plot/Year')
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
                            },
                            /*{
                                xtype: 'checkboxgroup',
                                fieldLabel: lang('Which trees are fertilized with Non Organic/Chemical'),
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
                            },*/
                            {
                                html:'<br /><div style="margin-top:-4px;" class="subtitleForm">'+lang('Fertilizing using compost and organic')+'</div>'
                            },{
                                fieldLabel: lang('Do you use compost and/ organic fertilizer'),
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
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'numericfield',
                                fieldLabel: lang('How much money did you spend in the past 24 months on Compost and Organic Fertilizers'),
                                labelWidth: 525,
                                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertMoneySpentOrganic',
                                name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FertMoneySpentOrganic',
                                emptyText: lang('in rupiah'),
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
                                        text: lang('Times/Year')
                                    }]
                                },{
                                    columnWidth: 0.15,
                                    layout: 'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('Dose/Plot/Times per Year')
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
                                        text: lang('Dose/Plot/Year')
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
                                fieldLabel: lang('Which trees are fertilized using compost and/ Organic'),
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
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        style: 'font-weight:bold;text-align:center;',
                                        text: lang('Herbicide')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        style: 'font-weight:bold;text-align:center;',
                                        text: lang('Insecticide')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        style: 'font-weight:bold;text-align:center;',
                                        text: lang('Fungicide')
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'radiogroup',
                                        columns: 2,
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
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingHerbicide',
                                            inputValue: '0',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingHerbicideNo',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        }]
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'radiogroup',
                                        columns: 2,
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
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingInsecticide',
                                            inputValue: '0',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingInsecticideNo',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        }]
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'radiogroup',
                                        columns: 2,
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
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingFungicide',
                                            inputValue: '0',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeUsingFungicideNo',
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
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('How much money did you spend in the past 24 months')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeMoneySpentHerbi',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeMoneySpentHerbi',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('in rupiah')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeMoneySpentInsec',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeMoneySpentInsec',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('in rupiah')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeMoneySpentFungi',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeMoneySpentFungi',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('in rupiah')
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
                                        text: lang('Pesticides Frequency (Times/Year)')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqHerbi',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqHerbi',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('Times/Year'),
                                        listeners:{
                                            change: function(){
                                                calcTotalUsageHerbi();
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqInsec',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqInsec',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('Times/Year'),
                                        listeners:{
                                            change: function(){
                                                calcTotalUsageInsec();
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqFungi',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFreqFungi',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('Times/Year'),
                                        listeners:{
                                            change: function(){
                                                calcTotalUsageFungi();
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                hidden:true, //Di hide, mirip dengan CT
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('Pesticides Dosage (Dose/Plot/Times per Year)')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseHerbi',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseHerbi',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('Dose/Plot/Times per Year'),
                                        listeners:{
                                            change: function(){
                                                calcTotalUsageHerbi();
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseInsec',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseInsec',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('Dose/Plot/Times per Year'),
                                        listeners:{
                                            change: function(){
                                                calcTotalUsageInsec();
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseFungi',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeDoseFungi',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('Dose/Plot/Times per Year'),
                                        listeners:{
                                            change: function(){
                                                calcTotalUsageFungi();
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                hidden:true, //Di hide, mirip dengan CT
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('Total Usage (Dose/Plot/Year)')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeTotalUsageHerbi',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeTotalUsageHerbi',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('Dose/Plot/Year'),
                                        readOnly: true
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeTotalUsageInsec',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeTotalUsageInsec',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('Dose/Plot/Year'),
                                        readOnly: true
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeTotalUsageFungi',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeTotalUsageFungi',
                                        allowNegative: false,
                                        minValue: 0,
                                        emptyText: lang('Dose/Plot/Year'),
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
                                        text: lang('Brand')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'checkboxfield',
                                        boxLabel: lang('Bento'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec4',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec4',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'checkboxfield',
                                        boxLabel: lang('Regent'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec5',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec5',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'checkboxfield',
                                        boxLabel: 'Bravoxone',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi12',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbi12',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungiOther',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeFungiOther',
                                        emptyText: lang('Other Brand')
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'checkboxfield',
                                        boxLabel: lang('Organic'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec23',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsec23',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsecOther',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeInsecOther',
                                        emptyText: lang('Other Brand')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
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
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                margin:'-12px 0 0 0',
                                items:[{
                                    columnWidth: 0.35,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbiOther',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PeHerbiOther',
                                        emptyText: lang('Other Brand')
                                    }]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
                                },{
                                    columnWidth: 0.215,
                                    layout: 'form',
                                    items:[{}]
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
                            items:[{
                                fieldLabel: lang('Dimana anda menyimpan pestisida sebelum dan selama pemakaian'),
                                xtype: 'radiogroup',
                                labelAlign: 'top',
                                columns: 2,
                                items: [{
                                    boxLabel  : lang('In the house'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation1',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Pesticide specific place'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation2',
                                    inputValue: '2'
                                },{
                                    boxLabel  : lang('Outside of the house (house area)'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation3',
                                    inputValue: '3'
                                },{
                                    boxLabel  : lang('Outside of the cocoa farm'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation4',
                                    inputValue: '4'
                                },{
                                    boxLabel  : lang('Others'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestStoreLocation5',
                                    inputValue: '5'
                                }]
                            },{
                                html:'<div>&nbsp;</div>'
                            },{
                                fieldLabel: lang('Apa yang anda lakukan dengan kemasan pestisida setelah pemakaian'),
                                xtype: 'radiogroup',
                                labelAlign: 'top',
                                columns: 2,
                                items: [{
                                    boxLabel  : lang('Random disposal (Garden or around the house)'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse1',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Use for something else'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse2',
                                    inputValue: '2'
                                },{
                                    boxLabel  : lang('Thoroughly and then burry it'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse3',
                                    inputValue: '3'
                                },{
                                    boxLabel  : lang('Burn'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse4',
                                    inputValue: '4'
                                },{
                                    boxLabel  : lang('Recycle'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse5',
                                    inputValue: '5'
                                },{
                                    boxLabel  : lang('Others'),
                                    name      : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse',
                                    id        : 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestPackageAfterUse6',
                                    inputValue: '6'
                                }]
                            }]
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
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'checkboxfield',
                                        items:[{
                                            boxLabel: lang('Rats'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainRats',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainRats'
                                        },{
                                            boxLabel: lang('Satora Nitens'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainSatora',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainSatora'
                                        },{
                                            boxLabel: lang('Rinocheros Beetle'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainRhino',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainRhino'
                                        },{
                                            boxLabel: lang('Orang Utan'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOrgUtan',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOrgUtan'
                                        },{
                                            boxLabel: lang('Babi'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainBabi',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainBabi'
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'checkboxfield',
                                        items:[{
                                            boxLabel: lang('Olygonichus'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOly',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOly'
                                        },{
                                            boxLabel: lang('Tirathaba Mundella'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainTira',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainTira'
                                        },{
                                            boxLabel: lang('Elephant'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainElep',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainElep'
                                        },{
                                            boxLabel: lang('Landak'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainLandak',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainLandak'
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
                                        xtype:'checkboxfield',
                                        boxLabel: lang('Lainnya'),
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOther',
                                        inputValue: '1',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOther',
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
                                    }]
                                },{
                                    columnWidth: 0.775,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOtherText',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PestMainOtherText',
                                        disabled: true,
                                        emptyText: lang('Other Text')
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
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'checkboxfield',
                                        items:[{
                                            boxLabel: lang('Blast Disease'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainBlast',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainBlast'
                                        },{
                                            boxLabel: lang('Upper Steam Rot'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainSteam',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainSteam'
                                        },{
                                            boxLabel: lang('Spear Rot'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainSpear',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainSpear'
                                        },{
                                            boxLabel: lang('Anthracnose'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainAnt',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainAnt'
                                        },{
                                            boxLabel: lang('Viscular Wilt'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainViscular',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainViscular'
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'checkboxfield',
                                        items:[{
                                            boxLabel: lang('Basal Steam Rot / Genoderma'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainGeno',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainGeno'
                                        },{
                                            boxLabel: lang('Bud Rot'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainBud',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainBud'
                                        },{
                                            boxLabel: lang('Patch Yellow'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainYellow',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainYellow'
                                        },{
                                            boxLabel: lang('Crown disease'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainCrown',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainCrown'
                                        },{
                                            boxLabel: lang('Bunch Rot'),
                                            name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainBunch',
                                            inputValue: '1',
                                            id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainBunch'
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
                                        xtype:'checkboxfield',
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
                                    }]
                                },{
                                    columnWidth: 0.775,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainOtherText',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DisMainOtherText',
                                        disabled: true,
                                        emptyText: lang('Other Text')
                                    }]
                                }]
                            }]
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
//                    if(totalPlantMateTree == 0 && totalTMTBMTRTree == 0){
//                        console.log('Manual Valid');
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
            console.log(totalTMTBMTRTree);
            console.log(MinTreeByHa);
            console.log(MaxTreeByHa);

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
        cls: 'Sfr_BtnFormClose',
        overCls: 'Sfr_BtnFormClose-Hover',
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

        //set MemberID
        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberID').setValue(thisObj.viewVar.MemberID);

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
                    console.log("usual");
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
}
});