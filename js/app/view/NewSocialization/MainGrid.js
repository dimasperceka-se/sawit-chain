Ext.define('Koltiva.view.NewSocialization.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.NewSocialization.MainGrid',
    renderTo: 'ext-content',
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;', 
    initComponent: function() {
        var thisObj = this;

        thisObj.contextMenuGrid = Ext.create('Ext.menu.Menu',{
		        items: [ 
				{
		            icon: varjs.config.base_url + 'images/icons/new/update.png',
		            text: lang('Update'),
		            hidden: !m_act_update,
		            id :'Koltiva.view.NewSocialization.MainGrid-Update', 
		            handler: function(){ 
						var sm = Ext.getCmp('Koltiva.view.NewSocialization.MainGrid-Grid').getSelectionModel().getSelection()[0]; 
						Ext.getCmp('Koltiva.view.NewSocialization.MainGrid').destroy(); //destory current view  
						if(Ext.getCmp('Koltiva.view.NewSocialization.MainForm') == undefined){
							var FormMainTraining = Ext.create('Koltiva.view.NewSocialization.MainForm', {
								opsiDisplay: 'update',
								IMSSocID: sm.get('IMSSocID'),
								EventName: sm.get('EventName')
							});
						}else{
							
							//destroy, create ulang 
							Ext.create('Koltiva.view.NewSocialization.MainForm', {
								opsiDisplay: 'update',
								IMSSocID: sm.get('IMSSocID'),
								EventName: sm.get('EventName')
							});
						}
						thisObj.contextMenuGrid.destroy();
						
						//cek sync data alert
						//diminta mas zen, utk mengetahui data participant dari mobile sudah di sync apa belum 
						Ext.Ajax.request({
							waitMsg: lang('Please Wait'),
							url: m_api + '/socialization/application_store/checkbelumsyncalert',
							method: 'POST',
							params: { IMSSocID : sm.get('IMSSocID') },
							success: function (response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj){
									Ext.fly("alertmissingSyncdata").update('<span class="col-md-12" style="background-color: #FAE88C; padding:10px; font-weight:bold;"> Ada '+ obj +' '+ lang ('peserta yang belum di sinkronisasi data umumnya, sehingga peserta tersebut tidak muncul dalam daftar di bawah ini.')+ '  <span>');
								}
							} 
						 });  
						 
					}
		        }, 
		        {
		            icon: varjs.config.base_url + 'images/icons/new/delete.png',
		            text: lang('Delete'),
		            hidden: !m_act_delete,
					id :'Koltiva.view.NewSocialization.MainGrid-Delete',
		            handler: function() {
		                var smb = Ext.getCmp('Koltiva.view.NewSocialization.MainGrid-Grid').getSelectionModel().getSelection()[0]
						Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini?'), function (btn) {
							if (btn == 'yes') {
								Ext.Ajax.request({
									waitMsg: lang('Please Wait'),
									 url: m_api + '/new_socialization/appform',
									method: 'DELETE',
									params: {IMSSocID: smb.raw.IMSSocID},
									success: function (response, opts) {
										var obj = Ext.decode(response.responseText);
										switch (obj.success) {
											case true:
												thisObj.storeMainGrid.load();
												break;
											default:
												Ext.MessageBox.alert('Warning', obj.message);
												break;
										}
									},
									failure: function (response, opts) {
										var obj = Ext.decode(response.responseText);
										Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
									}
								});
							}
						});
		            }
		        }
		        ]
		    });
		
		thisObj.storeMainGrid = Ext.create('Koltiva.store.NewSocialization.MainGrid'); 
		thisObj.MainGrid = {
						xtype: 'grid',
						id: 'Koltiva.view.NewSocialization.MainGrid-Grid',
						style: 'border:1px solid #CCC;margin-top:4px;',
						loadMask: true, 
						minHeight:125,
						store: thisObj.storeMainGrid,  
						viewConfig: {
							deferEmptyText: false,
							emptyText: lang('No data Available')
						}, 
						listeners: {
							itemclick: function(view, record, item, index, e){
								if(record.data.SocializationStatus == 2){
									Ext.getCmp('Koltiva.view.NewSocialization.MainGrid-Delete').show() 
								}else{
									Ext.getCmp('Koltiva.view.NewSocialization.MainGrid-Delete').hide() 
								}
							   thisObj.contextMenuGrid.showAt(e.getXY());
							}, 
							afterRender: function(store, v){
								  Ext.getCmp('Koltiva.view.NewSocialization.MainGrid-Grid').getStore().load();
							} 
						},
						dockedItems: [
						{
							xtype: 'pagingtoolbar',
							store: thisObj.storeMainGrid, // same store GridPanel is using
							dock: 'bottom',
							displayInfo: true
						},{
							xtype: 'toolbar',
							items: [{
								icon: varjs.config.base_url + 'images/icons/new/add.png',
								text: lang('Add'), 
								hidden: !m_act_add,
								cls:'Sfr_BtnGridGreen',
                    			overCls:'Sfr_BtnGridGreen-Hover',
								handler: function() {
									Ext.getCmp('Koltiva.view.NewSocialization.MainGrid').destroy(); //destory current view
									if(Ext.getCmp('Koltiva.view.NewSocialization.MainForm') == undefined){
										var FormMainTraining = Ext.create('Koltiva.view.NewSocialization.MainForm', {
											opsiDisplay: 'insert'
										});
									}else{
										//destroy, create ulang
										Ext.getCmp('Koltiva.view.NewSocialization.MainGrid').destroy(); 
										Ext.create('Koltiva.view.NewSocialization.MainForm', {
											opsiDisplay: 'insert'
										});
									}
									thisObj.contextMenuGrid.destroy();
								}
							},
							{
								icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
								text: lang('Export All'),
								cls:'Sfr_BtnGridPaleBlue',
                                overCls:'Sfr_BtnGridPaleBlue-Hover',
								handler: function() { 
									let url = m_api + '/socialization/application_store/exportexcelsocialization/';
									window.location.href = url;
								} 	
							},
							{
			                    xtype:'tbspacer',
			                    flex: 2
			                },
							{
								xtype: 'textfield',
								name: 'key',
								emptyText: lang('Search by Event Name'),
								id: 'key',
								flex: 1,
								baseCls:'Sfr_TxtfieldSearchGrid',
								listeners: {
									specialkey: function(field, event){
										if (event.getKey() == event.ENTER) {
								            thisObj.storeMainGrid.load({
								                params: {
								                    key: Ext.getCmp('key').getValue()
								                }
								            });
								        }
									}
								}
							},
							{
								xtype: 'button',
			                    margin: '0px 0px 0px 6px',
			                    icon: varjs.config.base_url + '/images/icons/new/reload.png',
			                    cls:'Sfr_BtnGridBlue',
			                    overCls:'Sfr_BtnGridBlue-Hover',
								handler: function () {
									thisObj.storeMainGrid.load({
										params: {
											key: Ext.getCmp('key').getValue()
										}
									});
								}
							},
							{
			                    xtype:'tbspacer',
			                    flex: 0
			                }]
						}
						], 
						columns: [{
				              text: lang('Action'),
				              xtype:'actioncolumn',
				              flex: 0.5,
				              items:[{
				                  icon: varjs.config.base_url + 'images/icons/new/action.png',
				                  handler: function(grid, rowIndex, colIndex, item, e, record) {
				                      thisObj.contextMenuGrid.showAt(e.getXY());
				                  }
				              }]
				        },{
							id: 'Koltiva.view.report.GridMainAppForm-colreportID',
							dataIndex: 'IMSSocID',
							hidden:true
						},{  
							text: lang('id acara'),
							flex:1,						
							dataIndex: 'IMSSocID',
							sortable: true,
							hidden:true
						},{   
							text: lang('nama acara'),
							flex:1,
							dataIndex: 'EventName', 
							sortable: true,
						},{  
							text: lang('batch'), 
							flex:1,
							// dataIndex: 'BatchNumber', // batch number sebagai patner id
							dataIndex: 'label',
							sortable: true,
						},{  
							text: lang('District'), 
							flex:1,
							dataIndex: 'DistrictName',
							sortable: true,
						}, {  
							text: lang('subdistrict'),
							flex:1,
							dataIndex: 'SubDistrictName',
							sortable: true,
						},{  
							text: lang('village'),
							flex:1,
							dataIndex: 'VillageName',
							sortable: true,
						},
						// {  
						// 	text: lang('Number of participant'),
						// 	flex:1,
						// 	dataIndex: 'peserta',
						// 	sortable: true,
						// },
						{  
							text: lang('Date of Event'),
							flex:1,
							dataIndex: 'EventStart',
							sortable: true,
						}, {  
							text: lang('hari'), 
							flex:1,
							dataIndex: 'EventDays',
							sortable: true,
						},{  
							text: lang('Certification Holder'), 
							flex:1,
							dataIndex: 'CertHolderOrgName',
							sortable: true,
						},{  
							text: lang('Status Event'), 
							flex:1,
							dataIndex: 'SocializationStatus',
							renderer: function(value, metaData, record, rowIndex, colIndex, store, view){
								let v = parseInt(value); 
								if (  v == 1) {
									return '<b >Complete</b>';
								}
								else{
									return '<b>On Going</b>';
								}
							},
							sortable: true,
						},
						{  
							text: lang('Date Updated'), 
							flex:1,
							dataIndex: 'DateUpdated',
							sortable: true,
						}]
			};
        thisObj.items = [thisObj.MainGrid];  
        this.callParent(arguments);
    }
});


