/*
* @Author: muhammad hidayaturrohman
* @Date:   2020-11-05
* @Last Modified by:   muhammad hidayaturrohman
* @Last Modified time: 2020-11-05
*/
/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar (RefineryID)
*/

Ext.define('Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    PrintProfile: function(RefineryID,RefineryTCDID){
        preview_cetak_surat(m_api + '/refinery/refinery_summary/RefineryID/'+RefineryID+'/Year//Period//RefineryTCDID/'+RefineryTCDID);
    },
    initComponent: function() {
        var thisObj = this;

        //Tracebility Declaration Document - Tracebility to Plantation ================= (Begin)
        var GridCompanyOwnedEstate = Ext.create('Koltiva.view.Refinery.GridCompanyOwnedEstate');
        var GridPlasma = Ext.create('Koltiva.view.Refinery.GridPlasma');
        var GridExternalEstate = Ext.create('Koltiva.view.Refinery.GridExternalEstate');
        var GridOtherSupplier = Ext.create('Koltiva.view.Refinery.GridOtherSupplier');

        //Call Panel Contact on Tab
        GridCompanyOwnedEstate.setViewVar({
            RefineryID : thisObj.RefineryID,
            opsiDisplay:thisObj.opsiDisplay,
            RefineryTCDID : thisObj.RefineryTCDID
        });
        
        GridPlasma.setViewVar({
            RefineryID : thisObj.RefineryID,
            opsiDisplay:thisObj.opsiDisplay,
            RefineryTCDID : thisObj.RefineryTCDID
        });
        
        GridExternalEstate.setViewVar({
            RefineryID : thisObj.RefineryID,
            opsiDisplay:thisObj.opsiDisplay,
            RefineryTCDID : thisObj.RefineryTCDID
        });
        
        GridOtherSupplier.setViewVar({
            RefineryID : thisObj.RefineryID,
            opsiDisplay:thisObj.opsiDisplay,
            RefineryTCDID : thisObj.RefineryTCDID
        });
        //Call Panel Contact on Tab
        //Tracebility Declaration Document - Tracebility to Plantation ================= (End) 


        //======================== LAYOUT UTAMA (Begin) =========================//
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
            	id: 'Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual-labelInfoTitle',
                html:'<h3 style="margin:0;padding:0px;">'+lang('Tracebility Declaration')+'</h3>'
            },{
                id: 'Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual-labelInfo',
                html:'',
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
                  '&nbsp;&nbsp;' + lang('Back to Tracebilitry Declaration List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual').destroy(); //destory current view
                        var GridMainFarcan = [];

                        if(Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual') == undefined){
                            GridMainFarcan = Ext.create('Koltiva.view.Refinery.GridTracebilityDeclarationManual');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual').destroy();
                            GridMainFarcan = Ext.create('Koltiva.view.Refinery.GridTracebilityDeclarationManual');
                        }
                    }
                }
            }
        },{
        	xtype:'panel',
        	border:false,
            style:'margin-top:8px;',
        	layout : {
			    type  : 'vbox',
			    align : 'center'
			},
			items:[{
				id:'Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual-NavBarStatus',
	        	html:''
			}]
        },{
        	xtype:'form',
        	title: lang('Tracebility Declaration'),
            frame: true,
            id:'Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual-FormTracebiltyDeclaration',
            margin:'10 0 20 0',
            items: [{
            	layout: 'column',
                border: false,
                padding:5,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 1,
                            layout:'form',
                            style:'padding:5px;',
                            items:[{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 1,
                                    layout:'form',
                                    style:'padding:5px;',
                                    items:[{
                                        layout: 'column',
                                        border: false,
                                        items:[{
                                            columnWidth: 0.2,
                                            layout:'form',
                                            style:'padding:5px;',
                                            items:[{
                                                id: 'RefineryTCDName',
                                                name: 'RefineryTCDName',
                                                xtype: 'textfield',
                                                anchor: '50%',
                                                fieldLabel: lang('Tracebility Declaration Name'),
                                                labelAlign:'top'
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
                                                    thisObj.PrintProfile(thisObj.RefineryID,thisObj.RefineryTCDID);
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    columnWidth: 1,
                                    layout:'form',
                                    style:'padding:5px;',
                                    items:[{
                                        html:'<div style="border-bottom:1px dashed gray;color:#34AA00;">&nbsp;</div>'
                                    }]
                                }]
                            }]
                        },{
                            columnWidth: 1,
                            layout:'form',
                            style:'padding:5px;',
                            items:[{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.5,
                                    layout:'form',
                                    style:'padding:5px;',
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'TotalFFB',
                                        name: 'TotalFFB',
                                        fieldLabel: lang('Total FFB Procured (ton)'),
                                        labelWidth: 200,
                                        readOnly:true
                                    }]
                                }]
                            }]
                        },{
                            columnWidth: 0.495,
                            layout:'form',
                            style:'padding:5px;',
                            items:[{
                                columnWidth: 1,
                                layout:'form',
                                style:'padding:5px;',
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
                                            id: 'FFBProcuredOwnedEstate',
                                            name: 'FFBProcuredOwnedEstate',
                                            fieldLabel: lang('FFB Procured (ton)'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{
                                            xtype: 'textfield',
                                            id: 'FFBProcuredProportionOwnedEstate',
                                            name: 'FFBProcuredProportionOwnedEstate',
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
                                            id: 'TotalTraceOwnedEstate',
                                            name: 'TotalTraceOwnedEstate',
                                            fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{
                                            xtype: 'textfield',
                                            id: 'TtpRefineryOwnedEstate',
                                            name: 'TtpRefineryOwnedEstate',
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
                                                id: 'GridCompanyOwnedEstate',
                                                items:[GridCompanyOwnedEstate]
                                            }
                                        ]
                                    }]
                                }]
                            },{
                                columnWidth: 1,
                                layout:'form',
                                style:'padding:5px;',
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
                                            id: 'FFBProcuredOtherSupplier',
                                            name: 'FFBProcuredOtherSupplier',
                                            fieldLabel: lang('FFB Procured (ton)'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{
                                            xtype: 'textfield',
                                            id: 'FFBProcuredProportionOtherSupplier',
                                            name: 'FFBProcuredProportionOtherSupplier',
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
                                            id: 'TotalTraceOtherSupplier',
                                            name: 'TotalTraceOtherSupplier',
                                            fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{
                                            xtype: 'textfield',
                                            id: 'TtpRefineryOtherSupplier',
                                            name: 'TtpRefineryOtherSupplier',
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
                                                id: 'GridOtherSupplier',
                                                items:[GridOtherSupplier]
                                            }
                                        ]
                                    }]
                                }]
                            }]
                        },{
                            columnWidth: 0.495,
                            layout:'form',
                            style:'padding:5px;',
                            items:[{
                                columnWidth: 1,
                                layout:'form',
                                style:'padding:5px;',
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
                                            id: 'FFBProcuredPlasma',
                                            name: 'FFBProcuredPlasma',
                                            fieldLabel: lang('FFB Procured (ton)'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{
                                            xtype: 'textfield',
                                            id: 'FFBProcuredProportionPlasma',
                                            name: 'FFBProcuredProportionPlasma',
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
                                            id: 'TotalTracePlasma',
                                            name: 'TotalTracePlasma',
                                            fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{
                                            xtype: 'textfield',
                                            id: 'TtpRefineryPlasma',
                                            name: 'TtpRefineryPlasma',
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
                                                id: 'GridPlasma',
                                                items:[GridPlasma]
                                            }
                                        ]
                                    }]
                                }]
                            },{
                                columnWidth: 1,
                                layout:'form',
                                style:'padding:5px;',
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
                                            id: 'FFBProcuredExternalEstate',
                                            name: 'FFBProcuredExternalEstate',
                                            fieldLabel: lang('FFB Procured (ton)'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{
                                            xtype: 'textfield',
                                            id: 'FFBProcuredProportionExternalEstate',
                                            name: 'FFBProcuredProportionExternalEstate',
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
                                            id: 'TotalTraceExternalEstate',
                                            name: 'TotalTraceExternalEstate',
                                            fieldLabel: lang('Jumlah Tracebility (Yes)'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{
                                            xtype: 'textfield',
                                            id: 'TtpRefineryExternalEstate',
                                            name: 'TtpRefineryExternalEstate',
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
                                                id: 'GridExternalEstate',
                                                items:[GridExternalEstate]
                                            }
                                        ]
                                    }]
                                }]
                            }]
                        }]
                    }]
                }]
            }]
        }];

        //======================== LAYOUT UTAMA (End)   =========================//

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            //update
            if(thisObj.opsiDisplay == 'view'){
                //load data form
                Ext.getCmp('Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual-FormTracebiltyDeclaration').getForm().load({
                    url: m_api + '/refinery/form_tc_declaration',
                    method: 'GET',
                    params: {
                        RefineryID: thisObj.RefineryID,
                        RefineryTCDID: thisObj.RefineryTCDID
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


            }
        }
    }
});