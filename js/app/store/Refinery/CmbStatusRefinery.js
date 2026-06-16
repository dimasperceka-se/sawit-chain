Ext.define('Koltiva.store.Refinery.CmbStatusRefinery', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.CmbStatusRefinery',
    fields: ['id', 'label'],
    data: [{
        "id": "UD",
        "label": lang('UD')
    },{
        "id": "Firma",
        "label": lang('Firma')
    },{
        "id": "CV",
        "label": lang('CV')
    },{
        "id": "Koperasi",
        "label": lang('Koperasi')
    },{
        "id": "PT",
        "label": lang('PT')
    },{
        "id": "Tidak Berbadan Hukum",
        "label": lang('No Legal Entity')
    }]
});