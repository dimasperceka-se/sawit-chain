/*
* @Author: nikolius
* @Date:   2017-09-06 15:31:55
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-06 15:45:25
*/
Ext.define('Koltiva.store.Trader.CmbRoleTrader', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Trader.CmbRoleTrader',
    fields: ['id', 'label'],
    data: [{
        "id": "5",
        "label": lang('Trader')
    }, {
        "id": "6",
        "label": lang('Village Collector')
    },{
        "id": "7",
        "label": lang('Dealer')
    },{
        "id": "8",
        "label": lang('Ramp')
    },{
        "id": "9",
        "label": lang('Delivery Order Holder')
    }]
});