/*
* @Author: muhammad hidayturrohman
* @Date:   2020-11-10
*/

Ext.define('Koltiva.store.PlotSurvey.CmbBrandInsecticide', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.PlotSurvey.CmbBrandInsecticide',
    id: 'Koltiva.store.PlotSurvey.CmbBrandInsecticide',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/cmb_list_insecticide',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});