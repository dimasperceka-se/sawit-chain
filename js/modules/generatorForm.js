Ext.onReady(() => {

	let itemTabSection = (section) => {
		let opt = []

		for (var i = 0; i < section.length; i++) {
			if (section[i].sectionName !== null) {
				items = {
					xtype: 'panel',
					autoScroll: true,
					title: section[i].sectionName,
					id: section[i].programid + '-' + section[i].sectionId,
					padding: 5,
					style: 'border:2px solid #D6EDA4',
					items: [{
						layout: 'column',
						border: false,
						items: [{
							columnWidth: .5,
							layout: 'form',
							padding: 3,
							border: false,
							id: 'contentSection_' + section[i].sectionId
						}]
					}],

				}
			}
			opt.push(items);
		}
		Ext.getCmp('sectionTab').down().tabPanel.removeAll()
		return {
			preview: () => Ext.getCmp('sectionTab').add(opt),
			stringOnly: opt
		}
	}

	let getOptionById = (optionElements) => {
		let loadOption = itemsData(optionElements.programid, optionElements.sectionId, optionElements.optionid);
		return new Promise((resolve, reject) => {
			loadOption.on('load', (store, records, successful) => {
				let elements = store.getProxy().getReader().rawData.data;
				let fromStore = [];
				elements.forEach((element) => {
					// The data store containing the list of dataStore
					items = {
						"val": element.optVal,
						"label": element.OpsiName
					}
					fromStore.push(items);
				})
				let dataStore = Ext.create('Ext.data.Store', {
					fields: ['val', 'label'],
					data: fromStore
				});
				let optType = {
					xtype: 'combobox',
					fieldLabel: elements[0].elementName,
					labelAlign: 'top',
					store: dataStore,
					queryMode: 'local',
					valueField: 'val',
					displayField: 'label',
				}
				resolve(optType);
			});
		});
	}

	let contentSection = (programAndSectionId) => {
		let programSection = programAndSectionId.split('-');
		let storeElement = itemsData(programSection[0], programSection[1]);
		var items = []
		storeElement.on('load', (store, records, successful) => {
			let elements = store.getProxy().getReader().rawData.data;
			elements.forEach((element) => {
				let type;
				switch (element.xtype) {
					case "LONG_TEXT":
						type = 'textarea'
						break;
					case "FILE_RESOURCE":
						type = 'fileuploadfield'
						break;
					case "PHONE_NUMBER":
						type = 'textfield'
						break;
					case "DATE":
						type = 'datefield'
						break;
					default:
						type = 'textfield'
						break;
				}
				let options = {
					xtype: type,
					labelAlign: 'top',
					fieldLabel: element.elementName,
					name: element.fields || 'unkownName',
					allowBlank: false
				}
				if (element.optid != null) {
					let optionElements = {
						programid: element.programid,
						sectionId: element.sectionId,
						optionid: element.optid
					}
					items.push(getOptionById(optionElements));
				} else {
					items.push(Promise.resolve(options));
				}
			})
			Promise.all(items).then(opt => {
				let contentPanelSection = Ext.getCmp('contentSection_' + programSection[1])
				if (contentPanelSection.items.getRange().length > 0) {
					contentPanelSection.show()
				} else {
					contentPanelSection.add(opt);
				}
			})
		})

	}

	let itemsData = (programid, sectionId, optionid) => {
		programid = programid || '';
		if (typeof sectionId === 'undefined') {
			sectionId = '';
		} else {
			sectionId = '/' + sectionId;
		}
		if (typeof optionid !== 'undefined') {
			optionid = '/' + optionid;
		} else {
			optionid = '';
		}
		let store = Ext.create('Ext.data.Store', {
			extend: 'Ext.data.Model',
			fields: ['programName', 'programid'],
			autoLoad: true,
			proxy: {
				type: 'rest',
				url: m_api + 'formgenerator/getPrograms/' + programid + sectionId + optionid,
				reader: {
					type: 'json',
					root: 'data',
					totalProperty: 'total'
				}
			}
		})
		return store;
	}


	Ext.create('Ext.panel.Panel', {
		width: 1200,
		height: 600,
		layout: 'border',
		items: [{
			title: 'Programs',
			region: 'west',
			xtype: 'panel',
			margins: '5 0 0 5',
			width: 200,
			collapsible: true, // make collapsible
			id: 'west-region-container',
			// layout: 'fit',
			// items: 
			layout: {
				type: 'vbox',
				align: 'stretch',
				pack: 'start',
			},
			items: [{
					xtype: 'grid',
					store: itemsData(),
					columns: [{
						header: 'Modules',
						dataIndex: 'programName',
						width: 150,
					}, {
						xtype: 'actioncolumn',
						width: 50,
						items: [{
							icon: varjs.config.base_url + 'images/icons/silk/page_white_edit.png',
							tooltip: 'View',
							handler: function(grid, rowIndex, colIndex) {
								let rec = grid.getStore().getAt(rowIndex);

								if (typeof Ext.getCmp('section') !== 'undefined') {
									Ext.getCmp('section').destroy();
								}
								let storeSection = itemsData(rec.get('programid'));
								storeSection.on({
									'load': (store, records, successful) => {
										let section = store.getProxy().getReader().rawData.data;
										itemTabSection(section).preview();
										Ext.getCmp('sectionTab').setActiveTab(0);
									}
								});
							}
						}, {
							icon: varjs.config.base_url + 'images/icons/silk/page_white_view.png',
							tooltip: 'Generate',
							handler: function(grid, rowIndex, colIndex) {
								let rec = grid.getStore().getAt(rowIndex);
								let storeSection = itemsData(rec.get('programid'));
								storeSection.on({
									'load': (store, records, successful) => {
										let dialog = Ext.create('Ext.window.Window', {
											title: 'Generate',
											width: 300,
											height: 240,
											modal: true,
											constraint: true,
											resizable: true,
											layout: {
												type: 'border'
											},
											items: [{
												xtype: 'panel',
												buttonAlign: 'right',
												padding: '10 5 10 10',
												items: [{
													xtype: 'textfield',
													id: 'ProgramName',
													fieldLabel: 'Name',
													name: 'activedate',
													disabled: true
												}, {
													xtype: 'datefield',
													fieldLabel: 'Active Date',
													name: 'activedate',
													allowBlank: true
												}, {
													xtype: 'textarea',
													fieldLabel: 'Description',
													name: 'description',
													allowBlank: true
												}],
												buttons: [{
													text: 'Save',
													iconCls: 'save',
													handler: function(c) {
														let section = JSON.stringify(store.getProxy().getReader().rawData.data);
														Ext.Ajax.request({
															url: m_api + '/Formgenerator/createFile',
															method: 'POST',
															waitTitle: 'Connecting',
															waitMsg: 'Sending data...',
															params: {
																"section": section,
															},
															// success: success => console.log(success),
															failure: err => console.error(err)
														});

													}
												}, {
													text: 'Cancel',
													iconCls: 'cancel',
													handler: function(c) {
														dialog.close();
													}
												}],
												listeners: {
													beforerender:() => {
														Ext.getCmp('ProgramName').setValue(rec.get('programName').replace(' ','')+'.js');
													}
												}
											}]
										}).show();
										// Ext.MessageBox.prompt('Name', 'Please enter your name:', (ok, name) => {
										// console.log(name)
										// });

										// console.log(Ext.getCmp('sectionTab').items.getRange());
										// let items = JSON.stringify(itemTabSection(section).stringOnly);
										// console.log(section);
									}
								});
							}
						}]
					}],
					height: 700,
					width: 400,


				}
				// , {
				// 	id: '',
				// 	title: 'asdf',
				// }
				// , {
				// 	items: itemsData,
				// 	flex: 2
				// }
			]

		}, {
			region: 'center', // center region is required, no width/height specified
			xtype: 'panel',
			layout: 'fit',
			margins: '5 5 0 0',
			items: [{
				xtype: 'tabpanel',
				flex: 1,
				margin: 2,
				activeTab: 0,
				plain: true,
				cls: 'tabSce',
				id: 'sectionTab',
				listeners: {
					'tabchange': function(tabPanel, tab) {
						contentSection(tab.id);
					}
				}
			}]
		}],
		renderTo: 'ext-content'
	});
})