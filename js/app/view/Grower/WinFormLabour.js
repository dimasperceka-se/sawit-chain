/*
* @Author: nikolius
* @Date:   2017-09-14 14:59:39
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 11:37:02
*/

/*
    Param2 yg diperlukan ketika load View ini
    - MemberID
    - opsiDisplay
    - Store yg panggil
    - LaboID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Grower.WinFormLabour' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Grower.WinFormLabour',
    title: lang('Labour Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '82%',
    height: '68%',
    overflowY: 'auto',
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    AddValidation: null,
    MsgAddValidation: null,
    initComponent: function() {
        var thisObj = this;

        //store ============================================================== (begin)
        var cmb_year_option = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption');
        cmb_year_option.setStoreVar({yearRange:90});
        cmb_year_option.load();

        var cmb_wage_period = Ext.create('Koltiva.store.PlotSurvey.CmbWagePeriod');
        var cmb_wage_curr = Ext.create('Koltiva.store.ComboGeneral.CmbWageCurrency');
        //store ============================================================== (end)

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Grower.WinFormLabour-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.495,
                            style:'padding-right:25px;border-right:1px dashed gray;',
                            layout:'form',
                            items:[{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-LaboID',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-LaboID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-MemberID',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-MemberID'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-LaboName',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-LaboName',
                                fieldLabel: lang('Full Name'),
                                allowBlank: false
                            },{
                                fieldLabel: lang('Gender'),
                                xtype: 'radiogroup',
                                allowBlank: false,
                                msgTarget: 'side',
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Male'),
                                    name: 'Koltiva.view.Grower.WinFormLabour-Form-Gender',
                                    inputValue: 'm',
                                    id: 'Koltiva.view.Grower.WinFormLabour-Form-GenderMale',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('Female'),
                                    name: 'Koltiva.view.Grower.WinFormLabour-Form-Gender',
                                    inputValue: 'f',
                                    id: 'Koltiva.view.Grower.WinFormLabour-Form-GenderFemale',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-YearOfBirth',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-YearOfBirth',
                                fieldLabel: lang('Year of Birth'),
                                labelWidth: 190,
                                store: cmb_year_option,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                editable: false,
                                listeners:{
                                    change :function(ob,nv,cv){
                                        var date = new Date();
                                        var now = date.getFullYear();

                                        var age = (now-nv);

                                        Ext.getCmp('Koltiva.view.Grower.WinFormLabour-Form-FamLabAge').setValue(age);

                                        if(age < 13){
                                            // Ext.getCmp('ChildSupervisionPanel').setDisabled(true);
                                        }else{
                                            // Ext.getCmp('ChildSupervisionPanel').setDisabled(false);
                                        }
                                    }
                                }
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-FamLabAge',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-FamLabAge',
                                fieldLabel: lang('Age'),
                                readOnly:true
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-TotalWorkingHrsPerDay',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-TotalWorkingHrsPerDay',
                                labelWidth: 190,
                                fieldLabel: lang('Total Working Hours per Day'),
                                allowNegative: false,
                                minValue: 0
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-DayWorkInMonth',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-DayWorkInMonth',
                                labelWidth: 190,
                                fieldLabel: lang('Total working days per month'),
                                allowNegative: false,
                                minValue: 0
                            },{
                                layout: {
                                    type: 'hbox'
                                },
                                items:[{
                                    xtype:'label',
                                    cls: 'x-form-item-label',
                                    style: 'text-align:left;width:190px;margin-right:5px;',
                                    text: lang('Wage Amount')+':'
                                },{
                                    xtype: 'combobox',
                                    width: 78,
                                    id: 'Koltiva.view.Grower.WinFormLabour-Form-WageCurr',
                                    name: 'Koltiva.view.Grower.WinFormLabour-Form-WageCurr',
                                    store: cmb_wage_curr,
                                    queryMode: 'local',
                                    hidden:true,
                                    displayField: 'label',
                                    valueField: 'id'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.Grower.WinFormLabour-Form-WageAmount',
                                    name: 'Koltiva.view.Grower.WinFormLabour-Form-WageAmount',
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-WagePeriod',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-WagePeriod',
                                store: cmb_wage_period,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                labelWidth: 190,
                                fieldLabel: lang('Wage Period')
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style:'padding-left:15px;',
                            items:[{
                                xtype: 'fieldcontainer',
                                fieldLabel: lang('Type of Work'),
                                labelWidth: 190,
                                layout: 'vbox',
                                defaultType: 'checkboxfield',
                                items: [{
                                    boxLabel  : lang('Seedling'),
                                    name      : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkSeed',
                                    id        : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkSeed',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Slashing'),
                                    name      : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkSlash',
                                    id        : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkSlash',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Circle Weeding'),
                                    name      : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkCircle',
                                    id        : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkCircle',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Pruning'),
                                    name      : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkPruning',
                                    id        : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkPruning',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Fertilizing'),
                                    name      : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkPemupukan',
                                    id        : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkPemupukan',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Pesticide Application'),
                                    name      : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkPest',
                                    id        : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkPest',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Harvest'),
                                    name      : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkHarvest',
                                    id        : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkHarvest',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Transportation'),
                                    name      : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkTransport',
                                    id        : 'Koltiva.view.Grower.WinFormLabour-Form-TypeWorkTransport',
                                    inputValue: '1'
                                }]
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-Enumerator',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-Enumerator',
                                fieldLabel: lang('Enumerator'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-DateCreated',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-DateCreated',
                                fieldLabel: lang('Created Date'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-ModifiedBy',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-ModifiedBy',
                                fieldLabel: lang('Modified by'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Grower.WinFormLabour-Form-DateUpdated',
                                name: 'Koltiva.view.Grower.WinFormLabour-Form-DateUpdated',
                                fieldLabel: lang('Updated Date'),
                                readOnly: true
                            }]
                        }]
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.Grower.WinFormLabour-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var formLabour = Ext.getCmp('Koltiva.view.Grower.WinFormLabour-Form').getForm();
                if (formLabour.isValid()) {

                    //Data Control Tambahan ======================================= (Begin)
                    thisObj.AddValidation = true;
                    thisObj.MsgAddValidation = "";
                    thisObj.AddValidationBasicForm();
                    //Data Control Tambahan ======================================= (Emd)

                    if(thisObj.AddValidation == true){
                        formLabour.submit({
                            url: m_api + '/grower/labour',
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
                                formLabour.reset();
    
                                //refresh store FamLab yg manggil
                                Ext.data.StoreManager.lookup('store.Grower.GridMemberLabour').load();
    
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
                            title: 'Data Control Validation',
                            msg: thisObj.MsgAddValidation,
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }

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
            var formNya = Ext.getCmp('Koltiva.view.Grower.WinFormLabour-Form');
            formNya.getForm().reset();

            //set MemberID
            Ext.getCmp('Koltiva.view.Grower.WinFormLabour-Form-MemberID').setValue(thisObj.formVar.MemberID);

            if(thisObj.formVar.opsiDisplay == 'insert'){
                Ext.Ajax.request({
                    waitMsg: lang('Please Wait'),
                    url: m_api + '/grower/member_data_detail',
                    method : 'GET',
                    params: {MemberID:  thisObj.formVar.MemberID},
                    success: function(response, opts){
                        var r = Ext.decode(response.responseText);

                        //Set Currency
                        var CurrDefa = GetCurrIdByCode(r.data.CountryCode);
                        Ext.getCmp('Koltiva.view.Grower.WinFormLabour-Form-WageCurr').setValue(CurrDefa);
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

            if(thisObj.formVar.opsiDisplay == 'update' || thisObj.formVar.opsiDisplay == 'view'){
                formNya.getForm().load({
                    url: m_api + '/grower/member_labour_form_data',
                    method: 'GET',
                    params: {
                        LaboID: thisObj.formVar.LaboID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);

                        if(thisObj.formVar.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.Grower.WinFormLabour-Form-BtnSave').setVisible(false);
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
    },
    AddValidationBasicForm: function(){
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;
        //thisObj.MsgAddValidation = "Cihuy";

        //Cek Umur ================================================== (Begin)
            var YearBirth = parseInt(Ext.getCmp('Koltiva.view.Grower.WinFormLabour-Form-YearOfBirth').getValue());
            var today = new Date();
            var age = today.getFullYear() - YearBirth;            
            if(age <= 9){
                thisObj.AddValidation = false;
                ArrMsg.push("Minimal Age is 10 years old");
            }
        //Cek Umur ================================================== (End)


        if(thisObj.AddValidation == false){
            var HtmlMsg = '<ul>';
            for (var index = 0; index < ArrMsg.length; index++) {
                HtmlMsg += '<li>'+ArrMsg[index]+'</li>'
            }
            HtmlMsg+='</ul>';
            thisObj.MsgAddValidation = HtmlMsg;
        }
    }
});