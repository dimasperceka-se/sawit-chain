Ext.define('Koltiva.store.NewSocialization.ComboHariEvent', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.NewSocialization.ComboProvince',
    id: 'Koltiva.store.NewSocialization.ComboProvince',
    fields: [ 'hari'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/new_socialization/comboharievent',
        reader: {
            type: 'json'
        }
    }
});