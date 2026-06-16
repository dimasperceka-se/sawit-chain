/*
* @Author: nikolius
* @Date:   2017-10-02 12:20:23
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-02 12:21:46
*/
Ext.define('Koltiva.store.Trader.CmbVehicleCapacity', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Trader.CmbVehicleCapacity',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Less than 1,000 kg')
    },{
        "id": "2",
        "label": lang('1,000 - 3,500 kg')
    },{
        "id": "3",
        "label": lang('3,500 - 8,500 kg')
    },{
        "id": "4",
        "label": lang('Above 8,000 kg')
    }]
});