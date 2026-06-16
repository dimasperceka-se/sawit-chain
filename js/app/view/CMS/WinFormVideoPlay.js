/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Sep 13 2018
 *  File : WinFormVideoPlay.js
 *******************************************/

Ext.define('Koltiva.view.CMS.WinFormVideoPlay' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.CMS.WinFormVideoPlay',
    title: lang('Watch Video'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: 568,
    height: 520,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.items = [{            
            id:'Koltiva.view.CMS.WinFormVideoPlay-Cont',
            html:''
        }];        

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //Get Load Video            
			Ext.Ajax.request({
				waitMsg: lang('Please Wait'),
				url: m_api + '/cms/video_watch',
				method : 'GET',
				params: {
					VidID:  thisObj.viewVar.VidID
				},
				success: function(response, opts){
					var r = response.responseText;
					//console.log(r);

					Ext.getCmp('Koltiva.view.CMS.WinFormVideoPlay-Cont').update(r);
					Ext.getCmp('Koltiva.view.CMS.WinFormVideoPlay-Cont').doComponentLayout();
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