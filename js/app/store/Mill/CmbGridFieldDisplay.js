/*
* @Author: nikolius
* @Date:   2017-08-03 16:40:31
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-03 16:44:50
*/

Ext.define('Koltiva.store.Mill.CmbGridFieldDisplay', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.CmbGridFieldDisplay',
    fields: ['id', 'label'],
    data:[{
        'id': 'Koltiva.view.Mill.GridMainMill-colid',
        'label': lang('ID')
    }, {
        'id': 'Koltiva.view.Mill.GridMainMill-colName',
        'label': lang('Name')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colAlias',
        'label': lang('Alias')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colAddress',
        'label': lang('Address')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colProvince',
        'label': lang('Province')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colDistrict',
        'label': lang('District')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colKecamatan',
        'label': lang('Kecamatan')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colDesa',
        'label': lang('Desa')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colStatusPerusahaan',
        'label': lang('Status Perusahaan')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colTahunTerbentuk',
        'label': lang('Tahun Terbentuk')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colPhone',
        'label': lang('Phone')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colTotalPermanentEmployee',
        'label': lang('Total Permanent Employee')
    },{
        'id': 'Koltiva.view.Mill.GridMainMill-colLastUpdated',
        'label': lang('Last Updated')
    }]
});