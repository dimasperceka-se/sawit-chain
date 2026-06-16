/*
* @Author: nikolius
* @Date:   2017-08-21 10:19:23
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-15 17:18:34
*/
/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar (PartnerID)
*/

Ext.define('Koltiva.view.Mill.FormTracebilityDeclaration' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Mill.FormTracebilityDeclaration',
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
    initComponent: function() {
        var thisObj = this;

        //Tracebility Declaration Document - Tracebility to Plantation ================= (Begin)
        var panelTabTracebilityDeclarationDocument= Ext.create('Koltiva.view.Mill.PanelTabTracebilityDeclarationDocument',{
            opsiDisplay: thisObj.opsiDisplay,
            viewVar: {
                PartnerID: thisObj.viewVar.PartnerID
            }
        });

        thisObj.panelTabTracebilityDeclarationDocument = panelTabTracebilityDeclarationDocument;
        //Tracebility Declaration Document - Tracebility to Plantation ================= (End) 


        //======================== LAYOUT UTAMA (Begin) =========================//
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
            	id: 'Koltiva.view.Mill.FormTracebilityDeclaration-labelInfoTitle',
                html:'<h3 style="margin:0;padding:0px;">'+lang('Tracebility Declaration')+'</h3>'
            },{
                id: 'Koltiva.view.Mill.FormTracebilityDeclaration-labelInfo',
                html:'',
            }]
        },{
        	xtype:'panel',
        	border:false,
            style:'margin-top:8px;',
        	layout : {
			    type  : 'vbox',
			    align : 'center'
			},
			items:[{
				id:'Koltiva.view.Mill.FormTracebilityDeclaration-NavBarStatus',
	        	html:''
			}]
        },{
        	xtype:'panel',
        	title: lang('Tracebility Declaration'),
            frame: true,
            margin:'10 0 20 0',
            items: [{
            	layout: 'column',
                border: false,
                padding:5,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'tabpanel',
                        flex: 1,
                        activeTab: 0,
                        plain: true,
                        cls:'tabSce',
                        id: 'Koltiva.view.Mill.FormTracebilityDeclaration-tab',
                        items:[panelTabTracebilityDeclarationDocument]
                    }]
                }]
            }]
        }];

        //======================== LAYOUT UTAMA (End)   =========================//

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
        }
    }
});