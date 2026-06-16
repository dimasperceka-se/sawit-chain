/*
* @Author: nikolius
* @Date:   2017-05-31 14:42:50
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-26 16:50:45
*/

Ext.define('Koltiva.store.PlotSurvey.CmbOwnershipDocument', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbOwnershipDocument',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('No Document')
    },{
        "id": "2",
        "label": lang('SKT')
    },{
        "id": "3",
        "label": lang('SHM / Certificate')
    },{
        "id": "4",
        "label": lang('HGU')
    },{
        "id": "5",
        "label": lang('SKGR')
    },{
        "id": "6",
        "label": lang('Other')
    }]
});