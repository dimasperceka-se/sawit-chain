
  

Ext.define('Koltiva.view.Traceability.Dispatch.FormMainDispatch' ,{
    extend: 'Ext.form.Panel',
    id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this;
 
        var cmbStatus       = Ext.create('Koltiva.store.ComboGeneral.ComboStatus');
        var CmbDestination  = Ext.create('Koltiva.store.Traceability.Dispatch.CmbDestination');
        var CmbProduction = Ext.create('Koltiva.store.Traceability.Dispatch.ProductType', {
            storeVar: {
                ProductID : null 
            } 
        });

        var CmbTransit = Ext.create('Koltiva.store.Traceability.Dispatch.CmbTransit');
		
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Dispatch Form'),
            frame: true,
            id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-FormBasicData',
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
                        xtype: 'panel',
                        items:[{
                            layout : 'column', 
                            items : [{
                                columnWidth: 0.495,
                                layout:'form',
                                style:'padding-left:10px;padding-right:20px;',
                                items:[{
                                    xtype: 'hidden',
                                    id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchID',
                                    name: 'DespatchID',
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchNumber',
                                    name: 'DespatchNumber ',
                                    fieldLabel: lang('Dispatch Number'),
                                    labelSeparator: '',
                                    labelWidth: 200,
                                    readOnly: true,
                                    value : ''
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchCode',
                                    name: 'DespatchCode',
                                    fieldLabel: lang("Dispatch Code"),
                                    labelSeparator: '',
                                    labelWidth: 200, 
                                    value : ''
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-ProductID',
                                    name: 'ProductID',
                                    fieldLabel: lang('Product Type'),
                                    store: CmbProduction,
                                    labelWidth: 200,
                                    allowBlank: false,
                                    fieldStyle : 'text-align:left;', 
                                    displayField: 'label',
                                    valueField: 'id',
                                    queryMode: 'local',
                                    // baseCls: 'Sfr_FormInputMandatory',
                                    listeners: {
                                        select : function(combo, record) {
                                            let DespatchID = Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchID').getValue();
                                            let objPanelProcPick

                                            if (DespatchID != "") {
                                                //grid pick
                                                Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridPick').destroy();
                                                if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridPick') == undefined){                            
                                                    objPanelProcPick = new Ext.create('Koltiva.view.Traceability.Dispatch.GridPick', {
                                                        viewVar: {
                                                            btnPick : false,
                                                            'DespatchID' : DespatchID,
                                                            'ProductID' : record[0].get('id') 
                                                        }
                                                    });
                                                } else {
                                                    objPanelProcPick = new Ext.create('Koltiva.view.Traceability.Dispatch.GridPick', {
                                                        viewVar: {
                                                            btnPick : false,
                                                            'DespatchID' : DespatchID,
                                                            'ProductID' : record[0].get('id')
                                                        }
                                                    });
                                                }

                                                Ext.getCmp('idobjPanelProcessPick').add([objPanelProcPick]);
                                                Ext.getCmp('idobjPanelProcessPick').doLayout();

                                                //vehicle
                                                Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridVehicle').destroy();
                                                if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridVehicle') == undefined){                            
                                                    var objPanelVehicle = new Ext.create('Koltiva.view.Traceability.Dispatch.GridVehicle', {
                                                        viewVar: {
                                                            btnVehicle : false,
                                                            'DespatchID' : DespatchID,
                                                            'ProductID' : record[0].get('id') 
                                                        }
                                                    });
                                                }else{
                                                    //destroy, create ulang
                                                    Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridVehicle').destroy();
                                                    var objPanelVehicle = new Ext.create('Koltiva.view.Traceability.Dispatch.GridVehicle', {
                                                        viewVar: {
                                                            btnVehicle : false,
                                                            'DespatchID' : DespatchID,
                                                            'ProductID' : record[0].get('id')
                                                        }
                                                    });
                                                }
                                                
                                                Ext.getCmp('idobjPanelVehicle').add([objPanelVehicle]);
                                                Ext.getCmp('idobjPanelVehicle').doLayout();
                                            }
                                        },
                                        change: function(cb, nv, ov) {
                                            CmbTransit.load({
                                                params: {
                                                    ProductID: nv
                                                }
                                            });

                                            let label 

                                            if (nv == 1) {
                                                label = lang("Bulking");
                                            } else if (nv == 2) {
                                                label = lang("KCP");
                                            } else {
                                                label = lang("Transit");
                                            }

                                            Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').setValue(null);
                                            Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').labelEl.update(label);
                                            
                                        }
                                    }
                                },{
                                    fieldLabel: lang('Dispatch Type'),
                                    xtype: 'radiogroup',
                                    allowBlank: false,
                                    // baseCls: 'Sfr_FormInputMandatory',
                                    msgTarget: 'side',
                                    columns: 2,
                                    id : 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-OpsiDespatchType',
                                    items:[{
                                        boxLabel: lang('Direct Dispatch'),
                                        name: 'DespatchType',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchTypeDirect',
                                        listeners:{
                                            change: function(cb, nv, ov){
                                                if (nv) {
                                                    Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').hide()
                                                    Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').allowBlank = true
                                                }
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Indirect Dispatch'),
                                        name: 'DespatchType',
                                        inputValue: '2',
                                        id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchTypeInDirect',
                                        listeners:{
                                            change: function(cb, nv, ov){
                                                if (nv) {
                                                    Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').show()
                                                    Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').allowBlank = false
                                                }
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.5,
                                margin:'0 0 0 0',
                                style:'padding-left:15px;',
                                layout:'form',
                                items:[{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestpatchStatusID',
                                    name: 'DestpatchStatusID',
                                    fieldLabel: lang('Status'),
                                    store: cmbStatus,
                                    labelWidth: 200,
                                    queryMode: 'local',
                                    allowBlank: false,
                                    fieldStyle : 'text-align:left;', 
                                    displayField: 'TransactionStatusName',
                                    valueField: 'TransactionStatusID',
									value:'1', 
									readOnly:true
                                },
                                {
                                    xtype: 'datefield', 
                                    id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-ShippingDate',
                                    name: 'ShippingDate',
                                    fieldLabel: lang('Shipping Date'),
                                    labelSeparator: '',
                                    readOnly:true,
                                    labelWidth: 200,
                                    value : new Date(),
                                    allowBlank:false,
                                    format:"Y-m-d"
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID',
                                    name: 'TransitID',
                                    labelSeparator: '',
                                    fieldLabel: lang('Transit'),
                                    store: CmbTransit,
                                    labelWidth: 200,
                                    allowBlank: false,
                                    fieldStyle : 'text-align:left;', 
                                    displayField: 'label',
                                    valueField: 'id',
                                    queryMode: 'local',
                                    hidden: true
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestinationID',
                                    name: 'DestinationID',
                                    labelSeparator: '',
                                    fieldLabel: lang('Destination'),
                                    store: CmbDestination,
                                    labelWidth: 200,
                                    allowBlank: false,
                                    fieldStyle : 'text-align:left;', 
                                    displayField: 'DestinationName',
                                    valueField: 'DestinationID'
                                }]
                            }]
                        },
						{
                            title : lang('Process Batch'),
                            layout : 'column',
                            items : [{
                                columnWidth:1,  
                                // anchor:'100%',
                                // height: 'auto',
                                id : 'idobjPanelProcessPick' 	
                            }]
						},
						{
                            title : lang('Additional Information - Vehicle'),
                            layout : 'column',
							style:'margin-top:40px;',
                            items : [{
                                columnWidth:1,  
                                anchor:'100%',
                                height: 'auto',
                                id : 'idobjPanelVehicle' 	
                            }]
						}, {   
							style:'margin-top:10px;', 
                            layout : 'column',
                            items : [
							    {
                                columnWidth: 0.5,
                                layout:'form',
                                style:'padding-left:10px;padding-right:20px;',
                                items:[{
											xtype: 'textfield',
											fieldLabel: lang('Dispatch Net Quantity (Kg)'), 
                                            id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestpatchNetto',
                                            name: 'DestpatchNetto', 
											readOnly :true,
											labelWidth: 200,  
										},
										{
											xtype: 'datefield',
											id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-PackingDate',
											format:'Y-m-d',
											name: 'PackingDate',
                                            fieldLabel: lang('Packing Date'), 
                                            allowBlank:false,
											labelWidth: 200										
										}, {
											xtype: 'textarea',
											fieldLabel: lang('Note'), 
											id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchNote',
											name: 'DespatchNote'   
										}]
								} ]
						}],
                        buttons: [{
                            text: 'Save',
                            margin: '5 15 5 5',
                            scale: 'large',
                            ui: 's-button',
                            id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-btnSave',
                            cls: 's-blue',
                            handler: function () {
                                submitData(objPanelBasicData ,'Save')
                            }
                        }]
                    }]
                }]
            }],
        });

        
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id: 'Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-labelInfoInsert',
                html:'',
            }]
        },{
            xtype: 'component',
            autoEl: {
                tag: 'a',
                href: '#',
                html: lang('Back to Dispatch List'),
                style:'text-decoration:underline;'
            },
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch').destroy(); //destory current view
                        if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch') == undefined){
                            var CompanyMain = Ext.create('Koltiva.view.Traceability.Dispatch.GridMainDispatch');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch').destroy();
                            var CompanyMain = Ext.create('Koltiva.view.Traceability.Dispatch.GridMainDispatch');
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
                columnWidth: 1,
                items:[
                    objPanelBasicData
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (END) ========================================//
        this.callParent(arguments);
    },
    listeners: {
        afterrender: function(c){ 
			var thisObj = this;
            var btnSave = thisObj.viewVar.btnSave;
            
            var DespatchID = thisObj.viewVar.DespatchID;
            var ProductID  = thisObj.viewVar.ProductID;
			
			var objPanelProcPick = new Ext.create('Koltiva.view.Traceability.Dispatch.GridPick', {
                viewVar: {
					btnPick : thisObj.viewVar.btnPick == 'insert' || thisObj.viewVar.btnPick == 'edit' ? false : true,
					'DespatchID' : DespatchID,
                    'ProductID' : ProductID	
				}
            });
            
			Ext.getCmp('idobjPanelProcessPick').add([objPanelProcPick]);
            Ext.getCmp('idobjPanelProcessPick').doLayout();

            var objPanelVehicle = new Ext.create('Koltiva.view.Traceability.Dispatch.GridVehicle', {
                viewVar: {
					btnVehicle : thisObj.viewVar.btnVehicle == 'insert' || thisObj.viewVar.btnVehicle == 'edit' ? false : true,
					'DespatchID' : DespatchID,
                    'ProductID' : ProductID
				}
            });
            
            Ext.getCmp('idobjPanelVehicle').add([objPanelVehicle]);
            Ext.getCmp('idobjPanelVehicle').doLayout();
			 
			 
			if(btnSave=='view'){
                 Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-btnSave').hide();
            }else{
                Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-btnSave').show();
            }
            var did = DespatchID;
            if(did > 0) {
                Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-FormBasicData').getForm().load({
                    url: m_api + '/dispatch/transaction/basicdata',
                    method:'GET',
                    params : {
                        DespatchID : did
                    },
                    success: function(c, r) {
                        var data = r.result.data;
						getBasicData(data)
                      
                    }
                });
            }
			  
        }
    }
});
 
 
function getBasicData(r)
{   
	 
	Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchID').setValue(r.DespatchID);
	Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchNumber').setValue(r.DespatchNumber);
	Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-ShippingDate').setValue(r.ShippingDate);  
	Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestpatchStatusID').setValue(r.DestpatchStatusID);
	Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchCode').setValue(r.DespatchCode );
	Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestinationID').setValue(r.DestinationID );
	Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestpatchNetto').setValue(r.DestpatchNetto );  
	Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-PackingDate').setValue(r.PackingDate );  
	Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchNote').setValue(r.DespatchNote);
    Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-ProductID').setValue(r.ProductID);
    Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').setValue(r.TransitID);

    if (r.DespatchType == 1) {
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchTypeDirect').setValue(true);
    } else if(r.DespatchType == 2) {
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchTypeInDirect').setValue(true);
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').show();

        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').allowBlank = false
    } else {
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchTypeInDirect').setValue(false);
    }
      
	if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestpatchStatusID').getValue() == '4'){
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.addPick').hide();
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.Complete').hide();        
		Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-PackingDate').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridPick-actionColoumn').destroy();
	}else if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestpatchStatusID').getValue() == '5'){
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.addPick').hide();
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.Complete').hide();
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.addVehicle').hide();
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.SentVehicle').hide();
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-btnSave').hide();
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-PackingDate').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridPick-actionColoumn').destroy();
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridVehicle-actionColoumn').destroy();        
	}else{
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.addPick').show();
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.Complete').show();
        Ext.getCmp('Koltiva.view.Traceability.Dispatch.SentVehicle').hide();
		Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-PackingDate').setReadOnly(false);
	} 	

    var myStore = Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridPick-Grid').getStore();
	myStore.on({
		load: {
			fn: function(store) { 
                var data = store.data.items;
                var ct = 0;

                Object.keys(data).forEach(function(k){
                    ct += Number(data[k].data.DespatchVolume);
                });				   
			   Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestpatchNetto').setValue(ct.toFixed(2));
			}
		}
	});
	myStore.load(); 	
}

function submitData(objPanelBasicData , button)
{
	if (objPanelBasicData.isValid()) {   
			var _params =''; 
			var _url = m_api + '/dispatch/transaction/submit';  
			objPanelBasicData.submit({
				url: _url , 
				method:'POST', 
				waitMsg: 'Saving data...', 
				success: function(fp, o) {
                    // if(o.result.status == 'insert'){
                        var DespatchID   = o.result.id;
                        var ProductID    = o.result.ProductID;
                        var DespatchType = o.result.DespatchType;
            
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridPick').destroy();
                        //destory current view
                        if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridPick') == undefined){                            
                            var objPanelProcPick = new Ext.create('Koltiva.view.Traceability.Dispatch.GridPick', {
                                viewVar: {
                                    btnPick : false,
                                    'DespatchID' : DespatchID,
                                    'ProductID' : ProductID	
                                }
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridPick').destroy();
                            var objPanelProcPick = new Ext.create('Koltiva.view.Traceability.Dispatch.GridPick', {
                                viewVar: {
                                    btnPick : false,
                                    'DespatchID' : DespatchID,
                                    'ProductID' : ProductID
                                }
                            });
                        }
                        
                        Ext.getCmp('idobjPanelProcessPick').add([objPanelProcPick]);
                        Ext.getCmp('idobjPanelProcessPick').doLayout();

                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridVehicle').destroy();
                        //destory current view
                        if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridVehicle') == undefined){                            
                            var objPanelVehicle = new Ext.create('Koltiva.view.Traceability.Dispatch.GridVehicle', {
                                viewVar: {
                                    btnVehicle : false,
                                    'DespatchID' : DespatchID,
                                    'ProductID' : ProductID	
                                }
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridVehicle').destroy();
                            var objPanelVehicle = new Ext.create('Koltiva.view.Traceability.Dispatch.GridVehicle', {
                                viewVar: {
                                    btnVehicle : false,
                                    'DespatchID' : DespatchID,
                                    'ProductID' : ProductID
                                }
                            });
                        }
                        
                        Ext.getCmp('idobjPanelVehicle').add([objPanelVehicle]);
                        Ext.getCmp('idobjPanelVehicle').doLayout();

                         
                    // }

					if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestpatchStatusID').getValue() == '4'){
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.addPick').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.Complete').hide();        
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-PackingDate').setReadOnly(true);
                    }else if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DestpatchStatusID').getValue() == '5'){
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.addPick').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.Complete').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.addVehicle').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.SentVehicle').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-btnSave').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-PackingDate').setReadOnly(true);
                    }else{
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.addPick').show();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.addVehicle').show();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.Complete').show();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.SentVehicle').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-PackingDate').setReadOnly(false);
                    }
					
					Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchID').setValue(o.result.id);  
					Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-DespatchNumber').setValue(o.result.DespatchNumber);

                    if (DespatchType == 2) {
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').show();
                    } else {
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch-Form-TransitID').setValue(null);
                    }  
					
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
						pesanNya = o.result.error;
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
		} else {
			Ext.MessageBox.show({
				title: 'Attention',
				msg: lang('Form not valid yet'),
				buttons: Ext.MessageBox.OK,
				animateTarget: 'mb9',
				icon: 'ext-mb-info'
			});
		}
}


 
