/*
 * @Author: nikolius
 * @Date:   2017-05-16 11:48:04
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-05-18 16:52:57
 */

//define view2 yg dipakai
var objGridMainGrower = Ext.create('Koltiva.view.Grower.GridMainGrower');
var objFormMainGrower = Ext.create('Koltiva.view.Grower.FormMainGrower');

//supaya di dua view ini nantinya bisa saling akses
objFormMainGrower.objGrid = objGridMainGrower;
objGridMainGrower.objForm = objFormMainGrower;

Ext.define('Koltiva.view.Grower.MainCardLayout', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva-view-Grower-MainCardLayout',
    renderTo: 'ext-content',
    layout: {
        type: 'card',
        animation: {
            type: 'slide',
            direction: 'left',
            duration: 1000,
        }
    },
    items: [
        objFormMainGrower
    ,
        objGridMainGrower
    ]
});