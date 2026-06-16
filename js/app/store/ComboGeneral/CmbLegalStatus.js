/*
* @Author: nikolius
* @Date:   2017-12-29 15:11:17
* @Last Modified by:   nikolius
* @Last Modified time: 2017-12-29 15:13:41
*/

Ext.define('Koltiva.store.ComboGeneral.CmbLegalStatus', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbLegalStatus',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Sole Proprietorship')
    },{
        "id": "2",
        "label": lang('Partnership')
    },{
    	"id": "3",
        "label": lang('Limited Partnership')
    },{
    	"id": "4",
        "label": lang('Limited Liability Company')
    },{
    	"id": "5",
        "label": lang('Corporation')
    },{
    	"id": "6",
        "label": lang('Cooperative')
    },{
    	"id": "7",
        "label": lang('Foundation')
    },{
    	"id": "8",
        "label": lang('Association')
    },{
    	"id": "9",
        "label": lang('State Owned')
    }]
});