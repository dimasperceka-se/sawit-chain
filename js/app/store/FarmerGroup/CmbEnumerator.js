/*
* @Author: nikolius
* @Date:   2017-11-09 16:09:28
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-09 18:33:50
*/

/*
    Param2 yg diperlukan ketika load Store ini
    - FarmerGroupID
*/

Ext.define('Koltiva.store.FarmerGroup.CmbEnumerator', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerGroup.CmbEnumerator',
    storeId: 'Koltiva.store.FarmerGroup.CmbEnumerator',
    fields: ['label','id'],
    autoLoad: true,
    remoteSort: true,
    pageSize: 30,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/farmer_group/enumerator_input_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        
    }
});