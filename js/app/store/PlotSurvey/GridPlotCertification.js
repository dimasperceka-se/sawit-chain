/*
* @Author: nikolius
* @Date:   2017-05-31 11:38:59
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-09 18:24:45
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.PlotSurvey.GridPlotCertification', {
    extend: 'Ext.data.Store',
    id: 'store.PlotSurvey.GridPlotCertification',
    storeId: 'store.PlotSurvey.GridPlotCertification',
    fields: ['SurveyID','MemberID','DateCollection','CreatedBy','Enumerator'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/grid_plot_certification',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
        }
    }
});