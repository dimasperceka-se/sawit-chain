/*
* @Author: muhammad hidayturrohman
* @Date:   2020-11-10
*/

Ext.define('Koltiva.store.PlotSurvey.CmbBrandFungicide', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.PlotSurvey.CmbBrandFungicide',
    id: 'Koltiva.store.PlotSurvey.CmbBrandFungicide',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/cmb_list_fungicide',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});