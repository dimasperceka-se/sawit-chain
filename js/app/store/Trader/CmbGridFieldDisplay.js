/*
* @Author: nikolius
* @Date:   2017-07-19 10:41:44
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-06 16:15:53
*/

Ext.define('Koltiva.store.Trader.CmbGridFieldDisplay', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Trader.CmbGridFieldDisplay',
    fields: ['id', 'label'],
    data: [{
        "id": "Koltiva.view.Trader.GridMainTrader-colid",
        "label": lang('SME ID')
    }, {
        "id": "Koltiva.view.Trader.GridMainTrader-colFarmerName",
        "label": lang('Name')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colMemberRole",
        "label": lang('Role')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colBirthdate",
        "label": lang('Birthdate')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colAge",
        "label": lang('Age')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colHandphone",
        "label": lang('Handphone')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colDateCollection",
        "label": lang('Date Collection')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colProvince",
        "label": lang('Province')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colDistrict",
        "label": lang('District')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colDesa",
        "label": lang('Desa')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colKecamatan",
        "label": lang('Kecamatan')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colEnumerator",
        "label": lang('Enumerator')
    },{
        "id": "Koltiva.view.Trader.GridMainTrader-colLastUpdated",
        "label": lang('Last Updated')
    }]
});