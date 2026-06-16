/*
* @Author: Gitandi Nadzari
* @Date:   2019-05-29 15:40:00
* @Last Modified by:  
* @Last Modified time: 
*/

Ext.define('Koltiva.store.DataAdm.MappingMetadata.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.MappingMetadata.MainGrid',
    storeId: 'Koltiva.store.DataAdm.MappingMetadata.MainGrid',
    fields: ['program_uid','name','sec_name','program_stage_uid','de_uid','de_name','mw_mapping_id','table_reff','field_reff','custom_function','executeSql','priority','forPull'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_metadata,
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ProgStageId = Ext.getCmp('filter-ProgStage').getValue();
        }
    }
});