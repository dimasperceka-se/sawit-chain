/*
* @Author: nikolius
* @Date:   2017-08-21 11:10:45
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-03 11:13:48
*/
Ext.define('Koltiva.store.Mill.CmbStatusMill', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.CmbStatusMill',
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