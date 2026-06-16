Ext.define('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline',
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
                Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-BtnSave').setVisible(true)

                Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ConductingPostlineID').reset()
            }
        } else {
            Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-BtnSave').setVisible(false)
        }

        Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline').searchComponentSpesificObj('disabled-component-farmer-labour-postline-init', disabledComponents, 'validateFirst', false)
    },
    validateConducting: function() {
       let conductingPostline  = 0
       let disabledComponents  = true

        if(Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ConductingPostlineYes').getValue() == true){
            conductingPostline = 1;
        }

        if(Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ConductingPostlineNo').getValue() == true){
            conductingPostline = 2;
        }

       if (conductingPostline == 1) {
            disabledComponents = false

            Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline').searchComponentSpesificObj('disabled-component-farmer-labour-postline-init2', disabledComponents, 'validateConducting', false)
            Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline').searchComponentSpesificObj('disabled-component-farmer-labour-postline-init3', disabledComponents, 'validateConducting', true)
       } else {
            Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline').searchComponentSpesificObj('disabled-component-farmer-labour-postline-init2', disabledComponents, 'validateConducting', true)
            Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline').searchComponentSpesificObj('disabled-component-farmer-labour-postline-init3', disabledComponents, 'validateConducting', false)
       }

       Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-survey_nr').reset()
    },
    searchComponentSpesificObj: function(classSpesific, disabledComponents, remarks, allowBlank) {
        let componentSpesific  = Ext.ComponentQuery.query('*[id^=Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-]', Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline'))

        if (this.viewVar.opsiDisplay == "view") {
            disabledComponents = true
            allowBlank = true
        }

        componentSpesific.forEach(function(components){
            if (Array.isArray(components.cls) == true) {
                if (remarks == "validateFirst") {
                    if (components.cls[0] == classSpesific) {
                        components.reset()

                        components.setDisabled(disabledComponents)
                        components.allowBlank = allowBlank
                    }
                } else {
                    if (components.cls[0] == classSpesific) {
                        components.reset()

                        if (components.id == "Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-survey_nr") {
                            components.setReadOnly(disabledComponents)
                        } else {
                            components.setDisabled(disabledComponents)
                        }

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
        thisObj.cmb_survey_nr        = Ext.create('Koltiva.store.FarmerLabourPostline.CmbSurveyNr');
        thisObj.cmb_farm_labour_name  = Ext.create('Koltiva.store.FarmerLabourPostline.CmbFarmLabourName', {
            storeVar: {
                MemberID: thisObj.viewVar.MemberID
            }
        });

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form',
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
                                id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-LaboID',
                                name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-LaboID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-LaboPostID',
                                name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-LaboPostID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-MemberID',
                                name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-MemberID'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-LaboName',
                                name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-LaboName',
                                allowBlank : false,
                                store: thisObj.cmb_farm_labour_name,
                                labelAlign: 'top', 
                                minChars: 3,
                                baseCls: 'Sfr_FormInputMandatory',
                                fieldLabel: lang('Farmer Labour Name'),  
                                displayField: 'label',
                                valueField: 'id',
                                enableKeyEvents: true,
                                emptyText: lang('Search by Farmer Labour Name'),
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
                                            Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline').validateFirst(post.raw.id)

                                            Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-LaboID').setValue(post.raw.id)
                                            Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-MemberID').setValue(post.raw.MemberID)
                                        }
                                    },
                                    keyup: function(value) {
                                        Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline').validateFirst(null)
                                    }                               
                                }
                                
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
                                        cls: ['disabled-component-farmer-labour-postline-init'],
                                        disabled: true,
                                        columns: 2,
                                        id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ConductingPostlineID',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ConductingPostline',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ConductingPostlineYes',
                                            listeners:{
                                                change: function(){
                                                    Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline').validateConducting()
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ConductingPostline',
                                            inputValue: '2',
                                            id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ConductingPostlineNo',
                                            listeners:{
                                                change: function(){
                                                    Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline').validateConducting()
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfrlp_ConductingPostline')+'</div>'
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
                                        id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-survey_nr',
                                        name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-survey_nr',
                                        store: thisObj.cmb_survey_nr,
                                        fieldLabel: lang('Survey Nr'),
                                        allowBlank: true,
                                        baseCls: 'Sfr_FormInputMandatory',
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                        labelAlign:'top',
                                        cls: ['disabled-component-farmer-labour-postline-init2'],
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfrlp_survey_nr')+'</div>'
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
                                        cls: ['disabled-component-farmer-labour-postline-init2'],
                                        baseCls: 'Sfr_FormInputMandatory',
                                        defaultType: 'checkboxfield',
                                        id : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkID',
                                        items: [{
                                            boxLabel  : lang('Planting'),
                                            name      : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkPlanting',
                                            id        : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkPlanting',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-farmer-labour-postline-init2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Slashing'),
                                            name      : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkSlash',
                                            id        : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkSlash',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-farmer-labour-postline-init2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Circle Weeding'),
                                            name      : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkCircle',
                                            id        : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkCircle',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-farmer-labour-postline-init2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Pruning'),
                                            name      : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkPruning',
                                            id        : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkPruning',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-farmer-labour-postline-init2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Fertilizing'),
                                            name      : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkFertilizing',
                                            id        : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkFertilizing',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-farmer-labour-postline-init2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Pesticide Application'),
                                            name      : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkPest',
                                            id        : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkPest',
                                            inputValue: '1',
                                            disabled: true,
                                            cls: ['disabled-component-farmer-labour-postline-init2'],
                                            uncheckedValue: ""
                                        },{
                                            boxLabel  : lang('Harvest'),
                                            name      : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkHarvest',
                                            id        : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkHarvest',
                                            inputValue: '1',
                                            disabled: true,
                                            uncheckedValue: "",
                                            cls: ['disabled-component-farmer-labour-postline-init2']
                                        },{
                                            boxLabel  : lang('Transportation'),
                                            name      : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkTransport',
                                            id        : 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TypeWorkTransport',
                                            inputValue: '1',
                                            disabled: true,
                                            uncheckedValue: "",
                                            cls: ['disabled-component-farmer-labour-postline-init2'],
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfrlp_TypeofWorkID')+'</div>'
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
                                        id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-DayWorkInMonth',
                                        name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-DayWorkInMonth',
                                        fieldLabel: lang('Total Working Days per Month'),
                                        labelAlign:'top',
                                        labelWidth: 190,
                                        allowNegative: false,
                                        minValue: 0,
                                        maxValue:30,
                                        disabled: true,
                                        cls: ['disabled-component-farmer-labour-postline-init2']
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfrlp_DayWorkInMonth')+'</div>'
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
                                        id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TotalWorkingHrsPerDay',
                                        name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-TotalWorkingHrsPerDay',
                                        fieldLabel: lang('Total Working Hours per Day'),
                                        labelAlign:'top',
                                        labelWidth: 190,
                                        allowNegative: false,
                                        minValue: 0,
                                        maxValue:20,
                                        disabled: true,
                                        cls: ['disabled-component-farmer-labour-postline-init2']
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfrlp_TotalWorkingHrsPerDay')+'</div>'
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
                                        id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-WageAmount',
                                        name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-WageAmount',
                                        allowNegative: false,
                                        minValue: 0,
                                        disabled: true,
                                        cls: ['disabled-component-farmer-labour-postline-init3']
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfrlp_WageAmount')+'</div>'
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
                                        id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-WagePeriod',
                                        name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-WagePeriod',
                                        labelAlign:'top',
                                        store: thisObj.cmb_wage_period,
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                        labelWidth: 190,
                                        fieldLabel: lang('Wage Period'),
                                        disabled: true,
                                        cls: ['disabled-component-farmer-labour-postline-init3']
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
                                            'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfrlp_WagePeriod')+'</div>'
                                        }
                                    }]
                                }]
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-Enumerator',
                                name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-Enumerator',
                                fieldLabel: lang('Enumerator'),
                                labelAlign:'top',
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-DateCreated',
                                name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-DateCreated',
                                fieldLabel: lang('Created Date'),
                                labelAlign:'top',
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ModifiedBy',
                                name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ModifiedBy',
                                fieldLabel: lang('Modified by'),
                                labelAlign:'top',
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-DateUpdated',
                                name: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-DateUpdated',
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
            id: 'Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-BtnSave',
            hidden: true,
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var WinFormFarmerLabourPostline = Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form').getForm();
                if (WinFormFarmerLabourPostline.isValid()) {
                    WinFormFarmerLabourPostline.submit({
                        url: m_api + '/grower/labour_postline',
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
                            WinFormFarmerLabourPostline.reset();

                            //refresh store Labo yg manggil
                            Ext.data.StoreManager.lookup('Koltiva.store.FarmerLabourPostline.GridFarmerLabourPostline').load();

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
            var formNya = Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form');
            formNya.getForm().reset();

            if(thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view'){
                setTimeout(() => {
                    formNya.getForm().load({
                        url: m_api + '/grower/member_labour_postline_form_data',
                        method: 'GET',
                        params: {
                            LaboPostID: thisObj.viewVar.LaboPostID
                        },
                        success: function(form, action) {
                            var r = Ext.decode(action.response.responseText);

                            if(thisObj.viewVar.opsiDisplay == 'view'){
                                Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-BtnSave').setVisible(false);

                                Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ConductingPostlineID').setDisabled(true);
                            } else {
                                if (thisObj.viewVar.opsiDisplay == 'update') {
                                    Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-BtnSave').setVisible(true);
                                    
                                    Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-ConductingPostlineID').setDisabled(false);
                                }
                            }

                            Ext.getCmp('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-LaboName').setReadOnly(true);
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