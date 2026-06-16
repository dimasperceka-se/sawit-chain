/*
* @Author: nikolius
* @Date:   2017-05-25 16:06:50
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 11:37:02
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
    2. opsiDisplay
    3. Store FamLab yg panggil
    4. FamLabID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
function behaWorkingPlantation(){
    if(Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-WorkingStatusYes').checked == true){
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TotalWorkingHrsPerDay').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkSeed').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkSlash').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkCircle').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPruning').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPemupukan').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPest').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkHarvest').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkTransport').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TotalWorkingHrsPerMonth').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-WageAmount').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-WagePeriod').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-DayWorkInMonth').setDisabled(false);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-ReasonFamilyWork').setDisabled(false);
    }else{
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TotalWorkingHrsPerDay').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkSeed').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkSlash').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkCircle').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPruning').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPemupukan').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPest').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkHarvest').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkTransport').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-TotalWorkingHrsPerMonth').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-WageAmount').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-WagePeriod').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-DayWorkInMonth').setDisabled(true);
        Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-ReasonFamilyWork').setDisabled(true);
    }
}
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Grower.WinFormFamLab' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Grower.WinFormFamLab',
    title: lang('Family Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '85%',
    height: '80%',
    overflowY: 'auto',
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    AddValidation: null,
    MsgAddValidation: null,
    initComponent: function() {
        var thisObj = this;

        //store
        var cmb_familylab_relation = Ext.create('Koltiva.store.Grower.CmbFamilyRelation');
        var cmb_familylab_activity_type = Ext.create('Koltiva.store.Grower.CmbFamilyActivityType');
        var cmb_familylab_reason_work = Ext.create('Koltiva.store.Grower.CmbFamilyReasonWork');

        var cmb_year_option = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption');
        cmb_year_option.setStoreVar({yearRange:90});
        cmb_year_option.load();

        var cmb_wage_period = Ext.create('Koltiva.store.PlotSurvey.CmbWagePeriod');
        var cmb_wage_curr = Ext.create('Koltiva.store.ComboGeneral.CmbWageCurrency');

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Grower.WinFormFamLab-Form',
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
                                id: 'Koltiva.view.Grower.WinFormFamLab-Form-FamLabID',
                                name: 'Koltiva.view.Grower.WinFormFamLab-Form-FamLabID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.Grower.WinFormFamLab-Form-MemberID',
                                name: 'Koltiva.view.Grower.WinFormFamLab-Form-MemberID'
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'datefield',
                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-FamLabInterviewDate',
                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-FamLabInterviewDate',
                                        fieldLabel: lang('Interview Date'),
                                        format:'Y-m-d',
                                        labelAlign:'top',
                                        allowBlank: false
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_FamLabInterviewDate')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-FamLabName',
                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-FamLabName',
                                        fieldLabel: lang('Family Member Name'),
                                        labelAlign:'top',
                                        allowBlank: false
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_FamLabName')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        fieldLabel: lang('Gender'),
                                        labelAlign:'top',
                                        xtype: 'radiogroup',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        columns: 2,
                                        items:[{
                                            boxLabel: lang('Male'),
                                            name: 'Koltiva.view.Grower.WinFormFamLab-Form-Gender',
                                            inputValue: 'm',
                                            id: 'Koltiva.view.Grower.WinFormFamLab-Form-GenderMale',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('Female'),
                                            name: 'Koltiva.view.Grower.WinFormFamLab-Form-Gender',
                                            inputValue: 'f',
                                            id: 'Koltiva.view.Grower.WinFormFamLab-Form-GenderFemale',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        }]
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_Gender')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-YearOfBirth',
                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-YearOfBirth',
                                        fieldLabel: lang('Year of Birth'),
                                        labelAlign:'top',
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
        
                                                Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-FamLabAge').setValue(age);
        
                                                if(age < 13){
                                                    // Ext.getCmp('ChildSupervisionPanel').setDisabled(true);
                                                }else{
                                                    // Ext.getCmp('ChildSupervisionPanel').setDisabled(false);
                                                }
                                            }
                                        }
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_YearOfBirth')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-FamLabAge',
                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-FamLabAge',
                                        fieldLabel: lang('Age'),
                                        labelAlign:'top',
                                        readOnly:true
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_FamLabAge')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-FamLabRelation',
                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-FamLabRelation',
                                        store: cmb_familylab_relation,
                                        fieldLabel: lang('Relationship'),
                                        labelAlign:'top',
                                        allowBlank: false,
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id'
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_FamLabRelation')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        fieldLabel: lang('Education Status'),
                                        xtype: 'radiogroup',
                                        labelAlign:'top',
                                        columns: 2,
                                        items:[{
                                            boxLabel: lang('School'),
                                            name: 'Koltiva.view.Grower.WinFormFamLab-Form-InSchool',
                                            inputValue: 'Yes',
                                            id: 'Koltiva.view.Grower.WinFormFamLab-Form-InSchoolYes',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('School, But not full-time'),
                                            name: 'Koltiva.view.Grower.WinFormFamLab-Form-InSchool',
                                            inputValue: 'NoFullTime',
                                            id: 'Koltiva.view.Grower.WinFormFamLab-Form-InSchoolNotFullTime',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No School'),
                                            name: 'Koltiva.view.Grower.WinFormFamLab-Form-InSchool',
                                            inputValue: 'No',
                                            id: 'Koltiva.view.Grower.WinFormFamLab-Form-InSchoolNo',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('N/A (not school age)'),
                                            name: 'Koltiva.view.Grower.WinFormFamLab-Form-InSchool',
                                            inputValue: 'N/A (not school age)',
                                            id: 'Koltiva.view.Grower.WinFormFamLab-Form-InSchoolNotSchoolAge',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        }]
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_InSchool')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        fieldLabel: lang('Working in the plantation'),
                                        xtype: 'radiogroup',
                                        labelAlign:'top',
                                        labelWidth: 190,
                                        columns: 2,
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.Grower.WinFormFamLab-Form-WorkingStatus',
                                            inputValue: 'Yes',
                                            id: 'Koltiva.view.Grower.WinFormFamLab-Form-WorkingStatusYes',
                                            listeners:{
                                                change: function(){
                                                    behaWorkingPlantation();
        
                                                    var date = new Date();
                                                    var now = date.getFullYear();
                                                    var birth = Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-YearOfBirth').getValue();
        
                                                    var age = (now-birth);
        
                                                    if(this.checked == true || age > 13){
                                                        // Ext.getCmp('ChildSupervisionPanel').setDisabled(false);
                                                    }else{
                                                        // Ext.getCmp('ChildSupervisionPanel').setDisabled(true);
                                                    }
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.Grower.WinFormFamLab-Form-WorkingStatus',
                                            inputValue: 'No',
                                            id: 'Koltiva.view.Grower.WinFormFamLab-Form-WorkingStatusNo',
                                            listeners:{
                                                change: function(){
                                                    behaWorkingPlantation();
        
                                                    var date = new Date();
                                                    var now = date.getFullYear();
                                                    var birth = Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-YearOfBirth').getValue();
        
                                                    var age = (now-birth);
        
                                                    if(this.checked == true || age < 13){
                                                        // Ext.getCmp('ChildSupervisionPanel').setDisabled(true);
                                                        // Ext.getCmp('UseSharpToolsPanel').setDisabled(true);
                                                        // Ext.getCmp('ApplyingInorganicFertPanel').setDisabled(true);
                                                        // Ext.getCmp('SprayPestPanel').setDisabled(true);
                                                        // Ext.getCmp('CaryingHeavyItemPanel').setDisabled(true);
                                                    }else{
                                                        // Ext.getCmp('ChildSupervisionPanel').setDisabled(false);
                                                        // Ext.getCmp('UseSharpToolsPanel').setDisabled(false);
                                                        // Ext.getCmp('ApplyingInorganicFertPanel').setDisabled(false);
                                                        // Ext.getCmp('SprayPestPanel').setDisabled(false);
                                                        // Ext.getCmp('CaryingHeavyItemPanel').setDisabled(false);
                                                    }
                                                    return false;
                                                }
                                            }
                                        }]
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_WorkingStatus')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonFamilyWork',
                                name: 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonFamilyWork',
                                labelAlign:'top',
                                store: cmb_familylab_reason_work,
                                fieldLabel: lang(''),
                                labelWidth: 190,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                hidden:true,
                                disabled: true
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-TotalWorkingHrsPerDay',
                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-TotalWorkingHrsPerDay',
                                        fieldLabel: lang('Total Working Hours per Day'),
                                        labelAlign:'top',
                                        labelWidth: 190,
                                        allowNegative: false,
                                        minValue: 0,
                                        disabled: true
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_TotalWorkingHrsPerDay')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-DayWorkInMonth',
                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-DayWorkInMonth',
                                        fieldLabel: lang('Total working days per month'),
                                        labelAlign:'top',
                                        labelWidth: 190,
                                        allowNegative: false,
                                        minValue: 0,
                                        disabled: true
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_DayWorkInMonth')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'fieldcontainer',
                                        fieldLabel: lang('Type of Work'),
                                        labelAlign:'top',
                                        labelWidth: 190,
                                        layout: 'vbox',
                                        defaultType: 'checkboxfield',
                                        items: [{
                                            boxLabel  : lang('Seedling'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkSeed',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkSeed',
                                            inputValue: '1',
                                            disabled: true
                                        },{
                                            boxLabel  : lang('Slashing'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkSlash',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkSlash',
                                            inputValue: '1',
                                            disabled: true
                                        },{
                                            boxLabel  : lang('Circle Weeding'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkCircle',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkCircle',
                                            inputValue: '1',
                                            disabled: true
                                        },{
                                            boxLabel  : lang('Pruning'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPruning',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPruning',
                                            inputValue: '1',
                                            disabled: true
                                        },{
                                            boxLabel  : lang('Fertilizing'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPemupukan',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPemupukan',
                                            inputValue: '1',
                                            disabled: true
                                        },{
                                            boxLabel  : lang('Pesticide Application'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPest',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkPest',
                                            inputValue: '1',
                                            disabled: true
                                        },{
                                            boxLabel  : lang('Harvest'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkHarvest',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkHarvest',
                                            inputValue: '1',
                                            disabled: true
                                        },{
                                            boxLabel  : lang('Transportation'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkTransport',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-TypeWorkTransport',
                                            inputValue: '1',
                                            disabled: true
                                        }]
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_TypeWork')+'</div>'
                                        }
                                    }]
                                }]
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style:'padding-left:15px;',
                            items:[{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'fieldcontainer',
                                        fieldLabel: lang('What is the reason the family member works in the plantation'),
                                        labelWidth: 190,
                                        labelAlign:'top',
                                        layout: 'vbox',
                                        defaultType: 'checkboxfield',
                                        items: [{
                                            boxLabel  : lang('Not Going to School'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonNotGoingToSchool',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonNotGoingToSchool',
                                            inputValue: '1'
                                        },{
                                            boxLabel  : lang('Lack of Labor'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonLackofLabor',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonLackofLabor',
                                            inputValue: '1'
                                        },{
                                            boxLabel  : lang('Helping Parent'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonHelpingParent',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonHelpingParent',
                                            inputValue: '1'
                                        },{
                                            boxLabel  : lang('I Do Not Have to Pay Them'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonNotToPay',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonNotToPay',
                                            inputValue: '1'
                                        },{
                                            boxLabel  : lang('Other'),
                                            name      : 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonOther',
                                            id        : 'Koltiva.view.Grower.WinFormFamLab-Form-ReasonOther',
                                            inputValue: '1'
                                        }]
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_ReasonWork')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.Grower.WinFormFamLab-Form-TotalWorkingHrsPerMonth',
                                name: 'Koltiva.view.Grower.WinFormFamLab-Form-TotalWorkingHrsPerMonth',
                                labelWidth: 190,
                                fieldLabel: lang('Total Working Hours per Month'),
                                labelAlign:'top',
                                allowNegative: false,
                                minValue: 0,
                                hidden:true,
                                disabled: true
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        fieldLabel: lang('Wage Amount'),
                                        xtype: 'numericfield',
                                        labelAlign:'top',
                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-WageAmount',
                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-WageAmount',
                                        allowNegative: false,
                                        minValue: 0,
                                        disabled: true
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_WageAmount')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-WagePeriod',
                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-WagePeriod',
                                        labelAlign:'top',
                                        store: cmb_wage_period,
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                        labelWidth: 190,
                                        fieldLabel: lang('Wage Period'),
                                        disabled: true
                                    }]
                                },{
                                    columnWidth: 0.04,
                                    layout: 'form',
                                    items:[{
                                        xtype: 'image',
                                        width: '18px',
                                        style: 'cursor:pointer;margin-left:5px;',
                                        src: varjs.config.base_url + 'images/icons/silk/information.png',
                                        autoEl: {
                                            tag: 'label',
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_WagePeriod')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                xtype: 'panel',
                                title: lang('Certification'),
                                frame: false,
                                id: 'Koltiva.view.Grower.WinFormFamLab-Form-SectionCertification',
                                style: 'margin-top:22px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                    layout: 'column',
                                    border: false,
                                    items: [{
                                        columnWidth: 1,
                                        layout: 'form',
                                        style: 'padding:10px 0px 0px 0px;',
                                        defaults: {
                                            labelAlign: 'top'
                                        },
                                        items: [{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 0.96,
                                                layout:'form',
                                                items:[{
                                                    fieldLabel: lang('Does your child receive adult supervision when working on the plantation'),
                                                    xtype: 'radiogroup',
                                                    labelAlign:'top',
                                                    labelWidth: 190,
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-ChildSupervision',
                                                        inputValue: 'Yes',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-ChildSupervisionYes',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-ChildSupervision',
                                                        inputValue: 'No',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-ChildSupervisionNo',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            },{
                                                columnWidth: 0.04,
                                                layout: 'form',
                                                items:[{
                                                    xtype: 'image',
                                                    width: '18px',
                                                    style: 'cursor:pointer;margin-left:5px;',
                                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                                    autoEl: {
                                                        tag: 'label',
                                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_ChildSupervision')+'</div>'
                                                    }
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 0.96,
                                                layout:'form',
                                                items:[{
                                                    fieldLabel: lang('Child Activity: Use sharp tools'),
                                                    xtype: 'radiogroup',
                                                    labelAlign:'top',
                                                    labelWidth: 190,
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-UseSharpTools',
                                                        inputValue: 'Yes',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-UseSharpToolsYes',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-UseSharpTools',
                                                        inputValue: 'No',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-UseSharpToolsNo',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            },{
                                                columnWidth: 0.04,
                                                layout: 'form',
                                                items:[{
                                                    xtype: 'image',
                                                    width: '18px',
                                                    style: 'cursor:pointer;margin-left:5px;',
                                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                                    autoEl: {
                                                        tag: 'label',
                                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_UseSharpTools')+'</div>'
                                                    }
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 0.96,
                                                layout:'form',
                                                items:[{
                                                    fieldLabel: lang('Child Actvitiy: Applying inorganic fertilizer'),
                                                    xtype: 'radiogroup',
                                                    labelAlign:'top',
                                                    labelWidth: 190,
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-ApplyingInorganicFert',
                                                        inputValue: 'Yes',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-ApplyingInorganicFertYes',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-ApplyingInorganicFert',
                                                        inputValue: 'No',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-ApplyingInorganicFertNo',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            },{
                                                columnWidth: 0.04,
                                                layout: 'form',
                                                items:[{
                                                    xtype: 'image',
                                                    width: '18px',
                                                    style: 'cursor:pointer;margin-left:5px;',
                                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                                    autoEl: {
                                                        tag: 'label',
                                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_ApplyingInorganicFert')+'</div>'
                                                    }
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 0.96,
                                                layout:'form',
                                                items:[{
                                                    fieldLabel: lang('Child Activity: Spraying Pesticides'),
                                                    xtype: 'radiogroup',
                                                    labelAlign:'top',
                                                    labelWidth: 190,
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-SprayPest',
                                                        inputValue: 'Yes',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-SprayPestYes',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-SprayPest',
                                                        inputValue: 'No',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-SprayPestNo',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            },{
                                                columnWidth: 0.04,
                                                layout: 'form',
                                                items:[{
                                                    xtype: 'image',
                                                    width: '18px',
                                                    style: 'cursor:pointer;margin-left:5px;',
                                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                                    autoEl: {
                                                        tag: 'label',
                                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_SprayPest')+'</div>'
                                                    }
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 0.96,
                                                layout:'form',
                                                items:[{
                                                    fieldLabel: lang('Child Activity:  Carrying heavy items (more than 10 kg)'),
                                                    xtype: 'radiogroup',
                                                    labelAlign:'top',
                                                    labelWidth: 190,
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-CaryingHeavyItem',
                                                        inputValue: 'Yes',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-CaryingHeavyItemYes',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-CaryingHeavyItem',
                                                        inputValue: 'No',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-CaryingHeavyItemNo',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            },{
                                                columnWidth: 0.04,
                                                layout: 'form',
                                                items:[{
                                                    xtype: 'image',
                                                    width: '18px',
                                                    style: 'cursor:pointer;margin-left:5px;',
                                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                                    autoEl: {
                                                        tag: 'label',
                                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_CaryingHeavyItem')+'</div>'
                                                    }
                                                }]
                                            }]
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 0.96,
                                                layout:'form',
                                                items:[{
                                                    fieldLabel: lang('Does the family member use PPE'),
                                                    xtype: 'radiogroup',
                                                    labelAlign:'top',
                                                    labelWidth: 190,
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-UsePPE',
                                                        inputValue: 'Yes',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-UsePPEYes',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.WinFormFamLab-Form-UsePPE',
                                                        inputValue: 'No',
                                                        id: 'Koltiva.view.Grower.WinFormFamLab-Form-UsePPENo',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            },{
                                                columnWidth: 0.04,
                                                layout: 'form',
                                                items:[{
                                                    xtype: 'image',
                                                    width: '18px',
                                                    style: 'cursor:pointer;margin-left:5px;',
                                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                                    autoEl: {
                                                        tag: 'label',
                                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_UsePPE')+'</div>'
                                                    }
                                                }]
                                            }]
                                        }]
                                    }]
                                }]
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Grower.WinFormFamLab-Form-Enumerator',
                                name: 'Koltiva.view.Grower.WinFormFamLab-Form-Enumerator',
                                fieldLabel: lang('Enumerator'),
                                labelAlign:'top',
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Grower.WinFormFamLab-Form-DateCreated',
                                name: 'Koltiva.view.Grower.WinFormFamLab-Form-DateCreated',
                                fieldLabel: lang('Created Date'),
                                labelAlign:'top',
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Grower.WinFormFamLab-Form-ModifiedBy',
                                name: 'Koltiva.view.Grower.WinFormFamLab-Form-ModifiedBy',
                                fieldLabel: lang('Modified by'),
                                labelAlign:'top',
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Grower.WinFormFamLab-Form-DateUpdated',
                                name: 'Koltiva.view.Grower.WinFormFamLab-Form-DateUpdated',
                                fieldLabel: lang('Updated Date'),
                                labelAlign:'top',
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
            id: 'Koltiva.view.Grower.WinFormFamLab-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var formFambLab = Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form').getForm();
                if (formFambLab.isValid()) {

                    //Data Control Tambahan ======================================= (Begin)
                    thisObj.AddValidation = true;
                    thisObj.MsgAddValidation = "";
                    thisObj.AddValidationBasicForm();
                    //Data Control Tambahan ======================================= (Emd)

                    if(thisObj.AddValidation == true){
                        formFambLab.submit({
                            url: m_api + '/grower/family_labour',
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
                                formFambLab.reset();
    
                                //refresh store FamLab yg manggil
                                Ext.data.StoreManager.lookup('store.Grower.GridMemberFamilyLabour').load();
    
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
            var formNya = Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form');
            formNya.getForm().reset();

            //set MemberID
            Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-MemberID').setValue(thisObj.formVar.MemberID);

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
                    url: m_api + '/grower/member_family_labour_data',
                    method: 'GET',
                    params: {
                        FamLabID: thisObj.formVar.FamLabID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);

                        if(thisObj.formVar.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-BtnSave').setVisible(false);
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
        if(Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-FamLabRelation').getValue() == '1'){
            var YearBirth = parseInt(Ext.getCmp('Koltiva.view.Grower.WinFormFamLab-Form-YearOfBirth').getValue());
            var today = new Date();
            var age = today.getFullYear() - YearBirth;            
            if(age <= 16){
                thisObj.AddValidation = false;
                ArrMsg.push("Minimal Age is 16 years old");
            }
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