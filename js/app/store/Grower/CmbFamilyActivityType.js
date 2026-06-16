/*
* @Author: nikolius
* @Date:   2017-05-25 17:19:11
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-25 17:20:29
*/

Ext.define('Koltiva.store.Grower.CmbFamilyActivityType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbFamilyActivityType',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Land Treatment')
    },{
        "id": "2",
        "label": lang('New Seeds')
    },{
        "id": "3",
        "label": lang('Plants Handling')
    },{
        "id": "4",
        "label": lang('Harvesting')
    },{
        "id": "5",
        "label": lang('Post Harvest')
    }]
});