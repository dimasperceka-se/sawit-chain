/*
* @Author: fikri
* @Date:   2019-10-25 09:25:45
*/

Ext.define('Koltiva.store.PlotSurvey.CmbCertification', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbCertification',
    storeId: 'Koltiva.store.PlotSurvey.CmbCertification',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_certPrograms',
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