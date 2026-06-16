/*
* @Author: nikolius
* @Date:   2018-04-27 14:09:14
* @Last Modified by:   nikolius
* @Last Modified time: 2018-04-27 14:09:54
*/

/*
    Param2 yg diperlukan ketika load Store ini
    - DistrictID
*/

Ext.define('Koltiva.store.ComboGeneral.CmbFarmerGroupByDistrict', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbFarmerGroupByDistrict',
    storeId: 'Koltiva.store.ComboGeneral.CmbFarmerGroupByDistrict',
    fields: ['id','label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_farmer_group_by_district',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.DistrictID = this.storeVar.DistrictID;
        }
    }
});