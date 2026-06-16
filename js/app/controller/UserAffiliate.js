Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.MultiSelect',
    'Ext.button.Button',
    'Ext.ux.form.ItemSelector',
]);

Ext.define('Koltiva.controller.UserAffiliate', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        Ext.create('Koltiva.view.UserAffiliate.MainGrid');
    }
});