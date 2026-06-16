/*
* @Author: muhammad hidayturrohman
* @Date:   2020-11-10
*/

Ext.define('Koltiva.store.PlotSurvey.CmbBrandHerbicide', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.PlotSurvey.CmbBrandHerbicide',
    id: 'Koltiva.store.PlotSurvey.CmbBrandHerbicide',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/cmb_list_herbicide',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});