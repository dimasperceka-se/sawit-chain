/*
* @Author: nikolius
* @Date:   2017-05-17 13:46:23
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-29 16:01:01
*/
Ext.define('Koltiva.store.Grower.CmbGridFieldDisplay', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbGridFieldDisplay',
    fields: ['id', 'label'],
    data: [{
        "id": "view.Grower.GridMainGrower-colid",
        "label": lang('MemberID')
    }, {
        "id": "view.Grower.GridMainGrower-colFarmerName",
        "label": lang('Farmer Name')
    },{
        "id": "view.Grower.GridMainGrower-colBirthdate",
        "label": lang('Birthdate')
    },{
        "id": "view.Grower.GridMainGrower-colAge",
        "label": lang('Age')
    },{
        "id": "view.Grower.GridMainGrower-colHandphone",
        "label": lang('Handphone')
    },{
        "id": "view.Grower.GridMainGrower-colMaritalStatus",
        "label": lang('Marital Status')
    },{
        "id": "view.Grower.GridMainGrower-colProvince",
        "label": lang('Province')
    },{
        "id": "view.Grower.GridMainGrower-colDistrict",
        "label": lang('District')
    },{
        "id": "view.Grower.GridMainGrower-colDesa",
        "label": lang('Desa')
    },{
        "id": "view.Grower.GridMainGrower-colKecamatan",
        "label": lang('Kecamatan')
    },{
        "id": "view.Grower.GridMainGrower-colNrOfPlantation",
        "label": lang('Nr Of Plantation')
    },{
        "id": "view.Grower.GridMainGrower-colTotalHectare",
        "label": lang('Total Hectare')
    },{
        "id": "view.Grower.GridMainGrower-colTotalHectarePolygon",
        "label": lang('Total Hectare Polygon')
    },{
        "id": "view.Grower.GridMainGrower-colDateCollection",
        "label": lang('Date Collection')
    },{
        "id": "view.Grower.GridMainGrower-colDateCreated",
        "label": lang('Date Created')
    },{
        "id": "view.Grower.GridMainGrower-colEnumerator",
        "label": lang('Enumerator')
    }]
});