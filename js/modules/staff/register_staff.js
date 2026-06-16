/*
* @Author: nikolius
* @Date:   2017-10-13 13:07:51
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 13:08:32
*/

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
    var controller = Ext.create('Koltiva.controller.Staff.RegisterStaff');
    controller.init();
});