/*
* @Author: Gitandi Nadzari
* @Date:   2019-05-29 15:40:00
* @Last Modified by:  
* @Last Modified time: 
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.DataAdm.MappingMetadata', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        var mainLayoutMappingMetadata = [];

        if(Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainGrid') == undefined){
            mainLayoutMappingMetadata = Ext.create('Koltiva.view.DataAdm.MappingMetadata.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainGrid').destroy();
            mainLayoutMappingMetadata = Ext.create('Koltiva.view.DataAdm.MappingMetadata.MainGrid');
        }
    }
});

