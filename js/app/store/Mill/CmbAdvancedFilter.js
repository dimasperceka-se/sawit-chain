/*
* @Author: nikolius
* @Date:   2017-08-04 10:06:14
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-04 11:00:06
*/

Ext.define('Koltiva.store.Mill.CmbAdvancedFilter', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.CmbAdvancedFilter',
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