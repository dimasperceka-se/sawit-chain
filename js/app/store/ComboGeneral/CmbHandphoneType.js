/*
* @Author: nikolius
* @Date:   2018-01-10 17:15:39
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-10 17:17:47
*/
Ext.define('Koltiva.store.ComboGeneral.CmbHandphoneType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbHandphoneType',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Smartphone (Android/iPhone)')
    },{
        "id": "2",
        "label": lang('Feature Phone (Basic Mobile Phone)')
    },{
    	"id": "3",
        "label": lang('No Handphone')
    }]
});