/*
* @Author: nikolius
* @Date:   2017-05-31 14:53:57
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-31 14:54:34
*/

Ext.define('Koltiva.store.PlotSurvey.CmbProvideSocAnimalProtect', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbProvideSocAnimalProtect',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('External Advisor')
    },{
        "id": "2",
        "label": lang('NGOs')
    },{
        "id": "3",
        "label": lang('Farmer Coordinator')
    },{
        "id": "4",
        "label": lang('Browsing The Internet')
    },{
        "id": "5",
        "label": lang('Neighboring Farmer')
    },{
        "id": "6",
        "label": lang('Others')
    }]
});