/*
* @Author: nikolius
* @Date:   2017-05-18 19:08:09
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-24 17:42:24
*/

/*
    Store ini memerlukan parameter
        1. ProvinceID
*/

Ext.define('Koltiva.store.PlotSurvey.CmbCollection', {
    extend: 'Ext.data.Store',
    storeId: 'store.PlotSurvey.CmbCollection',
    id: 'store.PlotSurvey.CmbCollection',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/combo_collection',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
        }
    }
});