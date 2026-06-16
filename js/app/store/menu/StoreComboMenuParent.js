Ext.define('Koltiva.store.menu.StoreComboMenuParent', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.menu.StoreComboMenuParent',
    storeId: 'Koltiva.store.menu.StoreComboMenuParent',
    fields: ['id', 'label'],
    data: [{
        label: lang('Show All Menu'),
        id: 'All'
    },{
    	label: lang('Parent Menu'),
        id: 'Parent'
    },{
    	label: lang('Child Menu'),
        id: 'Child'
    }]
});