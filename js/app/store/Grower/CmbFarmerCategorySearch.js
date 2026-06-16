/**
 * 09-01-2020
 */

Ext.define('Koltiva.store.Grower.CmbFarmerCategorySearch', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbFarmerCategorySearch',
    fields: ['id', 'label'],
    data: [{
        "id": "Registered",
        "label": lang('Registered')
    },{
        "id": "Mapped",
        "label": lang('Mapped')
    },{
        "id": "Unmapped",
        "label": lang('Unmapped')
    }]
});