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

Ext.define('Koltiva.view.GrowerWAGS.FormMainGrower' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.GrowerWAGS.FormMainGrower',
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
                    var sm = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridFamilyLabour').getSelectionModel().getSelection()[0];

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
                    var sm = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridFamilyLabour').getSelectionModel().getSelection()[0];

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
                    var sm = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridFamilyLabour').getSelectionModel().getSelection()[0];

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
            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-gridFamilyLabour',
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
                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-gridFamilyLabour-BtnAdd',
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
                    var sm = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour').getSelectionModel().getSelection()[0];

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
                    var sm = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour').getSelectionModel().getSelection()[0];

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
                    var sm = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour').getSelectionModel().getSelection()[0];

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
            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-PanelLabour',
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
                    id:'Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour-BtnAdd',
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
                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour',
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
            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-FormLabourExtension',
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

                                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour-BtnAdd').setVisible(true);
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

                                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
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
                        labelWidth: 230,
                        disabled:true,
                        allowNegative: false                        
                    },{
                        fieldLabel: lang('Do You Workers Live on Your Plantation'),
                        labelWidth: 230,
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerLivePlantation',
                        disabled:true,
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
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerSafeHouse',
                        disabled:true,
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
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerKeepIdentity',
                        disabled:true,
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
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerAccessibleDocument',
                        disabled:true,
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
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerRecruitmentFee',
                        disabled:true,
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
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerWrittenContract',
                        disabled:true,
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
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerUnderstandRight',
                        disabled:true,
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
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerDeductionWage',
                        disabled:true,
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
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerFamilyWage',
                        disabled:true,
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
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerComplaintSystem',
                        disabled:true,
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
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerComplaintStored',
                        disabled:true,
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
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerOweMoney',
                        disabled:true,
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
                        xtype: 'radiogroup',
                        columns: 2,
                        id: 'Koltiva.view.Grower.FormLabourExtension-RowlabWorkerBasicSupplies',
                        disabled:true,
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
                        disabled:true,
                        name: 'Koltiva.view.Grower.FormLabourExtension-labWhatAccident',
                        id: 'Koltiva.view.Grower.FormLabourExtension-labWhatAccident',
                    },{
                        fieldLabel: lang('Do your workers have BPJS'),
                        labelWidth: 230,
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
                                params: {MemberID: Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberID').getValue()},
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
        
        //label if user Wags
        if(m_user_partnerid == 14)
        {
            var Fullname = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Fullname');     
            Fullname = 'Member Name';

            var ExtID = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-ExtID');  
            ExtID = 'WAGS Member ID';  

            var Plantation = Ext.getCmp('Koltiva.view.Grower.MainForm-FormBasicData-SectionFarmerStatus');
            Plantation = 'Do you work or manage the farm yourself?';

        } else {
            var Fullname = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Fullname');     
            Fullname = 'Farmer name';

            var ExtID = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-ExtID');  
            ExtID = 'External ID';  
            
            var Plantation = Ext.getCmp('Koltiva.view.Grower.MainForm-FormBasicData-SectionFarmerStatus');
            Plantation = 'Do you work in plantation yourself?';
        }

        //================================================== panel Form Basic Data (BEGIN) ==========================================//
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Basic Data'),
            frame: true,
            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData',
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
                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tab',
                        items:[{
                            xtype: 'panel',
                            title: lang('Farmer Data'),
                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tabFarmerData',
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
                                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhoto',
                                            height:'200px',
                                            src: m_api_base_url + '/assets/images/farmer-default.png'
                                        },{
                                            xtype: 'fileuploadfield',
                                            fieldLabel: lang('Photo'),
                                            labelAlign: 'top',
                                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhotoInput',
                                            name: 'Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhotoInput',
                                            buttonText: 'Browse',
                                            listeners: {
                                                'change': function (fb, v) {
                                                    objPanelBasicData.submit({
                                                        url: m_api + '/grower/image_member_wags',
                                                        clientValidation: false,
                                                        params: {
                                                            opsiDisplay: thisObj.opsiDisplay,
                                                            MemberID: Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberID').getValue()
                                                        },
                                                        waitMsg: 'Sending Photo...',
                                                        success: function (fp, o) {
                                                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhoto').setSrc(o.result.file);
                                                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhotoOld').setValue(o.result.filepath);
                                                        }
                                                    });
                                                }
                                            }
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhotoOld',
                                            name: 'Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhotoOld',
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
                                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-MemberID',
                                            name: 'Koltiva.view.GrowerWAGS.FormMainGrower-MemberID'
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-Fullname',
                                            name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Fullname',
                                            fieldLabel: Fullname,
                                            allowBlank: false
                                        }, {
                                            fieldLabel: lang('Gender'),
                                            xtype: 'radiogroup',
                                            allowBlank: false,
                                            msgTarget: 'side',
                                            columns: 2,
                                            items:[{
                                                boxLabel: lang('Male'),
                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Gender',
                                                inputValue: 'm',
                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-GenderMale',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Female'),
                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Gender',
                                                inputValue: 'f',
                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-GenderFemale',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }, {
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-DateCollection',
                                            name: 'Koltiva.view.GrowerWAGS.FormMainGrower-DateCollection',
                                            fieldLabel: lang('Date Collection'),
                                            //labelWidth: 150,
                                            style: 'margin-bottom:15px;',
                                            allowBlank: false,
                                            format: 'Y-m-d H:i:s'
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-MemberDisplayID',
                                            name: 'Koltiva.view.GrowerWAGS.FormMainGrower-MemberDisplayID',
                                            fieldLabel: lang('Farmer ID'),
                                            readOnly:true
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-ExtID',
                                            name: 'Koltiva.view.GrowerWAGS.FormMainGrower-ExtID',
                                            fieldLabel: ExtID,
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-DateOfBirth',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-DateOfBirth',
                                                    fieldLabel: lang('Date of Birth'),
                                                    //labelWidth: 150,
                                                    labelAlign: 'top',
                                                    //allowBlank: false, temporary disable for kristina
                                                    format: 'Y-m-d'
                                                }, {
                                                    html: '<div style="height:10px;">&nbsp;</div>'
                                                }, {
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-Nin',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Nin',
                                                    fieldLabel: lang('National Identification Number'),
                                                    //labelWidth: 180,
                                                    //allowBlank: false, temporary disable for kristina
                                                    labelAlign: 'top'
                                                }, {
                                                    html: '<div style="height:10px;">&nbsp;</div>'
                                                }, {
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-MaritalStatus',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-MaritalStatus',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-Education',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Education',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-DealerAssign',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-DealerAssign',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-Province',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Province',
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
                                                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-District').setValue('');
                                                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Subdistrict').setValue('');
                                                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Village').setValue('');
            
                                                            //load store
                                                            cmb_farmer_group.setStoreVar({ProvinceID:nv});
                                                            cmb_farmer_group.load();
                                                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FarmerGroupID').setValue('');
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-District',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-District',
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
                                                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Subdistrict').setValue('');
                                                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Village').setValue('');
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-Subdistrict',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Subdistrict',
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
                                                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Village').setValue('');
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-Village',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Village',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-Address',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Address',
                                                    height: 65
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-RtRw',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-RtRw',
                                                    fieldLabel: lang('RT / RW'),
                                                    labelAlign:'top',
                                                    hidden: true
                                                }]
                                            }]
                                        }]
                                    },{
                                        xtype: 'panel',
                                        title: lang('Certification'),
                                        frame: false,
                                        id: 'Koltiva.view.Grower.MainForm-FormBasicData-SectionCert',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-isCertified',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-isCertified',
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
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationRSPO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationRSPO',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('ISCC'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationISCC',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationISCC',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('ISPO'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationISPO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationISPO',
                                                                listeners:{
                                                                }
                                                            }]
                                                        },{
                                                            columnWidth: 0.25,
                                                            border: false,
                                                            defaultType: 'checkboxfield',
                                                            items:[{
                                                                boxLabel: lang('MSPO'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationMSPO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationMSPO',
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
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-ReceiveTraining',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-ReceiveTraining1',
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
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-ReceiveTraining',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-ReceiveTraining2',
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
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceGovernment',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceGovernment',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('NGO'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceNGO',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceNGO',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Mill'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceMill',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceMill',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Other Private Sector Organization'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourcePrivateOrg',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourcePrivateOrg',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Others'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceOthers',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceOthers',
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
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeFinancial',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeFinancial',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Good Agriculuture Practice'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeGoodAgriculture',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeGoodAgriculture',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Human Rights and Worker Rights'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeHumanRights',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeHumanRights',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Best Management of Pesticides'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeManagementPesticides',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeManagementPesticides',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('Fire Fighting'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeFireFighting',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeFireFighting',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('HCV and HCS'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeHCVHCS',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeHCVHCS',
                                                                listeners:{
                                                                }
                                                            },{
                                                                boxLabel: lang('P&C RSPO Independent Smallholder Standard'),
                                                                name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeRSPOIndependent',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeRSPOIndependent',
                                                                listeners:{
                                                                }
                                                            }]
                                                        }]
                                                    }]
                                                }]
                                            }]
                                        }]
                                    },{
                                        xtype: 'panel',
                                        title: lang('Status Join'),
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
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-JoinProgram',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-JoinProgram1',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReason').setValue('');                                                                 
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReason').setReadOnly(false);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-JoinProgram',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-JoinProgram2',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReason').setReadOnly(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReason').setValue('');
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReason',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReason',
                                                    store: cmb_not_join_reason,
                                                    fieldLabel: lang('Not Join Reason'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners:{
                                                        change:function(cb, nv, ov){
                                                            if(nv == 4){
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReasonText').setVisible(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReasonText').setVisible(false);
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReasonText').setValue('');
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReasonText',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReasonText',
                                                    fieldLabel: lang('Other Not Join Reason'),
                                                    labelAlign:'top',
                                                    hidden:true
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textarea',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-JoinComment',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-JoinComment',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-HandphoneType',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-HandphoneType',
                                                    store: cmb_handphone_type,
                                                    fieldLabel: lang('Handphone Type'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners: {
                                                        change: function(cb, nv, ov) {
                                                            if(nv == '3'){
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Handphone').setValue('');
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Handphone').setReadOnly(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Handphone').setReadOnly(false);
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-Handphone',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Handphone',
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
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-AccessToSmartphone',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-AccessToSmartphone1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-AccessToSmartphone',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-AccessToSmartphone2',
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
                                                    fieldLabel: lang('Is the farmer part of a Member Group'),
                                                    xtype: 'radiogroup',
                                                    columns: 2,
                                                    labelAlign: 'top',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-inGroup',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-inGroupYes',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    //load store
                                                                    cmb_farmer_group.setStoreVar({DistrictID:Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-District').getValue()});
                                                                    cmb_farmer_group.load();
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FarmerGroupID').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FarmerGroupID').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-inGroup',
                                                        inputValue: '0',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-inGroupNo',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-FarmerGroupID',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-FarmerGroupID',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-groupName',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-groupName',
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
                                                    id:'Koltiva.view.GrowerWAGS.FormMainGrower-RowinGapoktan',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-inGapoktan',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-inGapoktanYes',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-GapoktanName').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-GapoktanName').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-inGapoktan',
                                                        inputValue: '0',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-inGapoktanNo',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-GapoktanName',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-GapoktanName',
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
                                                    id:'Koltiva.view.GrowerWAGS.FormMainGrower-RowinCoop',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-inCoop',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-inCoopYes',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-CoopName').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-CoopName').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-inCoop',
                                                        inputValue: '0',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-inCoopNo',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CoopName',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CoopName',
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
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-StatusMember',
                                                        inputValue: 'Active',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-StatusMemberActive',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReason').setValue('');
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason').setReadOnly(true);                                                                    
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason').setValue('');                                                                    
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReason').setReadOnly(false);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason').setReadOnly(false);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('Inactive'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-StatusMember',
                                                        inputValue: 'Inactive',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-StatusMemberInactive',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReason').setReadOnly(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReason').setValue('');
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReason',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReason',
                                                    store: cmb_inactive_reason,
                                                    fieldLabel: lang('Inactive Reason'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners:{
                                                        change:function(cb, nv, ov){
                                                            if(nv == 5){
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReasonText').setVisible(true);
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason').setReadOnly(true);
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason').setValue('');
                                                            }else{
                                                                if(nv != 3){
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason').setReadOnly(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason').setValue('');
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason').setReadOnly(false);
                                                                }
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReasonText').setVisible(false);
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReasonText',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReasonText',
                                                    fieldLabel: lang('Other Inactive Reason'),
                                                    labelAlign:'top',
                                                    hidden:true
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason',
                                                    store: cmb_stopped_reason,
                                                    fieldLabel: lang('Stopped Farming Reason'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners:{
                                                        change:function(cb, nv, ov){
                                                            if(nv == 6){
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReasonText').setVisible(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReasonText').setVisible(false);
                                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReasonText').setValue('');
                                                            }
                                                        }
                                                    }
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReasonText',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReasonText',
                                                    fieldLabel: lang('Other Stopped Farming Reason'),
                                                    labelAlign:'top',
                                                    hidden:true
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
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-WorkInPlot',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-WorkInPlot1',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-RowUseAPD').setDisabled(false);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-RowHadAccident').setDisabled(false);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-RowHaveBPJS').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-RowUseAPD').setDisabled(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-RowHadAccident').setDisabled(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-HadAccident2').setValue(true);
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-RowHaveBPJS').setDisabled(true);
                                                                }
            
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-WorkInPlot',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-WorkInPlot2',
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
                                                    id:'Koltiva.view.GrowerWAGS.FormMainGrower-RowUseAPD',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-UseAPD',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-UseAPD1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-UseAPD',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-UseAPD2',
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
                                                    id:'Koltiva.view.GrowerWAGS.FormMainGrower-RowHadAccident',
                                                    columns: 2,
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-HadAccident',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-HadAccident1',
                                                        listeners:{
                                                            change: function(){
                                                                if(this.checked == true){
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-WhatAccident').setDisabled(false);
                                                                }else{
                                                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-WhatAccident').setDisabled(true);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-HadAccident',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-HadAccident2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    fieldLabel: lang('What kind of accident'),
                                                    labelAlign:'top',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-WhatAccident',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-WhatAccident',     
                                                    disabled:true                          
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    fieldLabel: lang('Do you have BPJS'),
                                                    labelAlign:'top',
                                                    xtype: 'radiogroup',
                                                    msgTarget: 'side',
                                                    columns: 2,
                                                    disabled:true,
                                                    id:'Koltiva.view.GrowerWAGS.FormMainGrower-RowHaveBPJS',
                                                    items:[{
                                                        boxLabel: lang('Yes'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-HaveBPJS',
                                                        inputValue: '1',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-HaveBPJS1',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    },{
                                                        boxLabel: lang('No'),
                                                        name: 'Koltiva.view.GrowerWAGS.FormMainGrower-HaveBPJS',
                                                        inputValue: '2',
                                                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-HaveBPJS2',
                                                        listeners:{
                                                            change: function(){
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{
                                                    xtype: 'textfield',
                                                    hidden:true,
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-PhotoDesc',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-PhotoDesc',
                                                    emptyText: lang('Notes on picture of visit')
                                                },{
                                                    html:'<div></div>',
                                                },
                                                {
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-CategoryFarmer',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-CategoryFarmer',
                                                    store: cmb_farmer_category,
                                                    fieldLabel: lang('Farmer Category'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id'
                                                },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-TotalProductionArea',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-TotalProductionArea',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-MembershipStatus',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-MembershipStatus',
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
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-SupplybaseType',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-SupplybaseType',
                                                    store: cmb_supplybase,
                                                    fieldLabel: lang('Supply Base Type'),
                                                    labelAlign:'top',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id'
                                                },{
                                                    html:'<div></div>',
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-HowManyPlot',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-HowManyPlot',
                                                    fieldLabel: lang('Anda memiliki berapa kebun kelapa sawit'),
                                                    labelAlign: 'top',
                                                    allowNegative: false
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype: 'numericfield',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-PlotTotalHectare',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-PlotTotalHectare',
                                                    fieldLabel: lang('Total hectare'),
                                                    readOnly: true,
                                                    labelAlign: 'top',
                                                    allowNegative: false
                                                },{
                                                    html:'<div></div>'
                                                },{
                                                    xtype:'textarea',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-frComment',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-frComment',
                                                    fieldLabel: lang('Comment'),
                                                    labelAlign: 'top'
                                                },{
                                                    html: '<div></div><div></div><div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-Enumerator',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-Enumerator',
                                                    fieldLabel: lang('Enumerator'),
                                                    labelAlign: 'top',
                                                    readOnly: true
                                                },{
                                                    html: '<div></div>'
                                                },{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-ModifiedBy',
                                                    name: 'Koltiva.view.GrowerWAGS.FormMainGrower-ModifiedBy',
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
                            title: lang('Farmer\'s Family'),
                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tabFarmerFamily',
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
                            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tabFarmerLabour',
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
                                    case 'Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tabFarmerFamily':
                                    case 'Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tabFarmerLabour':
                                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-btnSave').setVisible(false);
                                    break;
                                    default:
                                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-btnSave').setVisible(true);
                                    break;
                                }
                            }
                        }
                    }]
                }]
            }],
            buttons: [{
                text: lang('Save'),
                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-btnSave',
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
                                url: m_api + '/grower/member_wags',
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
    
                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower').destroy(); //destory current view
                                    //create object View untuk FormMainGrower
                                    if(Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower') == undefined){
                                        var FormMainGrower = Ext.create('Koltiva.view.GrowerWAGS.FormMainGrower', {
                                            opsiDisplay: 'update',
                                            formVar: {
                                                MemberID: o.result.MemberIDInc,
                                                PartnerSurvey: o.result.PartnerSurvey
                                            }
                                        });
                                    }else{
                                        //destroy, create ulang
                                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower').destroy();
                                        var FormMainGrower = Ext.create('Koltiva.view.GrowerWAGS.FormMainGrower', {
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
            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-RowEdit',
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
                    var sm = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-gridOtherLand').getSelectionModel().getSelection()[0];
                    //console.log(sm);

                    //get last row from store
                    var storeGridOtherLand = Ext.data.StoreManager.lookup('Koltiva.store.Grower.GridMemberOtherLand');
                    var lastRow = storeGridOtherLand.getAt(storeGridOtherLand.getCount()-1);
                    //console.log(lastRow);

                    if(sm.data.MemOtherID == lastRow.data.MemOtherID){
                        var heightGridNow = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-gridOtherLand').getHeight();
                        heightGridNow = heightGridNow + 55;
                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-gridOtherLand').setHeight(heightGridNow);
                    }

                    olRowEditing.cancelEdit();
                    olRowEditing.startEdit(sm.index, 0);
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-gridOtherLand').getSelectionModel().getSelection()[0];

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
            id: 'Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand',
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
                    id: 'Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-btnAdd',
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
                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-gridOtherLand',
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
                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-gridOtherLand-reditCommodity',
                        allowBlank: false
                    }
                },{
                    text: lang('Size (ha)'),
                    dataIndex: 'GardenHa',
                    flex: 1,
                    editor: {
                        xtype: 'numericfield',
                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-gridOtherLand-reditGardenHa',
                        allowNegative: false,
                        minValue: 0
                    }
                },{
                    text: lang('Remark'),
                    dataIndex: 'Remark',
                    flex: 2,
                    editor:{
                        xtype: 'textfield',
                        id: 'Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-gridOtherLand-reditRemark'
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
                            var Commodity = Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-PanelOtherLand-gridOtherLand-reditCommodity').getValue();
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
            //seperti ini saja dulu, karena yang masuk sini sudah pasti WAGS
            //panel Plot Survey
            var objPanelPlotSurveyWags = Ext.create('Koltiva.view.PlotSurvey.PlotSurPanelSummaryWags');
            thisObj.objPanelPlotSurveyWagsPanel = objPanelPlotSurveyWags; //biar bisa diakses di beforeactive
            objPanelDinamis.push(objPanelPlotSurveyWags);

            //set param view2 yg dipanggil view ini
            thisObj.objPanelPlotSurveyWagsPanel.setViewVar({
                MemberID:thisObj.formVar.MemberID
            });

            // if(thisObj.formVar.PartnerSurvey != null){
            //     //proses PartnerSurveynya
            //     var arrayPartnerSurvey = thisObj.formVar.PartnerSurvey.split(",");

            //     if(arrayPartnerSurvey.indexOf('Plantation Unilever') !== -1){
            //         //panel Plot Survey
            //         var objPanelPlotSurvey = Ext.create('Koltiva.view.PlotSurvey.PlotSurPanelSummary');
            //         thisObj.objPanelPlotSurveyPanel = objPanelPlotSurvey; //biar bisa diakses di beforeactive
            //         objPanelDinamis.push(objPanelPlotSurvey);

            //         //set param view2 yg dipanggil view ini
            //         thisObj.objPanelPlotSurveyPanel.setViewVar({
            //             MemberID:thisObj.formVar.MemberID
            //         });
            //     }

            //     if(arrayPartnerSurvey.indexOf('Plantation GAR') !== -1){
            //         //panel Plot Survey
            //         var objPanelPlotSurveyGar = Ext.create('Koltiva.view.PlotSurvey.PlotSurPanelSummaryGar');
            //         thisObj.objPanelPlotSurveyGarPanel = objPanelPlotSurveyGar; //biar bisa diakses di beforeactive
            //         objPanelDinamis.push(objPanelPlotSurveyGar);

            //         //set param view2 yg dipanggil view ini
            //         thisObj.objPanelPlotSurveyGarPanel.setViewVar({
            //             MemberID:thisObj.formVar.MemberID
            //         });
            //     }

            //     if(arrayPartnerSurvey.indexOf('Plantation STA') !== -1){
            //         //panel Plot Survey
            //         var objPanelPlotSurveySta = Ext.create('Koltiva.view.PlotSurvey.PlotSurPanelSummarySta');
            //         thisObj.objPanelPlotSurveyStaPanel = objPanelPlotSurveySta; //biar bisa diakses di beforeactive
            //         objPanelDinamis.push(objPanelPlotSurveySta);

            //         //set param view2 yg dipanggil view ini
            //         thisObj.objPanelPlotSurveyStaPanel.setViewVar({
            //             MemberID:thisObj.formVar.MemberID,
            //             User: 'Farmer'
            //         });
            //     }

            //     if(arrayPartnerSurvey.indexOf('Plantation WAGS') !== -1 || m_user_partnerid == 14 || m_user_partnerid == 194){
            //         //panel Plot Survey
            //         var objPanelPlotSurveyWags = Ext.create('Koltiva.view.PlotSurvey.PlotSurPanelSummaryWags');
            //         thisObj.objPanelPlotSurveyWagsPanel = objPanelPlotSurveyWags; //biar bisa diakses di beforeactive
            //         objPanelDinamis.push(objPanelPlotSurveyWags);

            //         //set param view2 yg dipanggil view ini
            //         thisObj.objPanelPlotSurveyWagsPanel.setViewVar({
            //             MemberID:thisObj.formVar.MemberID
            //         });
            //     }

            //     if(arrayPartnerSurvey.indexOf('Main Buyer Unilever') !== -1){
            //         //panel Main Buyer Survey
            //         thisObj.objPanelMainBuyerSurvey = Ext.create('Koltiva.view.MainBuyerSurvey.MainBuyerSurPanelSummary');
            //         objPanelDinamis.push(thisObj.objPanelMainBuyerSurvey);

            //         //set param view2 yg dipanggil view ini
            //         thisObj.objPanelMainBuyerSurvey.setViewVar({
            //             MemberID:thisObj.formVar.MemberID
            //         });
            //     }

            //     if(arrayPartnerSurvey.indexOf('Household Unilever') !== -1){
            //         //panel Household Survey
            //         thisObj.objPanelHouseholdSurvey = Ext.create('Koltiva.view.HouseholdSurvey.HouseholdSurPanelSummary');
            //         objPanelDinamis.push(thisObj.objPanelHouseholdSurvey);

            //         //set param view2 yg dipanggil view ini
            //         thisObj.objPanelHouseholdSurvey.setViewVar({
            //             MemberID:thisObj.formVar.MemberID
            //         });
            //     }

            //     if(arrayPartnerSurvey.indexOf('Finance SNV') !== -1){
            //         //panel Finance Survey
            //         thisObj.objPanelFinanceSurvey = Ext.create('Koltiva.view.FinanceSurvey.FinanceSurveyPanelSummary');
            //         objPanelDinamis.push(thisObj.objPanelFinanceSurvey);

            //         //set param view2 yg dipanggil view ini
            //         thisObj.objPanelFinanceSurvey.setViewVar({
            //             MemberID:thisObj.formVar.MemberID
            //         });
            //     }

            //     if(arrayPartnerSurvey.indexOf('Plantation Polygon Unilever') !== -1){
            //         //panel Garden Polygon
            //         thisObj.objPanelPlotPolygon  = Ext.create('Koltiva.view.PlotPolygon.PlotPolygonPanel',{
            //             viewVar: {
            //                 MemberID: thisObj.formVar.MemberID,
            //                 CallFrom: 'Farmer'
            //             }
            //         });
            //         objPanelDinamis.push(thisObj.objPanelPlotPolygon);
            //     }
            // }
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
                id: 'Koltiva.view.GrowerWAGS.FormMainGrower-labelInfoInsert',
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
                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower').destroy(); //destory current view
                        if(Ext.getCmp('Koltiva.view.GrowerWAGS.GridMainGrower-MainPanel') == undefined){
                            var GridMainGrower = Ext.create('Koltiva.view.GrowerWAGS.GridMainGrower');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.GrowerWAGS.GridMainGrower-MainPanel').destroy();
                            var GridMainGrower = Ext.create('Koltiva.view.GrowerWAGS.GridMainGrower');
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

                //Atur Show/Hide Form soal WAGS dan bukan WAGS ==== (Begin)
                if(m_user_partnerid != '1' && m_user_partnerid != '14'){ //Bukan Koltiva and WAG
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-ExtID').setVisible(false);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-CategoryFarmer').setVisible(false);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MembershipStatus').setVisible(false);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-PlotTotalHectare').setVisible(false);
                }

                if(m_user_partnerid == 14){ //Jika WAGS
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-RowinGapoktan').setVisible(false);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-GapoktanName').setVisible(false);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-RowinCoop').setVisible(false);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-CoopName').setVisible(false);
                    // Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-IsCertified').setVisible(false);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-CategoryFarmer').setVisible(false);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-TotalProductionArea').setVisible(true);
                } else {
                    // Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-IsCertified').setVisible(true);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-CategoryFarmer').setVisible(true);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-TotalProductionArea').setVisible(false);
                }

                //Atur Show/Hide Form soal WAGS dan bukan WAGS ==== (End)

                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-labelInfoInsert').update('<h3 style="margin:0px;padding:0px;">'+lang('Farmer Data')+'&nbsp;<span style="font-size:14px;">('+lang('Add New Farmer')+')</span></h3>');

                //form reset
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');

                //hidden tab
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tabFarmerFamily').setDisabled(true);
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tabFarmerLabour').setDisabled(true);
                //set aktif tab pertama
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tab').setActiveTab(0);

                //buka panel2
                // Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-PanelFamLab').collapse();

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
                //console.log(this.formVar);

                if(m_user_partnerid == 14){
                    // Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-IsCertified').setVisible(false);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-TotalProductionArea').setVisible(true);
                } else {
                    // Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-IsCertified').setVisible(true);
                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-TotalProductionArea').setVisible(false);
                }
                
                //hidden tab
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tabFarmerFamily').setDisabled(false);
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tabFarmerLabour').setDisabled(false);
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                //set aktif tab pertama
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData-tab').setActiveTab(0);


                //form reset
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');

                //load data form
                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FormBasicData').getForm().load({
                    url: m_api + '/grower/member_basic_data_form_wags',
                    method: 'GET',
                    params: {
                        MemberID: this.formVar.MemberID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);

                        //Cek ini data petani Partner siapa (Begin)
                        switch(r.data.PartnerID){
                            case '14': //WAGS
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-RowinGapoktan').setVisible(false);
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-GapoktanName').setVisible(false);
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-RowinCoop').setVisible(false);
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-CoopName').setVisible(false);
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-CategoryFarmer').setVisible(false);
                            break;
                            default:
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-PlotTotalHectare').setVisible(false);
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-ExtID').setVisible(false);
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MembershipStatus').setVisible(false);
                            break;
                        }
                        //Cek ini data petani Partner siapa (End)

                        //untuk handle combo bertingkat
                        var cmb_province = Ext.data.StoreManager.lookup('store.Grower.CmbProvince');
                        var cmb_district = Ext.data.StoreManager.lookup('store.Grower.CmbDistrict');
                        var cmb_subdistrict = Ext.data.StoreManager.lookup('store.Grower.CmbSubdistrict');
                        var cmb_village = Ext.data.StoreManager.lookup('store.Grower.CmbVillage');
                        var cmb_farmer_group = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.CmbFarmerGroup');
                        cmb_province.load({
                            callback: function(records, operation, success){
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Province').setValue(r.data.Province);
                                if (success == true) {

                                    //load combo farmer group lagi aja disini dan set nilainya jika ada
                                    cmb_farmer_group.setStoreVar({ProvinceID:r.data.Province});
                                    cmb_farmer_group.load({
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-FarmerGroupID').setValue(r.data.FarmerGroupID);
                                            }
                                        }
                                    });

                                    cmb_district.load({
                                        params: {
                                            ProvinceID: r.data.Province
                                        },
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-District').setValue(r.data.District);
                                                cmb_subdistrict.load({
                                                    params: {
                                                        DistrictID: r.data.District
                                                    },
                                                    callback: function(records, operation, success){

                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Subdistrict').setValue(r.data.Subdistrict);
                                                            cmb_village.load({
                                                                params: {
                                                                    SubdistrictID: r.data.Subdistrict
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Village').setValue(r.data.Village);
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
                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhotoOld').setValue(r.data.PhotoSrcPath);

                        //set photo
                        if(r.data.PhotoSrc != ""){
                            var fotoUser = r.data.PhotoSrc;
                            var angkaRand = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUser, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhoto').setSrc(fotoUser+'?'+angkaRand);
                                } else {
                                    if(r.data.Gender == 'f'){
                                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');
                                    }else{
                                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhoto').setSrc(m_api_base_url + '/assets/images/farmer-default.png');
                                    }
                                }
                            });
                        }

                        //buka panel2
                        // Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-PanelFamLab').expand();
                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-PanelLabour').expand();

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

                        Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-labelInfoInsert').update('<h3 style="margin:0px;padding:0px;">'+Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberDisplayID').getValue()+' - '+Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-Fullname').getValue()+'</h3>');


                        //Isi Form Labour Extension ======================= (Begin)
                        switch(r.data.labHaveWorkers){
                            case '1':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHaveWorkers1').setValue(true);
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour-BtnAdd').setVisible(true);
                            break;
                            case '2':
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHaveWorkers2').setValue(true);
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                            break;
                            default:
                                Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-labHaveWorkers2').setValue(true);
                                Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
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
                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridLabour-BtnAdd').setVisible(false);
                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-gridFamilyLabour-BtnAdd').setVisible(false);
                            Ext.getCmp('Koltiva.view.Grower.FormLabourExtension-btnSave').setVisible(false);
                            
                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-btnSave').setVisible(false);
                            Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhotoInput').setVisible(false);
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
        var DateBirth = Ext.Date.format(Ext.getCmp('Koltiva.view.GrowerWAGS.FormMainGrower-DateOfBirth').getValue(),'Y-m-d');

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