/*
* @Author: gitandi
* @Date:   2019-05-02 11:10:45
* @Last Modified by:   gitandi
* @Last Modified time: 2019-05-02 13:10:45
*/
Ext.define('Koltiva.store.Mill.CmbMillGroup', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.CmbMillGroup',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/mill/combo_mill_group',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});