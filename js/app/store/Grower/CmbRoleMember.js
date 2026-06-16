/*
* @Author: nikolius
* @Date:   2017-05-23 14:10:25
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-23 14:23:43
*/
Ext.define('Koltiva.store.Grower.CmbRoleMember', {
    extend: 'Ext.data.Store',
    id: 'store.Grower.CmbRoleMember',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_role_member',
        reader: {
            type: 'json'
        }
    }
});