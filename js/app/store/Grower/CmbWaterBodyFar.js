/*
* @Author: nikolius
* @Date:   2017-05-23 16:48:19
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-23 16:49:16
*/
Ext.define('Koltiva.store.Grower.CmbWaterBodyFar', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbWaterBodyFar',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Less Than 5m')
    },{
        "id": "2",
        "label": lang('Less Than 10m')
    },{
        "id": "3",
        "label": lang('Less Than 20m')
    },{
        "id": "4",
        "label": lang('Less Than 40m')
    },{
        "id": "5",
        "label": lang('Less Than 50m')
    },{
        "id": "6",
        "label": lang('Less Than 100m')
    },{
        "id": "7",
        "label": lang('More Than 100m')
    }]
});