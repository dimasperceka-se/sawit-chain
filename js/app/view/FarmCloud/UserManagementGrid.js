/*
* @Author: Fashah Darullah
* @Date:   2019-06-12 11:19:19
*/

Ext.define('Koltiva.view.FarmCloud.UserManagementGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmCloud.UserManagementGrid',
    loadMask: true,
    renderTo: 'ext-content',
    style:'padding:0 7px 7px 7px;margin:-12px 0 0 0;',
    submitOnEnterGrid: function(field, event){
    	if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid').setFilterLs();
            Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser').getStore().loadPage(1);
        }
    },
    setFilterLs: function(){
    	localStorage.setItem('ct_farmer_ls', JSON.stringify({
    		opsiCall: 'simple',
            ptextSearch: Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser-textSearch').getValue()
        }));
    },
    SetGridColumnCustom: function(ColDisplayArr){
    	var thisObj = this;

    	//Set Hide Semua terlebih dahulu
    	thisObj.SetGridColumnHideAll();

        if(ColDisplayArr.length > 0){
            for (var i = 0; i < ColDisplayArr.length; i++) {
                Ext.getCmp(ColDisplayArr[i]).setVisible(true);
            }
        }
    },
    listeners: {
        afterRender: function(component, eOpts){
        	var thisObj = this;

        	//Div nya Filter Region
            document.getElementById('divCommonContentRegion').style.display = 'none';
            // document.getElementById('main-breadcrumb').style.display = 'block';

        	var ct_farmer_ls = JSON.parse(localStorage.getItem('ct_farmer_ls'));
        	if(ct_farmer_ls != null){
        		if(ct_farmer_ls.opsiCall != undefined){
	            	if(ct_farmer_ls.opsiCall == "advanced"){
	            		Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser-BtnSimplifiedGrid').setVisible(true);
	            	}else{
	            		Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser-BtnSimplifiedGrid').setVisible(false);
	            	}
	            }

	            if(ct_farmer_ls.ptextSearch != undefined){
	            	Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser-textSearch').setValue(ct_farmer_ls.ptextSearch);
	            }
        	}

            var ct_farmer_grid_ls = JSON.parse(localStorage.getItem('ct_farmer_grid_ls'));
            if(ct_farmer_grid_ls != null){
            	if(ct_farmer_grid_ls.opsiShow != undefined){
	            	if(ct_farmer_grid_ls.opsiShow == "custom"){
	            		//Sesuaikan
                        thisObj.SetGridColumnCustom(ct_farmer_grid_ls.ColDisplayArr);
                        Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser-BtnSimplifiedGrid').setVisible(true);
	            	}else{
	            		//Tampilan Grid Column Default
                        thisObj.SetGridColumnDefault();
                        Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser-BtnSimplifiedGrid').setVisible(false);
	            	}
	            }
            }
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Define Store Main Grid
        thisObj.StoreGridMain = Ext.create('Koltiva.store.FarmCloud.UserManagementGrid');

        //Context Menu
        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu',{
            cls:'Sfr_ConMenu',
	        items:[{
	            icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('Detail Account'),
                cls:'Sfr_BtnConMenuWhite',
                // hidden: m_act_update,
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid').destroy(); //destory current view
                    var FormMainFarmer = [];

                    //create object View untuk FormMainGrower
                    if(Ext.getCmp('Koltiva.view.FarmCloud.UserManagementForm') == undefined){
                        FormMainFarmer = Ext.create('Koltiva.view.FarmCloud.UserManagementForm', {
                            viewVar: {
                                opsiDisplay: 'view',
                                PersonExtID: sm.get('PersonExtID')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.FarmCloud.UserManagementForm').destroy();
                        FormMainFarmer = Ext.create('Koltiva.view.FarmCloud.UserManagementForm', {
                            viewVar: {
                                opsiDisplay: 'view',
                                PersonExtID: sm.get('PersonExtID')
                            }
                        });
                    }
	            }
	        },{
	            icon: varjs.config.base_url + 'images/icons/new/reset.png',
                text: lang('Reset Account'),
                cls:'Sfr_BtnConMenuWhite',
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser').getSelectionModel().getSelection()[0];
                    
	                Ext.MessageBox.confirm('Reset Confirmation', 'Reset selected data, please confirm to proceed', function(r) {
                        if (r == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/farmcloud/reset_user',
                                method: 'POST',
                                params: {
                                    username : sm.get('username')
                                },
                                success: function(response, opts) {
                                    Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser').getStore().loadPage(1);
        
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', 'User has been reset');
                                            break;
                                        default:
                                            Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                    }
                                },
                                failure: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                }
                            });
                        }
                    });
	            }
	        },{
	            icon: varjs.config.base_url + 'images/icons/new/disable.png',
                text: lang('Disable Account'),
                cls:'Sfr_BtnConMenuWhite',
	            handler: function(){
	                var sm = Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser').getSelectionModel().getSelection()[0];
                    
                    Ext.MessageBox.confirm('Disable Confirmation', 'Disable selected data, please confirm to proceed', function(r) {
                        if (r == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/farmcloud/disable_user',
                                method: 'POST',
                                params: {
                                    username : sm.get('username')
                                },
                                success: function(response, opts) {
                                    Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser').getStore().loadPage(1);
        
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', 'Data has been deleted');
                                            break;
                                        default:
                                            Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                    }
                                },
                                failure: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                }
                            });
                        }
                    });

	            }
	        }]
	    });

        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.3,
                layout: 'form',
                items:[{
                    /*xtype: 'button',
                    id: 'Koltiva.view.FarmCloud.UserManagementGrid-btnListView',
                    style:'margin-top:5px;',
                    text: lang('All Farmers'),
                    arrowAlign: 'right',
                    menu : [{
                        text: lang('All Farmers'),
                        listeners: {
                            click: function(){
                                alert('All Farmers Click')
                            }
                        }
                    },{
                        text: lang('New This Week'),
                        listeners: {
                            click: function(){
                                alert('New This Week Click')
                            }
                        }
                    },{
                        text: lang('Modified This Week'),
                        listeners: {
                            click: function(){
                                alert('Modified This Week Click')
                            }
                        }
                    },{
                        text: lang('Recently Views'),
                        listeners: {
                            click: function(){
                                alert('Recently Views Click')
                            }
                        }
                    }]*/
                }]
            },{
                columnWidth: 0.7,
                xtype: 'panel',
                frame: false,
                id: 'Koltiva.view.FarmCloud.UserManagementGrid-gridInformation',
                hidden:true,
                html: ''
            }]
        },{
        	xtype: 'grid',
            id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            //height: 550,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No Data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true,
                displayMsg: 'Showing {0} to {1} of {2} entries'
            },{
            	xtype: 'toolbar',
                dock:'top',
                items: [{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Register Account'),
                    hidden: m_act_add,
                    cls:'Sfr_BtnGridGreen',
                    overCls:'Sfr_BtnGridGreen-Hover',
                    handler: function() {
                        Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid').destroy(); //destory current view
                    	var FormMainFarmer = [];

                        //create object View untuk FormMainGrower
                        if(Ext.getCmp('Koltiva.view.FarmCloud.UserRegisterForm') == undefined){
                            FormMainFarmer = Ext.create('Koltiva.view.FarmCloud.UserRegisterForm', {
                            	viewVar: {
		                            opsiDisplay: 'insert'
		                        }
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Farmer.UserRegisterForm').destroy();
                            FormMainFarmer = Ext.create('Koltiva.view.FarmCloud.UserRegisterForm', {
                                viewVar: {
		                            opsiDisplay: 'insert'
		                        }
                            });
                        }
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                	name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-textSearch',
                    xtype: 'textfield',
                    baseCls:'Sfr_TxtfieldSearchGrid',
                    width: 400,
                    emptyText: lang('Search by Name')+', '+lang('press_enter_search'),
                    listeners: {
                        specialkey: thisObj.submitOnEnterGrid
                    }
                },{
                	xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/visible-field.png',
                	text:lang('Default Grid'),
                    hidden: true,
                    cls:'Sfr_BtnGridPaleBlue',
                    overCls:'Sfr_BtnGridPaleBlue-Hover',
                	id:'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-BtnSimplifiedGrid',
                    handler: function() {
                    	localStorage.setItem('ct_farmer_ls', JSON.stringify({
				    		opsiCall: 'simple',
				            ptextSearch: Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser-textSearch').getValue()
				        }));

				        localStorage.setItem('ct_farmer_grid_ls', JSON.stringify({
				    		opsiShow: 'default'
				        }));
				        thisObj.SetGridColumnDefault();

                    	thisObj.StoreGridMain.load();

                    	//Hilangkan Tombol
                    	Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser-BtnSimplifiedGrid').setVisible(false);
                    }
                },{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/visible-field.png',
                    text: lang('Custom Fields'),
                    hidden: true,
                    cls:'Sfr_BtnGridPaleBlue',
                    overCls:'Sfr_BtnGridPaleBlue-Hover',
                    handler: function() {
                    	var WinCustomFields = Ext.create('Koltiva.view.Farmer.WinCustomFields');
                        if (!WinCustomFields.isVisible()) {
                            WinCustomFields.center();
                            WinCustomFields.show();
                        } else {
                            WinCustomFields.close();
                        }
                    }
                },{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/reload.png',
                    cls:'Sfr_BtnGridBlue',
                    overCls:'Sfr_BtnGridBlue-Hover',
                    handler: function() {
                        Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid-GridUser').getStore().loadPage(1);
                    }
                }]
            }],
            columns:[{
            	text: '',
                xtype:'actioncolumn',
                width:'2%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuGrid.showAt(e.getXY());
                    }
                }]
            },
            {
                 text: 'No',
                 xtype: 'rownumberer',
                 width:'3%'
            },{
            	text: lang('ID'),
                dataIndex: 'PersonExtID',
                id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-ColPersonExtID',
                width: '8%'
            },{
            	text: lang('Name'),
                dataIndex: 'PersonName',
                id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-ColPersonName',
                width: '15%'
            },{
            	text: lang('Gender'),
                dataIndex: 'Gender',
                id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-ColGender',
                width: '8%'
            },{
            	text: lang('Group Name'),
                dataIndex: 'GroupName',
                id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-ColGroupName',
                width: '13%'
            },{
            	text: lang('Email'),
                dataIndex: 'Email',
                id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-ColEmail',
                width: '14%'
            },{
            	text: lang('Handphone'),
                dataIndex: 'HandPhone',
                id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-ColHandphone',
                width: '8%'
            },{
            	text: lang('Disctrict'),
                dataIndex: 'DistrictName',
                id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-ColDistrictName',
                width: '10%'
            },{
            	text: lang('Sub Disctrict'),
                dataIndex: 'SubDistrictName',
                id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-ColSubDistrictName',
                width: '10%'
            },{
            	text: lang('Role'),
                dataIndex: 'role',
                id: 'Koltiva.view.FarmCloud.UserManagementGrid-GridUser-ColRole',
                width: '5%'
            }]
        }];

        this.callParent(arguments);
    }
});