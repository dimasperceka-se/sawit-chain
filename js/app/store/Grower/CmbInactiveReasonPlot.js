/*
* @Author: nikolius
* @Date:   2017-05-30 19:38:21
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-30 19:40:21
*/

Ext.define('Koltiva.store.Grower.CmbInactiveReasonPlot', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbInactiveReasonPlot',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Moved/Left Area')
    },{
        "id": "2",
        "label": lang('Switched to other crops')
    },{
        "id": "3",
        "label": lang('Land Sold')
    },{
        "id": "4",
        "label": lang('Inherited to family member')
    },{
        "id": "5",
        "label": lang('Force Majeure')
    }]
});