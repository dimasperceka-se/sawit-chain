/*
* @Author: nikolius
* @Date:   2017-04-06 18:02:11
* @Last Modified by:   nikolius
* @Last Modified time: 2017-04-12 11:28:10
*/
Ext.define('Koltiva.controller.DataAdm.OffData', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderGrid();
    },
    renderGrid: function() {
        var MetadataGrid = Ext.create('Koltiva.view.DataAdm.OffData.MetadataGrid');
        var DistrictGrid = Ext.create('Koltiva.view.DataAdm.OffData.DistrictGrid');
        var SubdistrictGrid = Ext.create('Koltiva.view.DataAdm.OffData.SubdistrictGrid');
        var BackendMetadata = Ext.create('Koltiva.view.DataAdm.OffData.BackendMetadataGrid');
        var fgMetadata = Ext.create('Koltiva.view.DataAdm.OffData.FgMetadataGrid');
    }
});