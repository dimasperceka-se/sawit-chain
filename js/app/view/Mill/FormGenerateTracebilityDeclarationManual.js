/*
* @Author: nikolius
* @Date:   2017-08-21 10:19:23
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-15 17:18:34
*/
/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar (MillID)
*/

Ext.define('Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual',
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
    PrintProfile: function(MillID,MillTCDID){
        preview_cetak_surat(m_api + '/mill/mill_summary/MillID/'+MillID+'/Year//Period//MillTCDID/'+MillTCDID);
    },
    initComponent: function() {
        var thisObj = this;

        //Tracebility Declaration Document - Tracebility to Plantation ================= (Begin)
        var GridCompanyOwnedEstate = Ext.create('Koltiva.view.Mill.GridCompanyOwnedEstate');
        var GridPlasma = Ext.create('Koltiva.view.Mill.GridPlasma');
        var GridExternalEstate = Ext.create('Koltiva.view.Mill.GridExternalEstate');
        var GridOtherSupplier = Ext.create('Koltiva.view.Mill.GridOtherSupplier');

        //Call Panel Contact on Tab
        GridCompanyOwnedEstate.setViewVar({
            MillID : thisObj.MillID,
            opsiDisplay:thisObj.opsiDisplay,
            MillTCDID : thisObj.MillTCDID
        });
        
        GridPlasma.setViewVar({
            MillID : thisObj.MillID,
            opsiDisplay:thisObj.opsiDisplay,
            MillTCDID : thisObj.MillTCDID
        });
        
        GridExternalEstate.setViewVar({
            MillID : thisObj.MillID,
            opsiDisplay:thisObj.opsiDisplay,
            MillTCDID : thisObj.MillTCDID
        });
        
        GridOtherSupplier.setViewVar({
            MillID : thisObj.MillID,
            opsiDisplay:thisObj.opsiDisplay,
            MillTCDID : thisObj.MillTCDID
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
            	id: 'Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual-labelInfoTitle',
                html:'<h3 style="margin:0;padding:0px;">'+lang('Tracebility Declaration')+'</h3>'
            },{
                id: 'Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual-labelInfo',
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
                        Ext.getCmp('Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual').destroy(); //destory current view
                        var GridMainFarcan = [];

                        if(Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual') == undefined){
                            GridMainFarcan = Ext.create('Koltiva.view.Mill.GridTracebilityDeclarationManual');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual').destroy();
                            GridMainFarcan = Ext.create('Koltiva.view.Mill.GridTracebilityDeclarationManual');
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
				id:'Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual-NavBarStatus',
	        	html:''
			}]
        },{
        	xtype:'form',
        	title: lang('Tracebility Declaration'),
            frame: true,
            id:'Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual-FormTracebiltyDeclaration',
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
                                                id: 'MillTCDName',
                                                name: 'MillTCDName',
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
                                                    thisObj.PrintProfile(thisObj.MillID,thisObj.MillTCDID);
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
                                            id: 'TtpMillOwnedEstate',
                                            name: 'TtpMillOwnedEstate',
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
                                            id: 'TtpMillOtherSupplier',
                                            name: 'TtpMillOtherSupplier',
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
                                            id: 'TtpMillPlasma',
                                            name: 'TtpMillPlasma',
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
                                            id: 'TtpMillExternalEstate',
                                            name: 'TtpMillExternalEstate',
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
                Ext.getCmp('Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual-FormTracebiltyDeclaration').getForm().load({
                    url: m_api + '/mill/form_tc_declaration',
                    method: 'GET',
                    params: {
                        MillID: thisObj.MillID,
                        MillTCDID: thisObj.MillTCDID
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