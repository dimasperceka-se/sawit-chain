/*
* @Author: nikolius
* @Date:   2017-05-30 19:41:52
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-30 19:44:55
*/

Ext.define('Koltiva.store.Grower.CmbOtherCommodity', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbOtherCommodity',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Jagung')
    },{
        "id": "2",
        "label": lang('Sawit')
    },{
        "id": "3",
        "label": lang('Karet')
    },{
        "id": "4",
        "label": lang('Cengkeh')
    },{
        "id": "5",
        "label": lang('Padi')
    },{
        "id": "6",
        "label": lang('Kosong')
    },{
        "id": "7",
        "label": lang('Buah-buahan')
    },{
        "id": "8",
        "label": lang('Kayu-kayuan')
    },{
        "id": "9",
        "label": lang('Other')
    }]
});