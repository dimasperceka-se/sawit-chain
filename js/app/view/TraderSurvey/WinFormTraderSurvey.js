/*
* @Author: nikolius
* @Date:   2017-07-24 10:56:23
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 11:37:03
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. Store yg panggil
    3. MemberID
    4. BusinessNr
    5. SurveyNr
    6. DateCollection
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.TraderSurvey.WinFormTraderSurvey' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey',
    title: lang('Trader Survey Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '83%',
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
        //store --------------------------------------------------------------------------------------------------------------- (end)

        //items ---------------------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form',
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
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-MemberID',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-MemberID'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-MemberDisplayID',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-MemberDisplayID',
                                fieldLabel: lang('Trader ID'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-MemberName',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-MemberName',
                                fieldLabel: lang('Trader Name'),
                                readOnly: true
                            },{
                                xtype: 'numberfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-BusinessNr',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-BusinessNr',
                                fieldLabel: lang('Business Nr'),
                                allowBlank: false,
                                minValue: 1
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style:'padding-left:15px;',
                            items:[{
                                xtype: 'combobox',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-SurveyNr',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-SurveyNr',
                                store: cmb_survey_nr,
                                fieldLabel: lang('Survey Nr'),
                                allowBlank: false,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                xtype: 'datefield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-DateCollection',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-DateCollection',
                                fieldLabel: lang('Date Collection'),
                                allowBlank: false,
                                format: 'Y-m-d H:i:s'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-CreatedByLabel',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-CreatedByLabel',
                                fieldLabel: lang('Created By'),
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
                                html:'<div class="subtitleForm" style="margin-top:-7px;">'+lang('Business Data')+'</div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-BusinessName',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-BusinessName',
                                fieldLabel: lang('Business name (according to your legal document)'),
                                labelWidth: 250,
                                allowBlank: false
                            },{
                                xtype: 'datefield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-DateEstablish',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-DateEstablish',
                                fieldLabel: lang('Date Establish'),
                                labelWidth: 250,
                                format: 'Y-m-d'
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 1,
                                    layout:'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('Address')
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
                                        id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-Address',
                                        name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-Address',
                                        width: '100%'
                                    }]
                                }]
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-LandlinePhone',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-LandlinePhone',
                                fieldLabel: lang('Landline Phone'),
                                labelWidth: 250
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-CellPhone',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-CellPhone',
                                fieldLabel: lang('Cellphone'),
                                labelWidth: 250
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-Email',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-Email',
                                fieldLabel: lang('Email'),
                                labelWidth: 250,
                                vtype: 'email'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-Latitude',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-Latitude',
                                allowNegative: false,
                                fieldLabel: lang('Latitude')
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-Longitude',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-Longitude',
                                allowNegative: false,
                                fieldLabel: lang('Longitude')
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style:'padding-left:15px;',
                            items:[{
                                html:'<div class="subtitleForm" style="margin-top:-7px;">'+lang('Business Question')+'</div>'
                            },{
                                fieldLabel: lang('Is trade your full-time activity ?'),
                                xtype: 'radiogroup',
                                labelWidth: 250,
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-FulltimeTrader',
                                    inputValue: '1',
                                    id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-FulltimeTraderYes',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-FulltimeTrader',
                                    inputValue: '2',
                                    id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-FulltimeTraderNo',
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
                                        text: lang('How is your business registered? (according to legal document)')
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
                                        columnWidth: 0.45,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('No legal status'),
                                            name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader',
                                            inputValue: '1',
                                            id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader1'
                                        },{
                                            boxLabel: lang('Limited Partnership'),
                                            name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader',
                                            inputValue: '3',
                                            id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader3'
                                        },{
                                            boxLabel: lang('Ltd.'),
                                            name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader',
                                            inputValue: '5',
                                            id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader5'
                                        }]
                                    },{
                                        columnWidth: 0.45,
                                        border: false,
                                        defaultType: 'radiofield',
                                        items:[{
                                            boxLabel: lang('Sole Proprietorship'),
                                            name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader',
                                            inputValue: '2',
                                            id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader2'
                                        },{
                                            boxLabel: lang('Cooperatives'),
                                            name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader',
                                            inputValue: '4',
                                            id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader4'
                                        },{
                                            boxLabel: lang('Others'),
                                            name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader',
                                            inputValue: '6',
                                            id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-StatusTrader6'
                                        }]
                                    }]
                                }]
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-YearRunning',
                                name: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-YearRunning',
                                fieldLabel: lang('How long have you been in the business ? (years)'),
                                labelWidth: 250,
                                minValue: 1,
                                emptyText: lang('years')
                            }]
                        }]
                    }]
                }]
            }]
        }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: 'Save',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-BtnSave',
            handler: function () {
                var formNya = Ext.getCmp('Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form').getForm();
                if (formNya.isValid()) {
                    formNya.submit({
                        url: m_api + '/trader_survey/survey',
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
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form');
            formNya.getForm().reset();

            //set MemberID
            Ext.getCmp('Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-MemberID').setValue(thisObj.viewVar.MemberID);

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

                        Ext.getCmp('Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-MemberDisplayID').setValue(r.data.MemberDisplayID);
                        Ext.getCmp('Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-MemberName').setValue(r.data.MemberName);
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
                    url: m_api + '/trader_survey/trader_survey_form_data',
                    method: 'GET',
                    params: {
                        MemberID: thisObj.viewVar.MemberID,
                        BusinessNr: thisObj.viewVar.BusinessNr,
                        SurveyNr: thisObj.viewVar.SurveyNr,
                        DateCollection: thisObj.viewVar.DateCollection
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        //kasih readonly untuk field yg tak boleh ubah
                        Ext.getCmp('Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-BusinessNr').setReadOnly(true);
                        Ext.getCmp('Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-SurveyNr').setReadOnly(true);
                        Ext.getCmp('Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-DateCollection').setReadOnly(true);

                        if(thisObj.viewVar.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.TraderSurvey.WinFormTraderSurvey-Form-BtnSave').setVisible(false);
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