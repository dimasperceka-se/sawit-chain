Ext.define('Koltiva.view.Mill.PanelTabTracebilityDeclarationDocument' ,{
    extend: 'Ext.form.Panel',
    id: 'Koltiva.view.Mill.PanelTabTracebilityDeclarationDocument',    
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
    Approving:function(PartnerID,Year,Period){
        Ext.Ajax.request({
            waitMsg: 'Please Wait',
            url: m_api + '/mill/approving',
            method: 'POST',
            params: {
                PartnerID : PartnerID,
                Year:Year,
                Period:Period
            },
            success: function(response, opts) {
                Ext.MessageBox.show({
                    title: 'Information',
                    msg: lang('Data Approved'),
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
                        PartnerID: PartnerID,
                        Year : Year,
                        Period : Period
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        
                        if(r.data.Approved == 1 ){
                            Ext.getCmp("button_add_data_OS").setVisible(false);
                            Ext.getCmp("button_add_data_plasma").setVisible(false);
                            Ext.getCmp("button_add_data_EE").setVisible(false);
                            Ext.getCmp("button_add_data").setVisible(false);
                            Ext.getCmp("SummaryBtn").setVisible(true);
                            Ext.getCmp("ApproveBtn").setVisible(false);
                            Ext.getCmp("UpdateGe").setVisible(false);
                            Ext.getCmp("DeleteGe").setVisible(false);
                            Ext.getCmp("UpdateOS").setVisible(false);
                            Ext.getCmp("DeleteOS").setVisible(false);
                            Ext.getCmp("UpdateGp").setVisible(false);
                            Ext.getCmp("DeleteGp").setVisible(false);
                            Ext.getCmp("UpdateCo").setVisible(false);
                            Ext.getCmp("DeleteCo").setVisible(false);
                            Ext.getCmp("Koltiva.view.Mill.GridOtherSupplier-Action").hide();
                            Ext.getCmp("Koltiva.view.Mill.GridExternalEstate-Action").hide();
                            Ext.getCmp("Koltiva.view.Mill.GridCompanyOwnedEstate-Action").hide();
                            Ext.getCmp("Koltiva.view.Mill.GridPlasma-Action").hide();
                        }else{
                            Ext.getCmp("SummaryBtn").setVisible(true);
                            Ext.getCmp("ApproveBtn").setVisible(true);
                            Ext.getCmp("button_add_data_OS").setVisible(true);
                            Ext.getCmp("button_add_data_plasma").setVisible(true);
                            Ext.getCmp("button_add_data_EE").setVisible(true);
                            Ext.getCmp("button_add_data").setVisible(true);
                            Ext.getCmp("UpdateGe").setVisible(true);
                            Ext.getCmp("DeleteGe").setVisible(true);
                            Ext.getCmp("UpdateOS").setVisible(true);
                            Ext.getCmp("DeleteOS").setVisible(true);
                            Ext.getCmp("UpdateGp").setVisible(true);
                            Ext.getCmp("DeleteGp").setVisible(true);
                            Ext.getCmp("UpdateCo").setVisible(true);
                            Ext.getCmp("DeleteCo").setVisible(true);
                            Ext.getCmp("Koltiva.view.Mill.GridOtherSupplier-Action").show();
                            Ext.getCmp("Koltiva.view.Mill.GridExternalEstate-Action").show();
                            Ext.getCmp("Koltiva.view.Mill.GridCompanyOwnedEstate-Action").show();
                            Ext.getCmp("Koltiva.view.Mill.GridPlasma-Action").show();
                        }

                        if(r.data.Hidden == 1){
                            Ext.getCmp("ApproveBtn").setVisible(false);
                            Ext.getCmp("button_add_data_OS").setVisible(false);
                            Ext.getCmp("button_add_data_plasma").setVisible(false);
                            Ext.getCmp("button_add_data_EE").setVisible(false);
                            Ext.getCmp("button_add_data").setVisible(false);
                            Ext.getCmp("SummaryBtn").setVisible(true);
                            Ext.getCmp("UpdateGe").setVisible(false);
                            Ext.getCmp("DeleteGe").setVisible(false);
                            Ext.getCmp("UpdateOS").setVisible(false);
                            Ext.getCmp("DeleteOS").setVisible(false);
                            Ext.getCmp("UpdateGp").setVisible(false);
                            Ext.getCmp("DeleteGp").setVisible(false);
                            Ext.getCmp("UpdateCo").setVisible(false);
                            Ext.getCmp("DeleteCo").setVisible(false);
                            Ext.getCmp("Koltiva.view.Mill.GridOtherSupplier-Action").hide();
                            Ext.getCmp("Koltiva.view.Mill.GridExternalEstate-Action").hide();
                            Ext.getCmp("Koltiva.view.Mill.GridCompanyOwnedEstate-Action").hide();
                            Ext.getCmp("Koltiva.view.Mill.GridPlasma-Action").hide();
                        }
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

                //refresh store
                Ext.getCmp('Koltiva.view.Mill.CompanyOwnedEstate-GridCompanyOwnedEstate').getStore().load();
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
    },
    Search: function(Year,Period){
        localStorage.setItem('palm_trdec_list_searchp', JSON.stringify({
            Year: Year,
            Period: Period
        }));
        Ext.getCmp('Koltiva.view.Mill.CompanyOwnedEstate-GridCompanyOwnedEstate').getStore().loadPage(1);
        Ext.getCmp('Koltiva.view.Mill.Plasma-GridPlasma').getStore().loadPage(1);
        Ext.getCmp('Koltiva.view.Mill.ExternalEstate-GridExternalEstate').getStore().loadPage(1);
        Ext.getCmp('Koltiva.view.Mill.OtherSupplier-GridOtherSupplier').getStore().loadPage(1);

        //load data form
        Ext.getCmp('Koltiva.view.Mill.PanelTabTracebilityDeclarationDocument').getForm().load({
            url: m_api + '/mill/mill_tracebilityDeclaration',
            method: 'GET',
            params: {
                PartnerID: this.viewVar.PartnerID,
                Year : Year,
                Period : Period
            },
            success: function(form, action) {
                var r = Ext.decode(action.response.responseText);
                
                if(r.data.Approved == 1 ){
                    Ext.getCmp("button_add_data_OS").setVisible(false);
                    Ext.getCmp("button_add_data_plasma").setVisible(false);
                    Ext.getCmp("button_add_data_EE").setVisible(false);
                    Ext.getCmp("button_add_data").setVisible(false);
                    Ext.getCmp("SummaryBtn").setVisible(true);
                    Ext.getCmp("ApproveBtn").setVisible(false);
                    Ext.getCmp("UpdateGe").setVisible(false);
                    Ext.getCmp("DeleteGe").setVisible(false);
                    Ext.getCmp("UpdateOS").setVisible(false);
                    Ext.getCmp("DeleteOS").setVisible(false);
                    Ext.getCmp("UpdateGp").setVisible(false);
                    Ext.getCmp("DeleteGp").setVisible(false);
                    Ext.getCmp("UpdateCo").setVisible(false);
                    Ext.getCmp("DeleteCo").setVisible(false);
                    Ext.getCmp("Koltiva.view.Mill.GridOtherSupplier-Action").hide();
                    Ext.getCmp("Koltiva.view.Mill.GridExternalEstate-Action").hide();
                    Ext.getCmp("Koltiva.view.Mill.GridCompanyOwnedEstate-Action").hide();
                    Ext.getCmp("Koltiva.view.Mill.GridPlasma-Action").hide();
                }else{
                    Ext.getCmp("SummaryBtn").setVisible(true);
                    Ext.getCmp("ApproveBtn").setVisible(true);
                    Ext.getCmp("button_add_data_OS").setVisible(true);
                    Ext.getCmp("button_add_data_plasma").setVisible(true);
                    Ext.getCmp("button_add_data_EE").setVisible(true);
                    Ext.getCmp("button_add_data").setVisible(true);
                    Ext.getCmp("UpdateGe").setVisible(true);
                    Ext.getCmp("DeleteGe").setVisible(true);
                    Ext.getCmp("UpdateOS").setVisible(true);
                    Ext.getCmp("DeleteOS").setVisible(true);
                    Ext.getCmp("UpdateGp").setVisible(true);
                    Ext.getCmp("DeleteGp").setVisible(true);
                    Ext.getCmp("UpdateCo").setVisible(true);
                    Ext.getCmp("DeleteCo").setVisible(true);
                    Ext.getCmp("Koltiva.view.Mill.GridOtherSupplier-Action").show();
                    Ext.getCmp("Koltiva.view.Mill.GridExternalEstate-Action").show();
                    Ext.getCmp("Koltiva.view.Mill.GridCompanyOwnedEstate-Action").show();
                    Ext.getCmp("Koltiva.view.Mill.GridPlasma-Action").show();
                }

                if(r.data.Hidden == 1){
                    Ext.getCmp("ApproveBtn").setVisible(false);
                    Ext.getCmp("button_add_data_OS").setVisible(false);
                    Ext.getCmp("button_add_data_plasma").setVisible(false);
                    Ext.getCmp("button_add_data_EE").setVisible(false);
                    Ext.getCmp("button_add_data").setVisible(false);
                    Ext.getCmp("SummaryBtn").setVisible(true);
                    Ext.getCmp("UpdateGe").setVisible(false);
                    Ext.getCmp("DeleteGe").setVisible(false);
                    Ext.getCmp("UpdateOS").setVisible(false);
                    Ext.getCmp("DeleteOS").setVisible(false);
                    Ext.getCmp("UpdateGp").setVisible(false);
                    Ext.getCmp("DeleteGp").setVisible(false);
                    Ext.getCmp("UpdateCo").setVisible(false);
                    Ext.getCmp("DeleteCo").setVisible(false);
                    Ext.getCmp("Koltiva.view.Mill.GridOtherSupplier-Action").hide();
                    Ext.getCmp("Koltiva.view.Mill.GridExternalEstate-Action").hide();
                    Ext.getCmp("Koltiva.view.Mill.GridCompanyOwnedEstate-Action").hide();
                    Ext.getCmp("Koltiva.view.Mill.GridPlasma-Action").hide();
                }
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
    PrintProfile: function(PartnerID,Year,Period){
        preview_cetak_surat(m_api + '/mill/mill_summary/PartnerID/'+PartnerID+'/Year/'+Year+'/Period/'+Period);
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
                {"id" : "half", "label" : lang("January - June")},
                {"id" : "half2", "label" : lang("July - December")},
                {"id" : "full", "label" : lang("January - December")}
            ]
        });
        //store yg dipakai (end)

        
        var GridCompanyOwnedEstate = Ext.create('Koltiva.view.Mill.GridCompanyOwnedEstate');
        var GridPlasma = Ext.create('Koltiva.view.Mill.GridPlasma');
        var GridExternalEstate = Ext.create('Koltiva.view.Mill.GridExternalEstate');
        var GridOtherSupplier = Ext.create('Koltiva.view.Mill.GridOtherSupplier');

        //Call Panel Contact on Tab
        GridCompanyOwnedEstate.setViewVar({
            PartnerID : thisObj.viewVar.PartnerID
        });
        
        GridPlasma.setViewVar({
            PartnerID : thisObj.viewVar.PartnerID
        });
        
        GridExternalEstate.setViewVar({
            PartnerID : thisObj.viewVar.PartnerID
        });
        
        GridOtherSupplier.setViewVar({
            PartnerID : thisObj.viewVar.PartnerID
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
                                columnWidth: 1,
                                layout:'form',
                                style:'padding-right:5px;',
                                items:[{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.07,
                                        layout:'form',
                                        items: [{
                                            style:'float:left;margin-right:180px;margin-top:30px',
                                            xtype:'button',
                                            iconCls:'search',
                                            text:lang('View'),
                                            handler:function(c){
                                                var Period  = Ext.getCmp("Period").getValue();
                                                var Year   = Ext.getCmp("Year").getValue();
                                                thisObj.Search(Year,Period);
                                            }
                                        }]
                                    },
                                    {
                                        columnWidth: 0.12,
                                        layout:'form',
                                        id:'SummaryBtn',
                                        items: [{
                                            style:'float:left;margin-left:11px;;margin-top:30px',
                                            xtype:'button',
                                            iconCls:'print',
                                            text:lang('Summary'),
                                            handler:function(c){
                                                 //load data form
                                                 Ext.getCmp('Koltiva.view.Mill.PanelTabTracebilityDeclarationDocument').getForm().load({
                                                    url: m_api + '/mill/mill_tracebilityDeclaration',
                                                    method: 'GET',
                                                    params: {
                                                        PartnerID: thisObj.viewVar.PartnerID,
                                                        Year : Ext.getCmp('Year').getValue(),
                                                        Period : Ext.getCmp('Period').getValue()
                                                    },
                                                    success: function(form, action) {
                                                        var r = Ext.decode(action.response.responseText);
                                                        
                                                        if(r.data.Total == 0 ){
                                                            Ext.MessageBox.show({
                                                                title: 'Failed',
                                                                msg: lang('Please input declaration data first'),
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-error'
                                                            });
                                                        } else {
                                                            var Period  = Ext.getCmp("Period").getValue();
                                                            var Year   = Ext.getCmp("Year").getValue();
                                                            thisObj.PrintProfile(thisObj.viewVar.PartnerID,Year,Period);
                                                        }
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
                                            }
                                        }]
                                    },
                                    {
                                        columnWidth: 0.08,
                                        layout:'form',
                                        id:'ApproveBtn',
                                        items: [{
                                            style:'float:left;margin-top:30px',
                                            xtype:'button',
                                            text:lang('Approve'),
                                            handler:function(c){

                                                 //load data form
                                                Ext.getCmp('Koltiva.view.Mill.PanelTabTracebilityDeclarationDocument').getForm().load({
                                                    url: m_api + '/mill/mill_tracebilityDeclaration',
                                                    method: 'GET',
                                                    params: {
                                                        PartnerID: thisObj.viewVar.PartnerID,
                                                        Year : Ext.getCmp('Year').getValue(),
                                                        Period : Ext.getCmp('Period').getValue()
                                                    },
                                                    success: function(form, action) {
                                                        var r = Ext.decode(action.response.responseText);
                                                        
                                                        if(r.data.Unapproved == 1 ){
                                                            Ext.MessageBox.show({
                                                                title: 'Failed',
                                                                msg: lang('You have not inputted Traceability to Plantation Data, Please input the data first before doing approval'),
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-error'
                                                            });
                                                        } else {
                                                             Ext.MessageBox.confirm('Message', lang('Approving This Data?') , function(btn){
                                                                if(btn == 'yes'){
                                                                    var Period  = Ext.getCmp("Period").getValue();
                                                                    var Year   = Ext.getCmp("Year").getValue();
                                                                    thisObj.Approving(thisObj.viewVar.PartnerID,Year,Period);
                                                                }
                                                            });
                                                        }
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
                                            }
                                        }]
                                    }
                                ]
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
                            id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalFFB',
                            name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalFFB',
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
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredOwnedEstate',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredOwnedEstate',
                                fieldLabel: lang('FFB Procured (ton)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOwnedEstate',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOwnedEstate',
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
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTraceOwnedEstate',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTraceOwnedEstate',
                                fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillOwnedEstate',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillOwnedEstate',
                                fieldLabel: lang('% TtP Mill'),
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
                                    id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-GridCompanyOwnedEstate',
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
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredOtherSupplier',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredOtherSupplier',
                                fieldLabel: lang('FFB Procured (ton)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOtherSupplier',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionOtherSupplier',
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
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTraceOtherSupplier',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTraceOtherSupplier',
                                fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillOtherSupplier',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillOtherSupplier',
                                fieldLabel: lang('% TtP Mill'),
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
                                    id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-GridOtherSupplier',
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
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredPlasma',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredPlasma',
                                fieldLabel: lang('FFB Procured (ton)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionPlasma',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionPlasma',
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
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTracePlasma',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTracePlasma',
                                fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillPlasma',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillPlasma',
                                fieldLabel: lang('% TtP Mill'),
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
                                    id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-GridPlasma',
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
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredExternalEstate',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredExternalEstate',
                                fieldLabel: lang('FFB Procured (ton)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionExternalEstate',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-FFBProcuredProportionExternalEstate',
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
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTraceExternalEstate',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TotalTraceExternalEstate',
                                fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillExternalEstate',
                                name: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-TtpMillExternalEstate',
                                fieldLabel: lang('% TtP Mill'),
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
                                    id: 'Koltiva.view.Mill.FormTracebilityDeclaration-FormBasicData-GridExternalEstate',
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

            //isikan variabel dari local storage
            var palm_trdec_list_searchp = JSON.parse(localStorage.getItem('palm_trdec_list_searchp'));
            if(palm_trdec_list_searchp != null){
                Ext.getCmp('Year').setValue(palm_trdec_list_searchp.Year);
                Ext.getCmp('Period').setValue(palm_trdec_list_searchp.Period);
            }
            Ext.getCmp("SummaryBtn").setVisible(true);
            Ext.getCmp("ApproveBtn").setVisible(false);

            //load data form
            Ext.getCmp('Koltiva.view.Mill.PanelTabTracebilityDeclarationDocument').getForm().load({
                url: m_api + '/mill/mill_tracebilityDeclaration',
                method: 'GET',
                params: {
                    PartnerID: this.viewVar.PartnerID,
                    Year : Ext.getCmp('Year').getValue(),
                    Period : Ext.getCmp('Period').getValue()
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                    
                    if(r.data.Approved == 1 ){
                        Ext.getCmp("button_add_data_OS").setVisible(false);
                        Ext.getCmp("button_add_data_plasma").setVisible(false);
                        Ext.getCmp("button_add_data_EE").setVisible(false);
                        Ext.getCmp("button_add_data").setVisible(false);
                        Ext.getCmp("SummaryBtn").setVisible(true);
                        Ext.getCmp("ApproveBtn").setVisible(false);
                        Ext.getCmp("UpdateGe").setVisible(false);
                        Ext.getCmp("DeleteGe").setVisible(false);
                        Ext.getCmp("UpdateOS").setVisible(false);
                        Ext.getCmp("UpdateGp").setVisible(false);
                        Ext.getCmp("DeleteGp").setVisible(false);
                        Ext.getCmp("UpdateCo").setVisible(false);
                        Ext.getCmp("DeleteCo").setVisible(false);
                        Ext.getCmp("Koltiva.view.Mill.GridOtherSupplier-Action").hide();
                        Ext.getCmp("Koltiva.view.Mill.GridExternalEstate-Action").hide();
                        Ext.getCmp("Koltiva.view.Mill.GridCompanyOwnedEstate-Action").hide();
                        Ext.getCmp("Koltiva.view.Mill.GridPlasma-Action").hide();
                    }else{
                        Ext.getCmp("SummaryBtn").setVisible(true);
                        Ext.getCmp("ApproveBtn").setVisible(true);
                        Ext.getCmp("button_add_data_OS").setVisible(true);
                        Ext.getCmp("button_add_data_plasma").setVisible(true);
                        Ext.getCmp("button_add_data_EE").setVisible(true);
                        Ext.getCmp("button_add_data").setVisible(true);
                        Ext.getCmp("UpdateGe").setVisible(true);
                        Ext.getCmp("DeleteGe").setVisible(true);
                        Ext.getCmp("UpdateOS").setVisible(true);
                        Ext.getCmp("UpdateGp").setVisible(true);
                        Ext.getCmp("DeleteGp").setVisible(true);
                        Ext.getCmp("UpdateCo").setVisible(true);
                        Ext.getCmp("DeleteCo").setVisible(true);
                        Ext.getCmp("Koltiva.view.Mill.GridOtherSupplier-Action").show();
                        Ext.getCmp("Koltiva.view.Mill.GridExternalEstate-Action").show();
                        Ext.getCmp("Koltiva.view.Mill.GridCompanyOwnedEstate-Action").show();
                        Ext.getCmp("Koltiva.view.Mill.GridPlasma-Action").show();
                    }

                    if(r.data.Hidden == 1){
                        Ext.getCmp("ApproveBtn").setVisible(false);
                        Ext.getCmp("button_add_data_OS").setVisible(false);
                        Ext.getCmp("button_add_data_plasma").setVisible(false);
                        Ext.getCmp("button_add_data_EE").setVisible(false);
                        Ext.getCmp("button_add_data").setVisible(false);
                        Ext.getCmp("SummaryBtn").setVisible(true);
                        Ext.getCmp("UpdateGe").setVisible(false);
                        Ext.getCmp("DeleteGe").setVisible(false);
                        Ext.getCmp("UpdateOS").setVisible(false);
                        Ext.getCmp("DeleteOS").setVisible(false);
                        Ext.getCmp("UpdateGp").setVisible(false);
                        Ext.getCmp("DeleteGp").setVisible(false);
                        Ext.getCmp("UpdateCo").setVisible(false);
                        Ext.getCmp("DeleteCo").setVisible(false);
                        Ext.getCmp("Koltiva.view.Mill.GridOtherSupplier-Action").hide();
                        Ext.getCmp("Koltiva.view.Mill.GridExternalEstate-Action").hide();
                        Ext.getCmp("Koltiva.view.Mill.GridCompanyOwnedEstate-Action").hide();
                        Ext.getCmp("Koltiva.view.Mill.GridPlasma-Action").hide();
                    }
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
            
            //load storenya sebelum viewnya aktif
            this.setFilterListFarcan();
            Ext.getCmp('Koltiva.view.Mill.CompanyOwnedEstate-GridCompanyOwnedEstate').getStore().load();
            Ext.getCmp('Koltiva.view.Mill.Plasma-GridPlasma').getStore().load();
            Ext.getCmp('Koltiva.view.Mill.ExternalEstate-GridExternalEstate').getStore().load();
            Ext.getCmp('Koltiva.view.Mill.OtherSupplier-GridOtherSupplier').getStore().load();
        }
    }
})