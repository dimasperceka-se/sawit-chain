Ext.define('Koltiva.view.Refinery.panelTabTracebilityDeclarationDocumentManual' ,{
    extend: 'Ext.form.Panel',
    id: 'Koltiva.view.Refinery.panelTabTracebilityDeclarationDocumentManual',    
    fileUpload: true,
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    title: lang('Tracebility Declaration Document - Tracebility to Plantation'),
    fileUpload: true,
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    setFilterListFarcan: function(YearStart){
        localStorage.setItem('palm_trdec_list_searchp', JSON.stringify({
            Year: Ext.getCmp('Year').getValue(),
            Period: Ext.getCmp('Period').getValue(),
        }));
    },
    Search: function(Year,Period){
        localStorage.setItem('palm_trdec_list_searchp', JSON.stringify({
            Year: Year,
            Period: Period
        }));
        Ext.getCmp('Koltiva.view.Refinery.CompanyOwnedEstate-GridCompanyOwnedEstate').getStore().loadPage(1);
        Ext.getCmp('Koltiva.view.Refinery.Plasma-GridPlasma').getStore().loadPage(1);
        Ext.getCmp('Koltiva.view.Refinery.ExternalEstate-GridExternalEstate').getStore().loadPage(1);
        Ext.getCmp('Koltiva.view.Refinery.OtherSupplier-GridOtherSupplier').getStore().loadPage(1);

        //load data form
        Ext.getCmp('Koltiva.view.Refinery.panelTabTracebilityDeclarationDocumentManual').getForm().load({
            url: m_api + '/refinery/refinery_tracebilityDeclaration',
            method: 'GET',
            params: {
                RefineryID: this.viewVar.RefineryID,
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
    },
    PrintProfile: function(RefineryID,Year,Period){
        preview_cetak_surat(m_api + '/refinery/refinery_summary/RefineryID/'+RefineryID+'/Year/'+Year+'/Period/'+Period+'/RefineryTCDID/');
    },
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai (start)
        var cmb_year_option = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption',{
            storeVar: {
                yearRange: 2
            }
        });

        var cmb_period = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"id" : "half", "label" : lang("6 Month")},
                {"id" : "full", "label" : lang("1 Year")}
            ]
        });
        //store yg dipakai (end)

        
        var GridCompanyOwnedEstate = Ext.create('Koltiva.view.Refinery.GridCompanyOwnedEstate');
        var GridPlasma = Ext.create('Koltiva.view.Refinery.GridPlasma');
        var GridExternalEstate = Ext.create('Koltiva.view.Refinery.GridExternalEstate');
        var GridOtherSupplier = Ext.create('Koltiva.view.Refinery.GridOtherSupplier');

        //Call Panel Contact on Tab
        GridCompanyOwnedEstate.setViewVar({
            RefineryID : thisObj.viewVar.RefineryID
        });
        
        GridPlasma.setViewVar({
            RefineryID : thisObj.viewVar.RefineryID
        });
        
        GridExternalEstate.setViewVar({
            RefineryID : thisObj.viewVar.RefineryID
        });
        
        GridOtherSupplier.setViewVar({
            RefineryID : thisObj.viewVar.RefineryID
        });
        //Call Panel Contact on Tab

        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 1,
                layout:'form',
                style:'padding-right:25px;',
                items:[{
                    layout: 'column',
                    border: false,
                    items:[{
                        columnWidth: 1,
                        layout:'form',
                        style:'padding-right:25px;',
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 1,
                                html: '<p><b>'+lang("Filter")+' :</b></p>'
                            },{
                                columnWidth: 0.2,
                                layout:'form',
                                style:'padding-right:5px;',
                                items:[{
                                    id: 'Year',
                                    name: 'Year',
                                    xtype: 'combobox',
                                    anchor: '50%',
                                    fieldLabel: lang('Year'),
                                    labelAlign:'top',
                                    store: cmb_year_option,
                                    value:m_year,
                                    displayField: 'label',
                                    valueField: 'id',
                                    queryMode: 'local',
                                }]
                            },{
                                columnWidth: 0.2,
                                layout:'form',
                                style:'padding-right:5px;',
                                items:[{
                                    id: 'Period',
                                    name: 'Period',
                                    xtype: 'combobox',
                                    anchor: '50%',
                                    fieldLabel: lang('Period'),
                                    labelAlign:'top',
                                    store: cmb_period,
                                    value:'half',
                                    displayField: 'label',
                                    valueField: 'id',
                                    queryMode: 'local',
                                }]
                            },{
                                columnWidth: 0.08,
                                layout:'form',
                                items: [{
                                    style:'float:left;margin-left:10px;margin-top:30px',
                                    xtype:'button',
                                    iconCls:'search',
                                    text:lang('View'),
                                    handler:function(c){
                                        var Period  = Ext.getCmp("Period").getValue();
                                        var Year   = Ext.getCmp("Year").getValue();
                                        thisObj.Search(Year,Period);
                                    }
                                }]
                            },{
                                columnWidth: 0.12,
                                layout:'form',
                                items: [{
                                    style:'float:left;margin-top:30px',
                                    xtype:'button',
                                    iconCls:'print',
                                    text:lang('Summary'),
                                    handler:function(c){
                                        var Period  = Ext.getCmp("Period").getValue();
                                        var Year   = Ext.getCmp("Year").getValue();
                                        thisObj.PrintProfile(thisObj.viewVar.RefineryID,Year,Period);
                                    }
                                }]
                            }]
                        }]
                    },{
                        columnWidth: 1,
                        layout:'form',
                        style:'padding-right:25px;',
                        items:[{
                            html:'<div style="border-bottom:1px dashed gray;color:#34AA00;">&nbsp;</div>'
                        }]
                    }]
                }]
            },{
                columnWidth: 1,
                layout:'form',
                style:'padding-right:25px;',
                items:[{
                    layout: 'column',
                    border: false,
                    items:[{
                        columnWidth: 0.5,
                        layout:'form',
                        style:'padding-right:25px;',
                        items:[{
                            xtype: 'textfield',
                            id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalFFB',
                            name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalFFB',
                            fieldLabel: lang('Total FFB Procured (ton)'),
                            labelWidth: 200,
                            readOnly:true
                        }]
                    }]
                }]
            },{
                columnWidth: 0.495,
                layout:'form',
                style:'padding-right:25px;',
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    style:'padding-right:25px;',
                    items:[{
                    html:'<div></div>',
                    },{
                        xtype:'panel',
                        title: lang('Company Owned Estates ( Estate Inti )'),
                        frame: false,
                        id: 'Koltiva.view.Farcan.PanelTabFormApplication-Form-SectionAddLocation',
                        style:'margin-top:10px;'
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.5,
                            layout:'form',
                            style: 'padding-right: 5px',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredOwnedEstate',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredOwnedEstate',
                                fieldLabel: lang('FFB Procured (ton)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOwnedEstate',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOwnedEstate',
                                fieldLabel: lang('Proportion of Total FBB Procured (%)'),
                                labelAlign:'top',
                                readOnly:true
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style: 'padding-left: 5px',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTraceOwnedEstate',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTraceOwnedEstate',
                                fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryOwnedEstate',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryOwnedEstate',
                                fieldLabel: lang('% TtP Refinery'),
                                labelAlign:'top',
                                readOnly:true
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 1,
                            layout:'form',
                            items:[
                                {
                                    xtype: 'panel',
                                    id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-GridCompanyOwnedEstate',
                                    items:[GridCompanyOwnedEstate]
                                }
                            ]
                        }]
                    }]
                },{
                    columnWidth: 1,
                    layout:'form',
                    style:'padding-right:25px;',
                    items:[{
                        html:'<div></div>',
                    },{
                        xtype:'panel',
                        title: lang('Other Suppliers (Direct Smallholder, Dealer/Agent/Vendor)'),
                        frame: false,
                        id: 'Koltiva.view.Farcan.PanelTabFormApplication-Form-SectionOtherSuppliers',
                        style:'margin-top:10px;'
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.5,
                            layout:'form',
                            style: 'padding-right: 5px',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredOtherSupplier',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredOtherSupplier',
                                fieldLabel: lang('FFB Procured (ton)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOtherSupplier',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOtherSupplier',
                                fieldLabel: lang('Proportion of Total FBB Procured (%)'),
                                labelAlign:'top',
                                readOnly:true
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style: 'padding-left: 5px',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTraceOtherSupplier',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTraceOtherSupplier',
                                fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryOtherSupplier',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryOtherSupplier',
                                fieldLabel: lang('% TtP Refinery'),
                                labelAlign:'top',
                                readOnly:true
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 1,
                            layout:'form',
                            items:[
                                {
                                    xtype: 'panel',
                                    id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-GridOtherSupplier',
                                    items:[GridOtherSupplier]
                                }
                            ]
                        }]
                    }]
                }]
            },{
                columnWidth: 0.495,
                layout:'form',
                style:'padding-right:25px;',
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    style:'padding-right:25px;',
                    items:[{
                        html:'<div></div>',
                    },{
                        xtype:'panel',
                        title: lang('Plasma Smallholders (Estate Plasma)'),
                        frame: false,
                        id: 'Koltiva.view.Farcan.PanelTabFormApplication-Form-SectionPlasmaSmallholder',
                        style:'margin-top:10px;'
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.5,
                            layout:'form',
                            style: 'padding-right: 5px',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredPlasma',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredPlasma',
                                fieldLabel: lang('FFB Procured (ton)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionPlasma',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionPlasma',
                                fieldLabel: lang('Proportion of Total FBB Procured (%)'),
                                labelAlign:'top',
                                readOnly:true
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style: 'padding-left: 5px',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTracePlasma',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTracePlasma',
                                fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryPlasma',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryPlasma',
                                fieldLabel: lang('% TtP Refinery'),
                                labelAlign:'top',
                                readOnly:true
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 1,
                            layout:'form',
                            items:[
                                {
                                    xtype: 'panel',
                                    id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-GridPlasma',
                                    items:[GridPlasma]
                                }
                            ]
                        }]
                    }]
                },{
                    columnWidth: 1,
                    layout:'form',
                    style:'padding-right:25px;',
                    items:[{
                        html:'<div></div>',
                    },{
                        xtype:'panel',
                        title: lang('External Estates'),
                        frame: false,
                        id: 'Koltiva.view.Farcan.PanelTabFormApplication-Form-SectionExternalEstates',
                        style:'margin-top:10px;'
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.5,
                            layout:'form',
                            style: 'padding-right: 5px',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredExternalEstate',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredExternalEstate',
                                fieldLabel: lang('FFB Procured (ton)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionExternalEstate',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionExternalEstate',
                                fieldLabel: lang('Proportion of Total FBB Procured (%)'),
                                labelAlign:'top',
                                readOnly:true
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style: 'padding-left: 5px',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTraceExternalEstate',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TotalTraceExternalEstate',
                                fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryExternalEstate',
                                name: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-TtpRefineryExternalEstate',
                                fieldLabel: lang('% TtP Refinery'),
                                labelAlign:'top',
                                readOnly:true
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 1,
                            layout:'form',
                            items:[
                                {
                                    xtype: 'panel',
                                    id: 'Koltiva.view.Refinery.FormTracebilityDeclaration-FormBasicData-GridExternalEstate',
                                    items:[GridExternalEstate]
                                }
                            ]
                        }]
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //load data form
            Ext.getCmp('Koltiva.view.Refinery.panelTabTracebilityDeclarationDocumentManual').getForm().load({
                url: m_api + '/refinery/refinery_tracebilityDeclaration',
                method: 'GET',
                params: {
                    RefineryID: this.viewVar.RefineryID,
                    Year : Ext.getCmp('Year').getValue(),
                    Period : Ext.getCmp('Period').getValue()
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

            //isikan variabel dari local storage
            var palm_trdec_list_searchp = JSON.parse(localStorage.getItem('palm_trdec_list_searchp'));
            if(palm_trdec_list_searchp != null){
                Ext.getCmp('Year').setValue(palm_trdec_list_searchp.Year);
                Ext.getCmp('Period').setValue(palm_trdec_list_searchp.Period);
            }

            //load storenya sebelum viewnya aktif
            this.setFilterListFarcan();
            Ext.getCmp('Koltiva.view.Refinery.CompanyOwnedEstate-GridCompanyOwnedEstate').getStore().load();
            Ext.getCmp('Koltiva.view.Refinery.Plasma-GridPlasma').getStore().load();
            Ext.getCmp('Koltiva.view.Refinery.ExternalEstate-GridExternalEstate').getStore().load();
            Ext.getCmp('Koltiva.view.Refinery.OtherSupplier-GridOtherSupplier').getStore().load();
        }
    }
})