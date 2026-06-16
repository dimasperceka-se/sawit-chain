/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Oct 24 2018
 *  File : WinImsDistMap.js
 *******************************************/

/*
    Params
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinImsDistMap' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsDistMap',
    title: lang('IMS - Distribution Map'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '98%',
    height: '98%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            /*console.log(thisObj.getWidth());
            console.log(thisObj.getHeight());*/

            Ext.Ajax.request({
                url: m_api + '/ims_polygon/render_distribution_map',
                method: 'GET',
                params: {
                    IMSID:thisObj.viewVar.IMSID,
                    WinWidth: thisObj.getWidth(),
                    WinHeight: thisObj.getHeight()
                },
                success: function(response){
                    var MapReturn = response.responseText;
                    
                    thisObj.update(MapReturn, true);
                    thisObj.doLayout();
                },
                failure: function(response){
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: lang('Failed to render distribution map'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });

                    //tutup popup
                    thisObj.close();
                }
            });

            
        }
    }
});