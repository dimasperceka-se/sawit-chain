/*
* @Author: Fashah Darullah
* @Date:   2019-10-07 13:57:57
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinImsCoachAct' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsCoachAct',
    title: lang('IMS - Coaching Activity'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '94%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },    
    submitOnEnterGrid: function(field, event){
    	if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct').setFilterLs();
            Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-CoachingActivity').getStore().loadPage(1);
        }
    },
    setFilterLs: function(){
    	localStorage.setItem('ct_farmer_ls', JSON.stringify({
    		opsiCall: 'simple',
            ptextSearch: Ext.getCmp('textSearch').getValue()
        }));
    },
    initComponent: function() {
        var thisObj = this;

        //store (Begin)
        var store_tab_coaching_activity = Ext.create('Koltiva.store.IMS.GridCoachingActivity', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });
        //store (End)

        thisObj.contextMenuGridCoachingActivity = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-CoachingActivity').getSelectionModel().getSelection()[0];

                        var WinFormCoachActivity = Ext.create('Koltiva.view.IMS.WinFormCoachActivityViewOnly', {
                            viewVar: {
                                opsiDisplay: 'view',
                                ActivityID: sm.get('ActivityID')
                            }
                        });
                        if (!WinFormCoachActivity.isVisible()) {
                            WinFormCoachActivity.center();
                            WinFormCoachActivity.show();
                        } else {
                            WinFormCoachActivity.close();
                        }
                    }
                }]
        });

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinImsCoachAct-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    style: '',
                    items:[
                        {
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 0.495,
                                style:'padding-right:25px;',
                                layout:'form',
                                fieldDefaults: {
                                    labelWidth: 275
                                },
                                items:[{
                                    xtype: 'hiddenfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-SigningLockSocSelBy',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-SigningLockSocSelBy'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertEventName',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertEventName',
                                    fieldLabel: lang('Event Name'),
                                    labelWidth: 200,
                                    readOnly: true
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-IMSID',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-IMSID',
                                    fieldLabel: lang('Event ID'),
                                    labelWidth: 200,
                                    readOnly: true
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-Location',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-Location',
                                    fieldLabel: lang('Location'),
                                    labelWidth: 200,
                                    readOnly: true
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-Year',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-Year',
                                    fieldLabel: lang('Year of Certification'),
                                    labelWidth: 200,
                                    readOnly: true
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-SocSelPeriodLabel',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-SocSelPeriodLabel',
                                    fieldLabel: lang('Socialization Selection Period'),
                                    labelWidth: 200,
                                    readOnly: true
                                }]
                            },{
                                columnWidth: 0.5,
                                style:'padding-right:25px;',
                                layout:'form',
                                items:[{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertificateHolder',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertificateHolder',
                                    fieldLabel: lang('Certificate Holders'),
                                    labelWidth: 200,
                                    readOnly: true
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-ProgramName',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-ProgramName',
                                    fieldLabel: lang('Program Name'),
                                    labelWidth: 200,
                                    readOnly: true
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertificationBody',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertificationBody',
                                    fieldLabel: lang('Certification Body'),
                                    labelWidth: 200,
                                    readOnly: true
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-FirstBuyer',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-FirstBuyer',
                                    fieldLabel: lang('First Buyer'),
                                    labelWidth: 200,
                                    readOnly: true
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.IMS.WinImsAcqPro-Form-TrainingPeriodLabel',
                                    name: 'Koltiva.view.IMS.WinImsAcqPro-Form-TrainingPeriodLabel',
                                    fieldLabel: lang('Training Period'),
                                    labelWidth: 200,
                                    readOnly: true
                                }]
                            }]
                        },{
                    	xtype: 'tabpanel',
		                flex: 1,
		                margin: 0,
		                activeTab: 0,
		                plain: true,
		                items: [{
		                	xtype: 'gridpanel',
		                    title: lang('Coaching Activity'),
		                    id: 'Koltiva.view.IMS.WinImsCoachAct-Tab-CoachingActivity',
		                    style: 'border:1px solid #CCC;padding-right:3px;',
                                    cls: 'Sfr_GridNew',
		                    store: store_tab_coaching_activity,
		                    width: '100%',
		                    loadMask: true,
		                    selType: 'rowmodel',
		                    viewConfig: {
		                        deferEmptyText: false,
		                        emptyText: lang('No data Available')
		                    },
			                dockedItems: [{
			                	xtype: 'pagingtoolbar',
		                        store: store_tab_coaching_activity,
		                        dock: 'bottom',
		                        displayInfo: true
			                },{
                                xtype: 'toolbar',
                                items: [{
                                    name: 'key',
                                    id: 'textSearch',
                                    xtype: 'textfield',
                                    baseCls:'Sfr_TxtfieldSearchGrid',
                                    width: 400,
                                    emptyText: lang('Cari berdasar nama/username/ID')+', '+lang('press_enter_search'),
                                    listeners: {
                                        specialkey: thisObj.submitOnEnterGrid
                                    }
                                },{
                                    // Export sesuai dgn query Sql View
                                    xtype: 'button',
                                    icon: varjs.config.base_url + 'images/icons/new/export.png',
                                    text: lang('Export'),
                                    hidden: m_act_export,
                                    cls: 'Sfr_BtnGridPaleBlue',
                                    overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                    handler: function () {
                                        var grid = Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-CoachingActivity').getStore().sort();
                                        var sort = [];
                                        if (grid[0] !== undefined) {
                                            sort[0] = {};
                                            sort[0].property = grid[0].property;
                                            sort[0].direction = grid[0].direction;
                                            paramSort = JSON.stringify(sort);
                                        } else {
                                            sort[0] = {};
                                            paramSort = JSON.stringify(sort);
                                        }
                                        
                                        
                                            Ext.MessageBox.show({
                                                msg: 'Please wait...',
                                                progressText: 'Exporting...',
                                                width: 300,
                                                wait: true,
                                                waitConfig: {
                                                    interval: 200
                                                },
                                                icon: 'ext-mb-info', //custom class in msg-box.html
                                                animateTarget: 'mb9'
                                            });

                                            Ext.Ajax.request({
                                                url: m_api + '/ims/grid_coaching_activity_sql_export',
                                                method: 'POST',
                                                waitMsg: lang('Please Wait'),
                                                params: {
                                                    IMSID: thisObj.viewVar.IMSID,
                                                    textSearch: Ext.getCmp('textSearch').getValue(),
                                                    sort: paramSort
                                                },
                                                success: function (data) {
                                                    Ext.MessageBox.hide();
                                                    var jsonResp = JSON.parse(data.responseText);
                                                    window.location = jsonResp.filenya;
                                                },
                                                failure: function () {
                                                    Ext.MessageBox.hide();
                                                    Ext.MessageBox.show({
                                                        title: 'Notifications',
                                                        msg: 'Failed to Export, Please Try Again.',
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-error'
                                                    });
                                                }
                                            });
                                        }
                                }]
                            }],
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
			                columns: [{
                                            text: '',
                                            xtype:'actioncolumn',
                                            width:'4%',
                                            items:[{
                                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                handler: function(grid, rowIndex, colIndex, item, e, record) {
                                                    thisObj.contextMenuGridCoachingActivity.showAt(e.getXY());
                                                }
                                            }]
                                        },{
			                    dataIndex: 'ActivityID',
			                    hidden: true
			                },{
			                    text: 'No',
			                    xtype: 'rownumberer',
			                    align: 'center',
			                    width: '3%'
			                },{
			                    text: lang('Farmer ID'),
                                                flex: 1,
			                    dataIndex: 'FarmerID'
			                },{
			                	text: lang('Name'),
                                                flex: 2,
			                    dataIndex: 'FarmerName'
			                },{
			                	text: lang('Event Date'),
                                                flex: 1,
			                    dataIndex: 'EventDate'
			                },{
			                	text: lang('Time Start'),
                                                flex: 1,
			                    dataIndex: 'TimeStart'
			                },{
			                	text: lang('Time End'),
                                                flex: 1,
			                    dataIndex: 'TimeEnd'
			                },{
			                	text: lang('Date Created'),
                                                flex: 1,
			                    dataIndex: 'DateCreated'
			                },{
			                	text: lang('Username'),
                                                flex: 2,
			                    dataIndex: 'UserName'
			                },{
			                	text: lang('Created By'),
                                                flex: 1,
			                    dataIndex: 'CreatedBy'
			                }]
//                                    ,
//			                listeners: {
//			                    itemclick: function(view, record, item, index, e){
//			                       thisObj.contextMenuGridCoachingActivity.showAt(e.getXY());
//			                    }
//			                }		                
		                }],
		                listeners: {
		                    'tabchange': function (tabPanel, tab) {
		                        switch(tab.id){
		                            case 'Koltiva.view.IMS.WinImsCoachAct-Tab-CoachingActivity':
		                                store_tab_coaching_activity.load();
		                            break;
		                        }
		                    }
		                }
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Form');
            formNya.getForm().reset();

            var ct_farmer_ls = JSON.parse(localStorage.getItem('ct_farmer_ls'));
        	if(ct_farmer_ls != null){
	            if(ct_farmer_ls.ptextSearch != undefined){
	            	Ext.getCmp('textSearch').setValue(ct_farmer_ls.ptextSearch);
	            }
        	}

            //load nilainya
            formNya.getForm().load({
                url: m_api + '/ims/acq_pro_get_form',
                method: 'GET',
                params: {
                    IMSID: thisObj.viewVar.IMSID,
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                    //console.log(r);

                    if(r.data.TrainStatus == "1"){
                    	//hide tombol generate
                        Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Training-BtnBulkProcessCandidate').setVisible(false);
                    }

                    //Jika sudah Signing Lock Soc Sel
                    if(r.data.SigningLockSocSelBy != undefined && r.data.SigningLockSocSelBy != null && r.data.SigningLockSocSelBy != ''){
                    	Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Selection-BtnApprove').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Training-BtnGenSocSel').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Selection-BtnImportCertFarmer').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-SelectionApproved-BtnProcessCandidateSelection').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Form-SigningLockSocSelBy').setValue(r.data.SigningLockSocSelBy);
                    }

					//Jika sudah Signing Lock Gap Coc
                    if(r.data.SigningLockGapCocBy != undefined && r.data.SigningLockGapCocBy != null && r.data.SigningLockGapCocBy != ''){
                    	Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Training-BtnGenTrainCandidate').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Training-BtnApprove').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-TrainingApproved-BtnProcessCandidateTraining').setVisible(false);
					}
					
					//Pengecekan terakhir jika Ims Event sudah completed
					if(r.data.CertEventStatus == "2"){
						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Training-BtnGenSocSel').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Socialization-BtnApprove').setDisabled(true);

						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Selection-BtnApprove').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Selection-BtnImportCertFarmer').setDisabled(true);
						
						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-SelectionApproved-BtnSigningLock').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-SelectionApproved-BtnProcessCandidateSelection').setDisabled(true);
						
						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Training-BtnApprove').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Training-BtnGenTrainCandidate').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-Training-BtnBulkProcessCandidate').setDisabled(true);
						
						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-TrainingApproved-BtnSigningLock').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsCoachAct-Tab-TrainingApproved-BtnProcessCandidateTraining').setDisabled(true);
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
});