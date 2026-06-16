/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Sep 17 2018
 *  File : WinFormDocumentView.js
 *******************************************/

Ext.define('Koltiva.view.CMS.WinFormDocumentView' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.CMS.WinFormDocumentView',
    title: lang('View Document'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: 724,
    height: 560,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.items = [{
            id:'Koltiva.view.CMS.WinFormDocumentView-Cont',
            overflowY: 'hidden',
            html:''
        }];        

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            
            //Get Load Document            
			Ext.Ajax.request({
				waitMsg: lang('Please Wait'),
				url: m_api + '/cms/document_view',
				method : 'GET',
				params: {
					DocID:  thisObj.viewVar.DocID
				},
				success: function(response, opts){
					var r = response.responseText;
					//console.log(r);

					Ext.getCmp('Koltiva.view.CMS.WinFormDocumentView-Cont').update(r);
					Ext.getCmp('Koltiva.view.CMS.WinFormDocumentView-Cont').doComponentLayout();
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