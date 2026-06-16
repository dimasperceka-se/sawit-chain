/*
* @Author: nikolius
* @Date:   2017-11-08 14:17:10
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-10 14:17:41
*/

Ext.define('Koltiva.store.ComboGeneral.CmbCertProgramsGeneral', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbCertProgramsGeneral',
    storeId: 'Koltiva.store.ComboGeneral.CmbCertProgramsGeneral',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_certPrograms',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});