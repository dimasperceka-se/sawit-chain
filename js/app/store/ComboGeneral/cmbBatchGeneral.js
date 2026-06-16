/*
* @Author: nikolius
* @Date:   2017-11-08 14:17:10
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-08 14:18:45
*/

/*
    Param2 yg diperlukan ketika load Store ini
    - DistrictID
*/

Ext.define('Koltiva.store.ComboGeneral.cmbBatchGeneral', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.cmbBatchGeneral',
    storeId: 'Koltiva.store.ComboGeneral.cmbBatchGeneral',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmbBatchGeneral',
        reader: {
            type: 'json',
            root: 'data'
        }
    } ,
    listeners: {
        beforeload: function(store, operation, options){
             
        }
    }
});