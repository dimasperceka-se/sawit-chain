Ext.define('Koltiva.view.menu.MainGridMenu' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.menu.MainGridMenu',
    renderTo: 'ext-content',
    style:'padding:0 7px 7px 7px;margin:-12px 0 0 0;',
    submitOnEnterGrid: function(field, event){
    	if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.menu.MainGridMenu').setFilterLs();
            Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu').getStore().loadPage(1);
        }
    },
    /*searchByCategoryMenu: function(field, event){
    	if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.menu.MainGridMenu').setFilterLs();
            Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu').getStore().loadPage(1);
        }
    },*/
    /*function searchByCategoryMenu(){
        Ext.getCmp('Koltiva.view.menu.MainGridMenu').setFilterLs();
        Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu').getStore().loadPage(1);
    }*/
    setFilterLs: function(){
    	localStorage.setItem('ct_menu_ls', JSON.stringify({
    		opsiCall: 'simple',
            ptextSearch: Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu-textSearch').getValue(),
            parentMenuSearch: Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu-ComboParentMenu').getValue()
        }));
    },
    SetGridColumnHideAll: function(){
        
    },
    SetGridColumnDefault: function(){
    	
        
    	
    },
    listeners: {
        afterRender: function(component, eOpts){
        	var thisObj = this;

        	var ct_menu_ls = JSON.parse(localStorage.getItem('ct_menu_ls'));
        	if(ct_menu_ls != null){
        		if(ct_menu_ls.opsiCall != undefined){
	            	if(ct_menu_ls.opsiCall == "advanced"){
	            		Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu-BtnSimplifiedGrid').setVisible(true);
	            	}else{
	            		Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu-BtnSimplifiedGrid').setVisible(false);
	            	}
	            }

	            if(ct_menu_ls.ptextSearch != undefined){
	            	Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu-textSearch').setValue(ct_menu_ls.ptextSearch);
                }
                
                if(ct_menu_ls.parentMenuSearch != undefined){
	            	Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu-ComboParentMenu').setValue(ct_menu_ls.parentMenuSearch);
	            }
        	}

            var ct_menu_grid_ls = JSON.parse(localStorage.getItem('ct_menu_grid_ls'));
            if(ct_menu_grid_ls != null){
            	if(ct_menu_grid_ls.opsiShow != undefined){
	            	if(ct_menu_grid_ls.opsiShow == "custom"){
	            		//Sesuaikan
                        thisObj.SetGridColumnCustom(ct_menu_grid_ls.ColDisplayArr);
                        Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu-BtnSimplifiedGrid').setVisible(true);
	            	}else{
	            		//Tampilan Grid Column Default
                        thisObj.SetGridColumnDefault();
                        Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu-BtnSimplifiedGrid').setVisible(false);
	            	}
	            }
            }
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Define Store Main Grid
        thisObj.StoreGridMain       = Ext.create('Koltiva.store.menu.StoreMainGridMenu');
        var StoreComboMenuParent    = Ext.create('Koltiva.store.menu.StoreComboMenuParent');

        function searchByCategoryMenu(){
            Ext.getCmp('Koltiva.view.menu.MainGridMenu').setFilterLs();
            Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu').getStore().loadPage(1);
        }

        //Context Menu
        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu',{
            cls:'Sfr_ConMenu',
	        items:[
            {
	            icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls:'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu').getSelectionModel().getSelection()[0];

	                Ext.getCmp('Koltiva.view.menu.MainGridMenu').destroy(); //destory current view
                	var FormMainMenu = [];

                    //create object View untuk FormMainGrower
                    if(Ext.getCmp('Koltiva.view.menu.Form') == undefined){
                        FormMainMenu = Ext.create('Koltiva.view.menu.Form', {
                        	viewVar: {
                                opsiDisplay: 'update',
                                Tipe: 'update',
                                MenuId: sm.get('MenuId'),
                                PanelDisplayID: sm.get('PanelDisplayID')
	                        }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.menu.Form').destroy();
                        FormMainMenu = Ext.create('Koltiva.view.menu.Form', {
                            viewVar: {
                                opsiDisplay: 'update',
                                Tipe: 'update',
                                MenuId: sm.get('MenuId'),
                                PanelDisplayID: sm.get('PanelDisplayID')
	                        }
                        });
                    }
	            }
	        },{
	            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite',
	            hidden: m_act_delete,
	            handler: function(){
	                var sm = Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu').getSelectionModel().getSelection()[0];
                    
                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {

                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/menus/menu_api/delete_menu',
                                method: 'DELETE',
                                params: {
                                    MenuId: sm.get('MenuId'),
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
                                    thisObj.StoreGridMain.load();
                                },
                                failure: function(response, o) {
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

        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.3,
                layout: 'form',
                items:[{
    
                }]
            },{
                columnWidth: 0.7,
                xtype: 'panel',
                frame: false,
                id: 'Koltiva.view.Farmer.MainGrid-gridInformation',
                hidden:true,
                html: ''
            }]
        },{
        	xtype: 'grid',
            id: 'Koltiva.view.menu.MainGridMenu-GridMenu',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            //height: 550,
            viewConfig: {
                deferEmptyText: false,
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
                items: [
                {
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add'),
                    hidden: m_act_add,
                    handler: function() {
                        Ext.getCmp('Koltiva.view.menu.MainGridMenu').destroy(); //destory current view
                    	var FormMainMenu = [];

                        //create object View untuk FormMainGrower
                        if(Ext.getCmp('Koltiva.view.menu.Form') == undefined){
                            FormMainMenu = Ext.create('Koltiva.view.menu.Form', {
                            	viewVar: {
		                            opsiDisplay: 'insert'
		                        }
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.menu.Form').destroy();
                            FormMainMenu = Ext.create('Koltiva.view.menu.Form', {
                                viewVar: {
		                            opsiDisplay: 'insert'
		                        }
                            });
                        }
                    }
                    
                },{
                    xtype:'tbspacer',
                    flex:1
                },
                {
                    name: 'KategoryMenu',
                    id: 'Koltiva.view.menu.MainGridMenu-GridMenu-ComboParentMenu',
                    xtype: 'combo',
                    width: 200,
                    allowBlank: true,
                    store : StoreComboMenuParent,
                    valueField : 'id',
                    displayField :'label',
                    emptyText: lang('Parent Menu')+', '+lang('press_enter_search'),
                    listeners: {
                        change: function(cb, nv, ov) {
                            searchByCategoryMenu();
                        }
                    }
                    
                },
                {
                	name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.menu.MainGridMenu-GridMenu-textSearch',
                    xtype: 'textfield',
                    width: 500,
                    emptyText: lang('Cari berdasar Name / Module Menu')+', '+lang('press_enter_search'),
                    listeners: {
                        specialkey: thisObj.submitOnEnterGrid
                    }
                },{
                	xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/visible-field.png',
                	text:lang('Default Grid'),
                    hidden: true,
                	id:'Koltiva.view.menu.MainGridMenu-GridMenu-BtnSimplifiedGrid',
                    handler: function() {
                    	localStorage.setItem('ct_menu_ls', JSON.stringify({
				    		opsiCall: 'simple',
				            ptextSearch: Ext.getCmp('Koltiva.view.manu.MainGridMenu-GridMenu-textSearch').getValue()
				        }));

				        localStorage.setItem('ct_menu_grid_ls', JSON.stringify({
				    		opsiShow: 'default'
				        }));
				        thisObj.SetGridColumnDefault();

                    	thisObj.StoreGridMain.load();

                    	//Hilangkan Tombol
                    	Ext.getCmp('Koltiva.view.main.MainGridMenu-GridMenu-BtnSimplifiedGrid').setVisible(false);
                    }
                },{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/reload.png',
                    handler: function() {
                        Ext.getCmp('Koltiva.view.menu.MainGridMenu-GridMenu').getStore().loadPage(1);
                    }
                }]
            }],
            columns:[{
            	text: '',
                xtype:'actioncolumn',
                width:'4%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuGrid.showAt(e.getXY());
                    }
                }]
            },{
            	text: lang('Menu ID'),
	            dataIndex: 'MenuId',
	            hidden: true
            },{
            	text: lang('Menu Name'),
                dataIndex: 'MenuName',
                flex: 3            },{
                text: lang('Module'),
                dataIndex: 'MenuModule',
                flex: 3
            },{
            	text: lang('Show'),
                dataIndex: 'MenuShow',
                flex: 1,
                renderer: function (value) {
                    var RetVal;
                    if(value=="Yes")
                        RetVal = '<span class="text-success">'+value+'</span>';
                    else
                        RetVal = '<span class="text-warning">'+value+'</span>';
                    return RetVal;
                }
            },{
            	text: lang('Icon'),
                dataIndex: 'MenuIcon',
                flex: 2
            },{
            	text: lang('Menu Order'),
                dataIndex: 'MenuOrder',
                flex: 1
            },{
            	text: lang('Menu Jenis'),
                dataIndex: 'MenuJenis',
                hidden:true
            },{
            	text: lang('Param'),
                dataIndex: 'MenuParam',
                hidden:true
            }]
        }];

        this.callParent(arguments);
    }
});