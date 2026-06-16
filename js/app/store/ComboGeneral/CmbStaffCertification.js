/*
* @Author: nikolius
* @Date:   2018-07-10 14:17:20
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-10 14:18:04
*/

Ext.define('Koltiva.store.ComboGeneral.CmbStaffCertification', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbStaffCertification',
    storeId: 'Koltiva.store.ComboGeneral.CmbStaffCertification',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_staff_certification',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});