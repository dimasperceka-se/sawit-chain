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

Ext.define('Koltiva.view.GrowerSME.FormMainGrower' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.GrowerSME.FormMainGrower',
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
        //store yg dipakai (end)

        //panel Form Family ======================================================================== (begin)
        var storeGridFamilyLabour = Ext.create('Koltiva.store.Grower.GridMemberFamilyLabour');

        var contextMenuGridFamLab = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridFamilyLabour').getSelectionModel().getSelection()[0];

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
                    var sm = Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridFamilyLabour').getSelectionModel().getSelection()[0];

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
                    var sm = Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridFamilyLabour').getSelectionModel().getSelection()[0];

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
            id: 'Koltiva.view.GrowerSME.FormMainGrower-gridFamilyLabour',
            loadMask: true,
            selType: 'rowmodel',
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
                    id: 'Koltiva.view.GrowerSME.FormMainGrower-gridFamilyLabour-BtnAdd',
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
                    var sm = Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridLabour').getSelectionModel().getSelection()[0];

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
                    var sm = Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridLabour').getSelectionModel().getSelection()[0];

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
                    var sm = Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridLabour').getSelectionModel().getSelection()[0];

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
            frame: true,
            collapsible:true,
            margin:'0 0 20 0',
            id: 'Koltiva.view.GrowerSME.FormMainGrower-PanelLabour',
            dockedItems: [{
                xtype: 'toolbar',
                baseCls: 'bgToolbarTitlePanel',
                dock: 'top',
                items:[{
                    xtype: 'tbtext',
                    style:'font-weight:bold;text-decoration:underline;line-height:25px;',
                    text: ''
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    id:'Koltiva.view.GrowerSME.FormMainGrower-gridLabour-BtnAdd',
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
                id: 'Koltiva.view.GrowerSME.FormMainGrower-gridLabour',
                loadMask: true,
                selType: 'rowmodel',
                store: storeGridLabour,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
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
            id: 'Koltiva.view.GrowerSME.FormMainGrower-FormLabourExtension',
            fileUpload: true,
            margin:'0 0 10 0',
            items: [{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
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
                                        Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerHaveBpjs').setDisabled(false);
                                        Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabGiveInfoHealthSafety').setDisabled(false);

                                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridLabour-BtnAdd').setVisible(true);
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
                                        Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWorkerHaveBpjs').setDisabled(true);
                                        Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBpjs2').setValue(true);
                                        Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabGiveInfoHealthSafety').setDisabled(true);

                                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
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
                    },{
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Grower.FormLabourExtension-labHowManyWorker',
                        name: 'Koltiva.view.Grower.FormLabourExtension-labHowManyWorker',
                        fieldLabel: lang('How many workers do you have'),
                        labelAlign:'top',
                        labelWidth: 230,
                        disabled:true,
                        allowNegative: false                        
                    },{
                        fieldLabel: lang('Do You Workers Live on Your Plantation'),
                        labelAlign:'top',
                        labelWidth: 230,
                        xtype: 'radiogroup',
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
                    },{
                        fieldLabel: lang('Do Workers Have Safe and Adequate Housing, Including Toilets and Drinking Water'),
                        labelWidth: 230,
                        xtype: 'radiogroup',
                        labelAlign:'top',
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
                    },{
                        fieldLabel: lang('Do you keep the identity documents, e.g. passport from the workers on your plantation'),
                        labelWidth: 230,
                        xtype: 'radiogroup',
                        labelAlign:'top',
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
                    },{
                        fieldLabel: lang('Are the identity documents 24h accessible by the workers'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
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
                    },{
                        fieldLabel: lang('Did workers had to pay a recruitment fee to work on your plantation'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
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
                    },{
                        fieldLabel: lang('Does your worker have a written contract/ work agreement'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
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
                    },{
                        fieldLabel: lang('Do workers understand their rights & obligations in accordance with the work agreement/contract'),
                        labelWidth: 230,
                        labelAlign:'top',
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
                    },{
                        fieldLabel: lang('Are there any deductions of the wage if workers make mistakes while working'),
                        labelWidth: 230,
                        labelAlign:'top',
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
                    },{
                        fieldLabel: lang('Does your worker employ their family members/ relatives whose wages are paid by the workers themselves'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
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
                    },{
                        fieldLabel: lang('Is there a complaint system in place, where workers can file complaints'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
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
                    },{
                        fieldLabel: lang('Are complaints stored for 2 years'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
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
                    },{
                        fieldLabel: lang('Do workers owe money to you'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
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
                    },{
                        fieldLabel: lang('Do workers have access to basic supplies of first aid'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
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
                    },{
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
                    },{
                        fieldLabel: lang('Who buys the PPE'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
                        columns: 2,
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
                    },{
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
                    },{
                        xtype: 'textfield',
                        fieldLabel: lang('What kind of accident'),
                        labelWidth: 230,
                        labelAlign:'top',
                        disabled:true,
                        name: 'Koltiva.view.Grower.FormLabourExtension-labWhatAccident',
                        id: 'Koltiva.view.Grower.FormLabourExtension-labWhatAccident',
                    },{
                        fieldLabel: lang('Do your workers have BPJS'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerHaveBpjs',
                        disabled:true,
                        items:[{
                            boxLabel: lang('Yes'),
                            name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBpjs',
                            inputValue: '1',
                            id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBpjs1',
                            listeners:{
                                change: function(){
                                    if(this.checked == true){
                                        Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWhoPayBpjs').setDisabled(false);
                                    }else{
                                        Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-RowlabWhoPayBpjs').setDisabled(true);
                                    }
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('No'),
                            name: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBpjs',
                            inputValue: '2',
                            id: 'Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBpjs2',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    },{
                        fieldLabel: lang('Who pays the BPJS'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWhoPayBpjs',
                        disabled:true,
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
                    },{
                        fieldLabel: lang('Who gives your workers an explanation of Occupational Health and Safety (K3)'),
                        labelWidth: 230,
                        labelAlign:'top',
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabGiveInfoHealthSafety',
                        disabled:true,
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
                                params: {MemberID: Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberID').getValue()},
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
            id: 'Koltiva.view.GrowerSME.FormMainGrower-FormBasicData',
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
                        id: 'Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tab',
                        items:[{
                            xtype: 'panel',
                            title: lang('Farmer Data'),
                            id: 'Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tabFarmerData',
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
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-MemberPhoto',
                                            height:'200px',
                                            src: m_api_base_url + '/assets/images/farmer-default.png'
                                        },{
                                            xtype: 'fileuploadfield',
                                            fieldLabel: lang('Photo'),
                                            labelAlign: 'top',
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-MemberPhotoInput',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-MemberPhotoInput',
                                            buttonText: 'Browse',
                                            listeners: {
                                                'change': function (fb, v) {
                                                    objPanelBasicData.submit({
                                                        url: m_api + '/grower/image_member',
                                                        clientValidation: false,
                                                        params: {
                                                            opsiDisplay: thisObj.opsiDisplay,
                                                            MemberID: Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberID').getValue()
                                                        },
                                                        waitMsg: 'Sending Photo...',
                                                        success: function (fp, o) {
                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberPhoto').setSrc(o.result.file);
                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberPhotoOld').setValue(o.result.filepath);
                                                        }
                                                    });
                                                }
                                            }
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-MemberPhotoOld',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-MemberPhotoOld',
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
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-MemberID',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-MemberID'
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-MemberDisplayID',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-MemberDisplayID',
                                            fieldLabel: lang('Farmer ID'),
                                            readOnly:true
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-ExtID',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-ExtID',
                                            fieldLabel: lang('External ID'),
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-Fullname',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-Fullname',
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
                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-Gender',
                                                inputValue: 'm',
                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-GenderMale',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Female'),
                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-Gender',
                                                inputValue: 'f',
                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-GenderFemale',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }, {
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-DateCollection',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-DateCollection',
                                            fieldLabel: lang('Date Collection'),
                                            //labelWidth: 150,
                                            style: 'margin-bottom:15px;',
                                            allowBlank: false,
                                            format: 'Y-m-d H:i:s'
                                        }, {
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-DateLastVerfication',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-DateLastVerfication',
                                            fieldLabel: lang('Date of Last Verification'),
                                            //labelWidth: 150,
                                            style: 'margin-bottom:15px;',
                                            allowBlank: false,
                                            format: 'Y-m-d'
                                        },{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-SurveyNr',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-SurveyNr',
                                            store: cmb_survey_nr,
                                            fieldLabel: lang('Survey Nr'),
                                            allowBlank: false,
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    if(nv == 0){
                                                        Ext.getCmp('Koltiva.view.Grower.MainForm-FormBasicData-SectionFarmerStatus').setDisabled(true);
                                                        Ext.getCmp('Koltiva.view.Grower.MainForm-FormBasicData-SectionJoinStatus').setDisabled(true);
                                                    }else{
                                                        Ext.getCmp('Koltiva.view.Grower.MainForm-FormBasicData-SectionFarmerStatus').setDisabled(false);
                                                        Ext.getCmp('Koltiva.view.Grower.MainForm-FormBasicData-SectionJoinStatus').setDisabled(false);
                                                    }
                                                }
                                            }
                                        }, {
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-DateStart',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-DateStart',
                                            fieldLabel: lang('Date Start'),
                                            //labelWidth: 150,
                                            style: 'margin-bottom:15px;',
                                            allowBlank: false,
                                            format: 'Y-m-d'
                                        }, {
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.GrowerSME.FormMainGrower-DateEnd',
                                            name: 'Koltiva.view.GrowerSME.FormMainGrower-DateEnd',
                                            fieldLabel: lang('Date End'),
                                            //labelWidth: 150,
                                            style: 'margin-bottom:15px;',
                                            allowBlank: false,
                                            format: 'Y-m-d'
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
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionGeneralData',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-DateOfBirth',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-DateOfBirth',
                                                    fieldLabel: lang('Date of Birth'),
                                                    //labelWidth: 150,
                                                    labelAlign: 'top',
                                                    //allowBlank: false, temporary disable for kristina
                                                    format: 'Y-m-d'
                                                }, {
                                                    html: '<div style="height:10px;">&nbsp;</div>'
                                                }, {
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-MaritalStatus',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-MaritalStatus',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-Education',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-Education',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-DealerAssign',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-DealerAssign',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-Nin',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-Nin',
                                                    fieldLabel: lang('National Identification Number'),
                                                    //labelWidth: 180,
                                                    //allowBlank: false, temporary disable for kristina
                                                    labelAlign: 'top'
                                                }, {
                                                    html: '<div style="height:10px;">&nbsp;</div>'
                                                },{
                                                    xtype: 'image',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-KTPPhoto',
                                                    height:'200px',
                                                    src: m_api_base_url + '/assets/images/ktp-default.png'
                                                },{
                                                    xtype: 'fileuploadfield',
                                                    fieldLabel: lang('National Identification File'),
                                                    labelAlign: 'top',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-KTPPhotoInput',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-KTPPhotoInput',
                                                    buttonText: 'Browse',
                                                    listeners: {
                                                        'change': function (fb, v) {
                                                            objPanelBasicData.submit({
                                                                url: m_api + '/grower/image_KTP',
                                                                clientValidation: false,
                                                                params: {
                                                                    opsiDisplay: thisObj.opsiDisplay,
                                                                    MemberID: Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberID').getValue()
                                                                },
                                                                waitMsg: 'Sending Photo...',
                                                                success: function (fp, o) {
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-KTPPhoto').setSrc(o.result.file);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-KTPPhotoOld').setValue(o.result.filepath);
                                                                }
                                                            });
                                                        }
                                                    }
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-KTPPhotoOld',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-KTPPhotoOld',
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
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-HaveBankAccount',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-HaveBankAccount1',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('ReceiveTransferPanel').setDisabled(false);                                                                  
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-BankHolderName').setDisabled(false);                                                                  
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-BankAccNumber').setDisabled(false);  
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-BankID').setDisabled(false); 
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-BankClientID').setDisabled(false);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-BankBranchName').setDisabled(false);    
                                                                    Ext.getCmp('AccountHolderPanel').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('ReceiveTransferPanel').setDisabled(true);                                  
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-BankHolderName').setDisabled(true);                                                                  
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-BankAccNumber').setDisabled(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-BankID').setDisabled(true);  
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-BankClientID').setDisabled(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-BankBranchName').setDisabled(true);    
                                                                    Ext.getCmp('AccountHolderPanel').setDisabled(true);     
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-HaveBankAccount',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-HaveBankAccount2',
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
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveBankTransfer',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveBankTransfer1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveBankTransfer',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveBankTransfer2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-BankHolderName',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-BankHolderName',
                                                    fieldLabel: lang('Bank Holder Name'),
                                                    disabled:true,
                                                    labelAlign: 'top',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-BankAccNumber',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-BankAccNumber',
                                                    fieldLabel: lang('Bank Account Number'),
                                                    disabled:true,
                                                    labelAlign: 'top',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-BankID',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-BankID',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-BankClientID',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-BankClientID',
                                                    fieldLabel: lang('Bank Client ID'),
                                                    disabled:true,
                                                    labelAlign: 'top',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-BankBranchName',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-BankBranchName',
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
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-AccountHolderRelation',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-AccountHolderRelation1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('Spouse'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-AccountHolderRelation',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-AccountHolderRelation2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('Child'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-AccountHolderRelation',
                                                        inputValue: '3',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-AccountHolderRelation3',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('Other Household Member'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-AccountHolderRelation',
                                                        inputValue: '4',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-AccountHolderRelation4',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-Province',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-Province',
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
                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-District').setValue('');
                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Subdistrict').setValue('');
                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Village').setValue('');
            
                                                            //load store
                                                            cmb_farmer_group.setStoreVar({ProvinceID:nv});
                                                            cmb_farmer_group.load();
                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FarmerGroupID').setValue('');
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-District',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-District',
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
                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Subdistrict').setValue('');
                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Village').setValue('');
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-Subdistrict',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-Subdistrict',
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
                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Village').setValue('');
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-Village',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-Village',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-Address',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-Address',
                                                    height: 65
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-RtRw',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-RtRw',
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
                                    //                 id: 'Koltiva.view.GrowerSME.FormMainGrower-isCertified',
                                    //                 name: 'Koltiva.view.GrowerSME.FormMainGrower-isCertified',
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
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationRSPO',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationRSPO',
                                    //                             listeners:{
                                    //                             }
                                    //                         }]
                                    //                     },{
                                    //                         columnWidth: 0.25,
                                    //                         border: false,
                                    //                         defaultType: 'checkboxfield',
                                    //                         items:[{
                                    //                             boxLabel: lang('ISCC'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationISCC',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationISCC',
                                    //                             listeners:{
                                    //                             }
                                    //                         }]
                                    //                     },{
                                    //                         columnWidth: 0.25,
                                    //                         border: false,
                                    //                         defaultType: 'checkboxfield',
                                    //                         items:[{
                                    //                             boxLabel: lang('ISPO'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationISPO',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationISPO',
                                    //                             listeners:{
                                    //                             }
                                    //                         }]
                                    //                     },{
                                    //                         columnWidth: 0.25,
                                    //                         border: false,
                                    //                         defaultType: 'checkboxfield',
                                    //                         items:[{
                                    //                             boxLabel: lang('MSPO'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationMSPO',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationMSPO',
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
                                    //                     name: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveTraining',
                                    //                     inputValue: '1',
                                    //                     id: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveTraining1',
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
                                    //                     name: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveTraining',
                                    //                     inputValue: '2',
                                    //                     id: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveTraining2',
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
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceGovernment',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceGovernment',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('NGO'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceNGO',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceNGO',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Mill'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceMill',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceMill',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Other Private Sector Organization'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourcePrivateOrg',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourcePrivateOrg',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Others'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceOthers',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceOthers',
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
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeFinancial',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeFinancial',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Good Agriculuture Practice'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeGoodAgriculture',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeGoodAgriculture',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Human Rights and Worker Rights'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeHumanRights',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeHumanRights',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Best Management of Pesticides'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeManagementPesticides',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeManagementPesticides',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('Fire Fighting'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeFireFighting',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeFireFighting',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('HCV and HCS'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeHCVHCS',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeHCVHCS',
                                    //                             listeners:{
                                    //                             }
                                    //                         },{
                                    //                             boxLabel: lang('P&C RSPO Independent Smallholder Standard'),
                                    //                             name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeRSPOIndependent',
                                    //                             inputValue: '1',
                                    //                             id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeRSPOIndependent',
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
                                        disabled:true,
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
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-StatusMember',
                                                        inputValue: 'Active',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-StatusMemberActive',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-InactiveReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-InactiveReason').setValue('');
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReason').setReadOnly(true);                                                                    
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReason').setValue('');                                                                    
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-InactiveReason').setReadOnly(false);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReason').setReadOnly(false);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('Inactive'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-StatusMember',
                                                        inputValue: 'Inactive',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-StatusMemberInactive',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-InactiveReason').setReadOnly(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-InactiveReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-InactiveReason').setValue('');
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-InactiveReason',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-InactiveReason',
                                                    store: cmb_inactive_reason,
                                                    fieldLabel: lang('Inactive Reason'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners:{
                                                        change:function(cb, nv, ov){
                                                            if(nv == 5){
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-InactiveReasonText').setVisible(true);
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReason').setReadOnly(true);
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReason').setValue('');
                                                            }else{
                                                                if(nv != 3){
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReason').setValue('');
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReason').setReadOnly(false);
                                                                }
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-InactiveReasonText').setVisible(false);
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-InactiveReasonText',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-InactiveReasonText',
                                                    fieldLabel: lang('Other Inactive Reason'),
                                                    labelAlign:'top',
                                                    hidden:true
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-StoppedReason',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-StoppedReason',
                                                    store: cmb_stopped_reason,
                                                    fieldLabel: lang('Stopped Farming Reason'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners:{
                                                        change:function(cb, nv, ov){
                                                            if(nv == 6){
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReasonText').setVisible(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReasonText').setVisible(false);
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-StoppedReasonText').setValue('');
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-StoppedReasonText',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-StoppedReasonText',
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
                                        disabled:true,
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
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-JoinProgram',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-JoinProgram1',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReason').setValue('');                                                                 
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReason').setReadOnly(false);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-JoinProgram',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-JoinProgram2',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReason').setReadOnly(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReason').setValue('');
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReason',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReason',
                                                    store: cmb_not_join_reason,
                                                    fieldLabel: lang('Not Join Reason'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners:{
                                                        change:function(cb, nv, ov){
                                                            if(nv == 4){
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReasonText').setVisible(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReasonText').setVisible(false);
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReasonText').setValue('');
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReasonText',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReasonText',
                                                    fieldLabel: lang('Other Not Join Reason'),
                                                    labelWidth: 230,
                                                    labelAlign:'top',
                                                    hidden:true
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textarea',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-JoinComment',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-JoinComment',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-HandphoneType',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-HandphoneType',
                                                    store: cmb_handphone_type,
                                                    fieldLabel: lang('Handphone Type'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners: {
                                                        change: function(cb, nv, ov) {
                                                            if(nv == '3'){
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Handphone').setValue('');
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Handphone').setReadOnly(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Handphone').setReadOnly(false);
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-Handphone',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-Handphone',
                                                    fieldLabel: lang('Handphone'),
                                                    labelAlign:'top'
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
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-AccessToSmartphone',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-AccessToSmartphone1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-AccessToSmartphone',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-AccessToSmartphone2',
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
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-inGroup',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-inGroupYes',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    //load store
                                                                    cmb_farmer_group.setStoreVar({DistrictID:Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-District').getValue()});
                                                                    cmb_farmer_group.load();
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FarmerGroupID').setDisabled(false);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-groupName').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FarmerGroupID').setDisabled(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-groupName').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-inGroup',
                                                        inputValue: '0',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-inGroupNo',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-FarmerGroupID',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-FarmerGroupID',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-groupName',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-groupName',
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
                                                    id:'Koltiva.view.GrowerSME.FormMainGrower-RowinGapoktan',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-inGapoktan',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-inGapoktanYes',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-GapoktanName').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-GapoktanName').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-inGapoktan',
                                                        inputValue: '0',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-inGapoktanNo',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-GapoktanName',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-GapoktanName',
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
                                                    id:'Koltiva.view.GrowerSME.FormMainGrower-RowinCoop',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-inCoop',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-inCoopYes',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-CoopName').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-CoopName').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-inCoop',
                                                        inputValue: '0',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-inCoopNo',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-CoopName',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-CoopName',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-HowManyPlot',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-HowManyPlot',
                                                    fieldLabel: lang('How many oil palm plots do you have'),
                                                    labelAlign: 'top',
                                                    allowNegative: false
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-PlotTotalHectare',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-PlotTotalHectare',
                                                    fieldLabel: lang('Total hectare'),
                                                    readOnly: true,
                                                    labelAlign: 'top',
                                                    allowNegative: false
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
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-WorkInPlot',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-WorkInPlot1',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-RowUseAPD').setDisabled(false);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-RowHadAccident').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-RowUseAPD').setDisabled(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-RowHadAccident').setDisabled(true);
                                                                }
            
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-WorkInPlot',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-WorkInPlot2',
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
                                                    id:'Koltiva.view.GrowerSME.FormMainGrower-RowUseAPD',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-UseAPD',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-UseAPD1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-UseAPD',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-UseAPD2',
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
                                                    id:'Koltiva.view.GrowerSME.FormMainGrower-RowHadAccident',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-HadAccident',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-HadAccident1',
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
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-HadAccident',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-HadAccident2',
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
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-AccidentKnife',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-AccidentKnife',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Hit by a fruit'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-AccidentHitbyFruit',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-AccidentHitbyFruit',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Contamination from chemical liquid from pesticide/fertilizer/herbicide'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-AccidentContimination',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-AccidentContimination',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Other'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-AccidentOther',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-AccidentOther',
                                                                listeners:{
                                                                    change:function(){
                                                                        if(this.checked == true){
                                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-AccidentOtherText').setVisible(true);
                                                                        }else{
                                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-AccidentOtherText').setVisible(false);
                                                                        }
                                                                    }
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth:1,
                                                            border:false,
                                                            items:[{
                                                                xtype: 'textfield',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-AccidentOtherText',
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-AccidentOtherText',
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
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-HaveBPJS',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-HaveBPJS',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('BPJS Employees Social Security System'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-HaveBPJSKetenagakerjaan',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-HaveBPJSKetenagakerjaan',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('No'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-HaveBPJSNo',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-HaveBPJSNo',
                                                                listeners:{
                                                                }
                                                            }]
                                                        }]
                                                    }]
                                                },{
                                                    xtype: 'textfield',
                                                    hidden:true,
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-PhotoDesc',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-PhotoDesc',
                                                    emptyText: lang('Notes on picture of visit')
                                                },{
                                                    html:'<div></div>',
                                                },
                                                {
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-CategoryFarmer',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-CategoryFarmer',
                                                    store: cmb_farmer_category,
                                                    fieldLabel: lang('Farmer Category'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    hidden:true,
                                                    valueField: 'id'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-TotalProductionArea',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-TotalProductionArea',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-MembershipStatus',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-MembershipStatus',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-SupplybaseType',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-SupplybaseType',
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
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-frComment',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-frComment',
                                                    fieldLabel: lang('Comment'),
                                                    labelAlign: 'top'
                                                },{
                                                    html: '<div></div><div></div><div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-Enumerator',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-Enumerator',
                                                    fieldLabel: lang('Enumerator'),
                                                    labelAlign: 'top',
                                                    readOnly: true
                                                },{
                                                    html: '<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-ModifiedBy',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-ModifiedBy',
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
                            id: 'Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tabFarmerCertification',
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
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-isCertified',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-isCertified',
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
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    items:[{
                                                        columnWidth: 1,
                                                        layout:'form',
                                                        items:[{
                                                            xtype:'label',
                                                            cls: 'x-form-item-label',
                                                            text: lang('Certification Standard')
                                                        }]
                                                    }]
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    style:'margin-top:-20px;padding-top:0px;',
                                                    items:[{
                                                        layout:'column',
                                                        columnWidth: 1,
                                                        id:'CertificationPanel',
                                                        disabled:true,
                                                        style:'margin-top:-7px;padding-top:0px;',
                                                        items:[{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('RSPO'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationRSPO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationRSPO',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('ISCC'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationISCC',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationISCC',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('ISPO'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationISPO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationISPO',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('MSPO'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationMSPO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationMSPO',
                                                                listeners:{
                                                                }
                                                            }]
                                                        }]
                                                    }]
                                                },{
                                                    fieldLabel: lang('Did You Receive Any Trainings to Improve Your Agriculture or Business Practices'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveTraining',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveTraining1',
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
                                                        name: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveTraining',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerSME.FormMainGrower-ReceiveTraining2',
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
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    items:[{
                                                        columnWidth: 1,
                                                        layout:'form',
                                                        items:[{
                                                            xtype:'label',
                                                            cls: 'x-form-item-label',
                                                            text: lang('From Whom Did You Received Training')
                                                        }]
                                                    }]
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    style:'margin-top:-20px;padding-top:0px;',
                                                    items:[{
                                                        layout:'column',
                                                        columnWidth: 1,
                                                        id:'CertificationSourcePanel',
                                                        disabled:true,
                                                        style:'margin-top:-7px;padding-top:0px;',
                                                        items:[{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Government Extention Officer'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceGovernment',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceGovernment',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('NGO'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceNGO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceNGO',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Mill'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceMill',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceMill',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Other Private Sector Organization'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourcePrivateOrg',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourcePrivateOrg',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Others'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceOthers',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceOthers',
                                                                listeners:{
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
                                                            text: lang('What Type of Trainings Did You Receive')
                                                        }]
                                                    }]
                                                },{
                                                    layout: 'column',
                                                    border: false,
                                                    style:'margin-top:-20px;padding-top:0px;',
                                                    items:[{
                                                        layout:'column',
                                                        columnWidth: 1,
                                                        id:'CertificationTypePanel',
                                                        disabled:true,
                                                        style:'margin-top:-7px;padding-top:0px;',
                                                        items:[{
                                                            columnWidth: 1,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('Financial and Farm Business Operations'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeFinancial',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeFinancial',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Good Agriculuture Practice'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeGoodAgriculture',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeGoodAgriculture',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Human Rights and Worker Rights'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeHumanRights',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeHumanRights',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Best Management of Pesticides'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeManagementPesticides',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeManagementPesticides',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Fire Fighting'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeFireFighting',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeFireFighting',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('HCV and HCS'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeHCVHCS',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeHCVHCS',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('P&C RSPO Independent Smallholder Standard'),
                                                                name: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeRSPOIndependent',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeRSPOIndependent',
                                                                listeners:{
                                                                }
                                                            }]
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
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesParticipate',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesParticipate',
                                                    fieldLabel: lang('Willingness  to participate in independent smallholder support programs, including data collection and processing and certification standards'),
                                                    labelAlign: 'top'
                                                },{
                                                    xtype: 'image',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesSignature',
                                                    height:'200px',
                                                    src: m_api_base_url + '/assets/images/signature.png'
                                                },{
                                                    xtype: 'fileuploadfield',
                                                    fieldLabel: lang('Signature'),
                                                    labelAlign: 'top',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesSignatureInput',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesSignatureInput',
                                                    buttonText: 'Browse',
                                                    listeners: {
                                                        'change': function (fb, v) {
                                                            objPanelBasicData.submit({
                                                                url: m_api + '/grower/image_member',
                                                                clientValidation: false,
                                                                params: {
                                                                    opsiDisplay: thisObj.opsiDisplay,
                                                                    MemberID: Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberID').getValue()
                                                                },
                                                                waitMsg: 'Sending Photo...',
                                                                success: function (fp, o) {
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-WillingnesSignature').setSrc(o.result.file);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-WillingnesSignatureOld').setValue(o.result.filepath);
                                                                }
                                                            });
                                                        }
                                                    }
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesSignatureOld',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesSignatureOld',
                                                    inputType: 'hidden'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesCommit',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesCommit',
                                                    fieldLabel: lang('Willingness to commit to the RSPO certification standard'),
                                                    labelAlign: 'top'
                                                },{
                                                    xtype: 'image',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesCommitSignature',
                                                    height:'200px',
                                                    src: m_api_base_url + '/assets/images/signature.png'
                                                },{
                                                    xtype: 'fileuploadfield',
                                                    fieldLabel: lang('Signature'),
                                                    labelAlign: 'top',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesCommitSignatureInput',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesCommitSignatureInput',
                                                    buttonText: 'Browse',
                                                    listeners: {
                                                        'change': function (fb, v) {
                                                            objPanelBasicData.submit({
                                                                url: m_api + '/grower/image_member',
                                                                clientValidation: false,
                                                                params: {
                                                                    opsiDisplay: thisObj.opsiDisplay,
                                                                    MemberID: Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberID').getValue()
                                                                },
                                                                waitMsg: 'Sending Photo...',
                                                                success: function (fp, o) {
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-WillingnesCommitSignature').setSrc(o.result.file);
                                                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-WillingnesCommitSignatureOld').setValue(o.result.filepath);
                                                                }
                                                            });
                                                        }
                                                    }
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesCommitSignatureOld',
                                                    name: 'Koltiva.view.GrowerSME.FormMainGrower-WillingnesCommitSignatureOld',
                                                    inputType: 'hidden'
                                                }]
                                            }]
                                        }]
                                    }]
                                }]
                            }]
                        },{
                            xtype: 'panel',
                            title: lang('Farmer\'s Family'),
                            id: 'Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tabFarmerFamily',
                            items:[{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 1,
                                    layout:'form',
                                    style:'padding-right: 4px;',
                                    items:[
                                        objPanelFamily
                                    ]
                                }]
                            }]
                        },{
                            xtype: 'panel',
                            title: lang('Farmer\'s Labour'),
                            id: 'Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tabFarmerLabour',
                            items:[{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 1,
                                    layout:'form',
                                    style:'padding: 0',
                                    items:[
                                        objFormPanelLabour,
                                        objPanelLabour
                                    ]
                                }]
                            }]
                        }],
                        listeners: {
                            'tabchange': function (tabPanel, tab) {
                                //console.log(tabPanel.id + ' ' + tab.id);
                                switch(tab.id){
                                    case 'Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tabFarmerFamily':
                                    case 'Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tabFarmerLabour':
                                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-btnSave').setVisible(false);
                                    break;
                                    default:
                                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-btnSave').setVisible(true);
                                    break;
                                }
                            }
                        }
                    }]
                }]
            }],
            buttons: [{
                text: lang('Save'),
                id: 'Koltiva.view.GrowerSME.FormMainGrower-btnSave',
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
                                url: m_api + '/grower/member_sme',
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
    
                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower').destroy(); //destory current view
                                    //create object View untuk FormMainGrower
                                    if(Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower') == undefined){
                                        var FormMainGrower = Ext.create('Koltiva.view.GrowerSME.FormMainGrower', {
                                            opsiDisplay: 'update',
                                            formVar: {
                                                MemberID: o.result.MemberIDInc,
                                                PartnerSurvey: o.result.PartnerSurvey
                                            }
                                        });
                                    }else{
                                        //destroy, create ulang
                                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower').destroy();
                                        var FormMainGrower = Ext.create('Koltiva.view.GrowerSME.FormMainGrower', {
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
            id: 'Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-RowEdit',
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
                    var sm = Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-gridOtherLand').getSelectionModel().getSelection()[0];
                    //console.log(sm);

                    //get last row from store
                    var storeGridOtherLand = Ext.data.StoreManager.lookup('Koltiva.store.Grower.GridMemberOtherLand');
                    var lastRow = storeGridOtherLand.getAt(storeGridOtherLand.getCount()-1);
                    //console.log(lastRow);

                    if(sm.data.MemOtherID == lastRow.data.MemOtherID){
                        var heightGridNow = Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-gridOtherLand').getHeight();
                        heightGridNow = heightGridNow + 55;
                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-gridOtherLand').setHeight(heightGridNow);
                    }

                    olRowEditing.cancelEdit();
                    olRowEditing.startEdit(sm.index, 0);
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-gridOtherLand').getSelectionModel().getSelection()[0];

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
            id: 'Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand',
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
                    id: 'Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-btnAdd',
                    hidden: m_act_add,
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
                id: 'Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-gridOtherLand',
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
                        id: 'Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-gridOtherLand-reditCommodity',
                        allowBlank: false
                    }
                },{
                    text: lang('Size (ha)'),
                    dataIndex: 'GardenHa',
                    flex: 1,
                    editor: {
                        xtype: 'numericfield',
                        id: 'Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-gridOtherLand-reditGardenHa',
                        allowNegative: false,
                        minValue: 0
                    }
                },{
                    text: lang('Remark'),
                    dataIndex: 'Remark',
                    flex: 2,
                    editor:{
                        xtype: 'textfield',
                        id: 'Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-gridOtherLand-reditRemark'
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
                            var Commodity = Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-PanelOtherLand-gridOtherLand-reditCommodity').getValue();
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
        //================================================ PROSES PANEL (End)   ==============================================================//

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id: 'Koltiva.view.GrowerSME.FormMainGrower-labelInfoInsert',
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
                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower').destroy(); //destory current view
                        if(Ext.getCmp('Koltiva.view.GrowerSME.GridMainGrower-MainPanel') == undefined){
                            var GridMainGrower = Ext.create('Koltiva.view.GrowerSME.GridMainGrower');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.GrowerSME.GridMainGrower-MainPanel').destroy();
                            var GridMainGrower = Ext.create('Koltiva.view.GrowerSME.GridMainGrower');
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
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-TotalProductionArea').setVisible(false);

                //Atur Show/Hide Form soal WAGS dan bukan WAGS ==== (End)

                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-labelInfoInsert').update('<h3 style="margin:0px;padding:0px;">'+lang('Farmer Data')+'&nbsp;<span style="font-size:14px;">('+lang('Add New Farmer')+')</span></h3>');

                //form reset
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-KTPPhoto').setSrc(m_api_base_url + '/assets/images/ktp-default.png');

                //hidden tab
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tabFarmerFamily').setDisabled(true);
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tabFarmerLabour').setDisabled(true);
                //set aktif tab pertama
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tab').setActiveTab(0);

                //buka panel2
                // Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-PanelFamLab').collapse();

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

                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-TotalProductionArea').setVisible(false);                
                //hidden tab
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tabFarmerFamily').setDisabled(false);
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tabFarmerLabour').setDisabled(false);
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                //set aktif tab pertama
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FormBasicData-tab').setActiveTab(0);


                //form reset
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-KTPPhoto').setSrc(m_api_base_url + '/assets/images/ktp-default.png');

                //load data form
                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FormBasicData').getForm().load({
                    url: m_api + '/grower/member_basic_data_form_sme',
                    method: 'GET',
                    params: {
                        MemberID: this.formVar.MemberID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);

                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-ExtID').setVisible(true);
                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MembershipStatus').setVisible(false);

                        //untuk handle combo bertingkat
                        var cmb_province = Ext.data.StoreManager.lookup('store.Grower.CmbProvince');
                        var cmb_district = Ext.data.StoreManager.lookup('store.Grower.CmbDistrict');
                        var cmb_subdistrict = Ext.data.StoreManager.lookup('store.Grower.CmbSubdistrict');
                        var cmb_village = Ext.data.StoreManager.lookup('store.Grower.CmbVillage');
                        var cmb_farmer_group = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.CmbFarmerGroup');
                        cmb_province.load({
                            callback: function(records, operation, success){
                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Province').setValue(r.data.Province);
                                if (success == true) {

                                    //load combo farmer group lagi aja disini dan set nilainya jika ada
                                    cmb_farmer_group.setStoreVar({ProvinceID:r.data.Province});
                                    cmb_farmer_group.load({
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-FarmerGroupID').setValue(r.data.FarmerGroupID);
                                            }
                                        }
                                    });

                                    cmb_district.load({
                                        params: {
                                            ProvinceID: r.data.Province
                                        },
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-District').setValue(r.data.District);
                                                cmb_subdistrict.load({
                                                    params: {
                                                        DistrictID: r.data.District
                                                    },
                                                    callback: function(records, operation, success){

                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Subdistrict').setValue(r.data.Subdistrict);
                                                            cmb_village.load({
                                                                params: {
                                                                    SubdistrictID: r.data.Subdistrict
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Village').setValue(r.data.Village);
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
                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberPhotoOld').setValue(r.data.PhotoSrcPath);
                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-KTPPhotoOld').setValue(r.data.KTPSrcPath);

                        //set photo
                        if(r.data.PhotoSrc != ""){
                            var fotoUser = r.data.PhotoSrc;
                            var ktpfile = r.data.KTPSrc;
                            var angkaRand = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUser, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberPhoto').setSrc(fotoUser+'?'+angkaRand);
                                } else {
                                    if(r.data.Gender == 'f'){
                                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');
                                    }else{
                                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');
                                    }
                                }
                            });
                            checkImageExists(ktpfile, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-KTPPhoto').setSrc(ktpfile+'?'+angkaRand);
                                } else {
                                    if(r.data.Gender == 'f'){
                                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-KTPPhoto').setSrc(m_api_base_url + '/assets/images/ktp-default.png');
                                    }else{
                                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-KTPPhoto').setSrc(m_api_base_url + '/assets/images/ktp-default.png');
                                    }
                                }
                            });
                        }

                        //buka panel2
                        // Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-PanelFamLab').expand();
                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-PanelLabour').expand();

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

                        Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-labelInfoInsert').update('<h3 style="margin:0px;padding:0px;">'+Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberDisplayID').getValue()+' - '+Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-Fullname').getValue()+'</h3>');


                        //Isi Form Labour Extension ======================= (Begin)
                        switch(r.data.labHaveWorkers){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHaveWorkers1').setValue(true);
                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridLabour-BtnAdd').setVisible(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHaveWorkers2').setValue(true);
                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                            break;
                            default:
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHaveWorkers2').setValue(true);
                                Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
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
                        Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWhatAccident').setValue(r.data.labWhatAccident);
                        switch(r.data.labWorkerHaveBpjs){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBpjs1').setValue(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBpjs2').setValue(true);
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
                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-gridFamilyLabour-BtnAdd').setVisible(false);
                            Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-btnSave').setVisible(false);
                            
                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-btnSave').setVisible(false);
                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-MemberPhotoInput').setVisible(false);
                            Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-KTPPhotoInput').setVisible(false);
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
        var DateBirth = Ext.Date.format(Ext.getCmp('Koltiva.view.GrowerSME.FormMainGrower-DateOfBirth').getValue(),'Y-m-d');

        var today = new Date();
        var birthDate = new Date(DateBirth);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if(age <= 16){
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