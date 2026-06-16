/*
* @Author: nikolius
* @Date:   2017-06-01 17:07:40
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-11 14:30:23
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

Ext.define('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey',
    title: lang('Household Survey Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '98%',
    height: '94%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store --------------------------------------------------------------------------------------------------------------- (begin)
        var cmb_survey_nr = Ext.create('Koltiva.store.PlotSurvey.CmbSurveyNr');
        cmb_survey_nr.setStoreVar({from:'certification'});
        // var cmb_province_household    = Ext.create('Koltiva.store.Grower.CmbProvinceHouseHold');
        // cmb_province_household.load();

        var cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        cmb_province.load();
        //store --------------------------------------------------------------------------------------------------------------- (end)

        //items --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form',
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
                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-MemberID',
                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-MemberID'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-MemberDisplayID',
                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-MemberDisplayID',
                                fieldLabel: lang('Farmer ID'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-MemberName',
                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-MemberName',
                                fieldLabel: lang('Farmer Name'),
                                readOnly: true
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-SurveyNr',
                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-SurveyNr',
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
                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DateCollection',
                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DateCollection',
                                fieldLabel: lang('Date Collection'),
                                allowBlank: false,
                                format: 'Y-m-d H:i:s'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-CreatedByLabel',
                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-CreatedByLabel',
                                fieldLabel: lang('Enumerator'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-ModifiedByLabel',
                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-ModifiedByLabel',
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
                                xtype: 'panel',
                                title: lang('PPI 2019'),
                                frame: false,
                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-SectionPPI2019',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-ProvinceID',
                                            name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-ProvinceID',
                                            store: cmb_province,
                                            fieldLabel: lang('In which province is your household located'),
                                            queryMode: 'local',
                                            labelAlign:'top',
                                            displayField: 'label',
                                            valueField: 'id',
                                            listeners: {
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
                                            text: lang('How many members in the household ?')
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
                                                boxLabel: lang('Six or more'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember',
                                                inputValue: '6',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember1',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Four'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember',
                                                inputValue: '4',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember3',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Two'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember5',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Five'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember',
                                                inputValue: '5',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember2',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Three'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember',
                                                inputValue: '3',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember4',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('One'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMember6',
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
                                            text: lang('How many household members aged 10 years and over worked in the last week or if they temporarily did not work and will return to work ?')
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
                                                boxLabel: lang('None'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWNotWork',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWNotWork1',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('One'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWNotWork',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWNotWork2',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Two'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWNotWork',
                                                inputValue: '3',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWNotWork3',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Three or More'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWNotWork',
                                                inputValue: '4',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWNotWork4',
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
                                            text: lang('How many household members aged 10 years and over worked in the last week with the main job as laborers / employees / employees, or business owners ?')
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
                                                boxLabel: lang('None'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWMainJob',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWMainJob1',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('One'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWMainJob',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWMainJob2',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Two or More'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWMainJob',
                                                inputValue: '3',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberTenOWMainJob3',
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
                                            text: lang('In the last three months, did the female head of household (or the eldest wife of the male head of household) have a cellular telephone (HP) / wireless phone ?')
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
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberHavePhone',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberHavePhone1',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('No Female Houshold or Wife of Household'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberHavePhone',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberHavePhone2',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberHavePhone',
                                                inputValue: '3',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HhMemberHavePhone3',
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
                                            text: lang('What is the main floor type (covering most of the space) used in the house ?')
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
                                                boxLabel: lang('Earth or bamboo and orther'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HHMainFloorType',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HHMainFloorType1',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Cemnent/red brik, or woord/plank'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HHMainFloorType',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HHMainFloorType2',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Tile/title/terrazzo'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HHMainFloorType',
                                                inputValue: '3',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HHMainFloorType3',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Ceramic, or marble/granite'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HHMainFloorType',
                                                inputValue: '4',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HHMainFloorType4',
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
                                            text: lang('What is the primary of fuel the household ?')
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
                                                boxLabel: lang('Firewood, charcoal, or coal'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-PrimaryFuel',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-PrimaryFuel1',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Gas/LPG, kerosene, electricity, others, or does not cook'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-PrimaryFuel',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-PrimaryFuel2',
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
                                            text: lang('Does the household own a refrigerator ?')
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnRefri',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnRefri1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnRefri',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnRefri2',
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
                                            text: lang('Does the household own a motor cycle or motor boat ?')
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnMotor',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnMotor1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnMotor',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnMotor2',
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
                                            text: lang('In the last 4 months, has your household ever bought / received rice for a poor household (Raskin) / (Rastra) ?')
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-BoughtPoorRice',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-BoughtPoorRice1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-BoughtPoorRice',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-BoughtPoorRice2',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                }]
                            },{
                                xtype: 'panel',
                                title: lang('Additional Info'),
                                frame: false,
                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-SectionAdditioanl',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                    fieldLabel: lang('Do this household have a bank account ?'),
                                    xtype: 'radiogroup',
                                    labelAlign:'top',
                                    labelWidth: 300,
                                    columns: 2,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveBankAccount',
                                        inputValue: '1',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveBankAccountYes',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveBankAccount',
                                        inputValue: '2',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveBankAccountNo',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    fieldLabel: lang('Do you use mobile banking/cellphone-based banking ?'),
                                    xtype: 'radiogroup',
                                    labelWidth: 300,
                                    labelAlign:'top',
                                    columns: 2,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-UseMobileBanking',
                                        inputValue: '1',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-UseMobileBankingYes',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-UseMobileBanking',
                                        inputValue: '2',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-UseMobileBankingNo',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-TypeOfPhone',
                                    name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-TypeOfPhone',
                                    fieldLabel: lang('What type of phone does this household have? (phone model & type)'),
                                    labelWidth: 300,
                                    hidden: true
                                },{
                                    fieldLabel: lang('Does this household have an Android phone or iPhone ?'),
                                    xtype: 'radiogroup',
                                    labelWidth: 300,
                                    hidden: true,
                                    columns: 2,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveAndroIphone',
                                        inputValue: '1',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveAndroIphoneYes',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveAndroIphone',
                                        inputValue: '2',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveAndroIphoneNo',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    fieldLabel: lang('Does this household access e-mails, Facebook or Whatsapp with their phone ?'),
                                    xtype: 'radiogroup',
                                    labelWidth: 300,
                                    columns: 2,
                                    hidden:true,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveAccEmailFbWa',
                                        inputValue: '1',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveAccEmailFbWaYes',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveAccEmailFbWa',
                                        inputValue: '2',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveAccEmailFbWaNo',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
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
                                            text: lang('How much time each week do you spend accessing the internet on your mobile device ?')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    hidden:true,
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Never'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-TimeAccessInet',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-TimeAccessInet1',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('1-5 hours'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-TimeAccessInet',
                                                inputValue: '3',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-TimeAccessInet3',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Less than 1 hour'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-TimeAccessInet',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-TimeAccessInet2',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('More than 5 hours'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-TimeAccessInet',
                                                inputValue: '4',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-TimeAccessInet4',
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
                                    id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-AvgDaysConsumeBeef',
                                    name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-AvgDaysConsumeBeef',
                                    fieldLabel: lang('How many days per week on average did this household consume chicken,beef or fresh fish in the last month'),
                                    labelWidth: 300,
                                    allowNegative: false,
                                    minValue: 0,
                                    maxValue: 7,
                                    hidden:true
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Does the household own a private car ?')
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnPrivateCar',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnPrivateCar1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnPrivateCar',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnPrivateCar2',
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
                                            text: lang('Does the household own a gridded electricity ?')
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnGriddedElectricity',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnGriddedElectricity1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnGriddedElectricity',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnGriddedElectricity2',
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
                                            text: lang('Does the household own a computer ?')
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnComputer',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnComputer1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnComputer',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnComputer2',
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
                                            text: lang('Does the household own a AC ?')
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnAC',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnAC1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnAC',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OwnAC2',
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
                                            text: lang('What are your expectations of the mill to support the improvement of your plantation\'s conditions ?')
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
                                            id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-ExpectationOfImprovement',
                                            name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-ExpectationOfImprovement',
                                            width: '100%'
                                        }]
                                    }]
                                }]
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style:'padding-left:15px;',
                            items:[{
                                xtype: 'panel',
                                title: lang('Finance'),
                                frame: false,
                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-SectionFinance',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Does working in an oil palm plantation cover the economic needs of your family')
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WorkPalmCoverEconomy',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WorkPalmCoverEconomy1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WorkPalmCoverEconomy',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WorkPalmCoverEconomy2',
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
                                            text: lang('What needs are covered')
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
                                            columnWidth: 0.49,
                                            border: false,
                                            items:[{
                                                xtype: 'checkboxfield',
                                                boxLabel: lang('Foods'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverFoods',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverFoods',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                xtype: 'checkboxfield',
                                                boxLabel: lang('Clothing'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverClothing',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverClothing',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                xtype: 'checkboxfield',
                                                boxLabel: lang('Household equipment'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverHouseEquip',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverHouseEquip',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                xtype: 'checkboxfield',
                                                boxLabel: lang('Other'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverOther',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverOther',
                                                listeners:{
                                                    change: function(){
                                                        if(this.checked == true){
                                                            Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverOtherComment').setVisible(true);
                                                        }else{
                                                            Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverOtherComment').setVisible(false);
                                                        }
                                                    }
                                                }
                                            },{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverOtherComment',
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverOtherComment',
                                                hidden: true
                                            }]
                                        },{
                                            columnWidth: 0.5,
                                            border: false,
                                            items:[{
                                                xtype: 'checkboxfield',
                                                boxLabel: lang('Housing'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverHousing',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverHousing',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                xtype: 'checkboxfield',
                                                boxLabel: lang('Education'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverEducation',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverEducation',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                xtype: 'checkboxfield',
                                                boxLabel: lang('Recreation / holidays'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverRecre',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-NeedsCoverRecre',
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
                                            text: lang('Do you think of finding another job/planting different crop')
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-ThinkAnotherJobPlant',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-ThinkAnotherJobPlant1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-ThinkAnotherJobPlant',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-ThinkAnotherJobPlant2',
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
                                            text: lang('Do you have a loan')
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveLoan',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveLoan1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveLoan',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveLoan2',
                                                listeners: {
                                                    change: function() {
                                                        if(this.checked == true){
                                                            Ext.getCmp('LabelWhereTakeLoan').setDisabled(true);
                                                            Ext.getCmp('RowWhereTakeLoan').setDisabled(true);
                                                            Ext.getCmp('LabelLoanForPalm').setDisabled(true);
                                                            Ext.getCmp('RowLoanForPalm').setDisabled(true);
                                                        }else{
                                                            Ext.getCmp('LabelWhereTakeLoan').setDisabled(false);
                                                            Ext.getCmp('RowWhereTakeLoan').setDisabled(false);
                                                            Ext.getCmp('LabelLoanForPalm').setDisabled(false);
                                                            Ext.getCmp('RowLoanForPalm').setDisabled(false);
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
                                    id: 'LabelWhereTakeLoan',
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Where did you take out the loan')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    id: 'RowWhereTakeLoan',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Bank'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WhereLoanFrom',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WhereLoanFrom1',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Family/friend'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WhereLoanFrom',
                                                inputValue: '3',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WhereLoanFrom3',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Unofficial loan SME'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WhereLoanFrom',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WhereLoanFrom2',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Other'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WhereLoanFrom',
                                                inputValue: '4',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-WhereLoanFrom4',
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
                                    id: 'LabelLoanForPalm',
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Is the loan used for oil palm cultivation')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    id: 'RowLoanForPalm',
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-LoanForPalm',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-LoanForPalm1',
                                                listeners: {
                                                    change: function() {
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-LoanForPalm',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-LoanForPalm2',
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
                                    id: 'LabelOtherIncome',
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        items:[{
                                            xtype:'label',
                                            cls: 'x-form-item-label',
                                            baseCls: 'Sfr_FormInputMandatory',
                                            text: lang('Do you have other source of income than your palm oil plantation')
                                        }]
                                    }]
                                },{
                                    layout: 'column',
                                    border: false,
                                    style:'margin-top:-20px;padding-top:0px;',
                                    id: 'RowOtherIncome',
                                    items:[{
                                        layout:'column',
                                        columnWidth: 1,
                                        style:'margin-top:-7px;padding-top:0px;',
                                        allowBlank:false,
                                        items:[{
                                            columnWidth: 0.475,
                                            border: false,
                                            defaultType: 'radiofield',
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncome',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncome1',
                                                listeners: {
                                                    change: function() {
                                                        if(this.checked == true){
                                                            // Ext.getCmp('LabelOtherIncomeType').setDisabled(false);
                                                            Ext.getCmp('RowOtherIncomeType').setDisabled(false);
                                                            // Ext.getCmp('LabelOtherIncomeSource').setDisabled(false);
                                                            Ext.getCmp('RowOtherIncomeSource').setDisabled(false);
                                                            
                                                            Ext.getCmp('RowOtherIncomeType').allowBlank = false;
                                                            Ext.getCmp('RowOtherIncomeSource').allowBlank = false;
                                                        }else{
                                                            // Ext.getCmp('LabelOtherIncomeType').setDisabled(true);
                                                            Ext.getCmp('RowOtherIncomeType').setDisabled(true);
                                                            // Ext.getCmp('LabelOtherIncomeSource').setDisabled(true);
                                                            Ext.getCmp('RowOtherIncomeSource').setDisabled(true);

                                                            Ext.getCmp('RowOtherIncomeType').allowBlank = true;
                                                            Ext.getCmp('RowOtherIncomeSource').allowBlank = true;
                                                            Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeType1').setValue(false);
                                                            Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeType2').setValue(false);
                                                        }
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
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncome',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncome2',
                                                listeners: {
                                                    change: function() {
                                                        if(this.checked == true){
                                                            // Ext.getCmp('LabelOtherIncomeType').setDisabled(true);
                                                            Ext.getCmp('RowOtherIncomeType').setDisabled(true);
                                                            // Ext.getCmp('LabelOtherIncomeSource').setDisabled(true);
                                                            Ext.getCmp('RowOtherIncomeSource').setDisabled(true);

                                                            Ext.getCmp('RowOtherIncomeType').allowBlank = true;
                                                            Ext.getCmp('RowOtherIncomeSource').allowBlank = true;

                                                            Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeType1').setValue(false);
                                                            Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeType2').setValue(false);
                                                        }else{
                                                            // Ext.getCmp('LabelOtherIncomeType').setDisabled(false);
                                                            Ext.getCmp('RowOtherIncomeType').setDisabled(false);
                                                            // Ext.getCmp('LabelOtherIncomeSource').setDisabled(false);
                                                            Ext.getCmp('RowOtherIncomeSource').setDisabled(false);

                                                            Ext.getCmp('RowOtherIncomeType').allowBlank = false;
                                                            Ext.getCmp('RowOtherIncomeSource').allowBlank = false;
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
                                            fieldLabel: lang('Your other income is'),
                                            xtype: 'radiogroup',
                                            columns: 2,
                                            labelAlign:'top',
                                            msgTarget: 'side',
                                            id: 'RowOtherIncomeType',
                                            items:[{
                                                boxLabel: lang('Regular'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeType',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeType1',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Irregular'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeType',
                                                inputValue: '2',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeType2',
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
                                            fieldLabel: lang('What is the source of the other income'),
                                            labelWidth: 230,
                                            xtype: 'checkboxgroup',
                                            columns: 2,
                                            labelAlign:'top',
                                            msgTarget: 'side',
                                            id: 'RowOtherIncomeSource',
                                            items:[{
                                                boxLabel: lang('Salary From a Full Time / Part Time Job'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeJob',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeJob',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Relative Send Money From Abord'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeSendMoney',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeSendMoney',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Spouse Salary'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeSpouse',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeSpouse',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Income From Other Business'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeBusiness',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeBusiness',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Income From Other Crops'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeCrops',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeCrops',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Other'),
                                                name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeOther',
                                                inputValue: '1',
                                                id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncomeOther',
                                                listeners: {
                                                    change: function() {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    fieldLabel: lang('Do you want to discloce your information about your income?'),
                                    labelAlign:'top',
                                    xtype: 'radiogroup',
                                    msgTarget: 'side',
                                    columns: 3,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncome',
                                        inputValue: '1',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncome1',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('DiscloceIncomeMonthly').setVisible(true);
                                                    Ext.getCmp('DiscloceIncomeFarmer').setVisible(true);
                                                    Ext.getCmp('DiscloceIncomeSpend').setVisible(true);    
                                                    Ext.getCmp('DiscloceIncomeHousehold').setVisible(true);                                                
                                                }else{
                                                    Ext.getCmp('DiscloceIncomeMonthly').setVisible(false);
                                                    Ext.getCmp('DiscloceIncomeFarmer').setVisible(false);
                                                    Ext.getCmp('DiscloceIncomeSpend').setVisible(false);
                                                    Ext.getCmp('DiscloceIncomeHousehold').setVisible(false);                                                    
                                                }

                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncome',
                                        inputValue: '2',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncome2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    html:'<div></div>'
                                },{
                                    fieldLabel: lang('What is the average monthly income of oil palm sector farmers'),
                                    labelAlign:'top',
                                    xtype: 'radiogroup',
                                    msgTarget: 'side',
                                    hidden:true,
                                    id:'DiscloceIncomeMonthly',
                                    columns: 3,
                                    items:[{
                                        boxLabel: lang('< Rp 3.000.0000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly',
                                        inputValue: '1',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 3.000.000 - Rp 6.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly',
                                        inputValue: '2',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 6.000.000 - Rp 9.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly',
                                        inputValue: '3',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly3',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 9.000.0000 - 12.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly',
                                        inputValue: '4',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly4',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 12.000.000 - 15.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly',
                                        inputValue: '5',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly5',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 15.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly',
                                        inputValue: '6',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly6',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('I don`t want to disclose this information'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly',
                                        inputValue: '7',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeMonthly7',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    html:'<div></div>'
                                },{
                                    fieldLabel: lang('What is the average income of farmers other than the Palm Oil sector?'),
                                    labelAlign:'top',
                                    xtype: 'radiogroup',
                                    msgTarget: 'side',
                                    hidden:true,
                                    id:'DiscloceIncomeFarmer',
                                    columns: 3,
                                    items:[{
                                        boxLabel: lang('< Rp 3.000.0000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer',
                                        inputValue: '1',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 3.000.000 - Rp 6.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer',
                                        inputValue: '2',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 6.000.000 - Rp 9.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer',
                                        inputValue: '3',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer3',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 9.000.0000 - 12.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer',
                                        inputValue: '4',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer4',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 12.000.000 - 15.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer',
                                        inputValue: '5',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer5',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 15.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer',
                                        inputValue: '6',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer6',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('I don`t want to disclose this information'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer',
                                        inputValue: '7',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeFarmer7',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    html:'<div></div>'
                                },{
                                    fieldLabel: lang('What is your average monthly spend for the palm oil sector'),
                                    labelAlign:'top',
                                    xtype: 'radiogroup',
                                    msgTarget: 'side',
                                    hidden:true,
                                    id:'DiscloceIncomeSpend',
                                    columns: 3,
                                    items:[{
                                        boxLabel: lang('< Rp 3.000.0000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend',
                                        inputValue: '1',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 3.000.000 - Rp 6.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend',
                                        inputValue: '2',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 6.000.000 - Rp 9.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend',
                                        inputValue: '3',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend3',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 9.000.0000 - 12.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend',
                                        inputValue: '4',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend4',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 12.000.000 - 15.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend',
                                        inputValue: '5',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend5',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 15.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend',
                                        inputValue: '6',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend6',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('I don`t want to disclose this information'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend',
                                        inputValue: '7',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeSpend7',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    html:'<div></div>'
                                },{
                                    fieldLabel: lang('What is your average household expenses per month?'),
                                    labelAlign:'top',
                                    xtype: 'radiogroup',
                                    msgTarget: 'side',
                                    hidden:true,
                                    id:'DiscloceIncomeHousehold',
                                    columns: 3,
                                    items:[{
                                        boxLabel: lang('< Rp 3.000.0000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold',
                                        inputValue: '1',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 3.000.000 - Rp 6.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold',
                                        inputValue: '2',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 6.000.000 - Rp 9.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold',
                                        inputValue: '3',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold3',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 9.000.0000 - 12.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold',
                                        inputValue: '4',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold4',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 12.000.000 - 15.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold',
                                        inputValue: '5',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold5',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('> Rp 15.000.000'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold',
                                        inputValue: '6',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold6',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('I don`t want to disclose this information'),
                                        name: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold',
                                        inputValue: '7',
                                        id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DiscloceIncomeHousehold7',
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
        }];
        //items --------------------------------------------------------------------------------------------------------------- (end)

        //buttons --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var formNya = Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form').getForm();
                if (formNya.isValid()) {

                    formNya.submit({
                        url: m_api + '/household_survey/survey',
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
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
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
            var formNya = Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form');
            formNya.getForm().reset();

            //set MemberID
            Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-MemberID').setValue(thisObj.viewVar.MemberID);
            Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-HaveLoan2').setValue(true);
            Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-OtherIncome2').setValue(true);

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

                        Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-MemberDisplayID').setValue(r.data.MemberDisplayID);
                        Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-MemberName').setValue(r.data.MemberName);
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
                    url: m_api + '/household_survey/household_survey_form_data',
                    method: 'GET',
                    params: {
                        MemberID: thisObj.viewVar.MemberID,
                        SurveyNr: thisObj.viewVar.SurveyNr,
                        DateCollection: thisObj.viewVar.DateCollection
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        //kasih readonly untuk field yg tak boleh ubah
                        Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-SurveyNr').setReadOnly(true);
                        Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-DateCollection').setReadOnly(true);

                        if(thisObj.viewVar.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey-Form-BtnSave').setVisible(false);
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