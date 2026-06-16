  

Ext.define('Koltiva.view.Traceability_new.Supplychain_areafarmer.WinupdateFarmer' ,{ 
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Supplychain_areafarmer.WinupdateFarmer',
    title: lang('Farmer Update'),
    closable: false,
    modal: true,
    closeAction: 'destroy',
    width: '40%',
    height: '80%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //items --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Traceability_new.Supplychain_areafarmer.WinupdateFarmer-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    style:'padding:5px 10px 0px 10px;',
                    layout:'form',
                    items:[{
                        xtype: 'fieldset',
                        title: lang('Setting Date'),
                        items: [
                            /*Awal*/
                            {
                                xtype: 'hiddenfield',
                                fieldLabel: lang('SupplychainFarmerID'),
                                labelWidth:175,  
                                id: 'SupplychainFarmerID',
                                name: 'SupplychainFarmerID',  
                            },  
                            {
                                xtype: 'datefield',
                                fieldLabel: lang('Start Date'),
                                labelWidth:175,  
                                id: 'DateStart',
                                name: 'DateStart',  
                            },
                            {
                                xtype: 'datefield',
                                fieldLabel: lang('End Date'),
                                labelWidth:175, 
                                id: 'DateEnd',
                                name: 'DateEnd',  
                            }
                            /*akhir*/
                        ]
                    }]
                }]
            }]
        }];
        //items --------------------------------------------------------------------------------------------------------------- (end)

        //buttons --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.Traceability_new.Supplychain_areafarmer.WinupdateFarmer-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var formFarmerUpdate = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.WinupdateFarmer-Form').getForm();
                if (formFarmerUpdate.isValid()) {
                    formFarmerUpdate.submit({
                        url: m_api + '/traceability_api/Supplychain_areafarmer/formAccessFarmer',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid').getStore().load();
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
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid').getStore().load();
				thisObj.close();
            }
        }];
        //buttons --------------------------------------------------------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            var SupplychainFarmerID = this.viewVar.SupplychainFarmerID;
            Ext.getCmp('SupplychainFarmerID').setValue(SupplychainFarmerID);

            Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.WinupdateFarmer-Form').getForm().load({
                url: m_api + '/traceability_api/Supplychain_areafarmer/formAccessFarmer',
                method: 'GET',
                params: {
                    SupplychainFarmerID: this.viewVar.SupplychainFarmerID
                },
                success: function(response, opts){
                    var r = Ext.decode(opts.response.responseText);

                    Ext.getCmp('DateStart').setValue(r.data.DateStart);
                    Ext.getCmp('DateEnd').setValue(r.data.DateEnd);
                },
                failure: function(response, opts){
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
});

  