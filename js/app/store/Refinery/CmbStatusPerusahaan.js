Ext.define('Koltiva.store.Refinery.CmbStatusPerusahaan', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.CmbStatusPerusahaan',
    fields: ['id', 'label'],
    data: [{
        "id": 'UD',
        "label": 'UD'
    },{
        "id": "CV",
        "label": "CV"
    },{
        "id": "Koperasi",
        "label": "Koperasi"
    },{
        "id": "PT",
        "label": "PT"
    },{
        "id": "Tidak Berbadan Hukum",
        "label": lang('Tidak Berbadan Hukum')
    }]
});