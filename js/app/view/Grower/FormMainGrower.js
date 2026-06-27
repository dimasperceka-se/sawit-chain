/*
* @Author: nikolius
* @Date:   2017-05-18 11:32:59
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-29 15:25:17
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. formVar
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

    Ext.define('otherLandGridModel.Model', {
        extend: 'Ext.data.Model',
        fields: ['MemOtherID', 'MemberID','Commodity','GardenHa','Remark']
    });
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Grower.FormMainGrower' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Grower.FormMainGrower',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    renderTo: 'ext-content',
    AddValidation: null,
    MsgAddValidation: null,
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai (begin)
        var cmb_marital_status = Ext.create('Koltiva.store.Grower.CmbAdvancedFilterMaritalStatus');
        var cmb_education = Ext.create('Koltiva.store.Grower.CmbEducation');
        var cmb_farmer_category = Ext.create('Koltiva.store.Grower.CmbFarmerCategory');
        var cmb_membership_status = Ext.create('Koltiva.store.Grower.CmbMembershipStatus');
        var cmb_farmer_group_wags = Ext.create('Koltiva.store.Grower.CmbFarmerGroupWags');

        var cmb_total_production_area = Ext.create('Ext.data.Store',{
            fields: ['id', 'label'],
                data: [{
                    "id": "1",
                    "label": lang('Small (< 40 ha)')
                },{
                    "id": "2",
                    "label": lang('Medium (41-500 ha)')
                },{
                    "id": "3",
                    "label": lang('Large (> 500 ha)')
                }]
        });

        var cmb_supplybase = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('Farmer Ordinary'), "id":'farmer'},
                {"label":lang('Plasma'), "id":'plasma'},
                {"label":lang('Direct Smallholder'), "id":'direct'}
                //...
            ]
        });

        var cmb_certified_opt = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('Yes'), "id":'1'},
                {"label":lang('No'), "id":'2'},
                {"label":lang('I Do not Know About Sustainable Palm Oil Certification'), "id":'3'}
                //...
            ]
        });

        var cmb_survey_nr = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('0 - Baseline'), "id":'0'},
                {"label":lang('20 - Certification'), "id":'20'}
                //...
            ]
        });

        cmb_certified_opt.load();

        var cmb_certified = Ext.create('Koltiva.store.Grower.CmbCertified');
        cmb_certified.load();

        var cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        cmb_province.load();

        var cmb_dealer_assign = Ext.create('Koltiva.store.Grower.CmbDealerAssign');
        cmb_dealer_assign.load();

        var cmb_district = Ext.create('Koltiva.store.Grower.CmbDistrict');
        var cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        var cmb_village = Ext.create('Koltiva.store.Grower.CmbVillage');

        var cmb_bank = Ext.create('Koltiva.store.Grower.CmbBank');

        var cmb_inactive_reason = Ext.create('Koltiva.store.Grower.CmbInactiveReason');
        var cmb_not_join_reason = Ext.create('Koltiva.store.Grower.CmbNotJoinReason');
        var cmb_stopped_reason = Ext.create('Koltiva.store.Grower.CmbStoppedReason');
        var cmb_relation_to_owner = Ext.create('Koltiva.store.Grower.CmbRelationToOwner');
        var cmb_children_still_school = Ext.create('Koltiva.store.Grower.CmbChildrenInSchool');

        var cmb_farmer_group = Ext.create('Koltiva.store.ComboGeneral.CmbFarmerGroup');
        var cmb_handphone_type = Ext.create('Koltiva.store.ComboGeneral.CmbHandphoneType');
        var cmb_internal_program = Ext.create('Koltiva.store.SME.CmbInternalProgram');
        var cmb_external_program = Ext.create('Koltiva.store.SME.CmbExternalProgram');
        
        thisObj.ObjPanelTrainingCoachingGrid = Ext.create('Koltiva.view.Grower.PanelTrainingCoachingGrid', {
            viewVar: {
                MemberID: thisObj.formVar.MemberID
            }
        });
        //store yg dipakai (end)

        //panel Form Family ======================================================================== (begin)
        var storeGridFamilyLabour = Ext.create('Koltiva.store.Grower.GridMemberFamilyLabour');

        var contextMenuGridFamLab = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridFamilyLabour').getSelectionModel().getSelection()[0];

                    var winFormFamLab = Ext.create('Koltiva.view.Grower.WinFormFamLab');
                    winFormFamLab.setFormVar({MemberID:thisObj.formVar.MemberID,FamLabID:sm.get('FamLabID'),opsiDisplay:'view'});
                    if (!winFormFamLab.isVisible()) {
                        winFormFamLab.center();
                        winFormFamLab.show();
                    } else {
                        winFormFamLab.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridFamilyLabour').getSelectionModel().getSelection()[0];

                    var winFormFamLab = Ext.create('Koltiva.view.Grower.WinFormFamLab');
                    winFormFamLab.setFormVar({MemberID:thisObj.formVar.MemberID,FamLabID:sm.get('FamLabID'),opsiDisplay:'update'});
                    if (!winFormFamLab.isVisible()) {
                        winFormFamLab.center();
                        winFormFamLab.show();
                    } else {
                        winFormFamLab.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridFamilyLabour').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/grower/family_labour',
                                method: 'DELETE',
                                params: {
                                    FamLabID: sm.get('FamLabID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store FamLab
                                    Ext.data.StoreManager.lookup('store.Grower.GridMemberFamilyLabour').load();
                                },
                                failure: function(response, opts) {
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
                        }
                    });
                }
            }]
        });

        var objPanelFamily = Ext.create('Ext.grid.Panel',{
            id: 'Koltiva.view.Grower.FormMainGrower-gridFamilyLabour',
            loadMask: true,
            selType: 'rowmodel',
            minHeight: 320,
            store: storeGridFamilyLabour,
            cls:'Sfr_GridNew',
            style:'border:1px solid #CCC;',
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'toolbar',
                baseCls: 'bgToolbarTitlePanel',
                dock: 'top',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', 
                    cls:'Sfr_BtnGridGreen', 
                    overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    id: 'Koltiva.view.Grower.FormMainGrower-gridFamilyLabour-BtnAdd',
                    hidden: m_act_add,
                    handler: function() {
                        var winFormFamLab = Ext.create('Koltiva.view.Grower.WinFormFamLab');
                        winFormFamLab.setFormVar({MemberID:thisObj.formVar.MemberID,opsiDisplay:'insert'});
                        if (!winFormFamLab.isVisible()) {
                            winFormFamLab.center();
                            winFormFamLab.show();
                        } else {
                            winFormFamLab.close();
                        }
                    }
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                flex: 0.3,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridFamLab.showAt(e.getXY());
                    }
                }]
            },{
                text: 'No',
                xtype: 'rownumberer',
                flex: 1,
            },{
                text: lang('ID'),
                dataIndex: 'FamLabID',
                hidden:true
            },{
                text: lang('Name'),
                dataIndex: 'FamLabName',
                flex: 2,
            },{
                text: lang('Gender'),
                dataIndex: 'Gender',
                flex: 1,
            },{
                text: lang('Hubungan'),
                dataIndex: 'FamLabRelation',
                flex: 1,
            },{
                text: lang('Age'),
                dataIndex: 'Age',
                flex: 1,
            }]
        });
        //panel Form Family ======================================================================== (end)


        //Panel Form Labour ======================================================================== (begin)
        var storeGridLabour = Ext.create('Koltiva.store.Grower.GridMemberLabour');

        var contextMenuGridLabour = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridLabour').getSelectionModel().getSelection()[0];

                    var winFormLabour = Ext.create('Koltiva.view.Grower.WinFormLabour');
                    winFormLabour.setFormVar({MemberID:thisObj.formVar.MemberID,LaboID:sm.get('LaboID'),opsiDisplay:'view'});
                    if (!winFormLabour.isVisible()) {
                        winFormLabour.center();
                        winFormLabour.show();
                    } else {
                        winFormLabour.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridLabour').getSelectionModel().getSelection()[0];

                    var winFormLabour = Ext.create('Koltiva.view.Grower.WinFormLabour');
                    winFormLabour.setFormVar({MemberID:thisObj.formVar.MemberID,LaboID:sm.get('LaboID'),opsiDisplay:'update'});
                    if (!winFormLabour.isVisible()) {
                        winFormLabour.center();
                        winFormLabour.show();
                    } else {
                        winFormLabour.close();
                    }

                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridLabour').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/grower/labour',
                                method: 'DELETE',
                                params: {
                                    LaboID: sm.get('LaboID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store FamLab
                                    Ext.data.StoreManager.lookup('store.Grower.GridMemberLabour').load();
                                },
                                failure: function(response, opts) {
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
                        }
                    });
                }
            }]
        });

        var objPanelLabour = Ext.create('Ext.panel.Panel',{
            title: lang('List of Farmer\'s Labour'),
            frame: false,
            collapsible:false,
            margin:'0 0 40 0',
            id: 'Koltiva.view.Grower.FormMainGrower-PanelLabour',
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'view.Grower.GridMainGrower-gridToolbar',
                store: storeGridLabour,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                baseCls: 'bgToolbarTitlePanel',
                dock: 'top',
                items:[{
                    xtype: 'tbtext',
                    style:'font-weight:bold;text-decoration:underline;line-height:25px;',
                    text: lang('List of Farmer\'s Labour')
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    id:'Koltiva.view.Grower.FormMainGrower-gridLabour-BtnAdd',
                    hidden: m_act_add,
                    handler: function() {
                        var winFormLabour = Ext.create('Koltiva.view.Grower.WinFormLabour');
                        winFormLabour.setFormVar({MemberID:thisObj.formVar.MemberID,opsiDisplay:'insert'});
                        if (!winFormLabour.isVisible()) {
                            winFormLabour.center();
                            winFormLabour.show();
                        } else {
                            winFormLabour.close();
                        }
                    }
                }]
            }],
            items: [{
                xtype: 'grid',
                id: 'Koltiva.view.Grower.FormMainGrower-gridLabour',
                loadMask: true,
                selType: 'rowmodel',
                store: storeGridLabour,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                minHeight:125,
                columns: [{
                    text: lang('Action'),
                    xtype:'actioncolumn',
                    flex: 0.5,
                    items:[{
                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                        tooltip: 'Action',
                        handler: function(grid, rowIndex, colIndex, item, e, record) {
                            contextMenuGridLabour.showAt(e.getXY());
                        }
                    }]
                },{
                    text: 'No',
                    xtype: 'rownumberer',
                    flex: 0.5,
                },{
                    text: lang('ID'),
                    dataIndex: 'LaboID',
                    hidden:true
                },{
                    text: lang('Name'),
                    dataIndex: 'LaboName',
                    flex: 1,
                },{
                    text: lang('Gender'),
                    dataIndex: 'Gender',
                    flex: 1,
                },{
                    text: lang('Age'),
                    dataIndex: 'Age',
                    flex: 1,
                },{
                    text: lang('Wage Amount'),
                    dataIndex: 'WageAmount',
                    flex: 1,
                },{
                    text: lang('Wage Period'),
                    dataIndex: 'WagePeriod',
                    flex: 1,
                }]
            }]
        });

        var objFormPanelLabour = Ext.create('Ext.form.Panel',{
            id: 'Koltiva.view.Grower.FormMainGrower-FormLabourExtension',
            fileUpload: true,
            margin:'0 0 10 0',
            items: [{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 0.495,
                    layout:'form',
                    items:[{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: 0.9,
                                layout: 'form',
                                style: 'padding:10px 0px 0px 0px;',
                                defaults: {
                                    labelAlign: 'top'
                                },
                                items:[{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-SurveyNr',
                                    name: 'Koltiva.view.Grower.FormLabourExtension-SurveyNr',
                                    store: cmb_survey_nr,
                                    hidden:true,
                                    fieldLabel: lang('Survey Nr'),
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    readOnly: true,
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                        }
                                    }
                                },{
                                    fieldLabel: lang('Do you have workers'),
                                    labelWidth: 230,
                                    xtype: 'radiogroup',
                                    columns: 2,
                                    labelAlign:'top',
                                    allowBlank: false,
                                    msgTarget: 'side',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-RowlabHaveWorkers',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labHaveWorkers',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labHaveWorkers1',
                                        listeners:{
                                            change: function(){
                                                let getValueSurveyNr = Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-SurveyNr').getValue()

                                                if(this.checked == true){
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHowManyWorker').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerLivePlantation').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerKeepIdentity').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerAccessibleDocument').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerRecruitmentFee').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerWrittenContract').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerUnderstandRight').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerDeductionWage').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerFamilyWage').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerComplaintSystem').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerComplaintStored').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerOweMoney').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerBasicSupplies').setDisabled(false);
                                                    
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerUseApd').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerHadAccident').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabGiveInfoHealthSafety').setDisabled(false);
                    
                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridLabour-BtnAdd').setVisible(true);
                                                }else{
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHowManyWorker').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerLivePlantation').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerKeepIdentity').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerAccessibleDocument').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerRecruitmentFee').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerWrittenContract').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerUnderstandRight').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerDeductionWage').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerFamilyWage').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerComplaintSystem').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerComplaintStored').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerOweMoney').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerBasicSupplies').setDisabled(true);
                    
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerUseApd').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerUseApd2').setValue(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerHadAccident').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerHadAccident2').setValue(true);
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabGiveInfoHealthSafety').setDisabled(true);
                    
                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                                                }

                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labHaveWorkers',
                                        inputValue: '2',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labHaveWorkers2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.1,
                                layout: 'form',
                                items:[{
                                    xtype: 'image',
                                    width: '18px',
                                    style: 'cursor:pointer;margin-left:5px;',
                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                    autoEl: {
                                        tag: 'label',
                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labHaveWorkers')+'</div>'
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 0.90,
                                layout:'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labHowManyWorker',
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labHowManyWorker',
                                    fieldLabel: lang('How many workers do you have'),
                                    labelAlign:'top',
                                    labelWidth: 230,
                                    disabled:true,
                                    allowNegative: false                        
                                }]
                            },{
                                columnWidth: 0.1,
                                layout: 'form',
                                items:[{
                                    xtype: 'image',
                                    width: '18px',
                                    style: 'cursor:pointer;margin-left:5px;',
                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                    autoEl: {
                                        tag: 'label',
                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labHowManyWorker')+'</div>'
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 0.90,
                                layout:'form',
                                items:[{
                                    fieldLabel: lang('Do the workers use PPE when working'),
                                    labelWidth: 230,
                                    labelAlign:'top',
                                    xtype: 'radiogroup',
                                    columns: 2,
                                    id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerUseApd',
                                    disabled:true,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerUseApd',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerUseApd1',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWhoBuyApd').setDisabled(false);
                                                }else{
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWhoBuyApd').setDisabled(true);
                                                }
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerUseApd',
                                        inputValue: '2',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerUseApd2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.1,
                                layout: 'form',
                                items:[{
                                    xtype: 'image',
                                    width: '18px',
                                    style: 'cursor:pointer;margin-left:5px;',
                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                    autoEl: {
                                        tag: 'label',
                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerUseApd')+'</div>'
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 0.90,
                                layout:'form',
                                items:[{
                                    fieldLabel: lang('Who buys the PPE'),
                                    labelWidth: 230,
                                    labelAlign:'top',
                                    xtype: 'radiogroup',
                                    columns: 1,
                                    id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWhoBuyApd',
                                    disabled:true,
                                    items:[{
                                        boxLabel: lang('I buy it for my workers'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWhoBuyApd',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWhoBuyApd1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Workers buy it themselves'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWhoBuyApd',
                                        inputValue: '2',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWhoBuyApd2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('N/A'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWhoBuyApd',
                                        inputValue: '3',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWhoBuyApd3',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.1,
                                layout: 'form',
                                items:[{
                                    xtype: 'image',
                                    width: '18px',
                                    style: 'cursor:pointer;margin-left:5px;',
                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                    autoEl: {
                                        tag: 'label',
                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWhoBuyApd')+'</div>'
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 0.90,
                                layout:'form',
                                items:[{
                                    fieldLabel: lang('Has anyone of your workers had an accident while working'),
                                    labelWidth: 230,
                                    labelAlign:'top',
                                    xtype: 'radiogroup',
                                    columns: 2,
                                    id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerHadAccident',
                                    disabled:true,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHadAccident',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHadAccident1',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWhatAccident').setDisabled(false);
                                                }else{
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWhatAccident').setDisabled(true);
                                                }
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHadAccident',
                                        inputValue: '2',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHadAccident2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.1,
                                layout: 'form',
                                items:[{
                                    xtype: 'image',
                                    width: '18px',
                                    style: 'cursor:pointer;margin-left:5px;',
                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                    autoEl: {
                                        tag: 'label',
                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerHadAccident')+'</div>'
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            id:'Koltiva.view.Grower.FormLabourExtension-labWhatAccident',
                            disabled:true,
                            items:[{
                                columnWidth: 0.90,
                                layout:'form',
                                items:[{
                                    columnWidth: 1,
                                    layout:'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('What Kind of Accident')
                                    }]
                                },{
                                    columnWidth: 1,
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items:[{
                                        boxLabel: lang('Cutting from knife/harvest tools'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentKnife',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentKnife',
                                        listeners:{
                                        }
                                    }]
                                },{
                                    columnWidth: 1,
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items:[{
                                        boxLabel: lang('Hit by a fruit'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentHitbyFruit',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentHitbyFruit',
                                        listeners:{
                                        }
                                    }]
                                },{
                                    columnWidth: 1,
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items:[{
                                        boxLabel: lang('Contamination from chemical liquid from pesticide/fertilizer/herbicide'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentContimination',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentContimination',
                                        listeners:{
                                        }
                                    }]
                                },{
                                    columnWidth: 1,
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items:[{
                                        boxLabel: lang('Other'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentOther',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentOther',
                                        listeners:{
                                            change:function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentOtherText').setVisible(true);
                                                }else{
                                                    Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentOtherText').setVisible(false);
                                                }
                                            }
                                        }
                                    }]
                                },{
                                    columnWidth:1,
                                    border:false,
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentOtherText',
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentOtherText',
                                        hidden: true
                                    }]
                                }]
                            },{
                                columnWidth: 0.1,
                                layout: 'form',
                                items:[{
                                    xtype: 'image',
                                    width: '18px',
                                    style: 'cursor:pointer;margin-left:5px;',
                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                    autoEl: {
                                        tag: 'label',
                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWhatAccident')+'</div>'
                                    }
                                }]
                            }]
                        },{
                            html:'<div></div>'
                        },{
                            layout: 'column',
                            border: false,
                            id:'Koltiva.view.Grower.FormLabourExtension-labHaveBPJS',
                            items:[{
                                columnWidth: 0.90,
                                layout:'form',
                                items:[{
                                    columnWidth: 1,
                                    layout:'form',
                                    items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('Do You Worker Have BPJS')
                                    }]
                                },{
                                    columnWidth: 1,
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items:[{
                                        boxLabel: lang('BPJS Kesehatan'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBpjs',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBpjs',
                                        listeners:{
                                        }
                                    }]
                                },{
                                    columnWidth: 1,
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items:[{
                                        boxLabel: lang('BPJS Ketenagakerjaan'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBPJSKetenagakerjaan',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBPJSKetenagakerjaan',
                                        listeners:{
                                        }
                                    }]
                                },{
                                    columnWidth: 1,
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items:[{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBPJSNo',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBPJSNo',
                                        listeners:{
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.1,
                                layout: 'form',
                                items:[{
                                    xtype: 'image',
                                    width: '18px',
                                    style: 'cursor:pointer;margin-left:5px;',
                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                    autoEl: {
                                        tag: 'label',
                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labHaveBPJS')+'</div>'
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 0.90,
                                layout:'form',
                                items:[{
                                    fieldLabel: lang('Who pays the BPJS'),
                                    labelWidth: 230,
                                    labelAlign:'top',
                                    xtype: 'radiogroup',
                                    columns: 1,
                                    id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWhoPayBpjs',
                                    items:[{
                                        boxLabel: lang('I pay it for my workers'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWhoPayBpjs',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWhoPayBpjs1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Workers pay it themselves'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWhoPayBpjs',
                                        inputValue: '2',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWhoPayBpjs2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('N/A'),
                                        name: 'Koltiva.view.Grower.FormLabourExtension-labWhoPayBpjs',
                                        inputValue: '3',
                                        id: 'Koltiva.view.Grower.FormLabourExtension-labWhoPayBpjs3',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.1,
                                layout: 'form',
                                items:[{
                                    xtype: 'image',
                                    width: '18px',
                                    style: 'cursor:pointer;margin-left:5px;',
                                    src: varjs.config.base_url + 'images/icons/silk/information.png',
                                    autoEl: {
                                        tag: 'label',
                                        'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWhoPayBpjs')+'</div>'
                                    }
                                }]
                            }]
                        },{
                            xtype: 'textfield',
                            id: 'Koltiva.view.Grower.FormLabourExtension-Enumerator',
                            name: 'Koltiva.view.Grower.FormLabourExtension-Enumerator',
                            fieldLabel: lang('Enumerator'),
                            labelAlign:'top',
                            readOnly: true
                        },{
                            xtype: 'textfield',
                            id: 'Koltiva.view.Grower.FormLabourExtension-DateCreated',
                            name: 'Koltiva.view.Grower.FormLabourExtension-DateCreated',
                            fieldLabel: lang('Created Date'),
                            labelAlign:'top',
                            readOnly: true
                        },{
                            xtype: 'textfield',
                            id: 'Koltiva.view.Grower.FormLabourExtension-ModifiedBy',
                            name: 'Koltiva.view.Grower.FormLabourExtension-ModifiedBy',
                            fieldLabel: lang('Modified by'),
                            labelAlign:'top',
                            readOnly: true
                        },{
                            xtype: 'textfield',
                            id: 'Koltiva.view.Grower.FormLabourExtension-DateUpdated',
                            name: 'Koltiva.view.Grower.FormLabourExtension-DateUpdated',
                            fieldLabel: lang('Updated Date'),
                            labelAlign:'top',
                            readOnly: true
                        }]
                },{
                    columnWidth: 0.5,
                    layout:'form',
                    style:'margin-left:15px',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Do You Workers Live on Your Plantation'),
                                labelAlign:'top',
                                labelWidth: 230,
                                xtype: 'radiogroup',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerLivePlantation',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerLivePlantation',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerLivePlantation1',
                                    listeners:{
                                        change: function(){
                                            if(this.checked == true){
                                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerSafeHouse').setDisabled(false);
                                            }else{
                                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerSafeHouse').setDisabled(true);
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerLivePlantation',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerLivePlantation2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerLivePlantation')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Do Workers Have Safe and Adequate Housing, Including Toilets and Drinking Water'),
                                labelWidth: 230,
                                xtype: 'radiogroup',
                                labelAlign:'top',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerSafeHouse',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerSafeHouse',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerSafeHouse1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerSafeHouse',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerSafeHouse2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerSafeHouse')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Do you keep the identity documents, e.g. passport from the workers on your plantation'),
                                labelWidth: 230,
                                xtype: 'radiogroup',
                                labelAlign:'top',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerKeepIdentity',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerKeepIdentity',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerKeepIdentity1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerKeepIdentity',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerKeepIdentity2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerKeepIdentity')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Are the identity documents 24h accessible by the workers'),
                                labelWidth: 230,
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerAccessibleDocument',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccessibleDocument',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccessibleDocument1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccessibleDocument',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerAccessibleDocument2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerAccessibleDocument')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Did workers had to pay a recruitment fee to work on your plantation'),
                                labelWidth: 230,
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerRecruitmentFee',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerRecruitmentFee',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerRecruitmentFee1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerRecruitmentFee',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerRecruitmentFee2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerRecruitmentFee')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Does your worker have a written contract/ work agreement'),
                                labelWidth: 230,
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerWrittenContract',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerWrittenContract',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerWrittenContract1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerWrittenContract',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerWrittenContract2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerWrittenContract')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Do workers understand their rights & obligations in accordance with the work agreement/contract'),
                                labelWidth: 230,
                                labelAlign:'top',
                                disabled: true,
                                xtype: 'radiogroup',
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerUnderstandRight',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerUnderstandRight',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerUnderstandRight1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerUnderstandRight',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerUnderstandRight2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerUnderstandRight')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Are there any deductions of the wage if workers make mistakes while working'),
                                labelWidth: 230,
                                labelAlign:'top',
                                disabled: true,
                                xtype: 'radiogroup',
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerDeductionWage',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerDeductionWage',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerDeductionWage1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerDeductionWage',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerDeductionWage2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerDeductionWage')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Does your worker employ their family members/ relatives whose wages are paid by the workers themselves'),
                                labelWidth: 230,
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerFamilyWage',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerFamilyWage',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerFamilyWage1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerFamilyWage',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerFamilyWage2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerFamilyWage')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Is there a complaint system in place, where workers can file complaints'),
                                labelWidth: 230,
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerComplaintSystem',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintSystem',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintSystem1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintSystem',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintSystem2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerComplaintSystem')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Are complaints stored for 2 years'),
                                labelWidth: 230,
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerComplaintStored',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintStored',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintStored1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintStored',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintStored2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerComplaintStored')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Do workers owe money to you'),
                                labelWidth: 230,
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerOweMoney',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerOweMoney',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerOweMoney1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerOweMoney',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerOweMoney2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerOweMoney')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Do workers have access to basic supplies of first aid'),
                                labelWidth: 230,
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerBasicSupplies',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerBasicSupplies',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerBasicSupplies1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerBasicSupplies',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerBasicSupplies2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labWorkerBasicSupplies')+'</div>'
                                }
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.90,
                            layout:'form',
                            items:[{
                                fieldLabel: lang('Who gives your workers an explanation of Occupational Health and Safety (K3)'),
                                labelWidth: 230,
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                disabled: true,
                                columns: 2,
                                id: 'Koltiva.view.Grower.FormLabourExtension-RowlabGiveInfoHealthSafety',
                                items:[{
                                    boxLabel: lang('No explanation'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labGiveInfoHealthSafety',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labGiveInfoHealthSafety1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('I explain it to them'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labGiveInfoHealthSafety',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labGiveInfoHealthSafety2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('Third party'),
                                    name: 'Koltiva.view.Grower.FormLabourExtension-labGiveInfoHealthSafety',
                                    inputValue: '3',
                                    id: 'Koltiva.view.Grower.FormLabourExtension-labGiveInfoHealthSafety3',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.1,
                            layout: 'form',
                            items:[{
                                xtype: 'image',
                                width: '18px',
                                style: 'cursor:pointer;margin-left:5px;',
                                src: varjs.config.base_url + 'images/icons/silk/information.png',
                                autoEl: {
                                    tag: 'label',
                                    'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_labGiveInfoHealthSafety')+'</div>'
                                }
                            }]
                        }]
                    }]
                }],
                buttons: [{
                    text: lang('Save'),
                    id: 'Koltiva.view.Grower.FormLabourExtension-btnSave',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    cls: 'Sfr_BtnFormBlue',
                    overCls: 'Sfr_BtnFormBlue-Hover',
                    handler: function () {
                        if (objFormPanelLabour.isValid()) {
                            
                            objFormPanelLabour.submit({
                                url: m_api + '/grower/member_labour_extension',
                                method:'POST',
                                params: {MemberID: Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberID').getValue()},
                                waitMsg: 'Saving data...',
                                success: function(fp, o) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data saved'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
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
            }]
        });
        //Panel Form Labour ======================================================================== (end)

        //================================================== panel Form Basic Data (BEGIN) ==========================================//
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Basic Data'),
            frame: true,
            id: 'Koltiva.view.Grower.FormMainGrower-FormBasicData',
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
                        xtype: 'tabpanel',
                        flex: 1,
                        activeTab: 0,
                        plain: true,
                        cls:'tabSce',
                        id: 'Koltiva.view.Grower.FormMainGrower-FormBasicData-tab',
                        items:[{
                            xtype: 'panel',
                            title: lang('Farmer Data'),
                            id: 'Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerData',
                            items: [{
                                xtype: 'panel',
                                title: lang('Farmer Profile'),
                                frame: false,
                                id: 'Koltiva.view.Farmers.MainForm-FormBasicData-SectionFarmerProfile',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                    layout: 'column',
                                    border: false,
                                    items: [{
                                        columnWidth: 0.3,
                                        layout: 'form',
                                        style: 'padding:10px 0px 10px 5px;',
                                        items: [{
                                            xtype: 'image',
                                            id: 'Koltiva.view.Grower.FormMainGrower-MemberPhoto',
                                            height:'200px',
                                            src: m_api_base_url + '/assets/images/farmer-default.png'
                                        },{
                                            xtype: 'fileuploadfield',
                                            fieldLabel: lang('Photo'),
                                            labelAlign: 'top',
                                            id: 'Koltiva.view.Grower.FormMainGrower-MemberPhotoInput',
                                            name: 'Koltiva.view.Grower.FormMainGrower-MemberPhotoInput',
                                            buttonText: 'Browse',
                                            listeners: {
                                                'change': function (fb, v) {
                                                    objPanelBasicData.submit({
                                                        url: m_api + '/grower/image_member',
                                                        clientValidation: false,
                                                        params: {
                                                            opsiDisplay: thisObj.opsiDisplay,
                                                            MemberID: Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberID').getValue()
                                                        },
                                                        waitMsg: 'Sending Photo...',
                                                        success: function (fp, o) {
                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberPhoto').setSrc(o.result.file);
                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberPhotoOld').setValue(o.result.filepath);
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
                                                }
                                            }
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Grower.FormMainGrower-MemberPhotoOld',
                                            name: 'Koltiva.view.Grower.FormMainGrower-MemberPhotoOld',
                                            inputType: 'hidden'
                                        }]
                                    }, {
                                        columnWidth: 0.7,
                                        layout: 'form',
                                        style: 'padding:10px 5px 10px 20px;',
                                        defaults: {
                                            labelAlign: 'left',
                                            labelWidth: 150
                                        },
                                        items: [{
                                            xtype: 'hiddenfield',
                                            id: 'Koltiva.view.Grower.FormMainGrower-MemberID',
                                            name: 'Koltiva.view.Grower.FormMainGrower-MemberID'
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Grower.FormMainGrower-MemberDisplayID',
                                            name: 'Koltiva.view.Grower.FormMainGrower-MemberDisplayID',
                                            fieldLabel: lang('Farmer ID'),
                                            readOnly:true
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Grower.FormMainGrower-ExtID',
                                            name: 'Koltiva.view.Grower.FormMainGrower-ExtID',
                                            fieldLabel: lang('External ID'),
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Grower.FormMainGrower-Fullname',
                                            name: 'Koltiva.view.Grower.FormMainGrower-Fullname',
                                            fieldLabel: lang('Farmer Name'),
                                            allowBlank: false
                                        }, {
                                            fieldLabel: lang('Gender'),
                                            xtype: 'radiogroup',
                                            allowBlank: false,
                                            msgTarget: 'side',
                                            columns: 2,
                                            items:[{
                                                boxLabel: lang('Male'),
                                                name: 'Koltiva.view.Grower.FormMainGrower-Gender',
                                                inputValue: 'm',
                                                id: 'Koltiva.view.Grower.FormMainGrower-GenderMale',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Female'),
                                                name: 'Koltiva.view.Grower.FormMainGrower-Gender',
                                                inputValue: 'f',
                                                id: 'Koltiva.view.Grower.FormMainGrower-GenderFemale',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }, {
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Grower.FormMainGrower-DateCollection',
                                            name: 'Koltiva.view.Grower.FormMainGrower-DateCollection',
                                            fieldLabel: lang('Date Collection'),
                                            //labelWidth: 150,
                                            style: 'margin-bottom:15px;',
                                            allowBlank: false,
                                            format: 'Y-m-d H:i:s'
                                        }, {
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Grower.FormMainGrower-DateLastVerfication',
                                            name: 'Koltiva.view.Grower.FormMainGrower-DateLastVerfication',
                                            fieldLabel: lang('Date of Last Verification'),
                                            //labelWidth: 150,
                                            style: 'margin-bottom:15px;',
                                            allowBlank: false,
                                            format: 'Y-m-d'
                                        },{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Grower.FormMainGrower-SurveyNr',
                                            name: 'Koltiva.view.Grower.FormMainGrower-SurveyNr',
                                            store: cmb_survey_nr,
                                            hidden:true,
                                            fieldLabel: lang('Survey Nr'),
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    // if(nv == 0){
                                                    //     Ext.getCmp('Koltiva.view.Grower.MainForm-FormBasicData-SectionFarmerStatus').setDisabled(true);
                                                    //     Ext.getCmp('Koltiva.view.Grower.MainForm-FormBasicData-SectionJoinStatus').setDisabled(true);
                                                    // }else{
                                                    //     Ext.getCmp('Koltiva.view.Grower.MainForm-FormBasicData-SectionFarmerStatus').setDisabled(false);
                                                    //     Ext.getCmp('Koltiva.view.Grower.MainForm-FormBasicData-SectionJoinStatus').setDisabled(false);
                                                    // }
                                                }
                                            }
                                        }]
                                    }]
                                }]
                            }, {
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 0.5,
                                    layout: 'form',
                                    // style: 'padding:10px 5px 10px 20px;',
                                    defaults: {
                                        labelAlign: 'top'
                                    },
                                    items:[{
                                        xtype: 'panel',
                                        title: lang('General Data'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionGeneralDataLabour',
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
                                                    xtype: 'datefield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-DateOfBirth',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-DateOfBirth',
                                                    fieldLabel: lang('Date of Birth'),
                                                    //labelWidth: 150,
                                                    labelAlign: 'top',
                                                    //allowBlank: false, temporary disable for kristina
                                                    format: 'Y-m-d'
                                                }, {
                                                    html: '<div style="height:10px;">&nbsp;</div>'
                                                }, {
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-MaritalStatus',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-MaritalStatus',
                                                    store: cmb_marital_status,
                                                    fieldLabel: lang('Marital Status'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id'
                                                }, {
                                                    html: '<div style="height:10px;">&nbsp;</div>'
                                                }, {
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-Education',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-Education',
                                                    store: cmb_education,
                                                    fieldLabel: lang('Last Education'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id'
                                                }, {
                                                    html: '<div style="height:10px;">&nbsp;</div>'
                                                }, {
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-DealerAssign',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-DealerAssign',
                                                    store: cmb_dealer_assign,
                                                    fieldLabel: lang('Dealer Assign'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    hidden: m_act_dealer_assign,
                                                    valueField: 'id',
                                                    listeners: {
                                                        change: function(cb, nv, ov) {

                                                        }
                                                    }
                                                }, {
                                                    html: '<div style="height:10px;">&nbsp;</div>'
                                                }, {
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-Nin',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-Nin',
                                                    fieldLabel: lang('National Identification Number'),
                                                    //labelWidth: 180,
                                                    //allowBlank: false, temporary disable for kristina
                                                    labelAlign: 'top'
                                                }, {
                                                    html: '<div style="height:10px;">&nbsp;</div>'
                                                },{
                                                    xtype: 'image',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-KTPPhoto',
                                                    height:'200px',
                                                    src: m_api_base_url + '/assets/images/ktp-default.png'
                                                },{
                                                    xtype: 'fileuploadfield',
                                                    fieldLabel: lang('National Identification File'),
                                                    labelAlign: 'top',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-KTPPhotoInput',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-KTPPhotoInput',
                                                    buttonText: 'Browse',
                                                    listeners: {
                                                        'change': function (fb, v) {
                                                            objPanelBasicData.submit({
                                                                url: m_api + '/grower/image_KTP',
                                                                clientValidation: false,
                                                                params: {
                                                                    opsiDisplay: thisObj.opsiDisplay,
                                                                    MemberID: Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberID').getValue()
                                                                },
                                                                waitMsg: 'Sending Photo...',
                                                                success: function (fp, o) {
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-KTPPhoto').setSrc(o.result.file);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-KTPPhotoOld').setValue(o.result.filepath);
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
                                                        }
                                                    }
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-KTPPhotoOld',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-KTPPhotoOld',
                                                    inputType: 'hidden'
                                                }]
                                            }]
                                        }]
                                    },{
                                        xtype: 'panel',
                                        title: lang('Bank Information'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionBank',
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
                                                    fieldLabel: lang('Does your household have a bank account'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-HaveBankAccount',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-HaveBankAccount1',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('ReceiveTransferPanel').setDisabled(false);                                                                  
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-BankHolderName').setDisabled(false);                                                                  
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-BankAccNumber').setDisabled(false);  
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-BankID').setDisabled(false); 
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-BankClientID').setDisabled(false);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-BankBranchName').setDisabled(false);    
                                                                    Ext.getCmp('AccountHolderPanel').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('ReceiveTransferPanel').setDisabled(true);                                  
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-BankHolderName').setDisabled(true);                                                                  
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-BankAccNumber').setDisabled(true);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-BankID').setDisabled(true);  
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-BankClientID').setDisabled(true);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-BankBranchName').setDisabled(true);    
                                                                    Ext.getCmp('AccountHolderPanel').setDisabled(true);     
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-HaveBankAccount',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-HaveBankAccount2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    fieldLabel: lang('Do you want to receive your premium payments via bank transfer'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    disabled:true,
                                                    id:'ReceiveTransferPanel',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-ReceiveBankTransfer',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-ReceiveBankTransfer1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-ReceiveBankTransfer',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-ReceiveBankTransfer2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-BankHolderName',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-BankHolderName',
                                                    fieldLabel: lang('Bank Holder Name'),
                                                    disabled:true,
                                                    labelAlign: 'top',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-BankAccNumber',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-BankAccNumber',
                                                    fieldLabel: lang('Bank Account Number'),
                                                    disabled:true,
                                                    labelAlign: 'top',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-BankID',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-BankID',
                                                    store: cmb_bank,
                                                    disabled:true,
                                                    fieldLabel: lang('Bank Name'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners: {
                                                        change: function(cb, nv, ov) {
                                                            
                                                        }
                                                    }
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-BankClientID',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-BankClientID',
                                                    fieldLabel: lang('Bank Client ID'),
                                                    hidden:true,
                                                    labelAlign: 'top',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-BankBranchName',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-BankBranchName',
                                                    fieldLabel: lang('Bank Branch Name'),
                                                    disabled:true,
                                                    labelAlign: 'top',
                                                },{
                                                    fieldLabel: lang('Account Holder Relation to Farmer'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    disabled:true,
                                                    id:'AccountHolderPanel',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Registered Farmer'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-AccountHolderRelation',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-AccountHolderRelation1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('Spouse'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-AccountHolderRelation',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-AccountHolderRelation2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('Child'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-AccountHolderRelation',
                                                        inputValue: '3',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-AccountHolderRelation3',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('Other Household Member'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-AccountHolderRelation',
                                                        inputValue: '4',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-AccountHolderRelation4',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            }]
                                        }]
                                    },{
                                        xtype: 'panel',
                                        title: lang('Addresss and Location'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionAddLocation',
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
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-Province',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-Province',
                                                    store: cmb_province,
                                                    fieldLabel: lang('Province'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners: {
                                                        change: function(cb, nv, ov) {
                                                            cmb_district.load({
                                                                params: {
                                                                    ProvinceID: nv
                                                                }
                                                            });
                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-District').setValue('');
                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Subdistrict').setValue('');
                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Village').setValue('');
            
                                                            //load store
                                                            cmb_farmer_group.setStoreVar({ProvinceID:nv});
                                                            cmb_farmer_group.load();
                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FarmerGroupID').setValue('');
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-District',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-District',
                                                    store: cmb_district,
                                                    fieldLabel: lang('District'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners: {
                                                        change: function(cb, nv, ov) {
                                                            cmb_subdistrict.load({
                                                                params: {
                                                                    DistrictID: nv
                                                                }
                                                            });
                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Subdistrict').setValue('');
                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Village').setValue('');
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-Subdistrict',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-Subdistrict',
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
                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Village').setValue('');
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-Village',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-Village',
                                                    store: cmb_village,
                                                    fieldLabel: lang('Village'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    allowBlank: false
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'textarea',
                                                    fieldLabel: lang('Address'),
                                                    labelAlign:'top',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-Address',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-Address',
                                                    height: 65
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-RtRw',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-RtRw',
                                                    fieldLabel: lang('RT / RW'),
                                                    labelAlign:'top',
                                                    hidden: true
                                                }]
                                            }]
                                        }]
                                    },
                                    // {
                                    //     xtype: 'panel',
                                    //     title: lang('Certification'),
                                    //     frame: false,
                                    //     id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionCert',
                                    //     style: 'margin-top:15px;',
                                    //     cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                    //     items: [{
                                    //         layout: 'column',
                                    //         border: false,
                                    //         items: [{
                                    //             columnWidth: 1,
                                    //             layout: 'form',
                                    //             style: 'padding:10px 0px 0px 0px;',
                                    //             defaults: {
                                    //                 labelAlign: 'top'
                                    //             },
                                    //             items: [{
                                    //                 xtype: 'combobox',
                                    //                 id: 'Koltiva.view.Grower.FormMainGrower-isCertified',
                                    //                 name: 'Koltiva.view.Grower.FormMainGrower-isCertified',
                                    //                 store: cmb_certified_opt,
                                    //                 fieldLabel: lang('Do You Have Sustainable Palm Oil Certification'),
                                    //                 labelAlign:'top',
                                    //                 queryMode: 'local',
                                    //                 displayField: 'label',
                                    //                 valueField: 'id',
                                    //                 listeners: {
                                    //                     change: function(cb, nv, ov) {
                                    //                         if(nv == 1){
                                    //                             Ext.getCmp('CertificationPanel').setDisabled(false);
                                    //                         }else{
                                    //                             Ext.getCmp('CertificationPanel').setDisabled(true);
                                    //                         }
                                    //                     }
                                    //                 }
                                    //             },{
                                    //                 layout: 'column',
                                    //                 border: false,
                                    //                 items:[{
                                    //                     columnWidth: 1,
                                    //                     layout:'form',
                                    //                     items:[{
                                    //                         xtype:'label',
                                    //                         cls: 'x-form-item-label',
                                    //                         text: lang('Certification Standard')
                                    //                     }]
                                    //                 }]
                                    //             },{
                                    //                 layout: 'column',
                                    //                 border: false,
                                    //                 style:'margin-top:-20px;padding-top:0px;',
                                    //                 items:[{
                                    //                     layout:'column',
                                    //                     columnWidth: 1,
                                    //                     id:'CertificationPanel',
                                    //                     disabled:true,
                                    //                     style:'margin-top:-7px;padding-top:0px;',
                                    //                     items:[{
                                    //                         columnWidth: 0.25,
                                    //                         border: false,
                                    //                         defaultType: 'checkboxfield',
                                    //                         items:[{
                                    //                             boxLabel: lang('RSPO'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationRSPO',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationRSPO',
                                    //                             listeners:{
                                    //                             }
                                    //                         }]
                                    //                     },{
                                    //                         columnWidth: 0.25,
                                    //                         border: false,
                                    //                         defaultType: 'checkboxfield',
                                    //                         items:[{
                                    //                             boxLabel: lang('ISCC'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationISCC',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationISCC',
                                    //                             listeners:{
                                    //                             }
                                    //                         }]
                                    //                     },{
                                    //                         columnWidth: 0.25,
                                    //                         border: false,
                                    //                         defaultType: 'checkboxfield',
                                    //                         items:[{
                                    //                             boxLabel: lang('ISPO'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationISPO',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationISPO',
                                    //                             listeners:{
                                    //                             }
                                    //                         }]
                                    //                     },{
                                    //                         columnWidth: 0.25,
                                    //                         border: false,
                                    //                         defaultType: 'checkboxfield',
                                    //                         items:[{
                                    //                             boxLabel: lang('MSPO'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationMSPO',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationMSPO',
                                    //                             listeners:{
                                    //                             }
                                    //                         }]
                                    //                     }]
                                    //                 }]
                                    //             },{
                                    //                 fieldLabel: lang('Did You Receive Any Trainings to Improve Your Agriculture or Business Practices'),
                                    //                 labelAlign:'top',
                                    //                 xtype: 'radiogroup',
                                    //                 msgTarget: 'side',
                                    //                 columns: 2,
                                    //                 items:[{
                                    //                     boxLabel: lang('Yes'),
                                    //                     name: 'Koltiva.view.Grower.FormMainGrower-ReceiveTraining',
                                    //                     inputValue: '1',
                                    //                     id: 'Koltiva.view.Grower.FormMainGrower-ReceiveTraining1',
                                    //                     listeners:{
                                    //                         change: function(){
                                    //                             if(this.checked == true){
                                    //                                 Ext.getCmp('CertificationSourcePanel').setDisabled(false);
                                    //                                 Ext.getCmp('CertificationTypePanel').setDisabled(false);                                                                    
                                    //                             }else{
                                    //                                 Ext.getCmp('CertificationSourcePanel').setDisabled(true);
                                    //                                 Ext.getCmp('CertificationTypePanel').setDisabled(true);
                                    //                             }
                                    //                             return false;
                                    //                         }
                                    //                     }
                                    //                 },{
                                    //                     boxLabel: lang('No'),
                                    //                     name: 'Koltiva.view.Grower.FormMainGrower-ReceiveTraining',
                                    //                     inputValue: '2',
                                    //                     id: 'Koltiva.view.Grower.FormMainGrower-ReceiveTraining2',
                                    //                     listeners:{
                                    //                         change: function(){
                                    //                             if(this.checked == true){
                                    //                                 Ext.getCmp('CertificationSourcePanel').setDisabled(true);
                                    //                                 Ext.getCmp('CertificationTypePanel').setDisabled(true);
                                    //                             }else{
                                    //                                 Ext.getCmp('CertificationSourcePanel').setDisabled(false);
                                    //                                 Ext.getCmp('CertificationTypePanel').setDisabled(false);
                                    //                             }
                                    //                             return false;
                                    //                         }
                                    //                     }
                                    //                 }]
                                    //             },{
                                    //                 layout: 'column',
                                    //                 border: false,
                                    //                 items:[{
                                    //                     columnWidth: 1,
                                    //                     layout:'form',
                                    //                     items:[{
                                    //                         xtype:'label',
                                    //                         cls: 'x-form-item-label',
                                    //                         text: lang('From Whom Did You Received Training')
                                    //                     }]
                                    //                 }]
                                    //             },{
                                    //                 layout: 'column',
                                    //                 border: false,
                                    //                 style:'margin-top:-20px;padding-top:0px;',
                                    //                 items:[{
                                    //                     layout:'column',
                                    //                     columnWidth: 1,
                                    //                     id:'CertificationSourcePanel',
                                    //                     disabled:true,
                                    //                     style:'margin-top:-7px;padding-top:0px;',
                                    //                     items:[{
                                    //                         columnWidth: 1,
                                    //                         border: false,
                                    //                         defaultType: 'checkboxfield',
                                    //                         items:[{
                                    //                             boxLabel: lang('Government Extention Officer'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceGovernment',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceGovernment',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('NGO'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceNGO',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceNGO',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Mill'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceMill',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceMill',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Other Private Sector Organization'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationSourcePrivateOrg',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationSourcePrivateOrg',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Others'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceOthers',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceOthers',
                                    //                             listeners:{
                                    //                             }
                                    //                         }]
                                    //                     }]
                                    //                 }]
                                    //             },{
                                    //                 layout: 'column',
                                    //                 border: false,
                                    //                 items:[{
                                    //                     columnWidth: 1,
                                    //                     layout:'form',
                                    //                     items:[{
                                    //                         xtype:'label',
                                    //                         cls: 'x-form-item-label',
                                    //                         text: lang('What Type of Trainings Did You Receive')
                                    //                     }]
                                    //                 }]
                                    //             },{
                                    //                 layout: 'column',
                                    //                 border: false,
                                    //                 style:'margin-top:-20px;padding-top:0px;',
                                    //                 items:[{
                                    //                     layout:'column',
                                    //                     columnWidth: 1,
                                    //                     id:'CertificationTypePanel',
                                    //                     disabled:true,
                                    //                     style:'margin-top:-7px;padding-top:0px;',
                                    //                     items:[{
                                    //                         columnWidth: 1,
                                    //                         border: false,
                                    //                         defaultType: 'checkboxfield',
                                    //                         items:[{
                                    //                             boxLabel: lang('Financial and Farm Business Operations'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeFinancial',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeFinancial',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Good Agriculuture Practice'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeGoodAgriculture',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeGoodAgriculture',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Human Rights and Worker Rights'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeHumanRights',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeHumanRights',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Best Management of Pesticides'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeManagementPesticides',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeManagementPesticides',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Fire Fighting'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeFireFighting',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeFireFighting',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('HCV and HCS'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeHCVHCS',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeHCVHCS',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('P&C RSPO Independent Smallholder Standard'),
                                    //                             name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeRSPOIndependent',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeRSPOIndependent',
                                    //                             listeners:{
                                    //                             }
                                    //                         }]
                                    //                     }]
                                    //                 }]
                                    //             }]
                                    //         }]
                                    //     }]
                                    // },
                                    {
                                        xtype: 'panel',
                                        title: lang('Farmer Status'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionFarmerStatus',
                                        style: 'margin-top:15px;',
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
                                                    fieldLabel: lang('Farmer Status'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Active'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-StatusMember',
                                                        inputValue: 'Active',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-StatusMemberActive',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-InactiveReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-InactiveReason').setValue('');
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReason').setReadOnly(true);                                                                    
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReason').setValue('');                                                                    
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-InactiveReason').setReadOnly(false);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReason').setReadOnly(false);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('Inactive'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-StatusMember',
                                                        inputValue: 'Inactive',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-StatusMemberInactive',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-InactiveReason').setReadOnly(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-InactiveReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-InactiveReason').setValue('');
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-InactiveReason',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-InactiveReason',
                                                    store: cmb_inactive_reason,
                                                    fieldLabel: lang('Inactive Reason'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners:{
                                                        change:function(cb, nv, ov){
                                                            if(nv == 5){
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-InactiveReasonText').setVisible(true);
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReason').setReadOnly(true);
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReason').setValue('');
                                                            }else{
                                                                if(nv != 3){
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReason').setValue('');
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReason').setReadOnly(false);
                                                                }
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-InactiveReasonText').setVisible(false);
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-InactiveReasonText',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-InactiveReasonText',
                                                    fieldLabel: lang('Other Inactive Reason'),
                                                    labelAlign:'top',
                                                    hidden:true
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-StoppedReason',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-StoppedReason',
                                                    store: cmb_stopped_reason,
                                                    fieldLabel: lang('Stopped Farming Reason'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners:{
                                                        change:function(cb, nv, ov){
                                                            if(nv == 6){
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReasonText').setVisible(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReasonText').setVisible(false);
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-StoppedReasonText').setValue('');
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-StoppedReasonText',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-StoppedReasonText',
                                                    fieldLabel: lang('Other Stopped Farming Reason'),
                                                    labelAlign:'top',
                                                    hidden:true
                                                }]
                                            }]
                                        }]
                                    },
                                    {
                                        xtype: 'panel',
                                        title: lang('Program Status'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionJoinStatus',
                                        style: 'margin-top:15px;',
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
                                                    fieldLabel: lang('Does The Farmer Join The Program'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-JoinProgram',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-JoinProgram1',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-NotJoinProgramReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerCertification').setDisabled(false);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-NotJoinProgramReason').setValue('');                                                                 
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-NotJoinProgramReason').setReadOnly(false);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerCertification').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-JoinProgram',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-JoinProgram2',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-NotJoinProgramReason').setReadOnly(false);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerCertification').setDisabled(true);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-NotJoinProgramReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerCertification').setDisabled(false);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-NotJoinProgramReason').setValue('');
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-NotJoinProgramReason',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-NotJoinProgramReason',
                                                    store: cmb_not_join_reason,
                                                    fieldLabel: lang('Not Join Reason'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners:{
                                                        change:function(cb, nv, ov){
                                                            if(nv == 4){
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-NotJoinProgramReasonText').setVisible(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-NotJoinProgramReasonText').setVisible(false);
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-NotJoinProgramReasonText').setValue('');
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-NotJoinProgramReasonText',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-NotJoinProgramReasonText',
                                                    fieldLabel: lang('Other Not Join Reason'),
                                                    labelWidth: 230,
                                                    labelAlign:'top',
                                                    hidden:true
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textarea',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-JoinComment',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-JoinComment',
                                                    fieldLabel: lang('Comment'),
                                                    labelAlign:'top'
                                                }]
                                            }]
                                        }]
                                    }]
                                },{
                                    columnWidth: 0.49,
                                    layout: 'form',
                                    style: 'padding:8px 5px 10px 20px;',
                                    defaults: {
                                        labelAlign: 'top'
                                    },
                                    items:[{
                                        xtype: 'panel',
                                        title: lang('Communication'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionCom',
                                        style: 'margin-top:15px;',
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
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-HandphoneType',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-HandphoneType',
                                                    store: cmb_handphone_type,
                                                    fieldLabel: lang('Handphone Type'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners: {
                                                        change: function(cb, nv, ov) {
                                                            if(nv == '3'){
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Handphone').setValue('');
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Handphone').setReadOnly(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Handphone').setReadOnly(false);
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    html:lang('Handphone')
                                                },{
                                                    columnWidth: 1,
                                                    border: false,
                                                    layout: 'column',
                                                    style:'margin-bottom:3px;',
                                                    items:[{
                                                        xtype: 'textfield',
                                                        allowBlank: false,
                                                        style:'margin-right:5px;',
                                                        width:120,
                                                        readOnly:true,
                                                        id: 'Koltiva.view.Grower.FormMainGrower-HandphoneCode',
                                                        name: 'Koltiva.view.Grower.FormMainGrower-HandphoneCode',
                                                        value: '+62'
                                                    },{
                                                        xtype: 'textfield',
                                                        style:'margin-top:3px;',
                                                        width:200,
                                                        id: 'Koltiva.view.Grower.FormMainGrower-Handphone',
                                                        name: 'Koltiva.view.Grower.FormMainGrower-Handphone'
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    fieldLabel: lang('Access to Smartphone'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-AccessToSmartphone',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-AccessToSmartphone1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-AccessToSmartphone',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-AccessToSmartphone2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                }]
                                            }]
                                        }]
                                    },{
                                        xtype: 'panel',
                                        title: lang('Farmer Group'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionFarmerGroup',
                                        style: 'margin-top:15px;',
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
                                                    fieldLabel: lang('Is a member of farmer group'),
                                                    xtype: 'radiogroup',
                                                    columns: 2,
                                                    labelAlign: 'top',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-inGroup',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-inGroupYes',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    //load store
                                                                    cmb_farmer_group.setStoreVar({DistrictID:Ext.getCmp('Koltiva.view.Grower.FormMainGrower-District').getValue()});
                                                                    cmb_farmer_group.load();
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FarmerGroupID').setDisabled(false);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-groupName').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FarmerGroupID').setDisabled(true);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-groupName').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-inGroup',
                                                        inputValue: '0',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-inGroupNo',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-FarmerGroupID',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-FarmerGroupID',
                                                    store: cmb_farmer_group,
                                                    fieldLabel: lang('Farmer Group'),
                                                    labelAlign: 'top',
                                                    queryMode: 'local',
                                                    disabled: true,
                                                    displayField: 'label',
                                                    valueField: 'id'
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-groupName',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-groupName',
                                                    fieldLabel: lang('Group Name'),
                                                    labelAlign: 'top',
                                                    hidden:true,
                                                    disabled:true
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    fieldLabel: lang('Is a member of gapoktan'),
                                                    xtype: 'radiogroup',
                                                    columns: 2,
                                                    labelAlign: 'top',
                                                    id:'Koltiva.view.Grower.FormMainGrower-RowinGapoktan',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-inGapoktan',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-inGapoktanYes',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-GapoktanName').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-GapoktanName').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-inGapoktan',
                                                        inputValue: '0',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-inGapoktanNo',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-GapoktanName',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-GapoktanName',
                                                    fieldLabel: lang('Gapoktan Name'),
                                                    labelAlign: 'top',
                                                    disabled:true
                                                },{
                                                    html: '<div></div>'
                                                },{
                                                    fieldLabel: lang('Is a member of cooperatives'),
                                                    xtype: 'radiogroup',
                                                    columns: 2,
                                                    labelAlign: 'top',
                                                    id:'Koltiva.view.Grower.FormMainGrower-RowinCoop',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-inCoop',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-inCoopYes',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-CoopName').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-CoopName').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-inCoop',
                                                        inputValue: '0',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-inCoopNo',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-CoopName',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-CoopName',
                                                    fieldLabel: lang('Cooperatives Name'),
                                                    labelAlign:'top',
                                                    disabled:true
                                                },{
                                                    html:'<div></div>'
                                                }]
                                            }]
                                        }]
                                    },{
                                        xtype: 'panel',
                                        title: lang('Other Information'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionOtherInformation',
                                        style: 'margin-top:15px;',
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
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-HowManyPlot',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-HowManyPlot',
                                                    fieldLabel: lang('How many oil palm plots do you have'),
                                                    labelAlign: 'top',
                                                    allowNegative: false
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-PlotTotalHectare',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-PlotTotalHectare',
                                                    fieldLabel: lang('Total hectare'),
                                                    readOnly: true,
                                                    labelAlign: 'top',
                                                    allowNegative: false
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    fieldLabel: lang('Do you have other commodities?'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-HaveOtherCommodities',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-HaveOtherCommodities1',
                                                        listeners:{
                                                            change: function(){
                                                                if(thisObj.opsiDisplay == 'update'){
                                                                    if(this.checked == true){
                                                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-PanelOtherLand-btnAdd').setDisabled(false);
                                                                    }else{
                                                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-PanelOtherLand-btnAdd').setDisabled(true);
                                                                    }
                
                                                                    return false;
                                                                }
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-HaveOtherCommodities',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-HaveOtherCommodities2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    fieldLabel: lang('Do you work in the plantation yourself'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-WorkInPlot',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-WorkInPlot1',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-RowUseAPD').setDisabled(false);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-RowHadAccident').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-RowUseAPD').setDisabled(true);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-RowHadAccident').setDisabled(true);
                                                                }
            
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-WorkInPlot',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-WorkInPlot2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    fieldLabel: lang('Do you use PPE when working'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    disabled:true,
                                                    columns: 2,
                                                    id:'Koltiva.view.Grower.FormMainGrower-RowUseAPD',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-UseAPD',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-UseAPD1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-UseAPD',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-UseAPD2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    fieldLabel: lang('Have you ever had an accident while working'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    disabled:true,
                                                    id:'Koltiva.view.Grower.FormMainGrower-RowHadAccident',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-HadAccident',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-HadAccident1',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('AccidentPanel').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('AccidentPanel').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-HadAccident',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-HadAccident2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    items:[{
                                                        columnWidth: 1,
                                                        layout:'form',
                                                        items:[{
                                                            xtype:'label',
                                                            cls: 'x-form-item-label',
                                                            text: lang('What Kind of Accident')
                                                        }]
                                                    }]
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    style:'margin-top:-20px;padding-top:0px;',
                                                    items:[{
                                                        layout:'column',
                                                        columnWidth: 1,
                                                        id:'AccidentPanel',
                                                        disabled:true,
                                                        style:'margin-top:-7px;padding-top:0px;',
                                                        items:[{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Cutting from knife/harvest tools'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-AccidentKnife',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-AccidentKnife',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Hit by a fruit'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-AccidentHitbyFruit',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-AccidentHitbyFruit',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Contamination from chemical liquid from pesticide/fertilizer/herbicide'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-AccidentContimination',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-AccidentContimination',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Other'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-AccidentOther',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-AccidentOther',
                                                                listeners:{
                                                                    change:function(){
                                                                        if(this.checked == true){
                                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-AccidentOtherText').setVisible(true);
                                                                        }else{
                                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-AccidentOtherText').setVisible(false);
                                                                        }
                                                                    }
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth:1,
                                                            border:false,
                                                            items:[{
                                                                xtype: 'textfield',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-AccidentOtherText',
                                                                name: 'Koltiva.view.Grower.FormMainGrower-AccidentOtherText',
                                                                hidden: true
                                                            }]
                                                        }]
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    items:[{
                                                        columnWidth: 1,
                                                        layout:'form',
                                                        items:[{
                                                            xtype:'label',
                                                            cls: 'x-form-item-label',
                                                            text: lang('Have BPJS')
                                                        }]
                                                    }]
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    style:'margin-top:-20px;padding-top:0px;',
                                                    items:[{
                                                        layout:'column',
                                                        columnWidth: 1,
                                                        id:'BPJSPanel',
                                                        disabled:false,
                                                        style:'margin-top:-7px;padding-top:0px;',
                                                        items:[{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('BPJS Health Insurance'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-HaveBPJS',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-HaveBPJS',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('BPJS Employees Social Security System'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-HaveBPJSKetenagakerjaan',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-HaveBPJSKetenagakerjaan',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('No'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-HaveBPJSNo',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-HaveBPJSNo',
                                                                listeners:{
                                                                }
                                                            }]
                                                        }]
                                                    }]
                                                },{
                                                    xtype: 'textfield',
                                                    hidden:true,
                                                    id: 'Koltiva.view.Grower.FormMainGrower-PhotoDesc',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-PhotoDesc',
                                                    emptyText: lang('Notes on picture of visit')
                                                },{
                                                    html:'<div></div>',
                                                },
                                                {
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-CategoryFarmer',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-CategoryFarmer',
                                                    store: cmb_farmer_category,
                                                    fieldLabel: lang('Farmer Category'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    hidden:true,
                                                    valueField: 'id'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-TotalProductionArea',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-TotalProductionArea',
                                                    store: cmb_total_production_area,
                                                    fieldLabel: lang('Total Production Area'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id'
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-MembershipStatus',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-MembershipStatus',
                                                    store: cmb_membership_status,
                                                    fieldLabel: lang('Membership Status'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id'
                                                },{
                                                    html: '<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-SupplybaseType',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-SupplybaseType',
                                                    store: cmb_supplybase,
                                                    fieldLabel: lang('Supply Base Type'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id'
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype:'textarea',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-frComment',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-frComment',
                                                    fieldLabel: lang('Comment'),
                                                    labelAlign: 'top'
                                                },{
                                                    html: '<div></div><div></div><div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-Enumerator',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-Enumerator',
                                                    fieldLabel: lang('Enumerator'),
                                                    labelAlign: 'top',
                                                    readOnly: true
                                                },{
                                                    html: '<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-ModifiedBy',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-ModifiedBy',
                                                    fieldLabel: lang('Modified by'),
                                                    labelAlign: 'top',
                                                    readOnly: true
                                                }]
                                            }]
                                        }]
                                    }]
                                }]
                            }]
                        },{
                            xtype: 'panel',
                            title: lang('Farmer\'s Certification'),
                            id: 'Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerCertification',
                            items:[{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 0.5,
                                    layout: 'form',
                                    // style: 'padding:10px 5px 10px 20px;',
                                    defaults: {
                                        labelAlign: 'top'
                                    },
                                    items:[{
                                        xtype: 'panel',
                                        title: lang('General Data'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionCertificationGeneralData',
                                        style: 'margin-top:12px;',
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
                                                        columnWidth: 0.90,
                                                        layout:'form',
                                                        items:[{
                                                            xtype: 'combobox',
                                                            id: 'Koltiva.view.Grower.FormMainGrower-isCertified',
                                                            name: 'Koltiva.view.Grower.FormMainGrower-isCertified',
                                                            store: cmb_certified_opt,
                                                            fieldLabel: lang('Do You Have Sustainable Palm Oil Certification'),
                                                            labelAlign:'top',
                                                            queryMode: 'local',
                                                            displayField: 'label',
                                                            valueField: 'id',
                                                            listeners: {
                                                                change: function(cb, nv, ov) {
                                                                    if(nv == 1){
                                                                        Ext.getCmp('CertificationPanel').setDisabled(false);
                                                                    }else{
                                                                        Ext.getCmp('CertificationPanel').setDisabled(true);
                                                                    }
                                                                }
                                                            }
                                                        }]
                                                    },{
                                                        columnWidth: 0.1,
                                                        layout: 'form',
                                                        items:[{
                                                            xtype: 'image',
                                                            width: '18px',
                                                            style: 'cursor:pointer;margin-left:5px;',
                                                            src: varjs.config.base_url + 'images/icons/silk/information.png',
                                                            autoEl: {
                                                                tag: 'label',
                                                                'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_isCertified')+'</div>'
                                                            }
                                                        }]
                                                    }]
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    id:'CertificationPanel',
                                                    disabled:true,
                                                    items:[{
                                                        columnWidth: 0.90,
                                                        layout:'column',
                                                        items:[{
                                                            columnWidth: 1,
                                                            layout:'form',
                                                            items:[{
                                                                xtype:'label',
                                                                cls: 'x-form-item-label',
                                                                text: lang('Certification Standard')
                                                            }]
                                                        },{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('RSPO'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationRSPO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationRSPO',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('ISCC'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationISCC',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationISCC',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('ISPO'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationISPO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationISPO',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('MSPO'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationMSPO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationMSPO',
                                                                listeners:{
                                                                }
                                                            }]
                                                        }]
                                                    }]
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    items:[{
                                                        columnWidth: 0.90,
                                                        layout:'form',
                                                        items:[{
                                                            fieldLabel: lang('Did You Receive Any Trainings to Improve Your Agriculture or Business Practices'),
                                                            labelAlign:'top',
                                                            xtype: 'radiogroup',
                                                            msgTarget: 'side',
                                                            columns: 2,
                                                            items:[{
                                                                boxLabel: lang('Yes'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-ReceiveTraining',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-ReceiveTraining1',
                                                                listeners:{
                                                                    change: function(){
                                                                        if(this.checked == true){
                                                                            Ext.getCmp('CertificationSourcePanel').setDisabled(false);
                                                                            Ext.getCmp('CertificationTypePanel').setDisabled(false);                                                                    
                                                                        }else{
                                                                            Ext.getCmp('CertificationSourcePanel').setDisabled(true);
                                                                            Ext.getCmp('CertificationTypePanel').setDisabled(true);
                                                                        }
                                                                        return false;
                                                                    }
                                                                }
                                                            },{
                                                                boxLabel: lang('No'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-ReceiveTraining',
                                                                inputValue: '2',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-ReceiveTraining2',
                                                                listeners:{
                                                                    change: function(){
                                                                        if(this.checked == true){
                                                                            Ext.getCmp('CertificationSourcePanel').setDisabled(true);
                                                                            Ext.getCmp('CertificationTypePanel').setDisabled(true);
                                                                        }else{
                                                                            Ext.getCmp('CertificationSourcePanel').setDisabled(false);
                                                                            Ext.getCmp('CertificationTypePanel').setDisabled(false);
                                                                        }
                                                                        return false;
                                                                    }
                                                                }
                                                            }]
                                                        }]
                                                    },{
                                                        columnWidth: 0.1,
                                                        layout: 'form',
                                                        items:[{
                                                            xtype: 'image',
                                                            width: '18px',
                                                            style: 'cursor:pointer;margin-left:5px;',
                                                            src: varjs.config.base_url + 'images/icons/silk/information.png',
                                                            autoEl: {
                                                                tag: 'label',
                                                                'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_ReceiveTraining')+'</div>'
                                                            }
                                                        }]
                                                    }]
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    id:'CertificationSourcePanel',
                                                    disabled:true,
                                                    items:[{
                                                        columnWidth: 0.90,
                                                        layout:'column',
                                                        items:[{
                                                            columnWidth: 1,
                                                            layout:'form',
                                                            items:[{
                                                                xtype:'label',
                                                                cls: 'x-form-item-label',
                                                                text: lang('From Whom Did You Received Training')
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Government Extention Officer'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceGovernment',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceGovernment',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('NGO'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceNGO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceNGO',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Mill'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceMill',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceMill',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Other Private Sector Organization'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationSourcePrivateOrg',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationSourcePrivateOrg',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Others'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceOthers',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationSourceOthers',
                                                                listeners:{
                                                                }
                                                            }]
                                                        }]
                                                    },{
                                                        columnWidth: 0.1,
                                                        layout: 'form',
                                                        items:[{
                                                            xtype: 'image',
                                                            width: '18px',
                                                            style: 'cursor:pointer;margin-left:5px;',
                                                            src: varjs.config.base_url + 'images/icons/silk/information.png',
                                                            autoEl: {
                                                                tag: 'label',
                                                                'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_CertificationSourcePanel')+'</div>'
                                                            }
                                                        }]
                                                    }]
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    id:'CertificationTypePanel',
                                                    disabled:true,
                                                    items:[{
                                                        columnWidth: 0.90,
                                                        layout:'form',
                                                        items:[{
                                                            columnWidth: 1,
                                                            layout:'form',
                                                            items:[{
                                                                xtype:'label',
                                                                cls: 'x-form-item-label',
                                                                text: lang('What Type of Trainings Did You Receive')
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Financial and Farm Business Operations'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeFinancial',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeFinancial',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Good Agriculuture Practice'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeGoodAgriculture',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeGoodAgriculture',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Human Rights and Worker Rights'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeHumanRights',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeHumanRights',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Best Management of Pesticides'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeManagementPesticides',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeManagementPesticides',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Fire Fighting'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeFireFighting',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeFireFighting',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('HCV and HCS'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeHCVHCS',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeHCVHCS',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('P&C RSPO Independent Smallholder Standard'),
                                                                name: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeRSPOIndependent',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.Grower.FormMainGrower-CertificationTypeRSPOIndependent',
                                                                listeners:{
                                                                }
                                                            }]
                                                        }]
                                                    },{
                                                        columnWidth: 0.1,
                                                        layout: 'form',
                                                        items:[{
                                                            xtype: 'image',
                                                            width: '18px',
                                                            style: 'cursor:pointer;margin-left:5px;',
                                                            src: varjs.config.base_url + 'images/icons/silk/information.png',
                                                            autoEl: {
                                                                tag: 'label',
                                                                'data-qtip': "<div class='qtip-survey-explanation'>"+lang('gfex_CertificationTypePanel')+'</div>'
                                                            }
                                                        }]
                                                    }]
                                                }]
                                            }]
                                        }]
                                    }]
                                },{
                                    columnWidth: 0.5,
                                    layout: 'form',
                                    style: 'padding:0px 5px 0px 20px;',
                                    defaults: {
                                        labelAlign: 'top'
                                    },
                                    items:[{
                                        xtype: 'panel',
                                        title: lang('Documents'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionCertificationDocuments',
                                        style: 'margin-top:12px;',
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
                                                    fieldLabel: lang('Willingness  to participate in independent smallholder support programs, including data collection and processing and certification standards'),
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-WillingnesParticipate',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-WillingnesParticipate1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-WillingnesParticipate',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-WillingnesParticipate2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    xtype: 'image',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-WillingnesSignature',
                                                    height:'200px',
                                                    src: m_api_base_url + '/assets/images/signature.png'
                                                },{
                                                    xtype: 'fileuploadfield',
                                                    fieldLabel: lang('Signature'),
                                                    labelAlign: 'top',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-WillingnesSignatureInput',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-WillingnesSignatureInput',
                                                    buttonText: 'Browse',
                                                    listeners: {
                                                        'change': function (fb, v) {
                                                            objPanelBasicData.submit({
                                                                url: m_api + '/grower/image_member_willingnes',
                                                                clientValidation: false,
                                                                params: {
                                                                    opsiDisplay: thisObj.opsiDisplay,
                                                                    MemberID: Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberID').getValue()
                                                                },
                                                                waitMsg: 'Sending Photo...',
                                                                success: function (fp, o) {
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-WillingnesSignature').setSrc(o.result.file);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-WillingnesSignatureOld').setValue(o.result.filepath);
                                                                }
                                                            });
                                                        }
                                                    }
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-WillingnesSignatureOld',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-WillingnesSignatureOld',
                                                    inputType: 'hidden'
                                                },{
                                                    fieldLabel: lang('Willingness to commit to the RSPO certification standard'),
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-WillingnesCommit',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-WillingnesCommit1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.Grower.FormMainGrower-WillingnesCommit',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.Grower.FormMainGrower-WillingnesCommit2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    xtype: 'image',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-WillingnesCommitSignature',
                                                    height:'200px',
                                                    src: m_api_base_url + '/assets/images/signature.png'
                                                },{
                                                    xtype: 'fileuploadfield',
                                                    fieldLabel: lang('Signature'),
                                                    labelAlign: 'top',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-WillingnesCommitSignatureInput',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-WillingnesCommitSignatureInput',
                                                    buttonText: 'Browse',
                                                    listeners: {
                                                        'change': function (fb, v) {
                                                            objPanelBasicData.submit({
                                                                url: m_api + '/grower/image_member_willingnes_commit',
                                                                clientValidation: false,
                                                                params: {
                                                                    opsiDisplay: thisObj.opsiDisplay,
                                                                    MemberID: Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberID').getValue()
                                                                },
                                                                waitMsg: 'Sending Photo...',
                                                                success: function (fp, o) {
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-WillingnesCommitSignature').setSrc(o.result.file);
                                                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-WillingnesCommitSignatureOld').setValue(o.result.filepath);
                                                                }
                                                            });
                                                        }
                                                    }
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Grower.FormMainGrower-WillingnesCommitSignatureOld',
                                                    name: 'Koltiva.view.Grower.FormMainGrower-WillingnesCommitSignatureOld',
                                                    inputType: 'hidden'
                                                }]
                                            }]
                                        }]
                                    }]
                                }]
                            }]
                        },{
                            xtype: 'panel',
                            title: lang('Farmer\'s Labour'),
                            id: 'Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerLabour',
                            items:[objFormPanelLabour,objPanelLabour]
                        },{
                            xtype: 'panel',
                            title: lang('Farmer\'s Family'),
                            id: 'Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerFamily',
                            items:[objPanelFamily]
                        },{
                            xtype: 'panel',
                            title: lang('Training Coaching'),
                            id: 'Koltiva.view.Grower.FormMainGrower-FormBasicData-tabTrainingCoaching',
                            items:[thisObj.ObjPanelTrainingCoachingGrid]
                        }],
                        listeners: {
                            'tabchange': function (tabPanel, tab) {
                                //console.log(tabPanel.id + ' ' + tab.id);
                                switch(tab.id){
                                    case 'Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerFamily':
                                    case 'Koltiva.view.Grower.FormMainGrower-FormBasicData-tabTrainingCoaching':
                                    case 'Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerLabour':
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-btnSave').setVisible(false);
                                    break;
                                    default:
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-btnSave').setVisible(true);
                                    break;
                                }
                            }
                        }
                    }]
                }]
            }],
            buttons: [{
                text: lang('Save'),
                id: 'Koltiva.view.Grower.FormMainGrower-btnSave',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    if (objPanelBasicData.isValid()) {
                        //Data Control Tambahan ======================================= (Begin)
                        thisObj.AddValidation = true;
                        thisObj.MsgAddValidation = "";
                        thisObj.AddValidationBasicForm();
                        //Data Control Tambahan ======================================= (Emd)

                        if(thisObj.AddValidation == true){
                            objPanelBasicData.submit({
                                url: m_api + '/grower/member',
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
    
                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower').destroy(); //destory current view
                                    //create object View untuk FormMainGrower
                                    if(Ext.getCmp('Koltiva.view.Grower.FormMainGrower') == undefined){
                                        var FormMainGrower = Ext.create('Koltiva.view.Grower.FormMainGrower', {
                                            opsiDisplay: 'update',
                                            formVar: {
                                                MemberID: o.result.MemberIDInc,
                                                PartnerSurvey: o.result.PartnerSurvey
                                            }
                                        });
                                    }else{
                                        //destroy, create ulang
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower').destroy();
                                        var FormMainGrower = Ext.create('Koltiva.view.Grower.FormMainGrower', {
                                            opsiDisplay: 'update',
                                            formVar: {
                                                MemberID: o.result.MemberIDInc,
                                                PartnerSurvey: o.result.PartnerSurvey
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
            }]
        });
        //================================================== panel Form Basic Data (END)   ==========================================//

        //panel Other Land ===================================================================================================================================== (begin)
        var olRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'Koltiva.view.Grower.FormMainGrower-PanelOtherLand-RowEdit',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });

        var contextMenuGridOtherLand = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-PanelOtherLand-gridOtherLand').getSelectionModel().getSelection()[0];
                    //console.log(sm);

                    //get last row from store
                    var storeGridOtherLand = Ext.data.StoreManager.lookup('Koltiva.store.Grower.GridMemberOtherLand');
                    var lastRow = storeGridOtherLand.getAt(storeGridOtherLand.getCount()-1);
                    //console.log(lastRow);

                    if(sm.data.MemOtherID == lastRow.data.MemOtherID){
                        var heightGridNow = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-PanelOtherLand-gridOtherLand').getHeight();
                        heightGridNow = heightGridNow + 55;
                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-PanelOtherLand-gridOtherLand').setHeight(heightGridNow);
                    }

                    olRowEditing.cancelEdit();
                    olRowEditing.startEdit(sm.index, 0);
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-PanelOtherLand-gridOtherLand').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/grower/other_land',
                                method: 'DELETE',
                                params: {
                                    MemOtherID: sm.data.MemOtherID
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    storeGridOtherLand.setStoreVar({MemberID:thisObj.formVar.MemberID});
                                    storeGridOtherLand.load();
                                },
                                failure: function(response, opts) {
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
                        }
                    });

                }
            }]
        });

        var storeGridOtherLand = Ext.create('Koltiva.store.Grower.GridMemberOtherLand');
        var cmb_other_land_commodity = Ext.create('Koltiva.store.Grower.CmbOtherLandCommodity');

        var objPanelOtherLand = Ext.create('Ext.panel.Panel',{
            title: lang('Other Commodities'),
            frame: true,
            collapsible:true,
            id: 'Koltiva.view.Grower.FormMainGrower-PanelOtherLand',
            margin:'0 0 20 8',
            dockedItems: [{
                xtype: 'toolbar',
                baseCls: 'bgToolbarTitlePanel',
                dock: 'top',
                items:[{
                    xtype: 'tbtext',
                    style:'font-weight:bold;text-decoration:underline;',
                    text: lang('List of Other Commodities')
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    id: 'Koltiva.view.Grower.FormMainGrower-PanelOtherLand-btnAdd',
                    hidden: m_act_add,
                    disabled:true,
                    handler: function() {
                        olRowEditing.cancelEdit();
                        var r = Ext.create('otherLandGridModel.Model', {
                            MemOtherID: '',
                            MemberID: '',
                            Commodity: '',
                            GardenHa: '',
                            Remark: ''
                        });
                        storeGridOtherLand.insert(0, r);
                        olRowEditing.startEdit(0, 0);
                    }
                }]
            }],
            items: [{
                xtype: 'grid',
                id: 'Koltiva.view.Grower.FormMainGrower-PanelOtherLand-gridOtherLand',
                loadMask: true,
                selType: 'rowmodel',
                store: storeGridOtherLand,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                minHeight:125,
                columns: [{
                    text: lang('Action'),
                    xtype:'actioncolumn',
                    flex: 0.5,
                    items:[{
                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                        tooltip: 'Action',
                        handler: function(grid, rowIndex, colIndex, item, e, record) {
                            contextMenuGridOtherLand.showAt(e.getXY());
                        }
                    }]
                },{
                    text: 'No',
                    xtype: 'rownumberer',
                    flex: 0.5,
                },{
                    text: lang('ID'),
                    dataIndex: 'MemOtherID',
                    hidden:true
                },{
                    text: lang('MemberID'),
                    dataIndex: 'MemberID',
                    hidden:true
                },{
                    text: lang('Commodity'),
                    dataIndex: 'Commodity',
                    flex: 1,
                    editor: {
                        xtype: 'combobox',
                        store: cmb_other_land_commodity,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        id: 'Koltiva.view.Grower.FormMainGrower-PanelOtherLand-gridOtherLand-reditCommodity',
                        allowBlank: false
                    }
                },{
                    text: lang('Size (ha)'),
                    dataIndex: 'GardenHa',
                    flex: 1,
                    editor: {
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Grower.FormMainGrower-PanelOtherLand-gridOtherLand-reditGardenHa',
                        allowNegative: false,
                        minValue: 0
                    }
                },{
                    text: lang('Remark'),
                    dataIndex: 'Remark',
                    flex: 2,
                    editor:{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Grower.FormMainGrower-PanelOtherLand-gridOtherLand-reditRemark'
                    }
                }],
                plugins: [olRowEditing],
                listeners: {
                    'canceledit': function(editor, e, eOpts) {
                        storeGridOtherLand.setStoreVar({MemberID:thisObj.formVar.MemberID});
                        storeGridOtherLand.load();
                    },
                    'edit': function(editor, e) {
                        console.log(e);

                        //cek
                        if(thisObj.formVar.MemberID == undefined){
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: lang('Farmer not defined yet'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                            storeGridOtherLand.setStoreVar({MemberID:null});
                            storeGridOtherLand.load();

                            return false;
                        }

                        if (e.record.data.MemOtherID == '') {
                            //insert
                            var opsiPost = 'insert';
                            var Commodity = e.record.data.Commodity;
                        }else{
                            //update
                            var opsiPost = 'update';
                            var Commodity = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-PanelOtherLand-gridOtherLand-reditCommodity').getValue();
                        }

                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_api + '/grower/other_land',
                            method: 'POST',
                            params: {
                                opsiPost: opsiPost,
                                MemberID: thisObj.formVar.MemberID,
                                MemOtherID: e.record.data.MemOtherID,
                                Commodity: Commodity,
                                GardenHa: e.record.data.GardenHa,
                                Remark: e.record.data.Remark
                            },
                            success: function(response, opts) {
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: lang('Data saved'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                //refresh store
                                storeGridOtherLand.setStoreVar({MemberID:thisObj.formVar.MemberID});
                                storeGridOtherLand.load();
                            },
                            failure: function(response, opts) {
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
                    }
                }
            }]
        });
        //panel Other Land ===================================================================================================================================== (end)        
        
        //================================================ PROSES PANEL (Begin) ==============================================================//
        var objPanelDinamis = [objPanelOtherLand];

        //Panel Plot Status (Begin) ============================================//
        if(this.opsiDisplay == 'update' || this.opsiDisplay == 'view'){
            thisObj.PanelPlantationStatus = Ext.create('Koltiva.view.PlotSurvey.PanelPlantationStatus',{
                viewVar: {
                    MemberID: thisObj.formVar.MemberID,
                    CallFrom: 'Farmer'
                }
            });

            //Add ke object panel dinamis
            objPanelDinamis.push(thisObj.PanelPlantationStatus);
        }
        //Panel Plot Status (End) ============================================//

        if(thisObj.opsiDisplay == 'view' || thisObj.opsiDisplay == 'update'){
            if(thisObj.formVar.PartnerSurvey != null){
                //proses PartnerSurveynya
                var arrayPartnerSurvey = thisObj.formVar.PartnerSurvey.split(",");

                if(arrayPartnerSurvey.indexOf('Plantation Unilever') !== -1){
                    //panel Plot Survey
                    var objPanelPlotSurvey = Ext.create('Koltiva.view.PlotSurvey.PlotSurPanelSummary');
                    thisObj.objPanelPlotSurveyPanel = objPanelPlotSurvey; //biar bisa diakses di beforeactive
                    objPanelDinamis.push(objPanelPlotSurvey);

                    //set param view2 yg dipanggil view ini
                    thisObj.objPanelPlotSurveyPanel.setViewVar({
                        MemberID:thisObj.formVar.MemberID
                    });
                }

                if(arrayPartnerSurvey.indexOf('Plantation GAR') !== -1){
                    //panel Plot Survey
                    var objPanelPlotSurveyGar = Ext.create('Koltiva.view.PlotSurvey.PlotSurPanelSummaryGar');
                    thisObj.objPanelPlotSurveyGarPanel = objPanelPlotSurveyGar; //biar bisa diakses di beforeactive
                    objPanelDinamis.push(objPanelPlotSurveyGar);

                    //set param view2 yg dipanggil view ini
                    thisObj.objPanelPlotSurveyGarPanel.setViewVar({
                        MemberID:thisObj.formVar.MemberID
                    });
                }

                if(arrayPartnerSurvey.indexOf('Plantation Certification') !== -1){                    
                    //panel Plot Survey Sertifikasi
                    var objPanelPlotSurveyGarCertification = Ext.create('Koltiva.view.PlotSurvey.PlotSurPanelSummaryGarCertification');
                    thisObj.objPanelPlotSurveyGarPanelCertification = objPanelPlotSurveyGarCertification; //biar bisa diakses di beforeactive
                    objPanelDinamis.push(objPanelPlotSurveyGarCertification);

                    //set param view2 yg dipanggil view ini
                    thisObj.objPanelPlotSurveyGarPanelCertification.setViewVar({
                        MemberID:thisObj.formVar.MemberID
                    });

                    //panel Plot Sertifikasi
                    var objPanelPlotCertification = Ext.create('Koltiva.view.PlotSurvey.PlotPanelCertification');
                    thisObj.objPanelPlotSurveyGarPanelCertification = objPanelPlotCertification; //biar bisa diakses di beforeactive
                    objPanelDinamis.push(objPanelPlotCertification);

                    //set param view2 yg dipanggil view ini
                    thisObj.objPanelPlotSurveyGarPanelCertification.setViewVar({
                        MemberID:thisObj.formVar.MemberID
                    });
                }

                if(arrayPartnerSurvey.indexOf('Plantation STA') !== -1){
                    //panel Plot Survey
                    var objPanelPlotSurveySta = Ext.create('Koltiva.view.PlotSurvey.PlotSurPanelSummarySta');
                    thisObj.objPanelPlotSurveyStaPanel = objPanelPlotSurveySta; //biar bisa diakses di beforeactive
                    objPanelDinamis.push(objPanelPlotSurveySta);

                    //set param view2 yg dipanggil view ini
                    thisObj.objPanelPlotSurveyStaPanel.setViewVar({
                        MemberID:thisObj.formVar.MemberID,
                        User: 'Farmer'
                    });
                }

                if(arrayPartnerSurvey.indexOf('Plantation WAGS') !== -1){
                    //panel Plot Survey
                    var objPanelPlotSurveyWags = Ext.create('Koltiva.view.PlotSurvey.PlotSurPanelSummaryWags');
                    thisObj.objPanelPlotSurveyWagsPanel = objPanelPlotSurveyWags; //biar bisa diakses di beforeactive
                    objPanelDinamis.push(objPanelPlotSurveyWags);

                    //set param view2 yg dipanggil view ini
                    thisObj.objPanelPlotSurveyWagsPanel.setViewVar({
                        MemberID:thisObj.formVar.MemberID
                    });
                }

                if(arrayPartnerSurvey.indexOf('Main Buyer Unilever') !== -1){
                    //panel Main Buyer Survey
                    thisObj.objPanelMainBuyerSurvey = Ext.create('Koltiva.view.MainBuyerSurvey.MainBuyerSurPanelSummary');
                    objPanelDinamis.push(thisObj.objPanelMainBuyerSurvey);

                    //set param view2 yg dipanggil view ini
                    thisObj.objPanelMainBuyerSurvey.setViewVar({
                        MemberID:thisObj.formVar.MemberID
                    });
                }

                if(arrayPartnerSurvey.indexOf('Household Unilever') !== -1){
                    //panel Household Survey
                    thisObj.objPanelHouseholdSurvey = Ext.create('Koltiva.view.HouseholdSurvey.HouseholdSurPanelSummary');
                    objPanelDinamis.push(thisObj.objPanelHouseholdSurvey);

                    //set param view2 yg dipanggil view ini
                    thisObj.objPanelHouseholdSurvey.setViewVar({
                        MemberID:thisObj.formVar.MemberID
                    });
                }

                if(arrayPartnerSurvey.indexOf('Finance SNV') !== -1){
                    //panel Finance Survey
                    thisObj.objPanelFinanceSurvey = Ext.create('Koltiva.view.FinanceSurvey.FinanceSurveyPanelSummary');
                    objPanelDinamis.push(thisObj.objPanelFinanceSurvey);

                    //set param view2 yg dipanggil view ini
                    thisObj.objPanelFinanceSurvey.setViewVar({
                        MemberID:thisObj.formVar.MemberID
                    });
                }

                if(arrayPartnerSurvey.indexOf('Plantation Polygon Unilever') !== -1){
                    //panel Garden Polygon
                    thisObj.objPanelPlotPolygon  = Ext.create('Koltiva.view.PlotPolygon.PlotPolygonPanel',{
                        viewVar: {
                            MemberID: thisObj.formVar.MemberID,
                            CallFrom: 'Farmer'
                        }
                    });
                    objPanelDinamis.push(thisObj.objPanelPlotPolygon);
                }
            }
        }

        //panel Survey Document
        thisObj.objPanelDocumentSurvey = Ext.create('Koltiva.view.DocumentSurvey.DocumentSurveyPanel', {
            viewVar: {
                MemberID: thisObj.formVar.MemberID
            }
        });
        objPanelDinamis.push(thisObj.objPanelDocumentSurvey);

        if (thisObj.opsiDisplay == "update" || thisObj.opsiDisplay == "view") {
            thisObj.objPanelFamilyLabourPostline = Ext.create('Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel');
            objPanelDinamis.push(thisObj.objPanelFamilyLabourPostline);

            //set param view2 yg dipanggil view ini
            thisObj.objPanelFamilyLabourPostline.setViewVar({
                MemberID:thisObj.formVar.MemberID,
                opsiDisplay:thisObj.opsiDisplay
            });

            thisObj.objPanelFarmerLabourPostline = Ext.create('Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel');
            objPanelDinamis.push(thisObj.objPanelFarmerLabourPostline);

            //set param view2 yg dipanggil view ini
            thisObj.objPanelFarmerLabourPostline.setViewVar({
                MemberID:thisObj.formVar.MemberID,
                opsiDisplay:thisObj.opsiDisplay
            });
        }
        //================================================ PROSES PANEL (End)   ==============================================================//

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id: 'Koltiva.view.Grower.FormMainGrower-labelInfoInsert',
                html:''
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
                  '&nbsp;&nbsp;' + lang('Back to Farmer List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower').destroy(); //destory current view
                        if(Ext.getCmp('Koltiva.view.Grower.GridMainGrower-MainPanel') == undefined){
                            var GridMainGrower = Ext.create('Koltiva.view.Grower.GridMainGrower');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Grower.GridMainGrower-MainPanel').destroy();
                            var GridMainGrower = Ext.create('Koltiva.view.Grower.GridMainGrower');
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
                items:objPanelDinamis
            }]
        }];
        //========================================================== LAYOUT UTAMA (END) ========================================//

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'none';

            //set label
            if(this.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-TotalProductionArea').setVisible(false);

                //Atur Show/Hide Form soal WAGS dan bukan WAGS ==== (End)

                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-labelInfoInsert').update('<h3 style="margin:0px;padding:0px;">'+lang('Farmer Data')+'&nbsp;<span style="font-size:14px;">('+lang('Add New Farmer')+')</span></h3>');

                //form reset
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-KTPPhoto').setSrc(m_api_base_url + '/assets/images/ktp-default.png');

                //hidden tab
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerFamily').setDisabled(true);
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tabTrainingCoaching').setDisabled(true);
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerLabour').setDisabled(true);
                //set aktif tab pertama
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tab').setActiveTab(0);

                //buka panel2
                // Ext.getCmp('Koltiva.view.Grower.FormMainGrower-PanelFamLab').collapse();

                //set param view2 yg dipanggil view ini
                thisObj.objPanelDocumentSurvey.setViewVar({
                    MemberID:thisObj.formVar.MemberID
                });

                //load store Family & Labour
                var grid_family_labour = Ext.data.StoreManager.lookup('store.Grower.GridMemberFamilyLabour');
                grid_family_labour.setStoreVar({MemberID:null});
                grid_family_labour.load();

                //load store other land
                var grid_other_land = Ext.data.StoreManager.lookup('Koltiva.store.Grower.GridMemberOtherLand');
                grid_other_land.setStoreVar({MemberID:null});
                grid_other_land.load();

                //load store
                var grid_document_survey_panel = Ext.data.StoreManager.lookup('store.DocumentSurvey.GridDocumentSurveyPanel');
                grid_document_survey_panel.setStoreVar({MemberID:null});
                grid_document_survey_panel.load();

            }

            if(this.opsiDisplay == 'update' || this.opsiDisplay == 'view'){

                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-TotalProductionArea').setVisible(false);                
                //hidden tab
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerFamily').setDisabled(false);
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tabTrainingCoaching').setDisabled(false);
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tabFarmerLabour').setDisabled(false);
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                //set aktif tab pertama
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData-tab').setActiveTab(0);


                //form reset
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-KTPPhoto').setSrc(m_api_base_url + '/assets/images/ktp-default.png');

                //load data form
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FormBasicData').getForm().load({
                    url: m_api + '/grower/member_basic_data_form',
                    method: 'GET',
                    params: {
                        MemberID: this.formVar.MemberID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);

                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-ExtID').setVisible(true);
                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MembershipStatus').setVisible(false);

                        //untuk handle combo bertingkat
                        var cmb_province = Ext.data.StoreManager.lookup('store.Grower.CmbProvince');
                        var cmb_district = Ext.data.StoreManager.lookup('store.Grower.CmbDistrict');
                        var cmb_subdistrict = Ext.data.StoreManager.lookup('store.Grower.CmbSubdistrict');
                        var cmb_village = Ext.data.StoreManager.lookup('store.Grower.CmbVillage');
                        var cmb_farmer_group = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.CmbFarmerGroup');
                        cmb_province.load({
                            callback: function(records, operation, success){
                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Province').setValue(r.data.Province);
                                if (success == true) {

                                    //load combo farmer group lagi aja disini dan set nilainya jika ada
                                    cmb_farmer_group.setStoreVar({ProvinceID:r.data.Province});
                                    cmb_farmer_group.load({
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-FarmerGroupID').setValue(r.data.FarmerGroupID);
                                            }
                                        }
                                    });

                                    cmb_district.load({
                                        params: {
                                            ProvinceID: r.data.Province
                                        },
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-District').setValue(r.data.District);
                                                cmb_subdistrict.load({
                                                    params: {
                                                        DistrictID: r.data.District
                                                    },
                                                    callback: function(records, operation, success){

                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Subdistrict').setValue(r.data.Subdistrict);
                                                            cmb_village.load({
                                                                params: {
                                                                    SubdistrictID: r.data.Subdistrict
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Village').setValue(r.data.Village);
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
                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberPhotoOld').setValue(r.data.PhotoSrcPath);
                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-KTPPhotoOld').setValue(r.data.KTPSrcPath);

                        //set photo
                        if(r.data.PhotoSrc != ""){
                            var fotoUser = r.data.PhotoSrc;
                            var ktpfile = r.data.KTPSrc;
                            var wilsig = r.data.WillingnesSignature;
                            var wilcomsig = r.data.WillingnesCommitSignature;
                            var angkaRand = Math.floor((Math.random() * 100) + 1);

                            checkImageExists(fotoUser, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberPhoto').setSrc(fotoUser+'?'+angkaRand);
                                } else {
                                    if(r.data.Gender == 'f'){
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');
                                    }else{
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');
                                    }
                                }
                            });

                            checkImageExists(ktpfile, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-KTPPhoto').setSrc(ktpfile+'?'+angkaRand);
                                } else {
                                    if(r.data.Gender == 'f'){
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-KTPPhoto').setSrc(m_api_base_url + '/assets/images/ktp-default.png');
                                    }else{
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-KTPPhoto').setSrc(m_api_base_url + '/assets/images/ktp-default.png');
                                    }
                                }
                            });

                            checkImageExists(wilcomsig, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-WillingnesCommitSignature').setSrc(wilcomsig+'?'+angkaRand);
                                } else {
                                    if(r.data.Gender == 'f'){
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-WillingnesCommitSignature').setSrc(m_api_base_url + '/assets/images/signature.png');
                                    }else{
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-WillingnesCommitSignature').setSrc(m_api_base_url + '/assets/images/signature.png');
                                    }
                                }
                            });

                            checkImageExists(wilsig, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Grower.FormMainGrower-WillingnesSignature').setSrc(wilsig+'?'+angkaRand);
                                } else {
                                    if(r.data.Gender == 'f'){
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-WillingnesSignature').setSrc(m_api_base_url + '/assets/images/signature.png');
                                    }else{
                                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-WillingnesSignature').setSrc(m_api_base_url + '/assets/images/signature.png');
                                    }
                                }
                            });
                        }

                        //buka panel2
                        // Ext.getCmp('Koltiva.view.Grower.FormMainGrower-PanelFamLab').expand();
                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-PanelLabour').expand();

                        //set param view2 yg dipanggil view ini
                        thisObj.objPanelDocumentSurvey.setViewVar({
                            MemberID:thisObj.formVar.MemberID
                        });

                        //load store Family & Labour
                        var grid_family_labour = Ext.data.StoreManager.lookup('store.Grower.GridMemberFamilyLabour');
                        grid_family_labour.setStoreVar({MemberID:thisObj.formVar.MemberID});
                        grid_family_labour.load();

                        //load store farmer labour
                        var grid_farmer_labour = Ext.data.StoreManager.lookup('store.Grower.GridMemberLabour');
                        grid_farmer_labour.setStoreVar({MemberID:thisObj.formVar.MemberID});
                        grid_farmer_labour.load();

                        //load store other land
                        var grid_other_land = Ext.data.StoreManager.lookup('Koltiva.store.Grower.GridMemberOtherLand');
                        grid_other_land.setStoreVar({MemberID:thisObj.formVar.MemberID});
                        grid_other_land.load();

                        //load store
                        var grid_document_survey_panel = Ext.data.StoreManager.lookup('store.DocumentSurvey.GridDocumentSurveyPanel');
                        grid_document_survey_panel.setStoreVar({MemberID:thisObj.formVar.MemberID});
                        grid_document_survey_panel.load();

                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-labelInfoInsert').update('<h3 style="margin:0px;padding:0px;">'+Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberDisplayID').getValue()+' - '+Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Fullname').getValue()+'</h3>');


                        //Isi Form Labour Extension ======================= (Begin)
                        switch(r.data.labHaveWorkers){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHaveWorkers1').setValue(true);
                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridLabour-BtnAdd').setVisible(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHaveWorkers2').setValue(true);
                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                            break;
                            default:
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHaveWorkers2').setValue(true);
                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                            break;
                        }
                        Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHowManyWorker').setValue(r.data.labHowManyWorker);
                        switch(r.data.labWorkerUseApd){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerUseApd1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerUseApd2').setValue(true);
                            break;
                        }
                        switch(r.data.labWhoBuyApd){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWhoBuyApd1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWhoBuyApd2').setValue(true);
                            break;
                            case '3':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWhoBuyApd3').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerHadAccident){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerHadAccident1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerHadAccident2').setValue(true);
                            break;
                        }
                        switch(r.data.labWhoPayBpjs){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWhoPayBpjs1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWhoPayBpjs2').setValue(true);
                            break;
                            case '3':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWhoPayBpjs3').setValue(true);
                            break;
                        }
                        switch(r.data.labGiveInfoHealthSafety){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labGiveInfoHealthSafety1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labGiveInfoHealthSafety2').setValue(true);
                            break;
                            case '3':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labGiveInfoHealthSafety3').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerLivePlantation){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerLivePlantation1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerLivePlantation2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerSafeHouse){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerSafeHouse1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerSafeHouse2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerKeepIdentity){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerKeepIdentity1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerKeepIdentity2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerAccessibleDocument){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerAccessibleDocument1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerAccessibleDocument2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerRecruitmentFee){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerRecruitmentFee1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerRecruitmentFee2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerWrittenContract){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerWrittenContract1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerWrittenContract2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerDeductionWage){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerDeductionWage1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerDeductionWage2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerUnderstandRight){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerUnderstandRight1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerUnderstandRight2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerFamilyWage){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerFamilyWage1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerFamilyWage2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerComplaintSystem){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintSystem1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintSystem2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerComplaintStored){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintStored1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintStored2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerOweMoney){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerOweMoney1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerOweMoney2').setValue(true);
                            break;
                        }
                        switch(r.data.labWorkerBasicSupplies){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerBasicSupplies1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerBasicSupplies2').setValue(true);
                            break;
                        }
                        //Isi Form Labour Extension ======================= (End)

                        //khusus view only
                        if(thisObj.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-gridFamilyLabour-BtnAdd').setVisible(false);
                            Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-btnSave').setVisible(false);
                            
                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-btnSave').setVisible(false);
                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-MemberPhotoInput').setVisible(false);
                            Ext.getCmp('Koltiva.view.Grower.FormMainGrower-KTPPhotoInput').setVisible(false);
                        }

                        let getValueSurveyNr = Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-SurveyNr').getValue()
                        let getInfoWorkers   = r.data.labHaveWorkers
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
        function isNumber(n) { return !isNaN(parseFloat(n)) && !isNaN(n - 0) }

        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;
        //thisObj.MsgAddValidation = "Cihuy";
        
        //Cek Validasi Join Program (Begin)
        var ninid       = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-Nin').getValue();
        var joinprogram = Ext.getCmp('Koltiva.view.Grower.FormMainGrower-JoinProgram1').getValue();

        if(joinprogram == true){

            //cek value NIN ID number
            if(ninid.length < 16){

                thisObj.AddValidation = false;
                ArrMsg.push("Minimal National Identification Number 16 Digit");
            }
        }
        //Cek Validasi Join Program (End)                     

        //Cek Umur ================================================== (Begin)
        var DateBirth = Ext.Date.format(Ext.getCmp('Koltiva.view.Grower.FormMainGrower-DateOfBirth').getValue(),'Y-m-d');

        var today = new Date();
        var birthDate = new Date(DateBirth);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if(age < 16){
            thisObj.AddValidation = false;
            ArrMsg.push("Minimal Age is 16 years old");
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