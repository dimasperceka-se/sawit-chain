/*
* @Author: nikolius
* @Date:   2017-11-02 16:33:14
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 11:37:01
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. Store yg panggil
    3. MemberID
    4. SurveyNr
    5. DateCollection
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey',
    title: lang('Finance Survey Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '96%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store --------------------------------------------------------------------------------------------------------------- (begin)
        var cmb_survey_nr = Ext.create('Koltiva.store.PlotSurvey.CmbSurveyNr');
        //store --------------------------------------------------------------------------------------------------------------- (end)

        //Form Flow Function ---------------------------------------------------------------------------------------------- (begin)
        function formFlowInvestPurchasedLivestock(){
            if(Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvestOnLivestockNo').checked == true){
                Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestockLabel').setStyle('opacity',0.3);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock1').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock2').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock3').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock4').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aValueOfLivestock').setDisabled(true);
            }else{
                Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestockLabel').setStyle('opacity',1);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock1').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock2').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock3').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock4').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aValueOfLivestock').setDisabled(false);
            }
        }

        function formFlowRevenueRemit(){
            if(Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aRevenueRemitNo').checked == true){
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aValueRemitPerYear').setDisabled(true);
            }else{
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aValueRemitPerYear').setDisabled(false);
            }
        }

        function formFlowNonAgriBusiness(){
            if(Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvolvedNonAgriBusinessNo').checked == true){
                Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusinessLabel').setStyle('opacity',0.3);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness1').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness2').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness3').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness4').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness5').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness6').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness7').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aRevenueToHousehold').setDisabled(true);
            }else{
                Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusinessLabel').setStyle('opacity',1);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness1').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness2').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness3').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness4').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness5').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness6').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness7').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aRevenueToHousehold').setDisabled(false);
            }
        }

        function formFlowOutstandingDebts(){
            if(Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bHaveOutstandingDebtsNo').checked == true){
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bValueOfDebt').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTenorYear').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTimeToMature').setDisabled(true);
                Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRateLabel').setStyle('opacity',0.3);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate1').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate2').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate3').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bHowMuchInterestRate').setDisabled(true);
                Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoanLabel').setStyle('opacity',0.3);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan1').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan2').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan3').setDisabled(true);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan4').setDisabled(true);

                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBPR').setValue('');
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfCoop').setValue('');
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBank').setValue('');
            }else{
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bValueOfDebt').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTenorYear').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTimeToMature').setDisabled(false);
                Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRateLabel').setStyle('opacity',1);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate1').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate2').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate3').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bHowMuchInterestRate').setDisabled(false);
                Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoanLabel').setStyle('opacity',1);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan1').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan2').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan3').setDisabled(false);
                Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan4').setDisabled(false);
            }
        }

        function formFlowWhereHaveLoan(){
            var rgValue = Ext.ComponentQuery.query('[name=Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan]')[0].getGroupValue();

            switch(rgValue){
                case '1':
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBPR').setDisabled(false);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfCoop').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBank').setDisabled(true);
                break;
                case '2':
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBPR').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfCoop').setDisabled(false);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBank').setDisabled(true);
                break;
                case '3':
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBPR').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfCoop').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBank').setDisabled(false);
                break;
                case '4':
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBPR').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfCoop').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBank').setDisabled(true);
                break;
                default:
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBPR').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfCoop').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBank').setDisabled(true);
                break;
            }
        }

        function formFlowNotAccessibleFert(){
            var rgValue = Ext.ComponentQuery.query('[name=Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bFertAccess]')[0].getGroupValue();

            switch(rgValue){
                case '1':
                case '2':
                    Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancingLabel').setStyle('opacity',1);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing1').setDisabled(false);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing2').setDisabled(false);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing3').setDisabled(false);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing4').setDisabled(false);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing5').setDisabled(false);
                break;
                case '3':
                    Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancingLabel').setStyle('opacity',0.3);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing1').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing2').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing3').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing4').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing5').setDisabled(true);
                break;
                default:
                    Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancingLabel').setStyle('opacity',0.3);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing1').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing2').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing3').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing4').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing5').setDisabled(true);
                break;
            }
        }

        function formFlowApplyNewLoan(){
            var rgValue = Ext.ComponentQuery.query('[name=Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoan]')[0].getGroupValue();

            switch(rgValue){
                case '3':
                case '4':
                    Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRateLabel').setStyle('opacity',1);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate1').setDisabled(false);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate2').setDisabled(false);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate3').setDisabled(false);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate4').setDisabled(false);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate5').setDisabled(false);
                break;
                case '1':
                case '2':
                    Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRateLabel').setStyle('opacity',0.3);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate1').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate2').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate3').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate4').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate5').setDisabled(true);
                break;
                default:
                    Ext.get('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRateLabel').setStyle('opacity',0.3);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate1').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate2').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate3').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate4').setDisabled(true);
                    Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate5').setDisabled(true);
                break;
            }
        }
        //Form Flow Function ---------------------------------------------------------------------------------------------- (end)

        //items --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    style: 'border-bottom: 1px dashed gray;',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.495,
                            style:'padding-right:25px;',
                            layout:'form',
                            items:[{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-MemberID',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-MemberID'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-MemberDisplayID',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-MemberDisplayID',
                                fieldLabel: lang('Farmer ID'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-MemberName',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-MemberName',
                                fieldLabel: lang('Farmer Name'),
                                readOnly: true
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-SurveyNr',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-SurveyNr',
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
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-DateCollection',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-DateCollection',
                                fieldLabel: lang('Date Collection'),
                                allowBlank: false,
                                format: 'Y-m-d H:i:s'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-CreatedByLabel',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-CreatedByLabel',
                                fieldLabel: lang('Enumerator'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-ModifiedByLabel',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-ModifiedByLabel',
                                fieldLabel: lang('Modified by'),
                                readOnly: true
                            }]
                        }]
                    }]
                },{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.495,
                            style:'padding-right:25px;border-right: 1px dashed gray;',
                            layout:'form',
                            items:[{
                                html:'<div style="margin-top:-4px;" class="subtitleForm">'+lang('Additional Sources of Income')+'</div>'
                            },{
                                fieldLabel: lang('Do you invest in the purchase of livestock ?'),
                                xtype: 'radiogroup',
                                labelWidth: 350,
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvestOnLivestock',
                                    inputValue: '1',
                                    id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvestOnLivestockYes',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvestOnLivestock',
                                    inputValue: '2',
                                    id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvestOnLivestockNo',
                                    listeners:{
                                        change: function(){
                                            formFlowInvestPurchasedLivestock();
                                            return false;
                                        }
                                    }
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
                                        id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestockLabel',
                                        text: lang('What type of livestock ?')
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
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Goat'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock1',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Cow'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock',
                                            inputValue: '3',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock3',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Chicken'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock2',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Others'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock',
                                            inputValue: '4',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfLivestock4',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        }]
                                    }]
                                }]
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aValueOfLivestock',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aValueOfLivestock',
                                fieldLabel: lang('Value of Livestock')+' (IDR)',
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aMonthlyIncomeOtherCrop',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aMonthlyIncomeOtherCrop',
                                fieldLabel: lang('Monthly Income from Other Crops')+' (IDR)',
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            },{
                                fieldLabel: lang('Do you receive revenues from remittances ?'),
                                xtype: 'radiogroup',
                                labelWidth: 350,
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aRevenueRemit',
                                    inputValue: '1',
                                    id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aRevenueRemitYes',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aRevenueRemit',
                                    inputValue: '2',
                                    id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aRevenueRemitNo',
                                    listeners:{
                                        change: function(){
                                            formFlowRevenueRemit();
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aValueRemitPerYear',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aValueRemitPerYear',
                                fieldLabel: lang('Value Remitttance (IDR/Year)'),
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            },{
                                fieldLabel: lang('Are you or any member of your family involved in non agri businesses ?'),
                                xtype: 'radiogroup',
                                labelWidth: 350,
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvolvedNonAgriBusiness',
                                    inputValue: '1',
                                    id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvolvedNonAgriBusinessYes',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvolvedNonAgriBusiness',
                                    inputValue: '2',
                                    id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvolvedNonAgriBusinessNo',
                                    listeners:{
                                        change: function(){
                                            formFlowNonAgriBusiness();
                                            return false;
                                        }
                                    }
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
                                        id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusinessLabel',
                                        text: lang('Specify Type')+' :'
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
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Trading'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness1',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Government Job'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness',
                                            inputValue: '3',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness3',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Fisherman'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness',
                                            inputValue: '5',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness5',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Services'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness',
                                            inputValue: '7',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness7',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Construction'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness2',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Teacher'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness',
                                            inputValue: '4',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness4',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Store'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness',
                                            inputValue: '6',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTypeOfNonAgriBusiness6',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        }]
                                    }]
                                }]
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aRevenueToHousehold',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aRevenueToHousehold',
                                fieldLabel: lang('Revenue IDR/Month to Household'),
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aIncomeOtherPlot',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aIncomeOtherPlot',
                                fieldLabel: lang('How much income can you get from other plots per Year (IDR)?'),
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTransportCost',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aTransportCost',
                                fieldLabel: lang('Transportation Cost to the Nearest Point of Sale (IDR/Month)'),
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Financial Capacity')+'</div>'
                            },{
                                fieldLabel: lang('Do you currently have outstanding debts ?'),
                                xtype: 'radiogroup',
                                labelWidth: 350,
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bHaveOutstandingDebts',
                                    inputValue: '1',
                                    id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bHaveOutstandingDebtsYes',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bHaveOutstandingDebts',
                                    inputValue: '2',
                                    id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bHaveOutstandingDebtsNo',
                                    listeners:{
                                        change: function(){
                                            formFlowOutstandingDebts();
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bValueOfDebt',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bValueOfDebt',
                                fieldLabel: lang('Value of Debt')+' (IDR)',
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTenorYear',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTenorYear',
                                fieldLabel: lang('Tenor (years)'),
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTimeToMature',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTimeToMature',
                                fieldLabel: lang('Time to Maturity (months)'),
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 1,
                                    layout:'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRateLabel',
                                        text: lang('Interest Rate (for outstanding debt)')
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
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Annual'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate1',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Weekly'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate',
                                            inputValue: '3',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate3',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Monthly'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bInterestRate2',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        }]
                                    }]
                                }]
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bHowMuchInterestRate',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bHowMuchInterestRate',
                                fieldLabel: lang('How much interest rate? (% / year)'),
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            }]
                        },{
                            columnWidth: 0.5,
                            style:'padding-left:15px;',
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
                                        id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoanLabel',
                                        text: lang('Where do you have your loan ?')
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
                                            xtype: 'radiofield',
                                            boxLabel: lang('BPR'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan1',
                                            listeners:{
                                                change: function(obj, radioGroup){
                                                    formFlowWhereHaveLoan();
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.05,
                                        border: false,
                                        items:[{}]
                                    },{
                                        columnWidth: 0.6,
                                        border: false,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBPR',
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBPR',
                                            emptyText: lang('Name of BPR')
                                        }]
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
                                            xtype: 'radiofield',
                                            boxLabel: lang('Cooperative'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan2',
                                            listeners:{
                                                change: function(){
                                                    formFlowWhereHaveLoan();
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.05,
                                        border: false,
                                        items:[{}]
                                    },{
                                        columnWidth: 0.6,
                                        border: false,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfCoop',
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfCoop',
                                            emptyText: lang('Name of Cooperative')
                                        }]
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
                                            xtype: 'radiofield',
                                            boxLabel: lang('Bank'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan',
                                            inputValue: '3',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan3',
                                            listeners:{
                                                change: function(){
                                                    formFlowWhereHaveLoan();
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.05,
                                        border: false,
                                        items:[{}]
                                    },{
                                        columnWidth: 0.6,
                                        border: false,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBank',
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBank',
                                            emptyText: lang('Name of Bank')
                                        }]
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
                                            xtype: 'radiofield',
                                            boxLabel: lang('Friends / Other'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan',
                                            inputValue: '4',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bWhereDoYouHaveLoan4',
                                            listeners:{
                                                change: function(){
                                                    formFlowWhereHaveLoan();
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.05,
                                        border: false,
                                        items:[{}]
                                    },{
                                        columnWidth: 0.6,
                                        border: false,
                                        items:[{}]
                                    }]
                                }]
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bLevelCurrentSavings',
                                name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bLevelCurrentSavings',
                                fieldLabel: lang('Level of Current Savings (IDR)'),
                                labelWidth: 350,
                                allowNegative: false,
                                minValue: 0
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 1,
                                    layout:'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTypeOfHealthInsuranceLabel',
                                        text: lang('Which type of health insurance do you have ?')
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
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Basic'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTypeOfHealthInsurance',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTypeOfHealthInsurance1',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Full'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTypeOfHealthInsurance',
                                            inputValue: '3',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTypeOfHealthInsurance3',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Medium'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTypeOfHealthInsurance',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTypeOfHealthInsurance2',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Don\'t Have'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTypeOfHealthInsurance',
                                            inputValue: '4',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bTypeOfHealthInsurance4',
                                            listeners: {
                                                change: function() {
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
                                        id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bFertAccessLabel',
                                        text: lang('How accessible / Are you able to get fertilizers on time ?')
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
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Yes, Accessible'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bFertAccess',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bFertAccess1',
                                            listeners: {
                                                change: function() {
                                                    formFlowNotAccessibleFert();
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No, Not Accessible'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bFertAccess',
                                            inputValue: '3',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bFertAccess3',
                                            listeners: {
                                                change: function() {
                                                    formFlowNotAccessibleFert();
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Sometime Difficult'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bFertAccess',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bFertAccess2',
                                            listeners: {
                                                change: function() {
                                                    formFlowNotAccessibleFert();
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
                                        id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancingLabel',
                                        text: lang('Source of financing and how do you pay for farm investments (fertilizers) ?')
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
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Own Savings'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing1',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Trader'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing',
                                            inputValue: '3',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing3',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Bank'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing',
                                            inputValue: '5',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing5',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Family or Friends'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing2',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('KUD'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing',
                                            inputValue: '4',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bSourceOfFinancing4',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        }]
                                    }]
                                }]
                            },{
                                xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Loan Facility')+'</div>'
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 1,
                                    layout:'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoanLabel',
                                        text: lang('Given the opportunity how likely are you to apply for a new loan from a financial institution to conduct tree replantation ?')
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
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('I would not apply'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoan',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoan1',
                                            listeners: {
                                                change: function() {
                                                    formFlowApplyNewLoan();
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('It is likely that I would apply'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoan',
                                            inputValue: '3',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoan3',
                                            listeners: {
                                                change: function() {
                                                    formFlowApplyNewLoan();
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('It is unlikely that I would apply'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoan',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoan2',
                                            listeners: {
                                                change: function() {
                                                    formFlowApplyNewLoan();
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('I would definitely apply'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoan',
                                            inputValue: '4',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoan4',
                                            listeners: {
                                                change: function() {
                                                    formFlowApplyNewLoan();
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
                                        id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRateLabel',
                                        text: lang('What would be the acceptable interest rate ?')
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
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('18% p.a'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate1',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('14% p.a'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate',
                                            inputValue: '3',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate3',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('< 10% p.a'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate',
                                            inputValue: '5',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate5',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('16% p.a'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate2',
                                            listeners: {
                                                change: function() {
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('12% p.a'),
                                            name: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate',
                                            inputValue: '4',
                                            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cAcceptableInterestRate4',
                                            listeners: {
                                                change: function() {
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
        }];
        //items --------------------------------------------------------------------------------------------------------------- (end)

        //buttons --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: 'Save',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-BtnSave',
            handler: function () {
                var formNya = Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form').getForm();
                if (formNya.isValid()) {

                    formNya.submit({
                        url: m_api + '/finance_survey/survey',
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
                        msg: lang('Form not complete yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons --------------------------------------------------------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form');
            formNya.getForm().reset();

            //set MemberID
            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-MemberID').setValue(thisObj.viewVar.MemberID);

            //set nilai default form (begin)
            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvestOnLivestockNo').setValue(true);
            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aRevenueRemitNo').setValue(true);
            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-aInvolvedNonAgriBusinessNo').setValue(true);
            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bHaveOutstandingDebtsNo').setValue(true);
            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBPR').setDisabled(true);
            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfCoop').setDisabled(true);
            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bNameOfBank').setDisabled(true);
            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-bFertAccess3').setValue(true);
            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-cApplyNewLoan2').setValue(true);
            //set nilai default form (end)

            if(thisObj.viewVar.opsiDisplay == 'insert'){
                //get var yg diperlukan
                Ext.Ajax.request({
                    waitMsg: lang('Please Wait'),
                    url: m_api + '/grower/member_data_detail',
                    method : 'GET',
                    params: {MemberID:  thisObj.viewVar.MemberID},
                    success: function(response, opts){
                        var r = Ext.decode(response.responseText);

                        Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-MemberDisplayID').setValue(r.data.MemberDisplayID);
                        Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-MemberName').setValue(r.data.MemberName);
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

                //load formnya
                formNya.getForm().load({
                    url: m_api + '/finance_survey/finance_survey_form_data',
                    method: 'GET',
                    params: {
                        MemberID: thisObj.viewVar.MemberID,
                        SurveyNr: thisObj.viewVar.SurveyNr
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        //kasih readonly untuk field yg tak boleh ubah
                        Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-SurveyNr').setReadOnly(true);
                        Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-DateCollection').setReadOnly(true);

                        if(thisObj.viewVar.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey-Form-BtnSave').setVisible(false);
                        }
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
    }
});