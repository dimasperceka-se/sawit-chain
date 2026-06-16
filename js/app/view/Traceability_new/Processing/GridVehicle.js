Ext.define('Koltiva.view.Traceability_new.Processing.GridVehicle' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Processing.GridVehicle',
    initComponent: function() {
        var thisObj = this; 
        //store
		 
        var storeGridVehicle = Ext.create('Koltiva.store.Traceability_new.Processing.MainGridVehicle', {
            storeVar: { 
				ProcessingID : thisObj.viewVar.ProcessingID
            } 
        });

        var GridVehicle = Ext.create('Ext.menu.Menu',{
            items:[{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    scope: this,
                    cls: 'Sfr_BtnConMenuWhite',
                    id: 'Koltiva.view.Traceability_new.Processing.GridVehicle-GridBtnUpdate',
                    handler: function () {

                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridVehicle-Grid').getSelectionModel().getSelection()[0];

                        var FormWinVehicle = Ext.create('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle', {
                            viewVar: {
                                ProcessingProductID : sm.get('ProcessingProductID'),
                                ProcessingID : sm.get('ProcessingID'),
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
                    id : 'Koltiva.view.Traceability_new.Processing.GridVehicle-GridBtnDelete',
                    handler: function(){
                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridVehicle-Grid').getSelectionModel().getSelection()[0]; 
                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/processing/transaction/del_vehicle/' ,
                                    params : { 'ProcessingProductID' : sm.get('ProcessingProductID')},
                                    method: 'GET',
                                    success: function(response, opts) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data deleted'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridVehicle-Grid').getStore().load();  
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
            id: 'Koltiva.view.Traceability_new.Processing.GridVehicle-Grid', 
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
                    id : 'Koltiva.view.Traceability_new.Processing.addVehicle',
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

                        var FormWinVehicle = Ext.create('Koltiva.view.Traceability_new.Processing.win.FormWinVehicle', {
                            viewVar: {
                                ProcessingID : thisObj.viewVar.ProcessingID,
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
                }]
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
                id :'Koltiva.view.Traceability_new.Processing.GridVehicle-actionColoumn',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) { 
						GridVehicle.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('ID'),
                dataIndex: 'ProcessingProductID',
                hidden:true
            },{
                text: 'No',
                xtype: 'rownumberer',
                width: '5%',
                align : 'center'
            },
            {
                text: lang('Product Name'),
				flex: 2
                dataIndex: 'ProductName',
                hidden: false
            }, {
                text: lang('Percentage'),
				flex: 1,
                dataIndex: 'ProductPercentage',
                hidden: false,
                renderer: function(v, metaData, record, rowIndex, colIndex, store) {
                    if(parseFloat(record.data.ProductPercentage)==0){
                        var output = '-';
                    }else{
                        var output = record.data.ProductPercentage + ' %';
                    }
                    return output;
                }
            },{  
                text: lang('Product Volume (kg)'),
				dataIndex: 'ProductVolume', 
				flex: 2,
                // align : 'center'
            },{  
                text: lang('Remaining (kg)'),
				dataIndex: 'RemainingVolume', 
				flex: 2,
                // align : 'center'
            }],
        }];

        this.callParent(arguments);
    }
});