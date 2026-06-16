/* modules*/

//override time out ajax exts js yg cuman 30 detikan jadi 10 menit
Ext.Ajax.timeout = 600000;
Ext.override(Ext.form.Basic, {
    timeout: Ext.Ajax.timeout / 1000
});
Ext.override(Ext.data.proxy.Server, {
    timeout: Ext.Ajax.timeout
});
Ext.override(Ext.data.Connection, {
    timeout: Ext.Ajax.timeout
});

Ext.onReady(function() {
    var controller = Ext.create('Koltiva.controller.menu.menuController');
    controller.init();
});