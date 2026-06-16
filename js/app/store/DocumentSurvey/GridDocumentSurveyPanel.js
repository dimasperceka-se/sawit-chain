/*
* @Author: nikolius
* @Date:   2017-08-10 11:32:22
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-10 13:25:50
*/

Ext.define('Koltiva.store.DocumentSurvey.GridDocumentSurveyPanel', {
    extend: 'Ext.data.Store',
    id: 'store.DocumentSurvey.GridDocumentSurveyPanel',
    storeId: 'store.DocumentSurvey.GridDocumentSurveyPanel',
    fields: ['DocName','DocNameID','Status','StatusId','FileAvail'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/document_survey/grid_document_survey_panel',
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