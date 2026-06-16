 
 
Ext.define('Koltiva.store.ComboGeneral.cmbStaffGeneral', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.cmbStaffGeneral',
    storeId: 'Koltiva.store.ComboGeneral.cmbStaffGeneral',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_staff',
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