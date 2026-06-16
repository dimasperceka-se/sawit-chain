Ext.define('Koltiva.view.Traceability_new.Processing.FormMainProcessing' ,{
    extend: 'Ext.form.Panel',
    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing',
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
        var CmbDestination  = Ext.create('Koltiva.store.Traceability_new.Processing.CmbDestination');
        var CmbProduction = Ext.create('Koltiva.store.Traceability_new.Processing.ProductType', {
            storeVar: {
                ProductID : null 
            } 
        });

        var CmbTransit = Ext.create('Koltiva.store.Traceability_new.Processing.CmbTransit');
        
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Processing Form'),
            frame: true,
            id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-FormBasicData',
            fileUpload: true,
            margin:'0 0 20 0',
            items: [{
                layout: 'column',
                border: false,
                padding:5,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    cls: 'Sfr_PanelSubLayoutForm',
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
                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProcessingID',
                                    name: 'ProcessingID',
                                },
                                {
                                    xtype: 'hidden',
                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-SupplychainID',
                                    name: 'SupplychainID',
                                },
                                {
                                    xtype: 'hidden',
                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-FlagOer',
                                    name: 'FlagOer',
                                },
                                {
                                    xtype: 'hidden',
                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProductionCapacity',
                                    name: 'ProductionCapacity',
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProcessingNumber',
                                    name: 'ProcessingNumber ',
                                    fieldLabel: lang('Processing Number'),
                                    labelSeparator: '',
                                    labelWidth: 200,
                                    readOnly: true,
                                    value : ''
                                }]
                            },{
                                columnWidth: 0.5,
                                margin:'0 0 0 0',
                                style:'padding-left:15px;',
                                layout:'form',
                                items:[{
                                    xtype: 'datefield', 
                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProcessingDate',
                                    name: 'ProcessingDate',
                                    fieldLabel: lang('Processing Date'),
                                    labelSeparator: '',
                                    labelWidth: 200,
                                    value : new Date(),
                                    allowBlank:false,
                                    readOnly:true,
                                    format:"Y-m-d"
                                }]
                            }]
                        },
                        {
                            title : lang('Processing Detail'),
                            layout : 'column',
                            style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            items : [{
                                columnWidth:1,  
                                // anchor:'100%',
                                // height: 'auto',
                                id : 'idobjPanelProcessPick'    
                            }]
                        },
                        {
                            title : lang('Product'),
                            layout : 'column',
                            id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Panel-Product',
                            hidden: true,
                            frame: false,
                            //id: 'Koltiva.view.Traceability.Batch.Neo.FormBatchDelivery-FormBasicData-SectionFarmerProfile',
                            style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            items : [
                            // {
                            //     columnWidth:1,  
                            //     id : 'idobjPanelProduct'    
                            // }
                            {
                                columnWidth: 1,
                                items: [
                                    {
                                        xtype: 'panel',
                                        items:[{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 0.5,
                                                style: 'padding-right:15px',
                                                layout:'form',
                                                items:[{
                                                    xtype: 'panel',
                                                    title: lang('CPO'),
                                                    items: [{
                                                        layout: 'column',
                                                        items: [{
                                                            columnWidth: 1,
                                                            layout: 'form',
                                                            padding:5,
                                                            items:[ 
                                                                {
                                                                    xtype: 'hidden',
                                                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProcessingProductID',
                                                                    name: 'CPO_ProcessingProductID',
                                                                },
                                                                {
                                                                    xtype: 'numericfield',
                                                                    baseCls: 'Sfr_FormInputMandatory',
                                                                    labelWidth: 250,
                                                                    fieldLabel: lang('Product Percentage (%)'),
                                                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage',
                                                                    name: 'CPO_ProductPercentage',
                                                                    listeners: {
                                                                        change: function (cb, nv, ov) {
                                                                            var ProductionCapacity = Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProductionCapacity').getValue();
                                                                            var ProductPercentage = Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').getValue();
                                                                            var ProductVolume = (parseFloat(ProductionCapacity)*parseFloat(ProductPercentage)/100).toFixed(2);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').setValue(ProductVolume);
                                                                        }
                                                                    }
                                                                },
                                                                {
                                                                    xtype: 'numericfield',
                                                                    baseCls: 'Sfr_FormInputMandatory',
                                                                    labelWidth: 250,
                                                                    fieldLabel: lang('Product Volume (kg)'),
                                                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume',
                                                                    name: 'CPO_ProductVolume',
                                                                }
                                                            ]
                                                        }]
                                                    }]
                                                }]
                                            }, {
                                                columnWidth: 0.5,
                                                layout:'form',
                                                items:[{
                                                    xtype: 'panel',
                                                    width: '100%',
                                                    title: lang('PK'),
                                                    items: [{
                                                        layout: 'column',
                                                        items: [{
                                                            columnWidth: 1,
                                                            layout: 'form',
                                                            width: '100%',
                                                            padding:5,
                                                            items:[ 
                                                                {
                                                                    xtype: 'hidden',
                                                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProcessingProductID',
                                                                    name: 'PK_ProcessingProductID',
                                                                },
                                                                {
                                                                    xtype: 'numericfield',
                                                                    baseCls: 'Sfr_FormInputMandatory',
                                                                    labelWidth: 250,
                                                                    fieldLabel: lang('Product Percentage (%)'),
                                                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage',
                                                                    name: 'PK_ProductPercentage',
                                                                    listeners: {
                                                                        change: function (cb, nv, ov) {
                                                                            var ProductionCapacity = Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProductionCapacity').getValue();
                                                                            var ProductPercentage = Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').getValue();
                                                                            var ProductVolume = (parseFloat(ProductionCapacity)*parseFloat(ProductPercentage)/100).toFixed(2);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').setValue(ProductVolume);
                                                                        }
                                                                    }
                                                                },
                                                                {
                                                                    xtype: 'numericfield',
                                                                    baseCls: 'Sfr_FormInputMandatory',
                                                                    labelWidth: 250,
                                                                    fieldLabel: lang('Product Volume (kg)'),
                                                                    id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume',
                                                                    name: 'PK_ProductVolume',
                                                                }
                                                            ]
                                                        }
                                                        ]
                                                    }]
                                                }]
                                            }]
                                        }]
                                    }
                                ]
                            }
                            ]
                        },
                        // {
      //                       title : lang('Product'),
      //                       layout : 'column',
                        //  style:'margin-top:40px;',
      //                       items : [{
      //                           columnWidth:1,  
      //                           anchor:'100%',
      //                           height: 'auto',
      //                           id : 'idobjPanelVehicle'     
      //                       }]
                        // }
      //                   {   
                        //  style:'margin-top:10px;', 
      //                       layout : 'column',
      //                       items : [
                        //      {
      //                           columnWidth: 0.5,
      //                           layout:'form',
      //                           style:'padding-left:10px;padding-right:20px;',
      //                           items:[{
                        //                  xtype: 'textfield',
                        //                  fieldLabel: lang('Dispatch Net Quantity (Kg)'), 
      //                                       id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-DestpatchNetto',
      //                                       name: 'DestpatchNetto', 
                        //                  readOnly :true,
                        //                  labelWidth: 200,  
                        //              },
                        //              {
                        //                  xtype: 'datefield',
                        //                  id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PackingDate',
                        //                  format:'Y-m-d',
                        //                  name: 'PackingDate',
      //                                       fieldLabel: lang('Packing Date'), 
      //                                       allowBlank:false,
                        //                  labelWidth: 200                                     
                        //              }, {
                        //                  xtype: 'textarea',
                        //                  fieldLabel: lang('Note'), 
                        //                  id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-DespatchNote',
                        //                  name: 'DespatchNote'   
                        //              }]
                        //      } ]
                        // }
                        ],
                        buttons: [{
                            text: 'Save',
                            margin: '5 15 5 5',
                            scale: 'large',
                            ui: 's-button',
                            id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-btnSave',
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
                id: 'Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-labelInfoInsert',
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
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing').destroy(); //destory current view
                        if(Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridMainProcessing') == undefined){
                            var CompanyMain = Ext.create('Koltiva.view.Traceability_new.Processing.GridMainProcessing');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridMainProcessing').destroy();
                            var CompanyMain = Ext.create('Koltiva.view.Traceability_new.Processing.GridMainProcessing');
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
            
            var ProcessingID = thisObj.viewVar.ProcessingID;
            var ProductID  = '';
            
            var objPanelProcPick = new Ext.create('Koltiva.view.Traceability_new.Processing.GridPick', {
                viewVar: {
                    btnPick : thisObj.viewVar.btnPick == 'insert' || thisObj.viewVar.btnPick == 'edit' ? false : true,
                    'ProcessingID' : ProcessingID,
                    'ProductID' : ProductID 
                }
            });
            
            Ext.getCmp('idobjPanelProcessPick').add([objPanelProcPick]);
            Ext.getCmp('idobjPanelProcessPick').doLayout();

            // var idobjPanelProduct = new Ext.create('Koltiva.view.Traceability_new.Processing.GridProduct', {
            //     viewVar: {
            //         btnPick : thisObj.viewVar.btnPick == 'insert' || thisObj.viewVar.btnPick == 'edit' ? false : true,
            //         'ProcessingID' : ProcessingID
            //     }
            // });
            
            // Ext.getCmp('idobjPanelProduct').add([idobjPanelProduct]);
            // Ext.getCmp('idobjPanelProduct').doLayout();

    //         var objPanelVehicle = new Ext.create('Koltiva.view.Traceability_new.Processing.GridVehicle', {
    //             viewVar: {
                //  btnVehicle : thisObj.viewVar.btnVehicle == 'insert' || thisObj.viewVar.btnVehicle == 'edit' ? false : true,
                //  'ProcessingID' : ProcessingID
                // }
    //         });
            
    //         Ext.getCmp('idobjPanelVehicle').add([objPanelVehicle]);
    //         Ext.getCmp('idobjPanelVehicle').doLayout();
             
             
            if(btnSave=='view'){
                 Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-btnSave').hide();
            }else{
                Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-btnSave').show();
            }
            var did = ProcessingID;
            if(parseInt(did) > 0) {
                Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-FormBasicData').getForm().load({
                    url: m_api + '/processing/transaction/basicdata',
                    method:'GET',
                    params : {
                        ProcessingID : did
                    },
                    success: function(c, r) {
                        var data = r.result.data;
                        getBasicData(data);
                      
                    }
                });
                Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Panel-Product').show();
            }else{
                Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Panel-Product').hide();
            }
        }
    }
});
 
 
function getBasicData(r)
{   
    console.log(r);
    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProcessingID').setValue(r.ProcessingID);
    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProcessingNumber').setValue(r.ProcessingNumber);
    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProductionCapacity').setValue(r.ProductionCapacity);
    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-FlagOer').setValue(r.FlagOer);
    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProcessingProductID').setValue(r.CPO_ProcessingProductID);
    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').setValue(r.CPO_ProductPercentage);
    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').setValue(r.CPO_ProductVolume);
    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProcessingProductID').setValue(r.PK_ProcessingProductID);
    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').setValue(r.PK_ProductPercentage);
    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').setValue(r.PK_ProductVolume);

    if(r.FlagOer=='2'){
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').show();
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').show();

        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').allowBlank=false;
        // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').getValue());
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').allowBlank=false;
        // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').getValue());

        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').allowBlank=false;
        // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').getValue());
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').allowBlank=false;
        // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').getValue());
    }else{
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').hide();
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').hide();

        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').allowBlank=true;
        // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').getValue());
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').allowBlank=false;
        // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').getValue());

        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').allowBlank=true;
        // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').getValue());
        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').allowBlank=false;
        // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').getValue());
    }
    
    var myStore = Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridPick-Grid').getStore();
    myStore.load();
}

function submitData(objPanelBasicData , button)
{
    if (objPanelBasicData.isValid()) {   
            var _params =''; 
            var _url = m_api + '/processing/transaction/submit';  
            objPanelBasicData.submit({
                url: _url , 
                method:'POST', 
                waitMsg: 'Saving data...', 
                success: function(fp, o) {
                    if(o.result.success == true){
                        var ProcessingID   = o.result.id;
            
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridPick').destroy();
                        //destory current view
                        if(Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridPick') == undefined){                            
                            var objPanelProcPick = new Ext.create('Koltiva.view.Traceability_new.Processing.GridPick', {
                                viewVar: {
                                    btnPick : false,
                                    'ProcessingID' : ProcessingID
                                }
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridPick').destroy();
                            var objPanelProcPick = new Ext.create('Koltiva.view.Traceability_new.Processing.GridPick', {
                                viewVar: {
                                    btnPick : false,
                                    'ProcessingID' : ProcessingID
                                }
                            });
                        }
                        
                        Ext.getCmp('idobjPanelProcessPick').add([objPanelProcPick]);
                        Ext.getCmp('idobjPanelProcessPick').doLayout();

                        // Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct').destroy();
                        // //destory current view
                        // if(Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct') == undefined){                            
                        //     var idobjPanelProduct = new Ext.create('Koltiva.view.Traceability_new.Processing.GridProduct', {
                        //         viewVar: {
                        //             btnPick : false,
                        //             'ProcessingID' : ProcessingID
                        //         }
                        //     });
                        // }else{
                        //     //destroy, create ulang
                        //     Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct').destroy();
                        //     var idobjPanelProduct = new Ext.create('Koltiva.view.Traceability_new.Processing.GridProduct', {
                        //         viewVar: {
                        //             btnPick : false,
                        //             'ProcessingID' : ProcessingID
                        //         }
                        //     });
                        // }
                        
                        // Ext.getCmp('idobjPanelProduct').add([idobjPanelProduct]);
                        // Ext.getCmp('idobjPanelProduct').doLayout();

                         
                    // }

                    // if(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-DestpatchStatusID').getValue() == '4'){
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.addPick').hide();
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.Complete').hide();        
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PackingDate').setReadOnly(true);
     //                }else if(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-DestpatchStatusID').getValue() == '5'){
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.addPick').hide();
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.Complete').hide();
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.addVehicle').hide();
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.SentVehicle').hide();
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-btnSave').hide();
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PackingDate').setReadOnly(true);
     //                }else{
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.addPick').show();
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.addVehicle').show();
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.Complete').show();
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.SentVehicle').hide();
     //                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PackingDate').setReadOnly(false);
     //                }
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Panel-Product').show();
                        //Ext.getCmp('Koltiva.view.Traceability_new.Processing.addVehicle').show();
                        
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProcessingID').setValue(o.result.id);  
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProcessingNumber').setValue(o.result.ProcessingNumber);
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-ProductionCapacity').setValue(o.result.ProductionCapacity);
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-FlagOer').setValue(o.result.FlagOer);
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProcessingProductID').setValue(o.result.CPO_ProcessingProductID);
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').setValue(o.result.CPO_ProductPercentage);
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').setValue(o.result.CPO_ProductVolume);
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProcessingProductID').setValue(o.result.PK_ProcessingProductID);
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').setValue(o.result.PK_ProductPercentage);
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').setValue(o.result.PK_ProductVolume);

                        if(o.result.FlagOer=='2'){
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').show();

                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').allowBlank=false;
                            // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').getValue());
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').allowBlank=false;
                            // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').getValue());

                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').allowBlank=false;
                            // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').getValue());
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').allowBlank=false;
                            // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').getValue());
                        }else{
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').setReadOnly(false);
                            // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').hide();
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').setReadOnly(false);
                            // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').hide();

                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').allowBlank=true;
                            // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductPercentage').getValue());
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').allowBlank=false;
                            // Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-CPO_ProductVolume').getValue());

                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').allowBlank=true;
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductPercentage').getValue());
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').allowBlank=false;
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').validateValue(Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainProcessing-Form-PK_ProductVolume').getValue());
                        }


                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Data saved'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-success'
                        }); 
                    }else{
                        var pesanNya;
                        if(o.result.error != undefined){
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
                },
                failure: function(fp, o){
                    var pesanNya;
                    if(o.result.error != undefined){
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


 
