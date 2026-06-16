/*
 * @Author: fikri.fauzul
 * @Date:   2019-09-04 16:20:51
 */

/* global Ext */

Ext.onReady(function () {
    var controller = Ext.create('Koltiva.controller.ImportGardens');
    controller.init();
});