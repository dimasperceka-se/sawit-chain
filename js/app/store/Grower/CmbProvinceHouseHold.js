/*
* @Author: nikolius
* @Date:   2017-05-18 18:54:52
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-24 17:42:03
*/

/*
* @Author: nikolius
* @Date:   2017-05-18 18:48:33
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-18 18:52:49
*/
Ext.define('Koltiva.store.Grower.CmbProvinceHouseHold', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbProvinceHouseHold',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Jawa Timur, Jawa Tengah, Lampung, Sumatera Selatan, Nusa Tenggara Barat, DI Yogyakarta, Kepulauan Riau, Gorontalo, or Kepulauan Bangka Belitung')
    },{
        "id": "2",
        "label": lang('Bali, Jawa Barat, or Bengkulu')
    },{
        "id": "3",
        "label": lang('DKI Jakarta, Riau, Kalimantan Barat, Kalimantan Timur, or Papua Barat ')
    },{
        "id": "4",
        "label": lang('Banten, Sulawesi Selatan, Aceh, Jambi, Kalimantan Selatan, Sulawesi Tengah, or Sulawesi Barat')
    },{
        "id": "5",
        "label": lang('Sumatera Utara, Sumatera Barat, Nusa Tenggara Timur, Papua, Kalimantan Tengah, Sulawesi Tenggara, Sulawesi Utara, Maluku, Maluku Utara, or Kalimantan Utara ')
    }]
});