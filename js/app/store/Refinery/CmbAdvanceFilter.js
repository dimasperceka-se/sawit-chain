/*
* @Author: muhammad hidayaturrohman
* @Date:   2020-11-05
* @Last Modified by:   muhammad hidayaturrohman
* @Last Modified time: 2020-11-05
*/

Ext.define('Koltiva.store.Refinery.CmbAdvancedFilter', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.CmbAdvancedFilter',
    fields: ['id', 'label'],
    data: [{
        "id": "StatusPerusahaan",
        "label": lang('Status Perusahaan')
    },{
        "id": "TotalPermanentEmployee",
        "label": lang('Total Permanent Employee')
    },{
        "id": "TahunTerbentuk",
        "label": lang('Tahun Terbentuk')
    },{
        "id": "Phone",
        "label": lang('Phone')
    },{
        "id": "HavePhoto",
        "label": lang('Have Photo')
    }]
});