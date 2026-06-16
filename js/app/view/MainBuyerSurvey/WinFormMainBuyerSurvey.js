/*
* @Author: nikolius
* @Date:   2017-06-01 13:41:19
* @Last Modified by:   nikolius
* @Last Modified time: 2018-04-04 16:36:38
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. Store yg panggil
    3. MemberID
    4. SurveyNr
    5. DateCollection
    6. PlotNr
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
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey',
    title: lang('Main Buyer Survey Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '75%',
    height: '80%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store --------------------------------------------------------------------------------------------------------------- (begin)
        var cmb_survey_nr = Ext.create('Koltiva.store.PlotSurvey.CmbSurveyNr');
        var cmb_buyer_type = Ext.create('Koltiva.store.MainBuyerSurvey.CmbBuyerType');
        var cmb_transport_mode = Ext.create('Koltiva.store.MainBuyerSurvey.CmbTransportMode');
        var cmb_disagree_agree = Ext.create('Koltiva.store.MainBuyerSurvey.CmbDisagreeAgree');
        var cmb_disagree_agree_only = Ext.create('Koltiva.store.MainBuyerSurvey.CmbDisagreeAgreeOnly');
        var cmb_percentage = Ext.create('Koltiva.store.MainBuyerSurvey.CmbPercentage');
        var cmb_condition_state = Ext.create('Koltiva.store.MainBuyerSurvey.CmbConditionState');
        //store --------------------------------------------------------------------------------------------------------------- (end)

        //items ---------------------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form',
            fileUpload: true,
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
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberID',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberID'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberDisplayID',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberDisplayID',
                                fieldLabel: lang('Farmer ID'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberName',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberName',
                                fieldLabel: lang('Farmer Name'),
                                readOnly: true
                            },{
                                xtype: 'numberfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-PlotNr',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-PlotNr',
                                fieldLabel: lang('Garden Nr'),
                                allowBlank: false,
                                minValue: 1
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-SurveyNr',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-SurveyNr',
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
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-DateCollection',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-DateCollection',
                                fieldLabel: lang('Date Collection'),
                                allowBlank: false,
                                format: 'Y-m-d H:i:s'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-CreatedByLabel',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-CreatedByLabel',
                                fieldLabel: lang('Enumerator'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ModifiedByLabel',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ModifiedByLabel',
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
                            style:'padding-right:25px;',
                            layout:'form',
                            items:[{
                                xtype: 'combobox',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-BuyerType',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-BuyerType',
                                store: cmb_buyer_type,
                                fieldLabel: lang('Buyer Type'),
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-BuyerName',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-BuyerName',
                                fieldLabel: lang('Buyer Name')
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-DistanceToBuyer',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-DistanceToBuyer',
                                fieldLabel: lang('Distance from farm to buyer (Km)'),
                                labelWidth: 300,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-TransportMode',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-TransportMode',
                                store: cmb_transport_mode,
                                fieldLabel: lang('Transportation Mode'),
                                labelWidth: 200,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-FFBPriceLastSold',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-FFBPriceLastSold',
                                fieldLabel: lang('FFB price last time sold (Rp/Kg)'),
                                labelWidth: 300,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'datefield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-FFBLastSoldDate',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-FFBLastSoldDate',
                                fieldLabel: lang('When was the last time you sold your FFB ?'),
                                labelWidth: 300,
                                format: 'Y-m-d'
                            },{
                                fieldLabel: lang('Is this FFB price after deducting transportation cost, loading cost, weighing cost and other related selling cost ?'),
                                xtype: 'radiogroup',
                                labelWidth: 300,
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-isFFBPriceAfterReduce',
                                    inputValue: '1',
                                    id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-isFFBPriceAfterReduceYes',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-isFFBPriceAfterReduce',
                                    inputValue: '2',
                                    id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-isFFBPriceAfterReduceNo',
                                    listeners:{
                                        change: function(){
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
                                        text: lang('To which mill did you sell your FFB to in the last year ?')
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
                                            boxLabel: lang('Tidak tahu'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '1',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear1',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Sumber Kencana Indo Palma (SKIP)'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '3',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear3',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Bumi Inti Mekar (BIM)'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '5',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear5',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Riau Agri'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '7',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear7',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Kencana Amal Tani (KAT)'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '9',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear9',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Inecda'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '11',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear11',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Swakarsa Sawit Raya (SSR)'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '13',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear13',
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
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '2',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear2',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Nikmat Halona Reksa (NHR)'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '4',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear4',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Berkat Sawit Sejahtera (BSS)'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '6',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear6',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Soegih Riesta Jaya (SRJ)'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '8',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear8',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Sumatera Makmur Lestari (SML) – Harpena'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '10',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear10',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Talang Jerinjing Sawit (TJS)'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '12',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear12',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('PT. Banyu Bening Utama (BBU)'),
                                            name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                            inputValue: '14',
                                            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear14',
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
                                        name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear',
                                        inputValue: '15',
                                        id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYear15',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYearText').setDisabled(false);
                                                }else{
                                                    Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYearText').setDisabled(true);
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
                                        id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYearText',
                                        name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ToWhichMillSellFFBLastYearText',
                                        disabled: true,
                                        emptyText: lang('Other Text')
                                    }]
                                }]
                            },{
                                html: '<div></div>'
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-TransportationCost',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-TransportationCost',
                                fieldLabel: lang('How much is the transportation cost (Rp/Kg)'),
                                labelWidth: 300,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-OtherRelatedCost',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-OtherRelatedCost',
                                fieldLabel: lang('How much is the loading, weighing and other related cost (Rp/Kg)'),
                                labelWidth: 300,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-PenaltyDeduction',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-PenaltyDeduction',
                                fieldLabel: lang('Penalties and deductions (% of weight of FBB)'),
                                labelWidth: 300,
                                allowNegative: false,
                                minValue: 0
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
                                        id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFB',
                                        width: '160px',
                                        height:'210px',
                                        src: m_api_base_url + '/images/no-image-icon-port.png'
                                    },{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFBOld',
                                        name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFBOld',
                                        inputType: 'hidden'
                                    }]
                                }]
                            },{
                                xtype: 'fileuploadfield',
                                fieldLabel: lang('Picture of receipt last time FFB was sold'),
                                labelWidth: 300,
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFBInput',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFBInput',
                                buttonText: 'Browse',
                                listeners: {
                                    'change': function (fb, v) {
                                        Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form').getForm().submit({
                                            url: m_api + '/main_buyer_survey/last_receipt',
                                            clientValidation: false,
                                            params: {
                                                opsiDisplay: thisObj.viewVar.opsiDisplay,
                                                MemberID: thisObj.viewVar.MemberID
                                            },
                                            waitMsg: 'Sending Photo...',
                                            success: function (fp, o) {
                                                Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFB').setSrc(m_api_base_url + '/images/main_buyer_last_receipt/' + o.result.file);
                                                Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFBOld').setValue(o.result.file);
                                            }
                                        });
                                    }
                                }
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-HarvestingCost',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-HarvestingCost',
                                fieldLabel: lang('How much is the harvesting cost (Rp/Kg)'),
                                labelWidth: 300,
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-SatisfiedCropPriceLastYear',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-SatisfiedCropPriceLastYear',
                                store: cmb_disagree_agree,
                                fieldLabel: lang('Were you satisfied with the price you received for your crop last year ?'),
                                labelWidth: 300,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                hidden: true
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ExpectRelationContinue',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ExpectRelationContinue',
                                store: cmb_disagree_agree,
                                fieldLabel: lang('Do you expect that your relationship with your buyer will continue for a long time ?'),
                                labelWidth: 300,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-EstimatePercentHouseholdIncome',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-EstimatePercentHouseholdIncome',
                                store: cmb_percentage,
                                fieldLabel: lang('Please estimate the percent of your household income that comes from this crop'),
                                labelWidth: 300,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-HowImportantCrop',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-HowImportantCrop',
                                store: cmb_disagree_agree_only,
                                fieldLabel: lang('How important is this crop to your overall livelihood ?'),
                                labelWidth: 300,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-OverallEcoToLastYear',
                                name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-OverallEcoToLastYear',
                                store: cmb_condition_state,
                                fieldLabel: lang('How do you feel about your overall economic situation compared to last year ?'),
                                labelWidth: 300,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 1,
                                    layout:'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('Any comments about the buyer ?')
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
                                        id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-Comment',
                                        name: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-Comment',
                                        width: '100%'
                                    }]
                                }]
                            }]
                        }]
                    }]
                }]
            }]
        }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (end)

        //buttons ---------------------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: 'Save',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-BtnSave',
            handler: function () {
                var formNya = Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form').getForm();
                if (formNya.isValid()) {

                    formNya.submit({
                        url: m_api + '/main_buyer_survey/survey',
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
        //buttons ---------------------------------------------------------------------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form');
            formNya.getForm().reset();

            //set MemberID
            Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberID').setValue(thisObj.viewVar.MemberID);

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

                        Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberDisplayID').setValue(r.data.MemberDisplayID);
                        Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-MemberName').setValue(r.data.MemberName);
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
                    url: m_api + '/main_buyer_survey/main_buyer_survey_form_data',
                    method: 'GET',
                    params: {
                        MemberID: thisObj.viewVar.MemberID,
                        SurveyNr: thisObj.viewVar.SurveyNr,
                        DateCollection: thisObj.viewVar.DateCollection,
                        PlotNr: thisObj.viewVar.PlotNr
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        //photo
                        if(r.data.ReceiptPhotoLastSoldFFB != ""){
                            var fotoUser = m_api_base_url + '/images/main_buyer_last_receipt/'+r.data.ProvinceID+'/'+r.data.MemberUID+'/'+ r.data.ReceiptPhotoLastSoldFFB;
                            checkImageExists(fotoUser, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFB').setSrc(fotoUser);
                                } else {
                                    Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFB').setSrc(m_api_base_url + '/images/no-image-icon-port.png');
                                }
                            });
                        }

                        //kasih readonly untuk field yg tak boleh ubah
                        Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-PlotNr').setReadOnly(true);
                        Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-SurveyNr').setReadOnly(true);
                        Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-DateCollection').setReadOnly(true);

                        if(thisObj.viewVar.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey-Form-BtnSave').setVisible(false);
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