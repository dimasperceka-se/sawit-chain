Ext.define('Koltiva.store.Refinery.CmbGridFieldDisplay', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.CmbGridFieldDisplay',
    fields: ['id', 'label'],
    data:[{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colid',
        'label': lang('ID')
    }, {
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colName',
        'label': lang('Name')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colAlias',
        'label': lang('Alias')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colAddress',
        'label': lang('Address')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colProvince',
        'label': lang('Province')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colDistrict',
        'label': lang('District')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colKecamatan',
        'label': lang('Kecamatan')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colDesa',
        'label': lang('Desa')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colStatusPerusahaan',
        'label': lang('Status Perusahaan')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colTahunTerbentuk',
        'label': lang('Tahun Terbentuk')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colPhone',
        'label': lang('Phone')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colTotalPermanentEmployee',
        'label': lang('Total Permanent Employee')
    },{
        'id': 'Koltiva.view.Refinery.GridMainRefinery-colLastUpdated',
        'label': lang('Last Updated')
    }]
});