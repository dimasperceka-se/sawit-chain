/**
 * @author [Fashah Darullah]
 * @email [fashah.darullah@koltiva.com]
 * @create date 2019-08-21 11:25:59
 * @modify date 2019-08-21 11:25:59
 * @desc [description]
 */



Ext.define('Koltiva.view.Mill.FormAddData' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Mill.FormAddData',
    title: lang('Add New Data'),
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

        var cmb_kategori_kebun = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                    "id": "2",
                    "label": lang("Direct Smallholder")
                },{
                    "id": "3",
                    "label": lang("Agent / Dealer / Vendor")
                }
            ]
        });

        var cmb_source_name = Ext.create('Ext.data.Store', {
            fields: ["id", "label"],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/mill/get_source_name',
                extraParams:{
                    PartnerID : thisObj.PartnerID,
                    SourceType : thisObj.SourceType
                },
                reader: {
                    type: 'json'
                }
            }
        });
        if(thisObj.SourceType != 4){
            cmb_source_name.load();
        }

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Mill.FormAddData-Form',
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
                                name: 'MillTCID',
                                id: 'Koltiva.view.Mill.FormAddData-MillTCID',
                                xtype: 'hiddenfield',
                                fieldLabel: lang('MillTCID'),
                                labelSeparator: '',
                                labelAlign:'top',
                                allowBlank: false,
                                value : thisObj.MillTCID
                            },{
                                columnWidth: 0.4,
                                padding:'0 10 0 10',
                                layout:'form',
                                items:[{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Mill.FormAddData-SourceCategory',
                                    name: 'SourceCategory',
                                    store: cmb_kategori_kebun,
                                    fieldLabel: lang('Kategori Kebun'),
                                    labelAlign:'top',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    allowBlank: true,
                                    valueField: 'id',
                                    listeners:{
                                        change: function(cb, nv, ov){
                                            cmb_source_name.load({
                                                params: {
                                                    KategoriKebun: nv
                                                }
                                            });
                                        }
                                    }
                                }]
                            },{
                                columnWidth: 0.5,
                                padding:'0 10 0 10',
                                layout:'form',
                                items:[{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Mill.FormAddData-SourceName',
                                    name: 'SourceName',
                                    store: cmb_source_name,
                                    fieldLabel: lang('Supplier Name'),
                                    labelAlign:'top',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    allowBlank: false,
                                    valueField: 'id'
                                }]
                            },{
                                name: 'SourceType',
                                id: 'Koltiva.view.Mill.FormAddData-SourceType',
                                xtype: 'hiddenfield',
                                fieldLabel: lang('Source Type'),
                                labelSeparator: '',
                                labelAlign:'top',
                                allowBlank: false,
                                value : thisObj.SourceType
                            },{
                                name: 'PartnerID',
                                id: 'Koltiva.view.Mill.FormAddData-PartnerID',
                                xtype: 'hiddenfield',
                                fieldLabel: lang('PartnerID'),
                                labelSeparator: '',
                                labelAlign:'top',
                                allowBlank: false,
                                value : thisObj.PartnerID
                            },{
                                name: 'Year',
                                id: 'Koltiva.view.Mill.FormAddData-Year',
                                xtype: 'hiddenfield',
                                fieldLabel: lang('Year'),
                                labelSeparator: '',
                                labelAlign:'top',
                                allowBlank: true
                            },{
                                name: 'Period',
                                id: 'Koltiva.view.Mill.FormAddData-Period',
                                xtype: 'hiddenfield',
                                fieldLabel: lang('Period'),
                                labelSeparator: '',
                                labelAlign:'top',
                                allowBlank: true
                            }]
                        },{
                            columnWidth: 0.4,
                            margin:'0 10 0 10',
                            layout:'form',
                            items:[{
                                name: 'FFBSupply',
                                id: 'Koltiva.view.Mill.FormAddData-FFBSupply',
                                xtype: 'numberfield',
                                fieldLabel: lang('Tonnage Bridge'),
                                labelSeparator: '',
                                labelAlign:'top',
                                allowBlank: false,
                            },{
                                name: 'TCPercentage',
                                id: 'Koltiva.view.Mill.FormAddData-TCPercentage',
                                xtype: 'hiddenfield',
                                fieldLabel: lang('Tracebility')+' (%)',
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
                        Ext.getCmp('Koltiva.view.Mill.FormAddData-Form').getForm().reset();
                    }

                    if(thisObj.SourceType == 4){
                        Ext.getCmp('Koltiva.view.Mill.FormAddData-SourceCategory').setVisible(true);
                    }else{
                        Ext.getCmp('Koltiva.view.Mill.FormAddData-SourceCategory').setVisible(false);
                    }                    

                    var palm_trdec_list_searchp = JSON.parse(localStorage.getItem('palm_trdec_list_searchp'));
                    if(palm_trdec_list_searchp != null){
                        Year        = palm_trdec_list_searchp.Year;
                        Period      = palm_trdec_list_searchp.Period;
                    }else{
                        Year        = m_year;
                        Period      = m_period;
                    }

                    Ext.getCmp("Koltiva.view.Mill.FormAddData-Year").setValue(Year);
                    Ext.getCmp("Koltiva.view.Mill.FormAddData-Period").setValue(Period);
                }
            }
        }];

        thisObj.buttons = [{
            text: 'Save',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.Mill.FormAddData-Form-BtnSave',
            handler: function () {
                var formSelectContact = Ext.getCmp('Koltiva.view.Mill.FormAddData-Form').getForm();
                var SourceType = Ext.getCmp('Koltiva.view.Mill.FormAddData-SourceType').getValue();
                var SourceCategory = Ext.getCmp('Koltiva.view.Mill.FormAddData-SourceCategory').getValue();
                if(SourceType == "4"){
                    if(SourceCategory == "" || SourceCategory == "null" || SourceCategory == null){
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Please Select Category'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                        return;
                    }
                }
                if (formSelectContact.isValid()) {
                    formSelectContact.submit({
                        url: m_api + '/mill/submit_tc_declaration_new',
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

                            var palm_trdec_list_searchp = JSON.parse(localStorage.getItem('palm_trdec_list_searchp'));
                            if(palm_trdec_list_searchp != null){
                                Year        = palm_trdec_list_searchp.Year;
                                Period      = palm_trdec_list_searchp.Period;
                            }else{
                                Year        = m_year;
                                Period      = m_period;
                            }

                            Ext.getCmp('Koltiva.view.Mill.PanelTabTracebilityDeclarationDocument').getForm().load({
                                url: m_api + '/mill/mill_tracebilityDeclaration',
                                method: 'GET',
                                params: {
                                    PartnerID: thisObj.PartnerID,
                                    Year : Year,
                                    Period : Period
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
                            if(SourceType == 2){
                                Ext.getCmp('Koltiva.view.Mill.CompanyOwnedEstate-GridCompanyOwnedEstate').getStore().load();
                            }

                            if(SourceType == 1){
                                Ext.getCmp('Koltiva.view.Mill.Plasma-GridPlasma').getStore().load();
                            }

                            if(SourceType == 3){
                                Ext.getCmp('Koltiva.view.Mill.ExternalEstate-GridExternalEstate').getStore().load();
                            }

                            if(SourceType == 4){
                                Ext.getCmp('Koltiva.view.Mill.OtherSupplier-GridOtherSupplier').getStore().load();
                            }
                            thisObj.close();
                        },
                        failure: function(fp, o){
                            var r = Ext.decode(o.response.responseText);
                            console.log(o);
                            var pesanNya;
                            if(r.message != undefined){
                                pesanNya = r.message;
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

            if(thisObj.opsiDisplay == 'update'){
                Ext.getCmp('Koltiva.view.Mill.FormAddData-Form').getForm().load({
                    url: m_api + '/mill/form_tc_declaration_new',
                    method: 'GET',
                    params: {
                        MillTCID: this.MillTCID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                    }
                });
            }
        }
    }
});