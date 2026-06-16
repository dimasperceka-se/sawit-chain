Ext.define('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('FarmCloud - Register New Account'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '76%',
    height: 400,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.CmbAutoFarmerSearch = Ext.create('Koltiva.store.FarmCloud.UseraccManagement.CmbAutoFarmerSearch');

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form',
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
                        items:[{
                            columnWidth: 0.5,
                            style:'padding-right:15px;',
                            layout:'form',
                            items:[{
                                xtype: 'combo',
                                store: thisObj.CmbAutoFarmerSearch,
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-FarmerAuto',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-FarmerAuto',
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Farmer'),
                                typeAhead: false,
                                hideTrigger: true,
                                anchor: '100%',
                                emptyText: lang('Enter farmer id or farmer name to search'),
                                listConfig: {
                                    loadingText: lang('Searching'),
                                    emptyText: lang('No matching data found'),
                                    getInnerTpl: function() {
                                        return '<div class="search-item">' + '{label}' + '</div>';
                                    }
                                },
                                pageSize: 10,
                                listeners: {
                                    select: function(combo, selection) {
                                        let r = selection[0];
                                        //console.log(`rec:`,rec);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-FarmerID').setValue(r.data.FarmerID);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-PartnerID').setValue(r.data.PartnerID);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Province').setValue(r.data.Province);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-District').setValue(r.data.District);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-SubDistrict').setValue(r.data.SubDistrict);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Village').setValue(r.data.Village);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-FarmerName').setValue(r.data.FarmerName);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Partner').setValue(r.data.PartnerLabel);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Username').setValue(r.data.Username);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Email').setValue(r.data.Email);
                                        Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Handphone').setValue(r.data.Handphone);
                                    }
                                }
                            }]
                        },{
                            columnWidth: 0.5,
                            style:'padding-left:10px;',
                            layout:'form',
                            defaults: {
                                labelWidth:180
                            },
                            items:[{
                                xtype: 'textfield',
                                inputType: 'hidden',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-PartnerID',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-PartnerID'
                            },{
                                xtype: 'textfield',
                                inputType: 'hidden',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Province',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Province'
                            },{
                                xtype: 'textfield',
                                inputType: 'hidden',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-District',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-District'
                            },{
                                xtype: 'textfield',
                                inputType: 'hidden',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-SubDistrict',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-SubDistrict'
                            },{
                                xtype: 'textfield',
                                inputType: 'hidden',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Village',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Village'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-FarmerID',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-FarmerID',
                                readOnly:true,
                                fieldLabel: lang('Farmer ID')
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-FarmerName',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-FarmerName',
                                readOnly:true,
                                fieldLabel: lang('Farmer Name')
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Partner',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Partner',
                                readOnly:true,
                                fieldLabel: lang('Partner')
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Username',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Username',
                                fieldLabel: lang('Username'),
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Email',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Email',
                                fieldLabel: lang('Email'),
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Handphone',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-Handphone',
                                fieldLabel: lang('Handphone'),
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory'
                            },{
                                xtype: 'textfield',
                                inputType: 'password',
                                fieldLabel: lang('Password'),
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-UserPassword',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-UserPassword',
                                listeners:{
                                    afterrender:function(cmp){
                                        cmp.inputEl.set({
                                            autocomplete:'off'
                                        });
                                    }
                                }
                            },{
                                xtype: 'textfield',
                                inputType: 'password',
                                fieldLabel: lang('Re Type Password'),
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-UserPasswordRe',
                                name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form-UserPasswordRe',
                                listeners:{
                                    afterrender:function(cmp){
                                        cmp.inputEl.set({
                                            autocomplete:'off'
                                        });
                                    }
                                }
                            }]
                        }]
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (begin)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls:'Sfr_BtnFormBlue',
            overCls:'Sfr_BtnFormBlue-Hover',
            text: lang('Register Account'),
            handler: function () {
                let FormNya = Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount-Form').getForm();
                if (FormNya.isValid()) {

                    FormNya.submit({
                        url: m_api + '/farmcloud/account_new_register',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(rp, o){
                            let r = Ext.decode(o.response.responseText);
                            //console.log(`r:`,r);
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: r.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            thisObj.viewVar.CallerStore.load();
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

                } else {
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
            icon: varjs.config.base_url + 'images/icons/new/close.png',
			text: lang('Close'),
			cls:'Sfr_BtnFormGrey',
			overCls:'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});