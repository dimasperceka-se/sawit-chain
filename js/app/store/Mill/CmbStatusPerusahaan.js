/*
* @Author: nikolius
* @Date:   2017-08-04 10:59:47
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-04 11:06:52
*/

Ext.define('Koltiva.store.Mill.CmbStatusPerusahaan', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.CmbStatusPerusahaan',
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