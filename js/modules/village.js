Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
	//**Start Infrastructure Village**//
	var storeInfrastructure = Ext.create('Ext.data.Store', {
        model: 'Infrastructure.Model',
		fields: ['InfrastructureID', 'VillageID', 'InfrastructureType', 'InfrastructureName', 'Latitude', 'Longitude'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crudInfrastructure + 's',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

	function InfrastructureSet(InfrastructureID) {
		Ext.Ajax.request({
			url: m_crudInfrastructure,
			method: 'GET',
			params: {id: InfrastructureID},
			success: function(fp, o){
				var r = Ext.decode(fp.responseText);
				Ext.getCmp('InfrastructureID').setValue(r.InfrastructureID);
				Ext.getCmp('InfrastructureVillageID').setValue(r.VillageID);
				Ext.getCmp('InfrastructureType').setValue({InfrastructureType : r.InfrastructureType});
				Ext.getCmp('InfrastructureName').setValue(r.InfrastructureName);
				Ext.getCmp('Latitude').setValue(r.Latitude);
				Ext.getCmp('Longitude').setValue(r.Longitude);
			}
      });
    }

	var InfrastructureForm = Ext.widget('form', {
        frame: false,
        height: 280,
        autoScroll: true,
        width: 600,
        bodyPadding: 15,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150
        },
        items: [{
                xtype: 'textfield',
                id: 'InfrastructureVillageID',
                name: 'InfrastructureVillageID',
                inputType: 'hidden'
            }, {
                xtype: 'textfield',
                id: 'InfrastructureID',
                name: 'InfrastructureID',
                inputType: 'hidden'
            }, {
                xtype: 'radiogroup',
				anchor: '100%',
                fieldLabel: 'Infrastructure Type<b style="color:red">*</b> ',
				allowBlank: false,
				id: 'InfrastructureType',
                items: [
                    {boxLabel: 'School', name: 'InfrastructureType', inputValue: 1},
                    {boxLabel: 'Health Facility', name: 'InfrastructureType', inputValue: 2},
                    {boxLabel: 'Others', name: 'InfrastructureType', inputValue: 3},
                ]
            }, {
                xtype: 'textfield',
				anchor: '100%',
                fieldLabel: 'Infrastructure Name<b style="color:red">*</b> ',
                id: 'InfrastructureName',
                name: 'InfrastructureName',
				allowBlank: false
            }, {
                xtype: 'numberfield',
				hideTrigger: true,
				decimalPrecision: 4,
				anchor: '50%',
                fieldLabel: 'Latitude ',
                id: 'Latitude',
                name: 'Latitude',
                readOnly: m_hakakses_lat_short
            }, {
                xtype: 'numberfield',
				hideTrigger: true,
				decimalPrecision: 4,
				anchor: '50%',
                fieldLabel: 'Longitude ',
                id: 'Longitude',
                name: 'Longitude',
                readOnly: m_hakakses_long_short
            }],
        buttons: [{
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('InfrastructureID').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'PUT';

                    form.submit({
                        url: m_crudInfrastructure,
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
							winInfrastructureForm.hide(this, function() {
								var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
								storeInfrastructure.load({
									params: {
										id: vl.get('id')
									}
								});
							});
                        },
						failure : function(fp, o) {
                            Ext.MessageBox.alert('Warning', 'Please check your input data.');
						}
                    });
                }
            }, {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winInfrastructureForm.hide();
                }
            }]
    });

	var winInfrastructureForm = Ext.create('widget.window', {
        title: 'Village Infrastructure Form',
        id: 'winInfrastructureForm',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 600,
        frame: false,
        minWidth: 370,
        height: 280,
        layout: {
            type: 'fit'
        },
        items: [InfrastructureForm]
    });

	function displayInfrastructureFormWindow() {
        if (!winInfrastructureForm.isVisible()) {
            InfrastructureForm.getForm().reset();
            winInfrastructureForm.show();
        } else {
            winInfrastructureForm.hide(this, function() {
            });
            winInfrastructureForm.toFront();
        }

    }

	var contextMenuInfrastructure = Ext.create('Ext.menu.Menu', {
        items: [{
				icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: 'Edit',
                handler: function() {
					var inf = Ext.getCmp('gridInfrastructureList').getSelectionModel().getSelection()[0];
					displayInfrastructureFormWindow();
					InfrastructureSet(inf.get('InfrastructureID'));
                }
            }, {
				icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: 'Delete',
                handler: function() {
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
						if(btn == 'yes'){
							var inf = Ext.getCmp('gridInfrastructureList').getSelectionModel().getSelection()[0];
							Ext.Ajax.request({
								waitMsg: lang('Please Wait'),
								url: m_crudInfrastructure,
								method : 'DELETE',
								params: {id: inf.get('InfrastructureID')},
								success: function(response, opts){
									var obj = Ext.decode(response.responseText);
									switch(obj.success){
										case true:
											var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
											storeInfrastructure.load({
												params: {
													id: vl.get('id')
												}
											});
											break;
										default: Ext.MessageBox.alert('Warning',obj.message);
										break;
									}
								},
								failure: function(response, opts){
									var obj = Ext.decode(response.responseText);
									Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
								}
							});
						}
					});
                }
            }]
    });

	var winInfrastructureList = Ext.create('widget.window', {
        title: 'Village Infrastructure List',
        id: 'winInfrastructureList',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 1000,
        frame: false,
        minWidth: 370,
        height: 400,
        layout: {
            type: 'fit'
        },
        items: [{
				id: 'gridInfrastructureList',
                xtype: 'gridpanel',
                store: storeInfrastructure,
                style: 'border:1px solid #CCC;',
                //renderTo: 'ext-content',
                loadMask: true,
                selType: 'rowmodel',
                dockedItems: [{
                        store: storeInfrastructure,
						xtype: 'pagingtoolbar',
                        store: storeInfrastructure, // same store GridPanel is using
                        dock: 'bottom',
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        items: [
                            {
                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                text: lang('Add'),
                                scope: this,
                                cls: m_act_add,
								handler: function() {
                                    displayInfrastructureFormWindow();
									var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
									Ext.getCmp('InfrastructureVillageID').setValue(vl.get('id'));
                                }
                            }]
                    }],
                columns: [{
                        text: lang('InfrastructureID'),
                        dataIndex: 'InfrastructureID',
                        hidden: true
                    }, {
                        text: lang('VillageID'),
                        dataIndex: 'VillageID',
                        hidden: true
                    }, {
                        text: lang('Infrastructure Type'),
                        dataIndex: 'InfrastructureType',
                        width: '30%'
                    }, {
                        text: lang('Infrastructure Name'),
                        dataIndex: 'InfrastructureName',
                        width: '30%',
                    }, {
                        text: lang('Latitude'),
                        dataIndex: 'Latitude',
                        width: '20%'
                    }, {
                        text: lang('Longitude'),
                        dataIndex: 'Longitude',
                        width: '20%'
                    }],
                listeners: {
                    itemclick: function(view, record, item, index, e){
						contextMenuInfrastructure.showAt(e.getXY());
					}
                }
            }],
        buttons: [{
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winInfrastructureList.hide();
                }
            }]
    });

	function displayInfrastructureListWindow() {
        if (!winInfrastructureList.isVisible()) {
            winInfrastructureList.show();
        } else {
            winInfrastructureList.hide(this, function() {
            });
            winInfrastructureList.toFront();
        }
    }
	//**End Infrastructure Village**//
	//**Start Crop Village**//
	var storeCrop = Ext.create('Ext.data.Store', {
        model: 'Crop.Model',
		fields: ['VillageCropID', 'VillageID', 'CropName', 'CropYear', 'CropFarmers', 'CropHectares', 'CropProduction'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crudCrop + 's',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

	var CropForm = Ext.widget('form', {
        frame: false,
        height: 300,
        autoScroll: true,
        width: 500,
        bodyPadding: 15,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150
        },
        items: [{
                xtype: 'textfield',
                id: 'VillageCropID',
                name: 'VillageCropID',
                inputType: 'hidden'
            },{
                xtype: 'textfield',
                id: 'CropVillageID',
                name: 'CropVillageID',
                inputType: 'hidden'
            }, {
                id: 'CropName',
				anchor: '100%',
                xtype: 'combo',
                queryMode: 'local',
                triggerAction: 'all',
                forceSelection: true,
				allowBlank: false,
                editable: false,
                fieldLabel: 'Crop Name<b style="color:red">*</b> ',
                name: 'CropName',
                displayField: 'name',
                valueField: 'value',
                store: Ext.create('Ext.data.Store', {
                    fields: ['name', 'value'],
                    data: [
                        {name: 'Kakao', value: '1'},
                        {name: 'Jagung', value: '2'},
                        {name: 'Sawit', value: '3'},
                        {name: 'Karet', value: '4'},
                        {name: 'Cengkeh', value: '5'},
                        {name: 'Padi', value: '6'},
                        {name: 'Buah-buahan', value: '7'},
                        {name: 'Kayu-kayuan', value: '8'},
                    ]
                })
            }, {
                xtype: 'numberfield',
				hideTrigger: true,
				maxLength: 4,
				width: 250,
                fieldLabel: 'Crop Year<b style="color:red">*</b> ',
                id: 'CropYear',
                name: 'CropYear',
				allowBlank : false
            }, {
                xtype: 'numberfield',
				hideTrigger: true,
				hideTrigger: true,
				anchor: '100%',
                fieldLabel: 'Crop Farmers ',
                id: 'CropFarmers',
                name: 'CropFarmers'
            }, {
                xtype: 'numberfield',
				hideTrigger: true,
				hideTrigger: true,
				anchor: '100%',
                fieldLabel: 'Crop Hectares (Ha) ',
                id: 'CropHectares',
                name: 'CropHectares'
            }, {
                xtype: 'numberfield',
				hideTrigger: true,
				anchor: '100%',
                fieldLabel: 'Crop Production (Kg) ',
                id: 'CropProduction',
                name: 'CropProduction'
            }],
        buttons: [{
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('VillageCropID').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'PUT';

                    form.submit({
                        url: m_crudCrop,
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
							winCropForm.hide(this, function() {
								var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
								storeCrop.load({
									params: {
										id: vl.get('id')
									}
								});
							});
                        },
						failure : function(fp, o) {
                            Ext.MessageBox.alert('Warning', 'Please check your input data.');
                        }
                    });
                }
            }, {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winCropForm.hide();
                }
            }]
    });

	var winCropForm = Ext.create('widget.window', {
        title: 'Village Crop Form',
        id: 'winCropForm',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 500,
        frame: false,
        minWidth: 370,
        height: 300,
        layout: {
            type: 'fit'
        },
        items: [CropForm]
    });

	function displayCropFormWindow() {
        if (!winCropForm.isVisible()) {
            CropForm.getForm().reset();
            winCropForm.show();
        } else {
            winCropForm.hide(this, function() {
            });
            winCropForm.toFront();
        }

    }

	function cropSet(VillageCropID) {
		Ext.Ajax.request({
			url: m_crudCrop,
			method: 'GET',
			params: {id: VillageCropID},
			success: function(fp, o){
				var r = Ext.decode(fp.responseText);
				Ext.getCmp('VillageCropID').setValue(r.VillageCropID);
				Ext.getCmp('CropVillageID').setValue(r.VillageID);
				Ext.getCmp('CropName').setValue(r.CropName);
				Ext.getCmp('CropYear').setValue(r.CropYear);
				Ext.getCmp('CropFarmers').setValue(r.CropFarmers);
				Ext.getCmp('CropHectares').setValue(r.CropHectares);
				Ext.getCmp('CropProduction').setValue(r.CropProduction);
			}
      });
    }

	var contextMenuCrop = Ext.create('Ext.menu.Menu', {
        items: [{
				icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: 'Edit',
                handler: function() {
					var cr = Ext.getCmp('gridCropList').getSelectionModel().getSelection()[0];
					displayCropFormWindow();
					cropSet(cr.get('VillageCropID'));
                }
            }, {
				icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: 'Delete',
                handler: function() {
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
						if(btn == 'yes'){
							var cr = Ext.getCmp('gridCropList').getSelectionModel().getSelection()[0];
							Ext.Ajax.request({
								waitMsg: lang('Please Wait'),
								url: m_crudCrop,
								method : 'DELETE',
								params: {id: cr.get('VillageCropID')},
								success: function(response, opts){
									var obj = Ext.decode(response.responseText);
									switch(obj.success){
										case true:
											var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
											storeCrop.load({
												params: {
													id: vl.get('id')
												}
											});
											break;
										default: Ext.MessageBox.alert('Warning',obj.message);
										break;
									}
								},
								failure: function(response, opts){
									var obj = Ext.decode(response.responseText);
									Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
								}
							});
						}
					});
                }
            }]
    });

	var winCropList = Ext.create('widget.window', {
        title: 'Village Crop List',
        id: 'winCropList',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 1000,
        frame: false,
        minWidth: 370,
        height: 400,
        layout: {
            type: 'fit'
        },
        items: [{
				id: 'gridCropList',
                xtype: 'gridpanel',
                store: storeCrop,
                style: 'border:1px solid #CCC;',
                //renderTo: 'ext-content',
                loadMask: true,
                selType: 'rowmodel',
                dockedItems: [{
						store: storeCrop,
                        xtype: 'pagingtoolbar',
                        store: storeCrop, // same store GridPanel is using
                        dock: 'bottom',
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        items: [
                            {
                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                text: lang('Add'),
                                scope: this,
                                cls: m_act_add,
                                handler: function() {
                                    displayCropFormWindow();
									var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
									Ext.getCmp('CropVillageID').setValue(vl.get('id'));
                                }
                            }]
                    }],
                columns: [{
                        text: lang('VillageCropID'),
                        dataIndex: 'VillageCropID',
                        hidden: true
                    }, {
                        text: lang('VillageID'),
                        dataIndex: 'VillageID',
                        hidden: true
                    }, {
                        text: lang('Crop Name'),
                        dataIndex: 'CropName',
                        width: '30%'
                    }, {
                        text: lang('Crop Year'),
                        dataIndex: 'CropYear',
                        width: '10%',
                    }, {
                        text: lang('Crop Farmers'),
                        dataIndex: 'CropFarmers',
                        width: '20%',
                    }, {
                        text: lang('Crop Hectares (Ha)'),
                        dataIndex: 'CropHectares',
                        width: '20%',
                    }, {
                        text: lang('Crop Production (Kg)'),
                        dataIndex: 'CropProduction',
                        width: '20%',
                    }],
                listeners: {
                    itemclick: function(view, record, item, index, e){
						contextMenuCrop.showAt(e.getXY());
					}
                }
            }],
        buttons: [{
			text: 'Close',
			margin: '5px',
			scale: 'large',
			ui: 's-button',
			cls: 's-grey',
			disabled: false,
			handler: function() {
				winCropList.hide();
			}
		}]
    });

	function displayCropListWindow() {
        if (!winCropList.isVisible()) {
			winCropList.show();
        } else {
            winCropList.hide(this, function() {
            });
            winCropList.toFront();
        }
    }
	//**End Crop Village**//
	//**Start Village**//
	var store = Ext.create('Ext.data.Store', {
        pageSize: 50,
        autoLoad: true,
        remoteSort: true,
		fields: ['id', 'Village', 'SubDistrictID', 'SubDistrict', 'DistrictID', 'District', 'ProvinceID', 'Province', 'VillageHeadName', 'VillageHeadGender', 'VillageHeadLatitude', 'VillageHeadLongitude'],
        proxy: {
            type: 'ajax',
            extraParams: {
                prov: m_param,
                kab: m_DistrictID,
                kec: m_SubDistrictID,
            },
            url: m_crud + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            },
        },
        sorters: {property: 'id', direction: 'ASC'},
        groupField: 'SubDistrict',
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.key = Ext.getCmp('key').getValue();
                // store.proxy.extraParams.kab = Ext.getCmp('Kab').getValue();
            }
        }
    });

	var mc_Provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Provinsi,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Kabupaten,
            extraParams: {
                prov: m_param
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

	var mc_KabupatenForm = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_KabupatenForm,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Kecamatan,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    // mc_Kabupaten.on('load', function(st) {
    //     if (Ext.getCmp('Kab').getValue().length == 0)
    //         Ext.getCmp('Kab').setValue([st.getAt('0').get('label')]);
    //     store.load({
    //         params: {
    //             key: Ext.getCmp('key').getValue(),
    //             kab: Ext.getCmp('Kab').getValue(),
    //         }
    //     });
    // });

	function villageSet(villageID) {
        if (villageID) {
            console.log('here');
    		Ext.Ajax.request({
    			url: m_crud,
    			method: 'GET',
    			params: {id: villageID},
    			success: function(fp, o){
    				var r = Ext.decode(fp.responseText);
    				Ext.getCmp('VillageID_old').setValue(r.VillageID);
    				Ext.getCmp('Provinsi').setValue(r.ProvinceID).enable();
    				mc_KabupatenForm.load({
    					params: {
    						key: r.ProvinceID
    					}
    				});
    				Ext.getCmp('Kabupaten').enable();
    				Ext.getCmp('Kabupaten').setValue(r.DistrictID);
    				mc_Kecamatan.load({
    					params: {
    						key: r.DistrictID
    					}
    				});
    				Ext.getCmp('Kecamatan').enable();
    				Ext.getCmp('Kecamatan').setValue(r.SubDistrictID);
    				Ext.getCmp('VillageID').setValue(r.VillageID);
    				Ext.getCmp('Village').setValue(r.Village);
    				Ext.getCmp('VillageHeadName').setValue(r.VillageHeadName);
    				Ext.getCmp('VillageHeadGender').setValue({VillageHeadGender : r.VillageHeadGender});
    				Ext.getCmp('VillageHeadLatitude').setValue(r.VillageHeadLatitude);
    				Ext.getCmp('VillageHeadLongitude').setValue(r.VillageHeadLongitude);
    			}
          });
        } else {
            if (m_param) Ext.getCmp('Provinsi').setValue(m_param).disable();
            if (m_DistrictID) Ext.getCmp('Kabupaten').setValue(m_DistrictID).disable();
            if (m_SubDistrictID) Ext.getCmp('Kecamatan').setValue(m_SubDistrictID).disable();
        }
    }

	function villageIDSet(SubDistrictID, VillageID_old) {
		//Ext.getCmp('VillageID').setValue(SubDistrictID);
		Ext.Ajax.request({
			url: m_villageID,
			method: 'GET',
			params: {id: SubDistrictID, id_old: VillageID_old},
			success: function(fp, o){
				var r = Ext.decode(fp.responseText);
				Ext.getCmp('VillageID').setValue(r);
			}
		});
    }

	var VillageForm = Ext.widget('form', {
        frame: false,
        height: 590,
        autoScroll: true,
        width: 500,
		tes:'tes',
        bodyPadding: 10,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150
        },
        items: [{
                xtype: 'textfield',
                id: 'VillageID_old',
                name: 'VillageID_old',
                inputType: 'hidden'
            },
			{
				id: 'Provinsi',
				xtype: 'combobox',
				anchor: '100%',
				name: 'Provinsi',
				fieldLabel: lang('Provinsi')+ ' ',
				store: mc_Provinsi,
				displayField: 'label',
				valueField: 'id',
				queryMode: 'local',
                // forceSelection: true,
				typeAhead: true,
				allowBlank: false,
				// readOnly: true,
				// value: m_param,
                listeners: {
                    change: function (cb, nv, ov) {
                        mc_KabupatenForm.load({
                            params: {
                                key: Ext.getCmp('Provinsi').getValue()
                            }
                        });
                        // Ext.getCmp('Kabupaten').enable();
                    }
                },
			}, {
				id: 'Kabupaten',
				name: 'Kabupaten',
				xtype: 'combo',
				anchor: '100%',
				fieldLabel: lang('Kabupaten')+ ' ',
				store: mc_KabupatenForm,
				displayField: 'label',
				valueField: 'id',
				queryMode: 'local',
				allowBlank: false,
				listeners: {
					change: function (cb, nv, ov) {
						mc_Kecamatan.load({
							params: {
								key: Ext.getCmp('Kabupaten').getValue()
							}
						});
						// Ext.getCmp('Kecamatan').enable();
					}
				}
			}, {
				id: 'Kecamatan',
				name: 'Kecamatan',
				xtype: 'combo',
				anchor: '100%',
				fieldLabel: lang('Kecamatan')+ ' ',
				store: mc_Kecamatan,
				displayField: 'label',
				valueField: 'id',
				queryMode: 'local',
				allowBlank: false,
				listeners: {
					change: function (cb, nv, ov) {
                        if (nv) {
    						var oldID = Ext.getCmp('VillageID_old').getValue();
    						villageIDSet(Ext.getCmp('Kecamatan').getValue(), oldID);
                        }
					}
				}
			}, {
                xtype: 'numberfield',
				hideTrigger: true,
				anchor: '60%',
                fieldLabel: 'VillageID<b style="color:red">*</b> ',
                id: 'VillageID',
                name: 'VillageID',
				allowBlank: false,
            }, {
                xtype: 'textfield',
				anchor: '100%',
                fieldLabel: 'Village Name<b style="color:red">*</b> ',
                id: 'Village',
                name: 'Village',
				allowBlank: false,
            }, {
                xtype: 'textfield',
				anchor: '100%',
                fieldLabel: 'Village Head Name ',
                id: 'VillageHeadName',
                name: 'VillageHeadName'
            }, {
                xtype: 'radiogroup',
				anchor: '100%',
                fieldLabel: 'Village Head Gender ',
                id: 'VillageHeadGender',
				items: [
                    {boxLabel: lang('Laki-laki'), name: 'VillageHeadGender', inputValue: 1},
                    {boxLabel: lang('Perempuan'), name: 'VillageHeadGender', inputValue: 2}
                ]
            },{
				xtype:'fieldset',
				title: 'Village Head Office',
				collapsible: true,
				defaultType: 'textfield',
				layout: 'anchor',
				defaults: {
					anchor: '100%',
				},
				items :[{
					xtype: 'numberfield',
					hideTrigger: true,
					decimalPrecision: 4,
					fieldLabel: 'Latitude ',
					id: 'VillageHeadLatitude',
					name: 'VillageHeadLatitude',
               readOnly: m_hakakses_lat_short
				}, {
					xtype: 'numberfield',
					hideTrigger: true,
					decimalPrecision: 4,
					fieldLabel: 'Longitude ',
					id: 'VillageHeadLongitude',
					name: 'VillageHeadLongitude',
               readOnly: m_hakakses_long_short
				},]
			}],
        buttons: [{
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('VillageID_old').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'PUT';

                    form.submit({
                        url: m_crud,
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
							win.hide(this, function() {
								store.load();
							});
                        },failure : function(fp, o) {
                            Ext.MessageBox.alert('Error', 'Please check your VillageID.');
                        }

                    });
                }
            }, {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    win.hide();
                }
            }]
    });

	var win = Ext.create('widget.window', {
        title: 'Village Form',
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 500,
        frame: false,
        minWidth: 370,
        height: 450,
        layout: {
            type: 'fit'
        },
		items: [VillageForm]
    });

	function displayFormWindow() {
        if (!win.isVisible()) {
            VillageForm.getForm().reset();
            win.show();
            //Ext.getCmp('GroupName').focus(true,true);
        } else {
            win.hide(this, function() {
            });
            win.toFront();
        }
    }

	var contextMenuVillage = Ext.create('Ext.menu.Menu', {
        items: [{
				icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: 'Edit',
                handler: function() {
					var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
					mc_Provinsi.load();
                    displayFormWindow();
					villageSet(vl.get('id'));
                }
            }, {
				icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: 'Crop List',
                handler: function() {
					var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
					storeCrop.load({
						params: {
							id: vl.get('id')
						}
					});
					currentVillageID = vl.get('id');
                    displayCropListWindow();
                }
            }, {
				icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: 'Infrastructure List',
                handler: function() {
					var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
					storeInfrastructure.load({
						params: {
							id: vl.get('id')
						}
					});
					currentVillageID = vl.get('id');
                    displayInfrastructureListWindow();
                }
            }, {
				icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: 'Delete',
                handler: function() {
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
						if(btn == 'yes'){
							var sm = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
							Ext.Ajax.request({
								waitMsg: lang('Please Wait'),
								url: m_crud,
								method : 'DELETE',
								params: {id:  sm.get('id')},
								success: function(response, opts){
									var obj = Ext.decode(response.responseText);
									switch(obj.success){
										case true: store.load();
										break;
										default: Ext.MessageBox.alert('Warning',obj.message);
										break;
									}
								},
								failure: function(response, opts){
									var obj = Ext.decode(response.responseText);
									Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
								}
							});
						}
					});
                }
            }]
    });

	function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue(),
                    // kab: {
                    //     array: Ext.encode(Ext.getCmp('Kab').getValue())
                    // }
                }
            });
        }
    }

	var VillageList = Ext.create('Ext.grid.Panel', {
        store: store,
        id: 'VillageList',
        remoteSort: true,
        width: '100%',
		height: 700, //550
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function(view, record, item, index, e){
				contextMenuVillage.showAt(e.getXY());
			}
        },
        dockedItems: [{
				store: store,
                dock: 'bottom',
                xtype: 'pagingtoolbar',
				displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        cls: m_act_add,
                        scope: this,
                        handler: function() {
                            mc_Provinsi.load();
                            mc_KabupatenForm.load({
								params: {
									key: m_param
								}
							});
							Ext.getCmp('Kabupaten').enable();
                            displayFormWindow();
                            villageSet(0);
                        }
                    }, {
                        name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                        id: 'key',
                        xtype: 'textfield',
                        emptyText: lang('Cari berdasar nama/ID'),
                        listeners: {
                            specialkey: submitOnEnter
                        }
                    }, {
                        id: 'Kab',
                        name: 'Kab[]',
                        xtype: 'combo',
                        store: mc_Kabupaten,
                        displayField: 'label',
                        valueField: 'label',
                        queryMode: 'local',
                        selectOnFocus: true,
                        multiSelect: true,
                        hidden: true,
                        listeners: {
                            specialkey: submitOnEnter
                        }
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store.load({
                                params: {
                                    key: Ext.getCmp('key').getValue(),
                                    // kab: {
                                    //     array: Ext.encode(Ext.getCmp('Kab').getValue())
                                    // },
                                }
                            });
                        }
                    }]
            }],
        features: [{
                id: 'group',
                ftype: 'grouping',
                groupHeaderTpl: '{name}',
                hideGroupedHeader: true,
                remoteRoot: 'summaryData',
                enableGroupingMenu: true
            }],

        columns: [{
                text: lang('Village ID'),
                dataIndex: 'id',
                width: '10%'
            }, {
                text: lang('SubDistrictID'),
                dataIndex: 'SubDistrictID',
                hidden: true
            }, {
                text: lang('SubDistrict'),
                dataIndex: 'SubDistrict',
                hidden: true
            }, {
                text: lang('DistrictID'),
                dataIndex: 'DistrictID',
                hidden: true
            }, {
                text: lang('District'),
                dataIndex: 'District',
                hidden: true
            }, {
                text: lang('ProvinceID'),
                dataIndex: 'ProvinceID',
                hidden: true
            }, {
                text: lang('Village Name'),
                width: '25%',
                dataIndex: 'Village'
            }, {
                text: lang('Village Head Name'),
                width: '25%',
                dataIndex: 'VillageHeadName'
            }, {
                text: lang('Latitude'),
                width: '20%',
                dataIndex: 'VillageHeadLatitude'
            }, {
                text: lang('Longitude'),
                width: '20%',
                dataIndex: 'VillageHeadLongitude'
            }],
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return record.get('status');
            }
        }
    });
	//**End Village**//
});
