Ext.define('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle',
    title:lang('Additional Information - Vehicle'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '72%',
    maxHeight: 700,
//    height: '50%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form').getForm().reset();

//             if(thisObj.viewVar.OpsiDisplay == 'update' || thisObj.viewVar.OpsiDisplay == 'view'){
//                 //load data form
//                 Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form').getForm().load({
//                     url: m_api + '/plot_survey/plantation_status_form_data',
//                     method: 'GET',
//                     params: {
//                         MemberID: this.viewVar.MemberID,
//                         PlotNr: this.viewVar.PlotNr,
//                         CallFrom: this.viewVar.CallFrom
//                     },
//                     success: function(form, action) {
//                         var r = Ext.decode(action.response.responseText);

//                         if(thisObj.viewVar.OpsiDisplay == 'view'){
//                             Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-MemberName').readOnly = true;
//                             Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-PlotNr').readOnly = true;
//                             Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-GardenAreaHa').readOnly = true;
//                             Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-GardenAreaPolygon').readOnly = true;
//                             Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-AnnualProduction').readOnly = true;
//                             Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-Latitude').readOnly = true;
//                             Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-Longitude').readOnly = true;
                            
//                             if (thisObj.viewVar.CallFrom == 'Mill') {
//                                 Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-PlantedAreaHa').setReadOnly(true);
//                                 Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-TreeTM').setReadOnly(true);
//                                 Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-TreeTBM').setReadOnly(true);
//                                 Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-TreeTR').setReadOnly(true);
//                                 Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-FarmPhotoInput').setReadOnly(true);
//                                 Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-FarmPhotoDesc').setReadOnly(true);
//                                 Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-Comment').setReadOnly(true);
//                             }
                            
//                             Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-BtnSave').setVisible(false);
//                         }
//                         if (thisObj.viewVar.CallFrom == 'Mill') {
//                             //photo
//                             Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-FarmPhotoOld').setValue(r.data.FarmPhotoPath);
//                             if (r.data.FarmPhoto != "") {
//                                 var fotoUser = r.data.FarmPhoto;
//                                 //console.log(fotoUser);
//                                 checkImageExists(fotoUser, function (existsImage) {
//                                     if (existsImage == true) {
// //                                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-FarmPhotoOld').setValue(r.data.FarmPhoto);
//                                         Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-FarmPhoto').setSrc(fotoUser);
//                                     } else {
//                                         Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-FarmPhoto').setSrc(m_api_base_url + '/images/no-image-icon.png');
//                                     }
//                                 });
//                             }
//                         }
//                     },
//                     failure: function(form, action) {
//                         Ext.MessageBox.show({
//                             title: 'Failed',
//                             msg: 'Failed to retrieve data',
//                             buttons: Ext.MessageBox.OK,
//                             animateTarget: 'mb9',
//                             icon: 'ext-mb-error'
//                         });
//                     }
//                 });
//             }

            if (thisObj.viewVar.OpsiDisplay == 'update') {
                Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form').getForm().load({
                    url: m_api + '/dispatch/transaction/fetchvehiclebyID',
                    method: 'GET',
                    params: {
                        DespatchVehicleID: this.viewVar.DespatchVehicleID,
                        DespatchID: this.viewVar.DespatchID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
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
    },
    initComponent: function() {
        var thisObj = this;

        var cmb_vehicle_type = Ext.create('Koltiva.store.Traceability_new.Processing.CmbVehicleType');
        var cmb_owner_status = Ext.create('Koltiva.store.Traceability_new.Processing.OwnerStatus');

        // var cmb_product_type = Ext.create('Koltiva.store.Traceability_new.Processing.ProductType', {
        //     storeVar: {
        //         ProductID : thisObj.viewVar.ProductID 
        //     } 
        // });

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 0.5,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-DespatchVehicleID',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-DespatchVehicleID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-DriverName',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-DriverName',
                        fieldLabel: lang('Driver Name'),
                        labelWidth: 185
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-DeliveryOrderNumber',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-DeliveryOrderNumber',
                        fieldLabel: lang('Delivery Order Number'),
                        readOnly:true,
                        labelWidth: 185
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-ContainerNumber',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-ContainerNumber',
                        fieldLabel: lang('Container Number'),
                        labelWidth: 185
                    },{
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-VehicleWeight',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-VehicleWeight',
                        fieldLabel: lang('Vehicle Weight'),
                        labelWidth: 185
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-OwnerID',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-OwnerID',
                        store: cmb_owner_status,
                        fieldLabel: lang('Owner Status'),
                        labelWidth: 200,
                        queryMode: 'local',
                        displayField: 'OwnerName',
                        valueField: 'OwnerID'
                    }]
                },{
                    columnWidth: 0.5,
                    layout:'form',
                    style:'padding-left:12px;',
                    items:[{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-VehicleNumber',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-VehicleNumber',
                        fieldLabel: lang('Vehicle Number'),
                        labelWidth: 185
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-VehicleTypeID',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-VehicleTypeID',
                        store: cmb_vehicle_type,
                        fieldLabel: lang('Vehicle Type'),
                        labelWidth: 200,
                        queryMode: 'local',
                        displayField: 'VehicleTypeName',
                        valueField: 'VehicleTypeID'
                    },{
                        xtype: 'textarea',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-VehicleNote',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-VehicleNote',
                        fieldLabel: lang('Vehicle Note'),
                        labelWidth: 185
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
            	var FormNya = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle-Form').getForm();
                if (FormNya.isValid()) {
                    FormNya.submit({
                        url: m_api + '/dispatch/transaction/vehicle_list',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        params: {
                            DespatchID: thisObj.viewVar.DespatchID,
                            ProductID : thisObj.viewVar.ProductID
                        },
                        success: function(rp, o){
                            var r = Ext.decode(o.response.responseText);
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Vehicle Added'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });
                            
                            //load store CallerStore
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridVehicle-Grid').getStore().load();
                            thisObj.close();
                        },
                        failure: function(rp, o){
                            try {
                                var r = Ext.decode(o.response.responseText);
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
                            }
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: 'Attention',
                        msg: lang('Form not valid yet'),
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
    }
});