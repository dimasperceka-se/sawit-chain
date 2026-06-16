Ext.define('Koltiva.view.Traceability_new.Dispatch.GridVehicle' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Dispatch.GridVehicle',
    initComponent: function() {
        var thisObj = this; 
        //store
		 
        var storeGridVehicle = Ext.create('Koltiva.store.Traceability_new.Dispatch.MainGridVehicle', {
            storeVar: { 
				DespatchID : thisObj.viewVar.DespatchID,
                ProductID : thisObj.viewVar.ProductID
            } 
        });

        var GridVehicle = Ext.create('Ext.menu.Menu',{
            items:[{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    scope: this,
                    cls: 'Sfr_BtnConMenuWhite',
                    id: 'Koltiva.view.Traceability_new.Dispatch.GridVehicle-GridBtnUpdate',
                    handler: function () {

                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.GridVehicle-Grid').getSelectionModel().getSelection()[0];

                        var FormWinVehicle = Ext.create('Koltiva.view.Traceability_new.Dispatch.win.FormWinVehicle', {
                            viewVar: {
                                DespatchVehicleID : sm.get('DespatchVehicleID'),
                                DespatchID : sm.get('DespatchID'),
                                ProductID  : thisObj.viewVar.ProductID,
                                OpsiDisplay: 'update'
                            }
                        });

                        if (!FormWinVehicle.isVisible()) {
                            FormWinVehicle.center();
                            FormWinVehicle.show();
                        } else {
                            FormWinVehicle.close();
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    cls:'Sfr_BtnConMenuWhite', 
                    id : 'Koltiva.view.Traceability_new.Dispatch.GridVehicle-GridBtnDelete',
                    handler: function(){
                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.GridVehicle-Grid').getSelectionModel().getSelection()[0]; 
                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/dispatch/transaction/del_vehicle/' ,
                                    params : { 'DespatchVehicleID' : sm.get('DespatchVehicleID')},
                                    method: 'GET',
                                    success: function(response, opts) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data deleted'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.GridVehicle-Grid').getStore().load();  
                                    },
                                    failure: function(response, opts) {
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
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Dispatch.GridVehicle-Grid', 
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true, 
            minheight:300,
            selType: 'rowmodel', 
            store: storeGridVehicle,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData(),
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
					cls:'Sfr_BtnGridGreen',
                    overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Input Vehicle'), 
                    disabled: false,
                    hidden : thisObj.viewVar.btnVehicle,
                    id : 'Koltiva.view.Traceability_new.Dispatch.addVehicle',
                    handler: function() {

                        if (sessionStorage.getItem('lengthProcessBatch') == 0) {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: lang('Please input process batch first'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });

                            return;
                        }

                        var FormWinVehicle = Ext.create('Koltiva.view.Traceability_new.Dispatch.win.FormWinVehicle', {
                            viewVar: {
                                DespatchID : thisObj.viewVar.DespatchID,
                                ProductID : thisObj.viewVar.ProductID,
                                OpsiDisplay: 'insert'
                            }
                        });
                        if (!FormWinVehicle.isVisible()) {
                            FormWinVehicle.center();
                            FormWinVehicle.show();
                        } else {
                            FormWinVehicle.close();
                        } 
                    }
                }
                // {
                //     icon: varjs.config.base_url + 'images/icons/new/reload.png',
				// 	cls:'Sfr_BtnGridBlue',
                //     overCls:'Sfr_BtnGridBlue-Hover',
                //     text: lang('Sent'),
                //     hidden : thisObj.viewVar.btnVehicle,
                //     disabled: false,
                //     id : 'Koltiva.view.Traceability_new.Dispatch.SentVehicle',
                //     handler: function() {
                //         var sm = Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.GridVehicle-Grid').getSelectionModel().getSelection()[0]; 
                //         Ext.MessageBox.confirm('Message', 'Do you want to Sent this Dispatch ?', function(btn) {
                //             if (btn == 'yes') {
                //                 Ext.MessageBox.show({
                //                     msg: 'Please wait...',
                //                     progressText: 'Loading...',
                //                     width: 300,
                //                     wait: true,
                //                     waitConfig: {
                //                         interval: 200
                //                     },
                //                     icon: 'ext-mb-info', //custom class in msg-box.html
                //                     animateTarget: 'mb9'
                //                 });
                //                 Ext.Ajax.request({
                //                     waitMsg: 'Please Wait',
                //                     url: m_api + '/dispatch/transaction/sent_batch/' ,
                //                     params : { 'DespatchID' : thisObj.viewVar.DespatchID },
                //                     method: 'POST',
                //                     success: function(response, opts) {
                //                         Ext.MessageBox.hide();
                //                         Ext.MessageBox.show({
                //                             title: 'Information',
                //                             msg: lang('Dispatch Sent'),
                //                             buttons: Ext.MessageBox.OK,
                //                             animateTarget: 'mb9',
                //                             icon: 'ext-mb-success'
                //                         });

                //                         Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.addPick').setVisible(false);
                //                         Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.Complete').setVisible(false);
                //                         Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.addVehicle').setVisible(false);
                //                         Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.SentVehicle').setVisible(false);
                //                         Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.FormMainDispatch-Form-btnSave').setVisible(false);   

                //                         Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.FormMainDispatch-Form-DestpatchStatusID').setValue(5);     
                //                         Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.FormMainDispatch-Form-DestpatchStatusID').setRawValue(lang('Sent'));

                //                         Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.GridVehicle-Grid').getStore().load({
                //                         scope: this,
                //                             callback: function(records, operation, success) {
                //                                 // if(records.length > 0){
                //                                 //     Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.FormMainDispatch-Form-DestpatchStatusID').setValue('3'); //InProgress
                //                                 // }else{											 
                //                                 //     Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.FormMainDispatch-Form-DestpatchStatusID').setValue('2'); //draft
                //                                 // }
                //                             }
                //                         });  
                //                     },
                //                     failure: function(response, opts) {
                //                         var result = JSON.parse(response.responseText);
                //                         var pesanNya;
                //                         if(result.message != undefined){
                //                             pesanNya = result.message;
                //                         }else{
                //                             pesanNya = lang('Connection error');
                //                         }
                //                         Ext.MessageBox.show({
                //                             title: 'Error',
                //                             msg: pesanNya,
                //                             buttons: Ext.MessageBox.OK,
                //                             animateTarget: 'mb9',
                //                             icon: 'ext-mb-error'
                //                         });
                //                     }
                //                 });
                //             }
                //         });
                //     }
                // }
                ],
            }],
			listeners:{
				'afterrender': function(grid)
				{  
                    var myStore = grid.getStore();
                    myStore.on({
                        load: {
                            fn: function(store) { 
                                var ct = store.getCount();
                            }
                        }
                    });
                    myStore.load();
				}				
			},
            columns: [{ 
                xtype:'actioncolumn',
                width: 30,
                id :'Koltiva.view.Traceability_new.Dispatch.GridVehicle-actionColoumn',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) { 
						GridVehicle.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('ID'),
                dataIndex: 'DespatchVehicleID',
                hidden:true
            },{
                text: lang('No'),
                xtype: 'rownumberer',
                width: '5%',
                align : 'center'
            },
            {
                text: lang('Driver Name'),
				width: '10%',
                dataIndex: 'DriverName',
                hidden: false
            }, {
                text: lang('Vehicle Type'),
				width: '10%',
                dataIndex: 'VehicleTypeName',
                hidden: false
            },{  
                text: lang('Delivery Order Number'),
				dataIndex: 'DeliveryOrderNumber', 
				width: '20%',
                // align : 'center'
            },{  
                text: lang('Container Number'),
				dataIndex: 'ContainerNumber', 
				width: '15%',
                // align : 'center'
            },{  
                text: lang('Vehicle Number'),
				dataIndex: 'VehicleNumber', 
				width: '10%',
                // align : 'center'
            },{  
                text: lang('Product Name'),
                dataIndex: 'ProductName', 
                width: '10%',
                // align : 'center'
            },{  
                text: lang('Vehicle Weight'),
                dataIndex: 'VehicleWeight', 
                width: '10%',
                // align : 'center'
            },{  
                text: lang('Owner Status'),
                dataIndex: 'OwnerStatusName', 
                width: '10%',
                // align : 'center'
            }],
        }];

        storeGridVehicle.on('load', function(store, records){
           if (records.length > 0) {
            //   Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.addVehicle').hide();
           } else {
              if (thisObj.viewVar.btnVehicle == false) {
                //  Ext.getCmp('Koltiva.view.Traceability_new.Dispatch.addVehicle').show();
              }
           }

        }, this);

        this.callParent(arguments);
    }
});