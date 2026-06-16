Ext.define('Koltiva.view.Traceability_new.Processing.GridProduct' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Processing.GridProduct',
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this; 
        //store

        var storeGridProduct = Ext.create('Koltiva.store.Traceability_new.Processing.MainGridProduct', {
            storeVar: { 
				ProcessingID : thisObj.viewVar.ProcessingID
            } 
        });
         
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Processing.GridProduct-Grid', 
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true, 
            selType: 'rowmodel',
            minHeight: 320,
            store: storeGridProduct,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData(),
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability_new.Processing.GridProduct-gridToolbar',
                store: storeGridProduct,
                dock: 'bottom',
                displayInfo: true
            },
            {
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    cls:'Sfr_BtnGridGreen',
                    overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Input Product'), 
                    disabled: false,
                    hidden : thisObj.viewVar.btnVehicle,
                    handler: function() {
                        var FormWinProduct = Ext.create('Koltiva.view.Traceability_new.Processing.win.FormWinProduct', {
                            viewVar: {
                                ProcessingID : thisObj.viewVar.ProcessingID,
                                ProcessingProductID : '',
                                OpsiDisplay: 'insert'
                            }
                        });
                        if (!FormWinProduct.isVisible()) {
                            FormWinProduct.center();
                            FormWinProduct.show();
                        } else {
                            FormWinProduct.close();
                        } 
                    }
                }],
            }
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
            { 
                xtype:'actioncolumn',
                width: 30,
                id :'Koltiva.view.Traceability_new.Processing.GridProduct-actionColoumn',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        if(Ext.isDefined(Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct-ContextMenu'))){
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct-ContextMenu').destroy();
                        }

                        var GridProduct = Ext.create('Ext.menu.Menu',{
                            id:"Koltiva.view.Traceability_new.Processing.GridProduct-ContextMenu",
                            items:[{
                                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                                    text: lang('Update'),
                                    scope: this,
                                    cls: 'Sfr_BtnConMenuWhite',
                                    id: 'Koltiva.view.Traceability_new.Processing.GridProduct-GridBtnUpdate',
                                    handler: function () {

                                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct-Grid').getSelectionModel().getSelection()[0];

                                        var FormWinProduct = Ext.create('Koltiva.view.Traceability_new.Processing.win.FormWinProduct', {
                                            viewVar: {
                                                ProcessingProductID : sm.get('ProcessingProductID'),
                                                ProcessingID : sm.get('ProcessingID'),
                                                OpsiDisplay: 'update'
                                            }
                                        });

                                        if (!FormWinProduct.isVisible()) {
                                            FormWinProduct.center();
                                            FormWinProduct.show();
                                        } else {
                                            FormWinProduct.close();
                                        }
                                    }
                                },{
                                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                    text: lang('Delete'),
                                    cls:'Sfr_BtnConMenuWhite', 
                                    id : 'Koltiva.view.Traceability_new.Processing.GridProduct-GridBtnDelete',
                                    handler: function(){
                                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct-Grid').getSelectionModel().getSelection()[0]; 
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

                                                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct-Grid').getStore().load();  
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
                        if(record.data.ProductVolume == record.data.RemainingVolume){
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct-GridBtnUpdate').setVisible(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct-GridBtnDelete').setVisible(true);
                        }else{
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct-GridBtnUpdate').setVisible(false);
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct-GridBtnDelete').setVisible(false);
                        }
                        GridProduct.showAt(e.getXY());
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
                flex: 2,
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
            }]
        }];

        storeGridProduct.on('load', function(store, records){
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