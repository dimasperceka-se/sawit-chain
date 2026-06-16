/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Apr 25 2019
 *  File : CmbFarmerGroupWags.js
 *******************************************/
Ext.define('Koltiva.store.Grower.CmbFarmerGroupWags', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbFarmerGroupWags',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('TJC - Direct Managed (MG 34)')
    },{
        "id": "2",
        "label": lang('TJC - Partially Managed (MG 35)')
    },{
        "id": "3",
        "label": lang('TJC -Orang Asli (MG 36)')
    },{
        "id": "4",
        "label": lang('SL Sawit Langkap (MG 51)')
    }]
});