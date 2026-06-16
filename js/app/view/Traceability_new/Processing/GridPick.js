var contextMenuGridPick = Ext.create('Ext.menu.Menu',{
    items:[ {
        icon: varjs.config.base_url + 'images/icons/new/delete.png',
        text: lang('Delete'),
        cls:'Sfr_BtnConMenuWhite', 
        id : 'Koltiva.view.Traceability_new.Processing.GridPick-GridBtnDelete',
        handler: function(){
            var sm = Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridPick-Grid').getSelectionModel().getSelection()[0]; 
            Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/processing/transaction/del_pick/' ,
                        params : { 'DespatchDetailID' : sm.get('DespatchDetailID') },
                        method: 'GET',
                        success: function(response, opts) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data deleted'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridPick-Grid').getStore().load({
                            scope: this,
                                callback: function(records, operation, success) {
                                    // if(records.length > 0){
                                    //     Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-DestpatchStatusID').setValue('3'); //InProgress
                                    // }else{											 
                                    //     Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-DestpatchStatusID').setValue('2'); //draft
                                    // }
                                }
                            });  
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
	
Ext.define('Koltiva.view.Traceability_new.Processing.GridPick' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Processing.GridPick',
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this; 
        //store

        var storeGridPick = Ext.create('Koltiva.store.Traceability_new.Processing.MainGridPick', {
            storeVar: { 
				ProcessingID : thisObj.viewVar.ProcessingID
            } 
        });
         
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Processing.GridPick-Grid', 
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true, 
            selType: 'rowmodel',
            minHeight: 320,
            features: [{
                ftype: 'summary'
            }],
            store: storeGridPick,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData(),
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability_new.Processing.GridPick-gridToolbar',
                store: storeGridPick,
                dock: 'bottom',
                displayInfo: true
            },
     //        {
     //            xtype: 'toolbar',
     //            dock:'top',
     //            items: [{
     //                icon: varjs.config.base_url + 'images/icons/new/add.png',
					// cls:'Sfr_BtnGridGreen',
     //                overCls:'Sfr_BtnGridGreen-Hover',
     //                text: lang('Add'), 
     //                disabled: false,
     //                hidden : thisObj.viewVar.btnPick,
     //                id : 'Koltiva.view.Traceability_new.Processing.addPick',
     //                handler: function() { 
					//    if(Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing') == undefined){
     //                        var FormPick = Ext.create('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing', {
     //                            opsiDisplay: 'insert',
     //                            viewVar: {
     //                                ProcessingID : thisObj.viewVar.ProcessingID, 
     //                                ProductID : Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-ProductID').getValue(),
     //                                btnSave: true
     //                            }
     //                        });
     //                    }else{
     //                        //destroy, create ulang
     //                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing').destroy();
     //                        var FormPick = Ext.create('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing', {
     //                            opsiDisplay: 'insert',
     //                            viewVar: {
     //                                ProcessingID : thisObj.viewVar.ProcessingID, 
     //                                ProductID : Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-ProductID').getValue(),
     //                                btnSave: true
     //                            }
     //                        });
     //                    }
     //                    if (!FormPick.isVisible()) {
     //                        FormPick.center();
     //                        FormPick.show();
     //                    } else {
     //                        FormPick.close();
     //                    } 
     //                }
     //            },{
     //                icon: varjs.config.base_url + 'images/icons/new/save.png',
					// cls:'Sfr_BtnGridBlue',
     //                overCls:'Sfr_BtnGridBlue-Hover',
     //                text: lang('Complete'), 
     //                disabled: false,
     //                hidden : thisObj.viewVar.btnPick,
     //                id : 'Koltiva.view.Traceability_new.Processing.Complete',
     //                handler: function() { 
     //                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridPick-Grid').getSelectionModel().getSelection()[0]; 
     //                    Ext.MessageBox.confirm('Message', 'Do you want to confirm this proses batch ?', function(btn) {
     //                        if (btn == 'yes') {
     //                            Ext.Ajax.request({
     //                                waitMsg: 'Please Wait',
     //                                url: m_api + '/processing/transaction/proses_batch/' ,
     //                                params : { 'ProcessingID' : thisObj.viewVar.ProcessingID },
     //                                method: 'POST',
     //                                success: function(response, opts) {
     //                                    Ext.MessageBox.show({
     //                                        title: 'Information',
     //                                        msg: lang('Data Confirmed'),
     //                                        buttons: Ext.MessageBox.OK,
     //                                        animateTarget: 'mb9',
     //                                        icon: 'ext-mb-success'
     //                                    });

     //                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.addPick').setVisible(false);
     //                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.Complete').setVisible(false);
     //                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.SentVehicle').setVisible(true);
     //                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridPick-actionColoumn').destroy();
     //                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridVehicle-actionColoumn').destroy();

     //                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-DestpatchStatusID').setValue(4);
     //                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-DestpatchStatusID').setRawValue(lang('Completed'));

     //                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridPick-Grid').getStore().load({
     //                                    scope: this,
     //                                        callback: function(records, operation, success) {
     //                                            // if(records.length > 0){
     //                                            //     Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-DestpatchStatusID').setValue('3'); //InProgress
     //                                            // }else{											 
     //                                            //     Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-DestpatchStatusID').setValue('2'); //draft
     //                                            // }
     //                                        }
     //                                    });  
     //                                },
     //                                failure: function(response, opts) {
     //                                    var result = JSON.parse(response.responseText);
     //                                    var pesanNya;
     //                                    if(result.message != undefined){
     //                                        pesanNya = result.message;
     //                                    }else{
     //                                        pesanNya = lang('Connection error');
     //                                    }
     //                                    Ext.MessageBox.show({
     //                                        title: 'Error',
     //                                        msg: pesanNya,
     //                                        buttons: Ext.MessageBox.OK,
     //                                        animateTarget: 'mb9',
     //                                        icon: 'ext-mb-error'
     //                                    });
     //                                }
     //                            });
     //                        }
     //                    });
     //                }
     //            }],
     //        }
            ],
			listeners:{
				'afterrender': function(grid)
				{  
                    var myStore = grid.getStore();
                    myStore.on({
                        load: {
                            fn: function(store) { 
                                
                            }
                        }
                    });
                    myStore.load();
				}				
			},
            columns: [
      //       { 
      //           xtype:'actioncolumn',
      //           width: '5%',
      //           id :'Koltiva.view.Traceability_new.Processing.GridPick-actionColoumn',
      //           items:[{
      //               icon: varjs.config.base_url + 'images/icons/new/action.png',
      //               tooltip: 'Action',
      //               handler: function(grid, rowIndex, colIndex, item, e, record) { 
						// contextMenuGridPick.showAt(e.getXY());
      //               }
      //           }]
      //       },
            {
                text: lang('ID'),
                dataIndex: 'DespatchDetailID',
                hidden:true
            },{
                text: 'No',
                xtype: 'rownumberer',
                width: '5%',
                // align : 'center'
            },
            {
                text: lang('Receive Date'),
				flex : 1,
                dataIndex: 'ReceiveDate',
                hidden: false
            },
            {
                text: lang('External Code'),
				flex : 1,
                dataIndex: 'ExternalCode',
                hidden: false
            },{
                text: lang('Supplier'),
				flex : 2,
                dataIndex: 'Supplier',
                hidden: false
            },{  
                text: lang('Vehicle Number'),
				dataIndex: 'DestTransportNumber', 
				flex : 1,
                // align : 'center'
            },{  
                text: lang('Nett (kg)'),
                dataIndex: 'Nett', 
                flex : 1,
                align: 'right',
                renderer: Ext.util.Format.numberRenderer('0,000.00'),
                summaryType: 'sum'
            }],
        }];

        storeGridPick.on('load', function(store, records){
           sessionStorage.removeItem('lengthProcessBatch');

           // if (records.length > 0) {
           //    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-ProductID').setReadOnly(true);
           //    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-OpsiDespatchType').setReadOnly(true);

           //    sessionStorage.setItem('lengthProcessBatch', 1);
           // } else {
           //    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-ProductID').setReadOnly(false);
           //    Ext.getCmp('Koltiva.view.Traceability_new.Processing.FormMainDispatch-Form-OpsiDespatchType').setReadOnly(false);

           //    sessionStorage.setItem('lengthProcessBatch', 0);
           // }

        }, this);

        this.callParent(arguments);
    }
});