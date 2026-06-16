Ext.onReady(function(){
	Ext.tip.QuickTipManager.init();
	
	
	var contextMappingGrid = Ext.create('Ext.menu.Menu',{
		items:[{
			icon: varjs.config.base_url + 'images/icons/new/view.png',
			text: lang('Add'),
			hidden: true,
			handler: function(){
				var sm = Ext.getCmp('gridprogstage').getSelectionModel().getSelection()[0];
				var winFormMapping = Ext.create('Koltiva.view.DataAdm.MainForm');
				var id_mapping=sm.get('mw_mapping_id');
				winFormMapping.setFormVar({MappingId:sm.get('mw_mapping_id'),DeName:sm.get('de_name'),DeUid:sm.get('de_uid'),IdProgram:sm.get('program_uid'),opsiDisplay:'insert'});

				if (!winFormMapping.isVisible()) {
					winFormMapping.center();
					winFormMapping.show();
				} else {
					winFormMapping.close();
				}
			}

		},{
			icon: varjs.config.base_url + 'images/icons/new/update.png',
			text: lang('Update'),
			handler: function(){
				var sm = Ext.getCmp('gridprogstage').getSelectionModel().getSelection()[0];
				var winFormMapping = Ext.create('Koltiva.view.DataAdm.MainForm');
			//	winFormMapping.setFormVar({MappingId:sm.get('program_uid'),opsiDisplay:'update'});
				if(sm.get('mw_mapping_id')){
					winFormMapping.setFormVar({MappingId:sm.get('mw_mapping_id'),opsiDisplay:'update'});
					if (!winFormMapping.isVisible()) {
						winFormMapping.center();
						winFormMapping.show();
					} else {
						winFormMapping.close();
					}
				} else {
					winFormMapping.setFormVar({DeName:sm.get('de_name'),DeUid:sm.get('de_uid'),IdProgram:sm.get('program_uid'),opsiDisplay:'insert'});

					if (!winFormMapping.isVisible()) {
						winFormMapping.center();
						winFormMapping.show();
					} else {
						winFormMapping.close();
					}
				}
			}
		},{
			icon: varjs.config.base_url + 'images/icons/new/delete.png',
			text: lang('Reset Mapping'),
			hidden: m_act_delete,
			handler: function(){
				var sm = Ext.getCmp('gridprogstage').getSelectionModel().getSelection()[0];
				if(!sm.get('mw_mapping_id')){
					alert('Cannot reset this mapping data!');
					return;
				}
				Ext.MessageBox.confirm(lang('Message'), lang('Do you want to reset this mapping data ?'), function(btn) {
					if (btn == 'yes') {
						Ext.Ajax.request({
							waitMsg: 'Please Wait',
							url: m_api + '/map_metadata/MappingData_form',
							method: 'DELETE',
							params: {
								MappingId:sm.get('mw_mapping_id')
							},
							  
							success: function(response, opts) {
								Ext.MessageBox.show({
									title: 'Information',
									msg: lang('Mapping Data Reseted'),
									buttons: Ext.MessageBox.OK,
									animateTarget: 'mb9',
									icon: 'ext-mb-success'
								});

								//refresh store
								Ext.data.StoreManager.lookup('Koltiva-Store-Metadata-Gridmain').load();
							},
							failure: function(rp, o) {
								try {
									var r = Ext.decode(rp.responseText);
									Ext.MessageBox.show({
										title: 'Error',
										msg: r.message,
										buttons: Ext.MessageBox.OK,
										animateTarget: 'mb9',
										icon: 'ext-mb-error'
									});
								}
								catch(err) {
									Ext.MessageBox.show({
										title: 'Error',
										msg: 'Connection Error',
										buttons: Ext.MessageBox.OK,
										animateTarget: 'mb9',
										icon: 'ext-mb-error'
									});
								}                                }
						});
					}
				});
			}
		}]
	});

	 var prog_stage = Ext.create('Ext.data.Store', {
		 extend: 'Ext.data.Model',
		 fields: ['uid', 'name'],
		 autoLoad: true,
		 pageSize: 10,
		 proxy: {
			 type: 'ajax',
			 url: m_prog_stage,
			 reader: {
				 type: 'json',
				 root: 'data'
			 }
		 }
     });
     
     var metadata = Ext.create('Ext.data.Store', {
		extend: 'Ext.data.Model',
		storeId:'Koltiva-Store-Metadata-Gridmain',
        fields: ['program_uid','name','sec_name','de_uid','de_name','mw_mapping_id','table_reff','field_reff','custom_function','executeSql','priority'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_metadata,
            reader: {
                type: 'json',
                root: 'data'
            }
		},
		
		listeners: {
            beforeload: function(store, operation) {
                  store.proxy.extraParams.ProgStageId = Ext.getCmp('filter-ProgStage').getValue();
            }
		}
		
    });
	/*
	 var prog_stage = Ext.create('Ext.data.Store', {
		 extend: 'Ext.data.Model',
		 fields: ['programstageid', 'programid', 'name', 'programName', 'description', 'reference','status', 'order'],
		 autoLoad: true,
		 pageSize: 10,
		 proxy: {
			 type: 'ajax',
			 url: m_prog_stage,
			 reader: {
				 type: 'json',
				 root: 'data'
			 }
		 }
	 });*/
	// store.loadPage(1);
   
	 
	function filterRecord() {
       
		metadata.load({
            params: {
                start: 0,
                ProgStageId: Ext.getCmp('filter-ProgStage').getValue(),
            }
        });
    }


	 
	 var gridprogstage = Ext.create('Ext.grid.Panel', {
		store: metadata,
		width: '100%',
		minHeight:250,
		id:'gridprogstage',
		style: 'border:1px solid #CCC;',
		renderTo: 'ext-content',
		loadMask: true,
		selType: 'rowmodel',
		dockedItems: [{
			   xtype: 'pagingtoolbar',
			   store: metadata,   // same store GridPanel is using
			   dock: 'bottom',
			   displayInfo: true
		},
		{
			 xtype: 'toolbar',
			 items: [
			{
				 xtype: 'combobox',
				 id: 'filter-ProgStage',
				 emptyText: 'Program Name',
				 store: prog_stage,
				 queryMode: 'local',
				 displayField: 'name',
				 valueField: 'uid',
			 },
			 {
				 xtype: 'button',
				 margin: '0px 0px 0px 6px',
				 text: 'Search',
				 handler: function () {
					 filterRecord();
				 }
			 },
			 {
				 xtype:'container',
				 flex:1
			 },
			 ]
		}],
		columns: [
		{ 
			dataIndex: 'programid',
			hidden:true
		},{
			text: lang('Action'),
			xtype:'actioncolumn',
			width:'5%',
			items:[{
				icon: varjs.config.base_url + 'images/icons/new/action.png',
				handler: function(grid, rowIndex, colIndex, item, e, record) {
					contextMappingGrid.showAt(e.getXY());
				}
			}]
		},
		{
			 text: 'No',
			 xtype: 'rownumberer',
			 width:'5%'
		}, {
			text: 'mw_mapping_id',
			hidden: true,
			width: '20%',						
            dataIndex: 'mw_mapping_id'
        },      
		{
            text: 'Name',
            width: '20%',
            dataIndex: 'name'
        },{
            text: 'Sec Name',
            width: '20%',
            dataIndex: 'sec_name'
        },{
            text: 'Data Elemen',
            width: '20%',
            dataIndex: 'de_name'
        },{
            text: 'Table Reff',
            width: '20%',
            dataIndex: 'table_reff'
        },{
            text: 'Field Reff',
            width: '20%',
            dataIndex: 'field_reff'
        },{
            text: 'Custom Function',
            width: '20%',
            dataIndex: 'custom_function'
        },{
            text: 'Execute SQL',
            width: '20%',
            dataIndex: 'executeSql'
        },{
            text: 'Priority',
            width: '20%',
            dataIndex: 'priority'
        }]
 
	 });
 
	 
	 
 });
 