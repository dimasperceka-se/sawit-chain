/*
* @Author: nikolius
* @Date:   2017-08-04 14:05:48
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-19 14:42:43
*/

Ext.define('Koltiva.store.PlotSurvey.CmbWagePeriod', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbWagePeriod',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Daily')
    },{
        "id": "2",
        "label": lang('Weekly')
    },{
        "id": "3",
        "label": lang('Monthly')
    // },{
    //     "id": "4",
    //     "label": lang('per year')
    },{
        "id": "5",
        "label": lang('One Time Payment')
    }]
});