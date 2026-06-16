/*
* @Author: nikolius
* @Date:   2017-08-18 14:55:58
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-18 14:56:57
*/
Ext.define('Koltiva.store.Grower.CmbOtherLandCommodity', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbOtherLandCommodity',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Corn')
    },{
        "id": "2",
        "label": lang('Rubber')
    },{
        "id": "3",
        "label": lang('Clove')
    },{
        "id": "4",
        "label": lang('Rice')
    },{
        "id": "5",
        "label": lang('Fruits')
    },{
        "id": "6",
        "label": lang('Woods')
    },{
        "id": "7",
        "label": lang('Other')
    },{
        "id": "8",
        "label": lang('Cocoa')
    }]
});