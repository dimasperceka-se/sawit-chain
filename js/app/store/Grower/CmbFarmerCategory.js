/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Apr 25 2019
 *  File : CmbFarmerCategory.js
 *******************************************/

Ext.define('Koltiva.store.Grower.CmbFarmerCategory', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbFarmerCategory',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Smallholder (< 40ha)')
    },{
        "id": "2",
        "label": lang('Smallgrower (41 - 500 ha)')
    },{
        "id": "3",
        "label": lang('Lebih dari 500 ha')
    }]
});