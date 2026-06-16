/*
* @Author: nikolius
* @Date:   2017-05-23 16:48:19
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-23 16:49:16
*/
Ext.define('Koltiva.store.Grower.CmbStoppedReason', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbStoppedReason',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('All Oil Palm Farms Sold')
    },{
        "id": "2",
        "label": lang('All Oil Palm Farms Converted to Other Crops')
    },{
        "id": "3",
        "label": lang('All Oil Palm Cut Down Without The Intention of Replanting')
    },{
        "id": "4",
        "label": lang('All Farms Destroyed by a Natural Disaster')
    },{
        "id": "5",
        "label": lang('All Cocoa Old and Not Productive at All')
    },{
        "id": "6",
        "label": lang('Others')
    }]
});