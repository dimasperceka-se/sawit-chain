/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Apr 25 2019
 *  File : CmbFamilyReasonWork.js
 *******************************************/

Ext.define('Koltiva.store.Grower.CmbFamilyReasonWork', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbFamilyReasonWork',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Not going to school')
    },{
        "id": "2",
        "label": lang('Lack of labor')
    },{
        "id": "3",
        "label": lang('Helping parents')
    },{
        "id": "4",
        "label": lang('I dont have to pay them')
    },{
        "id": "5",
        "label": lang('Other')
    }]
});