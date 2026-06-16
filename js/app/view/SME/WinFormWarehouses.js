

function init_map2() {
    var lat = Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-Latitude').getValue();
    var longs = Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-Longitude').getValue();
	 
    if (Math.abs(lat) > 0 && Math.abs(longs)) {
        $('#map2').gmap3({
            map: {
                options: {
                    center: [lat, longs],
                    zoom: 14,
                    //mapTypeControl: false,
                    panControl: true,
                    zoomControl: true,
                    //scaleControl: false,
                    streetViewControl: false,
                    rotateControl: false,
                    rotateControlOptions: false,
                    overviewMapControl: false,
                    OverviewMapControlOptions: false,
                    scrollwheel: true
                }
            },
            marker: {
                latLng:[lat, longs]
            }
        }); 
    }
}

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
/*
    Param2 yg diperlukan ketika load View ini
    - opsiDisplay
    - callerObjID
    - callFromRole
    - callerStore 
*/

var cmb_warehousetype = Ext.create('Koltiva.store.SME.CmbsmeType'); 
Ext.define('Koltiva.view.SME.WinFormWarehouses' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.SME.WinFormWarehouses',
    title: lang('Form Warehouses'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '55%',
    height: '75%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
  

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
			fileUpload: true,
            id: 'Koltiva.view.SME.WinFormWarehouses-Form',
            padding:'5 25 5 8', 
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[
						  //Coloumn Left
							{
								columnWidth: 0.495,
								layout:'form', 
								items:[{
											xtype: 'numberfield',
											id: 'Koltiva.view.SME.WinFormWarehouses-WarehousesNr',
											name: 'Koltiva.view.SME.WinFormWarehouses-WarehousesNr',
											labelAlign: 'top',
											fieldLabel: lang('Warehouses Nr'),
											allowBlank: false,
									   },{
											xtype: 'combobox',
											id: 'Koltiva.view.SME.WinFormWarehouses-Warehousetype',
											name: 'Koltiva.view.SME.WinFormWarehouses-Warehousetype',
											store: cmb_warehousetype,
											fieldLabel: lang('Shop and Warehouse Type'),
											labelAlign:'top',
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											allowBlank: false
									   },{
											  html:'<div class="subtitleForm" style="padding-top:10px;">'+lang('Warehouses Location')+'</div>'
									   },{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.WinFormWarehouses-Latitude',
											name: 'Koltiva.view.SME.WinFormWarehouses-Latitude',
											allowNegative: false,
											labelAlign:'top',
											fieldLabel: lang('Latitude')
										},{
											html:'<div></div>',
										},{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.WinFormWarehouses-Longitude',
											name: 'Koltiva.view.SME.WinFormWarehouses-Longitude',
											allowNegative: false,
											labelAlign:'top',
											fieldLabel: lang('Longitude')
										}]
							},
							//Coloumn Right
							{
								columnWidth: 0.495,
								margin:'0 10 0 0',
								style:'padding-left:15px;',
								layout:'form',
								items:[{
											html:'<div class="subtitleForm" style="padding-top:10px;">'+lang('Warehouses Location Photo')+'</div>'
									   },
									   {
											layout:'column',
											border:false,
											items:[{
												columnWidth: 1,
												border: false,
												layout:{
													type:'hbox',
													pack:'end'
												},
												items:[{
													xtype: 'image',
													id: 'Koltiva.view.SME.WinFormWarehouses-agBusinessLocation',
													width: '225px',
													height:'175px',
													src: m_api_base_url + '/images/default_photo/agent-location.jpg'
												},{
													xtype: 'textfield',
													id: 'Koltiva.view.SME.WinFormWarehouses-agBusinessLocationOld',
													name: 'Koltiva.view.SME.WinFormWarehouses-agBusinessLocationOld',
													inputType: 'hidden'
												}]
											}]
									   },
									   {
											columnWidth: 1,
											border: false,
											layout:'form',
											items:[{
												xtype: 'fileuploadfield',
												fieldLabel: lang('Location Photo'),
												labelAlign: 'top',
												id: 'Koltiva.view.SME.WinFormWarehouses-agBusinessLocationInput',
												name: 'Koltiva.view.SME.WinFormWarehouses-agBusinessLocationInput',
												buttonText: 'Browse',
												listeners: {
													'change': function (fb, v) {
														var FormNya = Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-Form').getForm();
														FormNya.submit({
															url: m_api + '/sme/image_member_business_photo',
															clientValidation: false,
															params: {
																opsiDisplay: thisObj.viewVar.opsiDisplay,
																MemberID: thisObj.viewVar.callerObjID
															},
															waitMsg: 'Sending Photo...',
															success: function (fp, o) {
																Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-agBusinessLocation').setSrc(o.result.file);
																Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-agBusinessLocationOld').setValue(o.result.filepath);
															}
														});
													}
												}
											}]
								     }]
							},	
							{
							columnWidth: 1,
							layout:'form',
							items:[		
									{
										xtype: 'component',
										autoEl: {
											html: '<div id="map2" style="width:100%;height:250px;background:#e1e1e1;border:1px solid #e1e1e1;"></div>',
											style:'width:100%;'
										}	
									}
									]
							
							}]
					}]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.SME.WinFormWarehouses-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
            	var FormNya = Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-Form').getForm();
                if (FormNya.isValid()) {
                    FormNya.submit({
                        url: m_api + '/sme/warehouses',
                        method:'POST',
						params : {MemberID: thisObj.viewVar.callerObjID, opsiDisplay : thisObj.viewVar.opsiDisplay }, 
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //form reset
                            FormNya.reset();

                            //refresh store vehicle yg manggil
                            thisObj.viewVar.callerStore.load();

                            //tutup popup
                            thisObj.close();
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
        },{
            text: lang('Close'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-Form');
            formNya.getForm().reset(); 

            if(thisObj.viewVar.opsiDisplay == 'insert'){
                //insert
            }

            if(thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view'){
            	if(thisObj.viewVar.opsiDisplay == 'view'){
            		Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-BtnSave').setVisible(false);
            	}
				 
            	formNya.getForm().load({
                    url: m_api + '/sme/warehouses_form',
                    method: 'GET',
                    params: {
                        MemberID: thisObj.viewVar.callerObjID,
						WarehousesNr : thisObj.viewVar.WarehousesNr
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                         
						//set photo
                        Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-agBusinessLocationOld').setValue(r.data.PhotoSrcPath);
                        if(r.data.PhotoBusinessLocation != ""){
                            var fotoUserBusiness = r.data.PhotoBusinessLocation; 
                            var angkaRandBusiness = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUserBusiness, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-agBusinessLocation').setSrc(fotoUserBusiness);
                                } else {
                                    Ext.getCmp('Koltiva.view.SME.WinFormWarehouses-agBusinessLocation').setSrc(m_api_base_url + '/images/default_photo/agent-location.jpg');
                                }
                            });
                        }

						init_map2();//gmaps3 
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
    }
});