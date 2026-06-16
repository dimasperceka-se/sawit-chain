/**
 * @author [Muhammad Hidayaturrohman]
 * @email [muhammad.hidayaturrohman@koltiva.com]
 * @create date 2020-11-05
 * @modify date 2020-11-05
 * @desc [description]
 */



Ext.define('Koltiva.view.Refinery.FormAddSupplier' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Refinery.FormAddSupplier',
    title: lang('Add New Contact'),
    closable: false,
    modal: true,
    closeAction: 'destroy',
    width: '50%',
    height: '60%',
    overflowY: 'auto',
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        var ContactRecords = [];
        var OldID = [];
        var OldContactRecords = Ext.getCmp('Koltiva.view.Refinery.FormTracebilityDeclarationManual-gridSupplier').getStore().data.items;
       
        if(OldContactRecords.length > 0){
            Ext.each(OldContactRecords, function (item) {
                OldID.push(item.data.id);
                ContactRecords.push({                    
                    SupplierName    : item.data.SupplierName,
                    KategoriKebun   : item.data.KategoriKebun,
                    KategoriKebunName   : item.data.KategoriKebunName,
                    Tracebility     : item.data.Tracebility,
                    FFBSupply       : item.data.FFBSupply
                });
            });
        }

        var cmb_kategori_kebun = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [
                {
                    "id": "1",
                    "label": lang("Farmer Plasma")
                },{
                    "id": "2",
                    "label": lang("Direct Smallholder")
                },{
                    "id": "3",
                    "label": lang("Agent / Dealer / Vendor")
                },{
                    "id": "4",
                    "label": lang("Owner Estate")
                },{
                    "id": "5",
                    "label": lang("External Estate")
                }
            ]
        });

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Refinery.FormAddSupplier-Form',
            padding:'5 8 5 8',
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
                            margin:'0 0 0 0',
                            layout:'form',
                            items:[{
                                name: 'SupplierName',
                                id: 'Koltiva.view.Refinery.FormAddSupplier-SupplierName',
                                xtype: 'textfield',
                                fieldLabel: lang('Supplier Name'),
                                labelSeparator: '',
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Refinery.FormAddSupplier-KategoriKebun',
                                name: 'KategoriKebun',
                                store: cmb_kategori_kebun,
                                fieldLabel: lang('Kategori Kebun'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                allowBlank: false,
                                valueField: 'id'
                            }]
                        },{
                            columnWidth: 0.4,
                            margin:'0 10 0 10',
                            layout:'form',
                            items:[{
                                name: 'FFBSupply',
                                id: 'Koltiva.view.Refinery.FormAddSupplier-FFBSupply',
                                xtype: 'numberfield',
                                fieldLabel: lang('FFB Supply (Ton)'),
                                labelSeparator: '',
                                labelAlign:'top',
                                allowBlank: false,
                            },{
                                name: 'Tracebility',
                                id: 'Koltiva.view.Refinery.FormAddSupplier-Tracebility',
                                xtype: 'numberfield',
                                fieldLabel: lang('Tracebility'),
                                labelSeparator: '',
                                labelAlign:'top',
                                allowBlank: false,
                            }]
                        }]
                    }],
                }]
            }],
            listeners: {
                afterrender: function(){
                    if(thisObj.formVar.opsiDisplay == 'insert'){
                        //form reset
                        Ext.getCmp('Koltiva.view.Refinery.FormAddSupplier-Form').getForm().reset();
                    }
                }
            }
        }];

        thisObj.buttons = [{
            text: 'Add to List',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.Refinery.FormAddSupplier-Form-BtnSave',
            handler: function () {
                var formSelectContact = Ext.getCmp('Koltiva.view.Refinery.FormAddSupplier-Form').getForm();
                if (formSelectContact.isValid()) {
                    var SupplierName = Ext.getCmp('Koltiva.view.Refinery.FormAddSupplier-SupplierName').getValue();
                    var KategoriKebun = Ext.getCmp('Koltiva.view.Refinery.FormAddSupplier-KategoriKebun').getValue();
                    var Tracebility = Ext.getCmp('Koltiva.view.Refinery.FormAddSupplier-Tracebility').getValue();
                    var FFBSupply = Ext.getCmp('Koltiva.view.Refinery.FormAddSupplier-FFBSupply').getValue();
                    var KategoriKebunName;
                    if(KategoriKebun == 1){
                        KategoriKebunName = "Farmer Plasma";
                    }
                    if(KategoriKebun == 2){
                        KategoriKebunName = "Direct Smallholder";
                    }
                    if(KategoriKebun == 3){
                        KategoriKebunName = "Agent / Dealer / Vendor";
                    }
                    if(KategoriKebun == 4){
                        KategoriKebunName = "Owner Estate";
                    }
                    if(KategoriKebun == 5){
                        KategoriKebunName = "External Estate";
                    }
                    ContactRecords.push({
                        SupplierName    : SupplierName,
                        KategoriKebun   : KategoriKebun,
                        KategoriKebunName   : KategoriKebunName,
                        Tracebility     : Tracebility,
                        FFBSupply       : FFBSupply
                    });
                    Ext.getCmp('Koltiva.view.Refinery.FormTracebilityDeclarationManual-gridSupplier').getStore().loadData(ContactRecords);
                    thisObj.close();
                    //refresh store FamLab yg manggil
                    // /*Ext.data.StoreManager.lookup('Koltiva.store.ContactList.Contacts').load();
                    // thisObj.close();*/
                }
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterrender: function(){
            var thisObj = this;
        }
    }
});