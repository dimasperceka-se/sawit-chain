/*
* @Author: nikolius
* @Date:   2017-07-19 10:41:44
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-06 16:15:53
*/

Ext.define('Koltiva.store.SME.CmbGridFieldDisplay', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.CmbGridFieldDisplay',
    fields: ['id', 'label'],
    data: [{
        "id": "Koltiva.view.SME.GridMainTrader-colid",
        "label": lang('SME ID')
    }, {
        "id": "Koltiva.view.SME.GridMainTrader-colFarmerName",
        "label": lang('Name')
    }/*,{
        "id": "Koltiva.view.SME.GridMainTrader-colMemberRole",
        "label": lang('Role')
    },{
        "id": "Koltiva.view.SME.GridMainTrader-colBirthdate",
        "label": lang('Birthdate')
    },{
        "id": "Koltiva.view.SME.GridMainTrader-colAge",
        "label": lang('Age')
    },{
        "id": "Koltiva.view.SME.GridMainTrader-colHandphone",
        "label": lang('Handphone')
    }*/,{
        "id": "Koltiva.view.SME.GridMainTrader-colDateCollection",
        "label": lang('Date Collection')
    },{
        "id": "Koltiva.view.SME.GridMainTrader-colProvince",
        "label": lang('Province')
    },{
        "id": "Koltiva.view.SME.GridMainTrader-colDistrict",
        "label": lang('District')
    },{
        "id": "Koltiva.view.SME.GridMainTrader-colDesa",
        "label": lang('Desa')
    },{
        "id": "Koltiva.view.SME.GridMainTrader-colKecamatan",
        "label": lang('Kecamatan')
    },{
        "id": "Koltiva.view.SME.GridMainTrader-colEnumerator",
        "label": lang('Enumerator')
    },{
        "id": "Koltiva.view.SME.GridMainTrader-colLastUpdated",
        "label": lang('Last Updated')
    }]
});