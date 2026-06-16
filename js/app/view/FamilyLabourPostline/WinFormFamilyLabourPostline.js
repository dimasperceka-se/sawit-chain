Ext.define('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline',
    title: lang('Family Labour Postline Form'),
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
    validateFirst: function(value) {
        let disabledComponents = true

        if (value != null) {
            disabledComponents = false

            if (this.viewVar.opsiDisplay != 'view') {
                Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-BtnSave').setVisible(true)

                Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-conducting_postlineid').reset()
            }
        } else {
            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-BtnSave').setVisible(false)
        }

        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').searchComponentSpesificObj('disabled-component-family-labour-postline-init', disabledComponents, 'validateFirst', false)
    },
    validateConducting: function() {
       let conductingPostline  = 0
       let disabledComponents  = true

        if(Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-conducting_postlineYes').getValue() == true){
            conductingPostline = 1;
        }

        if(Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-conducting_postlineNo').getValue() == true){
            conductingPostline = 2;
        }

       if (conductingPostline == 1) {
            disabledComponents = false

            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').searchComponentSpesificObj('disabled-component-family-labour-postline-init2', disabledComponents, 'validateConducting', false)
       } else {
            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').searchComponentSpesificObj('disabled-component-family-labour-postline-init2', disabledComponents, 'validateConducting', true)
       }

        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-working_on_the_plantationid').reset()
        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-survey_nr').reset()
    },
    validateSecond: function() {
       let workingOnthePlantation    = 0
       let disabledComponents        = true
       let ageFamilyMember           = Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabAge').getValue()

        if(Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-working_on_the_plantationYes').getValue() == true){
            workingOnthePlantation = 1;
        }

        if(Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-working_on_the_plantationNo').getValue() == true){
            workingOnthePlantation = 2;
        }

       if (workingOnthePlantation == 1) {
            disabledComponents = false

            if (parseInt(ageFamilyMember) < 18) {
                Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').searchComponentSpesificObj('disabled-component-family-labour-postline-validatesecond-1', disabledComponents, 'validateSecond', false)
            }

            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').searchComponentSpesificObj('disabled-component-family-labour-postline-validatesecond-2', disabledComponents, 'validateSecond', true)
            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').searchComponentSpesificObj('disabled-component-family-labour-postline-validatesecond-3', disabledComponents, 'validateSecond', false)
       } else {
            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').searchComponentSpesificObj('disabled-component-family-labour-postline-validatesecond-1', disabledComponents, 'validateSecond', true)
            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').searchComponentSpesificObj('disabled-component-family-labour-postline-validatesecond-2', disabledComponents, 'validateSecond', true)

            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').searchComponentSpesificObj('disabled-component-family-labour-postline-validatesecond-3', disabledComponents, 'validateSecond', true)
       }
    },
    searchComponentSpesificObj: function(classSpesific, disabledComponents, remarks, allowBlank) {
        let componentSpesific  = Ext.ComponentQuery.query('*[id^=Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-]', Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline'))

        if (this.viewVar.opsiDisplay == "view") {
            disabledComponents = true
            allowBlank = true
        }

        componentSpesific.forEach(function(components){
            if (Array.isArray(components.cls) == true) {
                if (remarks == "validateFirst") {
                    if (components.cls[0] == classSpesific) {
                        components.setDisabled(disabledComponents)
                        components.allowBlank = allowBlank
                    }
                } else if (remarks == "validateConducting") {
                    if (components.cls[0] == classSpesific) {
                        components.reset()
                        
                        if (components.id == "Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-survey_nr") {
                            components.setReadOnly(disabledComponents)
                        } else {
                            components.setDisabled(disabledComponents)
                        }

                        /* if (components.id != "Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPE") {
                            if (components.cls.length == 1) {
                                components.allowBlank = allowBlank
                            }
                        } */

                        components.allowBlank = allowBlank
                    }
                } else {
                    if (components.cls[1] == classSpesific) {
                        components.reset()

                        components.setDisabled(disabledComponents)
                        components.allowBlank = allowBlank
                    }
                }
            }
        })
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.cmb_year_option      = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption');
        thisObj.cmb_year_option.setStoreVar({yearRange:90});
        thisObj.cmb_year_option.load();

        thisObj.cmb_wage_period      = Ext.create('Koltiva.store.PlotSurvey.CmbWagePeriod');
        thisObj.cmb_survey_nr        = Ext.create('Koltiva.store.FamilyLabourPostline.CmbSurveyNr');
        thisObj.cmb_fam_member_name  = Ext.create('Koltiva.store.FamilyLabourPostline.CmbFamMemberName', {
            storeVar: {
                MemberID: thisObj.viewVar.MemberID
            }
        });

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form',
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
                                id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabID',
                                name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabPostID',
                                name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabPostID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-MemberID',
                                name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-MemberID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-survey_nr_history',
                                name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-survey_nr_history'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabName',
                                name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabName',
                                allowBlank : false,
                                store: thisObj.cmb_fam_member_name,
                                labelAlign: 'top', 
                                minChars: 3,
                                baseCls: 'Sfr_FormInputMandatory',
                                fieldLabel: lang('Family Member Name'),  
                                displayField: 'label',
                                valueField: 'id',
                                enableKeyEvents: true,
                                emptyText: lang('Search by Family Member Name'),
                                typeAhead: false,
                                hideTrigger:false,
                                queryCaching:false,
                                listConfig: {
                                    loadingText: lang('Searching...'),
                                    emptyText: lang('No matching found.'),

                                    getInnerTpl: function() {
                                        return `<div class="search-item"><b>{label}</b></div>`;
                                    }
                                },
                                pageSize: 10,                           
                                listeners : {
                                    select: function (field, record) {
                                        var post = record[0];
                                        
                                        if (post) {
                                            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').validateFirst(post.raw.id)

                                            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabID').setValue(post.raw.id)
                                            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabInterviewDate').setValue(post.raw.interview_date)
                                            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-YearOfBirth').setValue(post.raw.year_birthdate)
                                            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-MemberID').setValue(post.raw.member_id)
                                        }
                                    },
                                    keyup: function(value) {
                                        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').validateFirst(null)
                                    }                               
                                }
                                
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        xtype: 'datefield',
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabInterviewDate',
                                        name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabInterviewDate',
                                        fieldLabel: lang('Interview Date'),
                                        format:'Y-m-d',
                                        labelAlign:'top',
                                        allowBlank: false,
                                        readOnly: true
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.96,
                                    layout:'form',
                                    items:[{
                                        fieldLabel: lang('Are you conducting a post-line survey?'),
                                        labelAlign:'top',
                                        xtype: 'radiogroup',
                                        allowBlank: false,
                                        baseCls: 'Sfr_FormInputMandatory',
                                        msgTarget: 'side',
                                        cls: ['disabled-component-family-labour-postline-init'],
                                        disabled: true,
                                        columns: 2,
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-conducting_postlineid',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-conducting_postline',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-conducting_postlineYes',
                                            listeners:{
                                                change: function(){
                                                    Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').validateConducting()
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-conducting_postline',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-conducting_postlineNo',
                                            listeners:{
                                                change: function(){
                                                    Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').validateConducting()
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_working_on_the_plantation')+'</div>'
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
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-survey_nr',
                                        name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-survey_nr',
                                        store: thisObj.cmb_survey_nr,
                                        fieldLabel: lang('Survey Nr'),
                                        allowBlank: false,
                                        baseCls: 'Sfr_FormInputMandatory',
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                        labelAlign:'top',
                                        cls: ['disabled-component-family-labour-postline-init2'],
                                        readOnly: true
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_survey_nr')+'</div>'
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
                                        fieldLabel: lang('Working on the plantation'),
                                        labelAlign:'top',
                                        xtype: 'radiogroup',
                                        allowBlank: false,
                                        baseCls: 'Sfr_FormInputMandatory',
                                        msgTarget: 'side',
                                        cls: ['disabled-component-family-labour-postline-init2'],
                                        disabled: true,
                                        columns: 2,
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-working_on_the_plantationid',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-working_on_the_plantation',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-working_on_the_plantationYes',
                                            listeners:{
                                                change: function(){
                                                    Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').validateSecond()
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-working_on_the_plantation',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-working_on_the_plantationNo',
                                            listeners:{
                                                change: function(){
                                                    Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline').validateSecond()
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_working_on_the_plantation')+'</div>'
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
                                        fieldLabel: lang('Does your child receive adult supervision when working on the plantation?'),
                                        labelAlign:'top',
                                        xtype: 'radiogroup',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-1'],
                                        disabled: true,
                                        columns: 2,
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_receive_adult_supervisionid',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_receive_adult_supervision',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_receive_adult_supervisionYes',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_receive_adult_supervision',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_receive_adult_supervisionNo',
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_child_receive_adult_supervision')+'</div>'
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
                                        labelAlign:'top',
                                        xtype: 'radiogroup',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-1'],
                                        disabled: true,
                                        columns: 2,
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_sharp_toolsid',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_sharp_tools',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_sharp_toolsYes',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_sharp_tools',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_sharp_toolsNo',
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_child_activity_sharp_tools')+'</div>'
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
                                        labelAlign:'top',
                                        xtype: 'radiogroup',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        disabled: true,
                                        cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-1'],
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_applying_inorganicid',
                                        columns: 2,
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_applying_inorganic',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_applying_inorganicYes',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_applying_inorganic',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_applying_inorganicNo',
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_child_activity_applying_inorganic')+'</div>'
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
                                        labelAlign:'top',
                                        xtype: 'radiogroup',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        disabled: true,
                                        cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-1'],
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_spraying_pesticidesid',
                                        columns: 2,
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_spraying_pesticides',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_spraying_pesticidesYes',
                                            listeners:{
                                                change: function(){
                                                    /* if (this.checked == true) {
                                                        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPEid').setDisabled(false);
                                                    } else {
                                                        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPEid').setDisabled(true);
                                                    } */

                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_spraying_pesticides',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_spraying_pesticidesNo',
                                            listeners:{
                                                change: function(){
                                                    /* if (this.checked == true) {
                                                        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPEid').setDisabled(true);
                                                    } else {
                                                        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPEid').setDisabled(false);
                                                    } */

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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_child_activity_spraying_pesticides')+'</div>'
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
                                        labelAlign:'top',
                                        xtype: 'radiogroup',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        disabled: true,
                                        cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-1'],
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_carrying_heavyid',
                                        columns: 2,
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_carrying_heavy',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_carrying_heavyYes',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_carrying_heavy',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-child_activity_carrying_heavyNo',
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_child_activity_carrying_heavy')+'</div>'
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
                                        xtype: 'checkboxgroup',
                                        fieldLabel: lang('Type of Work'),
                                        labelAlign:'top',
                                        labelWidth: 190,
                                        layout: 'vbox',
                                        allowBlank: true,
                                        cls: ['disabled-component-family-labour-postline', 'disabled-component-family-labour-postline-validatesecond-3'],
                                        baseCls: 'Sfr_FormInputMandatory',
                                        defaultType: 'checkboxfield',
                                        id : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_initid',
                                        items: [{
                                            boxLabel  : lang('Planting'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_planting',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_planting',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Slashing'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_slashing',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_slashing',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Circle Weeding'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_circle',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_circle',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Pruning'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_pruning',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_pruning',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Fertilizing'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_fertilizing',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_fertilizing',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Pesticide Application'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_pesticide',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_pesticide',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2'],
                                            uncheckedValue: "",
                                            listeners: {
                                                change: function(field, newValue, oldValue, eOpts) {
                                                    if (field.checked == true) {
                                                        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPEid').setDisabled(false);
                                                        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPEid').allowBlank = false
                                                    } else {
                                                        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPEid').setDisabled(true);
                                                        Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPEid').allowBlank = true
                                                    }
                                                }
                                            }
                                        },{
                                            boxLabel  : lang('Harvest'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_harvest',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_harvest',
                                            inputValue: '1',
                                            disabled: true,
                                            uncheckedValue: "",
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2']
                                        },{
                                            boxLabel  : lang('Transportation'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_transportation',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-type_work_transportation',
                                            inputValue: '1',
                                            disabled: true,
                                            uncheckedValue: "",
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2'],
                                            listeners:{                 
                                                change: function(field, newValue, oldValue, eOpts) {
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_type_of_work')+'</div>'
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
                                        fieldLabel: lang('Does the family member use PPE?'),
                                        labelAlign:'top',
                                        xtype: 'radiogroup',
                                        allowBlank: true,
                                        msgTarget: 'side',
                                        disabled: true,
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPEid',
                                        columns: 2,
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPE',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPEYes',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPE',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-does_the_family_member_use_PPENo',
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_does_the_family_member_use_PPE')+'</div>'
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
                                        xtype: 'hiddenfield',
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-YearOfBirth',
                                        name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-YearOfBirth',
                                        fieldLabel: lang('Year of Birth'),
                                        labelAlign:'top',
                                        labelWidth: 190,
                                        store: thisObj.cmb_year_option,
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                        editable: false,
                                        listeners:{
                                            change :function(ob,nv,cv){
                                                var date = new Date();
                                                var now = date.getFullYear();
        
                                                var age = (now-nv);
        
                                                Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabAge').setValue(age);
                                            }
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
                                        xtype: 'hiddenfield',
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabAge',
                                        name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabAge',
                                        fieldLabel: lang('Age'),
                                        labelAlign:'top',
                                        readOnly:true
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
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-total_working_hours_per_day',
                                        name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-total_working_hours_per_day',
                                        fieldLabel: lang('Total Working Hours per Day'),
                                        labelAlign:'top',
                                        labelWidth: 190,
                                        allowNegative: false,
                                        minValue: 0,
                                        disabled: true,
                                        cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2']
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_TotalWorkingHrsPerDay')+'</div>'
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
                                        xtype: 'numericfield',
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-total_working_days_per_month',
                                        name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-total_working_days_per_month',
                                        fieldLabel: lang('Total Working Days per Month'),
                                        labelAlign:'top',
                                        labelWidth: 190,
                                        allowNegative: false,
                                        minValue: 0,
                                        disabled: true,
                                        cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2']
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_total_working_days_per_month')+'</div>'
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
                                        fieldLabel: lang('Wage Amount'),
                                        xtype: 'numericfield',
                                        labelAlign:'top',
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-wage_amount',
                                        name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-wage_amount',
                                        allowNegative: false,
                                        minValue: 0,
                                        disabled: true,
                                        cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2']
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_wage_amount')+'</div>'
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
                                        id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-wage_period',
                                        name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-wage_period',
                                        labelAlign:'top',
                                        store: thisObj.cmb_wage_period,
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                        labelWidth: 190,
                                        fieldLabel: lang('Wage Period'),
                                        disabled: true,
                                        cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2']
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_wage_period')+'</div>'
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
                                        xtype: 'checkboxgroup',
                                        fieldLabel: lang('What is the reason the family member works in the plantation'),
                                        labelWidth: 190,
                                        labelAlign:'top',
                                        layout: 'vbox',
                                        allowBlank: true,
                                        baseCls: 'Sfr_FormInputMandatory',
                                        id : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_initid',
                                        cls: ['disabled-component-family-labour-postline', 'disabled-component-family-labour-postline-validatesecond-3'],
                                        defaultType: 'checkboxfield',
                                        items: [{
                                            boxLabel  : lang('Not Going to School'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_not_going_to_school',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_not_going_to_school',
                                            inputValue: '1',
                                            disabled: true,
                                            uncheckedValue: "",
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2']
                                        },{
                                            boxLabel  : lang('Lack of Labor'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_lack_of_labour',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_lack_of_labour',
                                            inputValue: '1',
                                            disabled: true,
                                            uncheckedValue: "",
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2']
                                        },{
                                            boxLabel  : lang('Helping Parents'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_helping_parents',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_helping_parents',
                                            inputValue: '1',
                                            disabled: true,
                                            uncheckedValue: "",
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2']
                                        },{
                                            boxLabel  : lang('I Do Not Have to Pay Them'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_not_pay_them',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_not_pay_them',
                                            inputValue: '1',
                                            disabled: true,
                                            uncheckedValue: "",
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2']
                                        },{
                                            boxLabel  : lang('Other'),
                                            name      : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_other',
                                            id        : 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-reason_other',
                                            inputValue: '1',
                                            disabled: true,
                                            uncheckedValue: "",
                                            cls: ['disabled-component-family-labour-postline','disabled-component-family-labour-postline-validatesecond-2']
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gflp_reason_the_family_member_works')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-Enumerator',
                                name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-Enumerator',
                                fieldLabel: lang('Enumerator'),
                                labelAlign:'top',
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-DateCreated',
                                name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-DateCreated',
                                fieldLabel: lang('Created Date'),
                                labelAlign:'top',
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-ModifiedBy',
                                name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-ModifiedBy',
                                fieldLabel: lang('Modified by'),
                                labelAlign:'top',
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-DateUpdated',
                                name: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-DateUpdated',
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
            id: 'Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-BtnSave',
            hidden: true,
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var WinFormFamilyLabourPostline = Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form').getForm();
                if (WinFormFamilyLabourPostline.isValid()) {
                    WinFormFamilyLabourPostline.submit({
                        url: m_api + '/grower/family_labour_postline',
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
                            WinFormFamilyLabourPostline.reset();

                            //refresh store FamLab yg manggil
                            Ext.data.StoreManager.lookup('Koltiva.store.FamilyLabourPostline.GridFamilyLabourPostline').load();

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
            var formNya = Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form');
            formNya.getForm().reset();

            if(thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view'){
                setTimeout(() => {
                    formNya.getForm().load({
                        url: m_api + '/grower/member_family_labour_postline_data',
                        method: 'GET',
                        params: {
                            FamLabPostID: thisObj.viewVar.FamLabPostID
                        },
                        success: function(form, action) {
                            var r = Ext.decode(action.response.responseText);

                            if(thisObj.viewVar.opsiDisplay == 'view'){
                                Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-BtnSave').setVisible(false);

                                Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-conducting_postlineid').setDisabled(true);
                            } else {
                                if (thisObj.viewVar.opsiDisplay == 'update') {
                                    Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-BtnSave').setVisible(true);

                                    Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-conducting_postlineid').setDisabled(false);
                                }
                            }

                            Ext.getCmp('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-FamLabName').setReadOnly(true);
                            Ext.MessageBox.hide();

                        },
                        failure: function(form, action) {
                            Ext.MessageBox.hide();
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: 'Failed to retrieve data',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }, 4000)
            } 

            if (thisObj.viewVar.opsiDisplay == 'insert') {
                setTimeout(() => {
                    Ext.MessageBox.hide();
                }, 4000)
            }
        }
    },
    afterShow: function(animateTarget, cb, scope) {
        Ext.MessageBox.show({
            msg: lang('Please wait...'),
            progressText: lang('Loading...'),
            width: 300,
            wait: true,
            waitConfig: {
                interval: 200
            },
            icon: 'ext-mb-info', //custom class in msg-box.html
            animateTarget: 'mb9'
        });
    },
});