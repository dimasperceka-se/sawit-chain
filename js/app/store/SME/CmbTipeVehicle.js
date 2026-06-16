/*
* @Author: nikolius
* @Date:   2018-03-27 14:28:25
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-27 14:42:28
*/

Ext.define('Koltiva.store.SME.CmbTipeVehicle', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.CmbTipeVehicle',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Truck')
    },{
        "id": "2",
        "label": lang('Mini Truck')
    },{
        "id": "3",
        "label": lang('Pick Up')
    },{
        "id": "4",
        "label": lang('Truck Colt Diesel')
    },{
    	"id": "5",
        "label": lang('Dump Truck')
    },{
    	"id": "6",
        "label": lang('Motorcycle')
    },{
		"id": "7",
        "label": lang('Other')
    }]
});