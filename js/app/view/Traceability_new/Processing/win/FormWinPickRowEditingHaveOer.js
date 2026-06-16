Ext.define('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer',
    title: lang('Set Manual Production'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '50%',
    overflowY: 'auto',
    // formVar: false,
    // setFormVar: function(value){
    //     this.formVar = value;
    // },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(c){
            var thisObj = this;
        },
        beforeclose: function() {
            Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid').getStore().load();
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.arrayPopUp = [];
  
        var storeGridDesPathPickRowHaveOer = Ext.create('Koltiva.store.Traceability_new.Processing.RefHaveOer', {
            storeVar: { 
                ProductID : thisObj.viewVar.ProductID 
            }
        });

		var RowEditingDespatchPickHaveOer = Ext.create('Ext.grid.plugin.RowEditing', {
            id : 'RowEditingDespatchPickHaveOer',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2,
            saveBtnText:'Set'
        });
		
		var contextMenuProcessingPickHaveOer = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[
                {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Set'),
                    itemId: 'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-contextMenuUpdateItem',
                    //hidden: m_act_update,
                    handler: function() {
                            RowEditingDespatchPickHaveOer.cancelEdit();
                            var sm = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid').getSelectionModel().getSelection()[0];

                            if (sm.raw.flag) {
                                RowEditingDespatchPickHaveOer.startEdit(sm.index, 0);
                            }else{
                                Ext.MessageBox.alert(lang('Warning'), lang('Please select data on the grid !'));
                            }
                            
                    }
                }]
        });
        
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridDesPathPickRowHaveOer,
            width: '100%',
            minHeight:350,
            //autoWidth : true,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            plugins: [RowEditingDespatchPickHaveOer],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-gridToolbar',
                store: storeGridDesPathPickRowHaveOer,
                dock: 'bottom',
                displayInfo: true,
                hidden:true
            }],
            columns: [{
                text: lang('Action'),	
                xtype:'actioncolumn',
				width:65,
				items:[{
					icon: varjs.config.base_url + 'images/icons/new/action.png',
					handler: function(grid, rowIndex, colIndex, item, e, record) {
						contextMenuProcessingPickHaveOer.showAt(e.getXY());
					}
				}]
            },
            {
                text: lang('Date'),
                dataIndex: 'date',
                flex:1,
                editor: {
                    xtype: 'textfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-date',
                    readOnly : true
                }
            },
            {
                text: lang('Product'),
                dataIndex: 'flag',
                flex:1,                
                editor: {
                    xtype: 'textfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-flag',
                    readOnly : true
                }
            },
            {
                text: lang('Netto Capacity'),
                dataIndex: 'nett',
                flex:1,                
                editor: {
                    xtype: 'textfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-nett',
                    readOnly : true
                }
            },
            {
                text: lang('Production (kg)'),
                dataIndex: 'setProduction',
                flex:1,
                renderer: function (value, meta) {
                
                    var val             = value ;
                    var RemainingVolume = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-nett').getValue();
                   
                    if(val>RemainingVolume){
                        Ext.MessageBox.alert(lang('Warning'), lang('Selected production should be less than netto capacity'));
                        Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-setProduction').setValue('');
                    }
                    if (value == "") { 
                        meta.style = "text-align:center;"; 
                    } else { 
                        meta.style = "text-align:center;"; 
                    }

                    return value;
                },
                editor: {
                    xtype: 'numericfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-setProduction',
                    allowBlank: false
                }
            }],
            listeners: {
				 
                'canceledit': function (editor, e, eOpts) {
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid').getStore().load();

                    thisObj.arrayPopUp = [];
                },
                'beforeedit': function (editor, e){
                   
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-date').setReadOnly(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-flag').setReadOnly(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-nett').setReadOnly(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-setProduction').setValue("");
                },
                'edit': function (editor, e, c) {

                	let data     = e.record.data;
                	let setArray = thisObj.arrayPopUp;

                	let setDataArray = {
                		date          : data.date,
                		flag          : data.flag,
                		setProduction : data.setProduction
                	}

                	if (parseInt(setArray.length) > 0) {
	                	setArray.forEach(function(v, k, o){
		                    if (v.flag == data.flag) {
		                    	o.splice(k, 1);
		                    }
		                })
		            }

		            setArray.push(setDataArray);			
                }
			}
        }];

        thisObj.buttons = [
        {
            text: lang('Submit'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-ButtonSubmit',
            handler: function() {
                var RemainingVolume = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-MainGrid-nett').getValue();
                var setProduction = thisObj.arrayPopUp[0].setProduction;

                if(setProduction > RemainingVolume){
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: lang('Selected production should be less than netto capacity'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });

                    return;
                }

            	if (parseInt(thisObj.arrayPopUp.length) == 0) {
            		Ext.MessageBox.show({
                        title: 'Error',
                        msg: lang('Data is not completed'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });

                    return;
            	}

		  		Ext.Ajax.request({
                    waitMsg: lang('Please wait...'),
                    url: m_api + '/scheduler/generate_processing_manual',
                    method: 'GET',
                    params: {
                    	fromPopUp : JSON.stringify(thisObj.arrayPopUp),
                    	HaveOer : 1,
                        ProductID : thisObj.viewVar.ProductID
                    },
                    success: function (response, opts) {
                        let r = Ext.decode(response.responseText);

                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: r.success == false ? lang(r.message) : lang('Data Generated'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: r.success == false ? 'ext-mb-error' : 'ext-mb-success'
                        });

                        if (r.success == true) {
                        	thisObj.close();

                        	Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid').getStore().load();
                        }
                    },
                    failure: function (response, opts) {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: lang('Failed to Generated'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });	
            }
        },
        {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            id: 'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-ButtonClose',
            handler: function() {
            	thisObj.close();
                Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid').getStore().load();
            }
        }
        ];

        this.callParent(arguments);
    }
});