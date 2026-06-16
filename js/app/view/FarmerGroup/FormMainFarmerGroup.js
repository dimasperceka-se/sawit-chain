/*
* @Author: nikolius
* @Date:   2017-11-08 17:46:33
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 11:37:01
*/

/*
    Param2 yg diperlukan ketika load View ini
    - opsiDisplay
    - FarmerGroupID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)


// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)


Ext.define('Koltiva.view.FarmerGroup.FormMainFarmerGroup' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai (begin)
        var cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        cmb_province.load();
        var cmb_district = Ext.create('Koltiva.store.Grower.CmbDistrict');
        var cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        var cmb_village = Ext.create('Koltiva.store.Grower.CmbVillage');

        var cmb_year_option = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption');
        var cmb_farmer_group_member = Ext.create('Koltiva.store.ComboGeneral.CmbFarmerGroupMember');
        var cmb_sme = Ext.create('Koltiva.store.ComboGeneral.CmbSMEDealer');
        //store yg dipakai (end)

        var cmb_member_group_category = Ext.create('Ext.data.Store',{
            fields: ['id', 'label'],
                data: [{
                    "id": "1",
                    "label": lang('Palm Oil Mill')
                },{
                    "id": "3",
                    "label": lang('Dealership')
                },{
                    "id": "4",
                    "label": lang('Estate Group')
                },{
                    "id": "5",
                    "label": lang('Consultant')
                },{
                    "id": "6",
                    "label": lang('Village Group')
                },{
                    "id": "7",
                    "label": lang('Association')
                },{
                    "id": "8",
                    "label": lang('Cooperative')
                },{
                    "id": "9",
                    "label": lang('Others')
                }
            ]
        });

        if(m_user_partnerid == 14)
        {
            var GroupExtID = Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-GroupExtID');  
            GroupExtID = 'WAGS Member Group ID';  

            var RowWagsGroupCat = Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-RowWagsGroupCat');
            RowWagsGroupCat = 'Member Group Category';

        } else {
            var GroupExtID = Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-GroupExtID');  
            GroupExtID = 'External Group ID';  

            var RowWagsGroupCat = Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-RowWagsGroupCat');
            RowWagsGroupCat = 'Group Category';
        }

        //Panel Basic Data ===================================== (Begin)
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Basic Data'),
            frame: true,
            id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData',
            fileUpload: true,
            margin:'0 0 20 0',
            items: [{
                layout: 'column',
                border: false,
                padding:5,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.495,
                            layout:'form',
                            style:'padding-right:15px;',
                            items:[{
                                xtype: 'panel',
                                title: lang('General Data'),
                                frame: false,
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-GeneralSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-FarmerGroupID',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-FarmerGroupID',
                                fieldLabel: lang('Farmer Group ID'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-GroupExtID',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-GroupExtID',
                                fieldLabel: GroupExtID,
                                labelAlign:'top'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-GroupName',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-GroupName',
                                fieldLabel: lang('Group Name'),
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-YearEstablished',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-YearEstablished',
                                store: cmb_year_option,
                                fieldLabel: lang('Year Established'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id:'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-RowWagsGroupCat',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-RowWagsGroupCat',
                                store: cmb_member_group_category,
                                fieldLabel: RowWagsGroupCat,
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'panel',
                                title: lang('Location'),
                                frame: false,
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-LocationSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Province',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Province',
                                store: cmb_province,
                                fieldLabel: lang('Province'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                allowBlank: false,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        cmb_district.load({
                                            params: {
                                                ProvinceID: nv
                                            }
                                        });
                                        cmb_sme.load({
                                            params: {
                                                ProvinceID: nv
                                            }
                                        });
                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-District').setValue('');
                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Subdistrict').setValue('');
                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Village').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-District',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-District',
                                store: cmb_district,
                                fieldLabel: lang('District'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                allowBlank: false,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        cmb_subdistrict.load({
                                            params: {
                                                DistrictID: nv
                                            }
                                        });
                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Subdistrict').setValue('');
                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Village').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Subdistrict',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Subdistrict',
                                store: cmb_subdistrict,
                                fieldLabel: lang('Subdistrict'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        cmb_village.load({
                                            params: {
                                                SubdistrictID: nv
                                            }
                                        });
                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Village').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Village',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Village',
                                store: cmb_village,
                                fieldLabel: lang('Village'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-ZipCode',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-ZipCode',
                                labelAlign: 'top',
                                fieldLabel: lang('Zip code')
                            },{
                                html:'<div></div>'
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.495,
                                    layout:'form',
                                    style: 'margin-right: 5px',
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Latitude',
                                        name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Latitude',
                                        labelAlign: 'top',
                                        fieldLabel: lang('Latitude')
                                    }]
                                },{
                                    columnWidth: 0.495,
                                    layout:'form',
                                    style: 'margin-left: 5px',
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Longitude',
                                        name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Longitude',
                                        labelAlign: 'top',
                                        fieldLabel: lang('Longitude')
                                    }]
                                }]
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textarea',
                                fieldLabel: lang('Address'),
                                labelAlign:'top',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Address',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Address',
                                height: 90
                            },{
                                xtype: 'panel',
                                title: lang('Relation'),
                                frame: false,
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-RelationSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SMERelation',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SMERelation',
                                store: cmb_sme,
                                fieldLabel: lang('Dealer'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                allowBlank: false,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        return true;
                                    }
                                }
                            }]
                        },{
                            columnWidth: 0.5,
                            margin:'0 10 0 0',
                            style:'padding-left:15px;',
                            layout:'form',
                            items:[{
                                xtype: 'panel',
                                title: lang('Management'),
                                frame: false,
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-OtherInformationSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 1,
                                    layout:'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HaveManagementLabel',
                                        text: lang('Does the Farmer Group have  Management')
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
                                            name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HaveManagement',
                                            inputValue: '1',
                                            id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HaveManagement1',
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
                                            name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HaveManagement',
                                            inputValue: '0',
                                            id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HaveManagement0',
                                            listeners: {
                                                change: function() {
                                                    if(this.checked == true){
                                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Chairman').setDisabled(true);
                                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Secretary').setDisabled(true);
                                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Treasurer').setDisabled(true);
                                                    }else{
                                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Chairman').setDisabled(false);
                                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Secretary').setDisabled(false);
                                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Treasurer').setDisabled(false);
                                                    }

                                                    return false;
                                                }
                                            }
                                        }]
                                    }]
                                }]
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Chairman',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Chairman',
                                // store: cmb_farmer_group_member,
                                fieldLabel: lang('Chairman'),
                                labelAlign:'top'
                                // queryMode: 'local',
                                // displayField: 'label',
                                // valueField: 'id'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Secretary',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Secretary',
                                // store: cmb_farmer_group_member,
                                fieldLabel: lang('Secretary'),
                                labelAlign:'top',
                                // queryMode: 'local',
                                // displayField: 'label',
                                // valueField: 'id'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Treasurer',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Treasurer',
                                // store: cmb_farmer_group_member,
                                fieldLabel: lang('Treasurer'),
                                labelAlign:'top',
                                // queryMode: 'local',
                                // displayField: 'label',
                                // valueField: 'id'
                            },{
                                html:'<div></div>'
                            },{
                                fieldLabel: lang('Legal status of group'),
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                allowBlank: false,
                                msgTarget: 'side',
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Legalized'),
                                    name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-LegalStatus',
                                    inputValue: '1',
                                    id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-LegalStatusLegalized',
                                    style: 'margin-top:-10px;',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('NonLegalized'),
                                    name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-LegalStatus',
                                    inputValue: '2',
                                    id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-LegalStatusNonLegalized',
                                    style: 'margin-top:-10px;',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'panel',
                                title: lang('Training and Assistance'),
                                frame: false,
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingAssistanceSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                fieldLabel: lang('Did you receive any support/assisstance as a group'),
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                allowBlank: false,
                                msgTarget: 'side',
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HadSupportGroup',
                                    inputValue: '1',
                                    id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HadSupportGroupYes',
                                    style: 'margin-top:-10px;',
                                    listeners:{
                                        change: function(){
                                            if(this.checked == true){
                                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HadSupportPanel').setVisible(true);
                                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TypeSupportPanel').setVisible(true);
                                                // Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TypeTrainingPanel').setVisible(true);
                                            }
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HadSupportGroup',
                                    inputValue: '2',
                                    id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HadSupportGroupNonNo',
                                    style: 'margin-top:-10px;',
                                    listeners:{
                                        change: function(){
                                            if(this.checked == true){
                                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HadSupportPanel').setVisible(false);
                                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TypeSupportPanel').setVisible(false);
                                                // Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TypeTrainingPanel').setVisible(false);
                                            }
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'fieldcontainer',
                                fieldLabel: lang('If yes, from whom'),
                                labelWidth: 80,
                                labelAlign:'top',
                                hidden:true,
                                id:'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HadSupportPanel',
                                layout: 'vbox',
                                defaultType: 'checkboxfield',
                                items: [{
                                    boxLabel  : lang('Government extention officer'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupGovernment',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupGovernment',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('NGO'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupNGO',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupNGO',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Mill'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupMill',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupMill',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Other Private Sector Oganization'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupPrivate',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupPrivate',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Others'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupOther',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupOther',
                                    inputValue: '1',
                                    listeners :{
                                        change:function(){
                                            if(this.checked == true){
                                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupOtherText').setVisible(true);
                                            }else{
                                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupOtherText').setVisible(false);
                                            }
                                        }
                                    }
                                }]
                            },{
                                xtype: 'textfield',
                                hidden:true,
                                labelAlign:'top',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupOtherText',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupOtherText'
                            },{
                                xtype: 'fieldcontainer',
                                fieldLabel: lang('What type of support'),
                                labelWidth: 80,
                                labelAlign:'top',
                                hidden:true,
                                id:'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TypeSupportPanel',
                                layout: 'vbox',
                                defaultType: 'checkboxfield',
                                items: [{
                                    boxLabel  : lang('Financial Support'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeFinnance',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeFinnance',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Advisory'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeAdvisor',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeAdvisor',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Training for Group Members'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeTraining',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeTraining',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Others'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeOther',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeOther',
                                    inputValue: '1',
                                    listeners :{
                                        change:function(){
                                            if(this.checked == true){
                                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeOtherText').setVisible(true);
                                            }else{
                                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeOtherText').setVisible(false);
                                            }
                                        }
                                    }
                                }]
                            },{
                                xtype: 'textfield',
                                hidden:true,
                                labelAlign:'top',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeOtherText',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeOtherText'
                            },{
                                xtype: 'fieldcontainer',
                                fieldLabel: lang('What type of trainings did you receive'),
                                labelWidth: 80,
                                labelAlign:'top',
                                hidden:true,
                                id:'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TypeTrainingPanel',
                                layout: 'vbox',
                                defaultType: 'checkboxfield',
                                items: [{
                                    boxLabel  : lang('Financial and farm business operations'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingFinance',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingFinance',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Good Agriculture Practice'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingAgriculture',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingAgriculture',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Human rights and worker rights'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingRights',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingRights',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Best Management of pesticides'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingBestManagement',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingBestManagement',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Fire fighting'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingFireFighter',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingFireFighter',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('HCV and HCS '),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingHCVHCS',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingHCVHCS',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('P&C RSPO Independent Smallholder Standard'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingRSPO',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingRSPO',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Others'),
                                    name      : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingOther',
                                    id        : 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingOther',
                                    inputValue: '1',
                                    listeners :{
                                        change:function(){
                                            if(this.checked == true){
                                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingOtherText').setVisible(true);
                                            }else{
                                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingOtherText').setVisible(false);
                                            }
                                        }
                                    }
                                }]
                            },{
                                xtype: 'textfield',
                                hidden:true,
                                labelAlign:'top',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingOtherText',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingOtherText'
                            },{
                                xtype: 'textarea',
                                fieldLabel: lang('Remark'),
                                labelAlign:'top',
                                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Remarks',
                                name: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Remarks',
                                height: 90
                            }]
                        }]
                    }]
                }]
            }],
            buttons: [{
                text: lang('Save'),
                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-btnSaveForm',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    if (objPanelBasicData.isValid()) {
                        objPanelBasicData.submit({
                            url: m_api + '/farmer_group/farmer_group_form',
                            method:'POST',
                            waitMsg: 'Saving data...',
                            success: function(fp, o) {
                                //console.log(o);
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: lang('Data saved'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup').destroy(); //destory current view
                                var FormMainFarmerGroup = [];

                                //create object View
                                if(Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup') == undefined){
                                    FormMainFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.FormMainFarmerGroup', {
                                        opsiDisplay: 'update',
                                        viewVar: {
                                            FarmerGroupID: o.result.FarmerGroupID
                                        }
                                    });
                                }else{
                                    //destroy, create ulang
                                    Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup').destroy();
                                    FormMainFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.FormMainFarmerGroup', {
                                        opsiDisplay: 'update',
                                        viewVar: {
                                            FarmerGroupID: o.result.FarmerGroupID
                                        }
                                    });
                                }
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
            }]
        });
        //Panel Basic Data ===================================== (End)

        //Panel Farmer Group Member ===================================== (Begin)
        var objPanelFarmerGroupMember = Ext.create('Koltiva.view.FarmerGroup.FarmerGroupMemberPanel');
        thisObj.objPanelFarmerGroupMember = objPanelFarmerGroupMember;
        //Panel Farmer Group Member ===================================== (End)

        //isi layout utama ================================================================================================= (Begin)
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                html:'<h3 style="margin:0px;padding:0px;">'+lang('Farmer Group Data')+'</h3>'
            },{
                id: 'Koltiva.view.FarmerGroup.FormMainFarmerGroup-labelInfoInsert',
                html:'',
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
                  '&nbsp;&nbsp;' + lang('Back to Farmer Group List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup').destroy(); //destory current view
                        var GridMainFarmerGroup = [];

                        if(Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup') == undefined){
                            GridMainFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.GridMainFarmerGroup');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup').destroy();
                            GridMainFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.GridMainFarmerGroup');
                        }
                    }
                }
            }
        },{
            html:'<br />'
        },{
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 0.6,
                items:[
                    objPanelBasicData
                ]
            },{
                //RIGHT CONTENT
                columnWidth: 0.4,
                items:[
                    thisObj.objPanelFarmerGroupMember
                ]
            }]
        }];
        //isi layout utama ================================================================================================= (Begin)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'none';

            if(m_user_partnerid == 14){ //Jika WAGS
                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-RowWagsGroupCat').setVisible(true);
                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-ZipCode').setVisible(true);
            }else{
                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-RowWagsGroupCat').setVisible(false);
                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-ZipCode').setVisible(false);
            }

            //insert
            if(thisObj.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-labelInfoInsert').update('<h5 style="margin:8px 0 0 15px;padding:0px;">('+lang('Add New Farmer Group')+')</h5>');

                // Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Chairman').setDisabled(true);
                // Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Secretary').setDisabled(true);
                // Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Treasurer').setDisabled(true);
                // Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HaveManagement1').setDisabled(true);
                // Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HaveManagement0').setDisabled(true);
                // Ext.get('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HaveManagementLabel').setStyle('opacity',0.3);

                thisObj.objPanelFarmerGroupMember.setVisible(false);
            }

            //view || update
            if(thisObj.opsiDisplay == 'view' || thisObj.opsiDisplay == 'update'){
                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-labelInfoInsert').update('');
                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HaveManagement0').setValue(true);

                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Province').setReadOnly(true);
                Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-District').setReadOnly(true);

                if(thisObj.opsiDisplay == 'view'){
                    Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-btnSaveForm').setVisible(false);
                }

                //load combo farmer group dl
                var cmb_farmer_group_member = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.CmbFarmerGroupMember');
                cmb_farmer_group_member.setStoreVar({FarmerGroupID:thisObj.viewVar.FarmerGroupID});
                cmb_farmer_group_member.load({
                    callback: function(records, operation, success){
                        if (success == true) {
                            //load data form
                            Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData').getForm().load({
                                url: m_api + '/farmer_group/farmer_group_basic_data_form',
                                method: 'GET',
                                params: {
                                    FarmerGroupID: thisObj.viewVar.FarmerGroupID
                                },
                                success: function(form, action) {
                                    var r = Ext.decode(action.response.responseText);

                                    //untuk handle combo bertingkat
                                    var cmb_province = Ext.data.StoreManager.lookup('store.Grower.CmbProvince');
                                    var cmb_district = Ext.data.StoreManager.lookup('store.Grower.CmbDistrict');
                                    var cmb_subdistrict = Ext.data.StoreManager.lookup('store.Grower.CmbSubdistrict');
                                    var cmb_village = Ext.data.StoreManager.lookup('store.Grower.CmbVillage');
                                    cmb_province.load({
                                        callback: function(records, operation, success){
                                            Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Province').setValue(r.data.ProvinceID);
                                            if (success == true) {
                                                cmb_district.load({
                                                    params: {
                                                        ProvinceID: r.data.ProvinceID
                                                    },
                                                    callback: function(records, operation, success){
                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-District').setValue(r.data.DistrictID);
                                                            cmb_subdistrict.load({
                                                                params: {
                                                                    DistrictID: r.data.DistrictID
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Subdistrict').setValue(r.data.SubDistrictID);
                                                                        cmb_village.load({
                                                                            params: {
                                                                                SubdistrictID: r.data.SubDistrictID
                                                                            },
                                                                            callback: function(records, operation, success){
                                                                                if (success == true) {
                                                                                    Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Village').setValue(r.data.VillageID);
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

                                    //panel Farmer Group Member
                                    thisObj.objPanelFarmerGroupMember.setViewVar({
                                        FarmerGroupID:thisObj.viewVar.FarmerGroupID
                                    });
                                    var grid_farmer_group_member = Ext.data.StoreManager.lookup('Koltiva.store.FarmerGroup.FarmerGroupMemberPanelGrid');
                                    grid_farmer_group_member.setStoreVar({FarmerGroupID:thisObj.viewVar.FarmerGroupID});
                                    grid_farmer_group_member.load();
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
                });
            }
        }
    }
});