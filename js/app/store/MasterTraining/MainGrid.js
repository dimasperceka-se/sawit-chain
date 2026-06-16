Ext.define('Koltiva.store.MasterTraining.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MasterTraining.MainGrid',
    storeId: 'Koltiva.store.MasterTraining.MainGrid',
    fields: ['id', 'training', 'batch', 'tot', 'participant', 'start', 'end', 'days', 'partner_name'],
    autoLoad: true,
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: m_crud + 's',
        extraParams: {
            prov: m_param,
            dist: m_DistrictID,
            subdist: m_SubDistrictID
        },
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function (store, operation) {
            let cof_gridmastertraining_params = JSON.parse(localStorage.getItem('cof_gridmastertraining_params'));
            if(cof_gridmastertraining_params != null){
                store.proxy.extraParams.ArrFilter = cof_gridmastertraining_params.ArrFilter.join(',');
                store.proxy.extraParams.CmbFilterProvince = cof_gridmastertraining_params.CmbFilterProvince;
                store.proxy.extraParams.CmbFilterDistrict = cof_gridmastertraining_params.CmbFilterDistrict;
                store.proxy.extraParams.CmbFilterSubDistrict = cof_gridmastertraining_params.CmbFilterSubDistrict;
                store.proxy.extraParams.CmbFilterVillage = cof_gridmastertraining_params.CmbFilterVillage;
                store.proxy.extraParams.TextFilterID = cof_gridmastertraining_params.TextFilterID;
                store.proxy.extraParams.TextFilterName = cof_gridmastertraining_params.TextFilterName;
            } else {
                //reset params
                store.proxy.extraParams.ArrFilter = null;
                store.proxy.extraParams.CmbFilterProvince = null;
                store.proxy.extraParams.CmbFilterDistrict = null;
                store.proxy.extraParams.CmbFilterSubDistrict = null;
                store.proxy.extraParams.CmbFilterVillage = null;
                store.proxy.extraParams.TextFilterID = null;
                store.proxy.extraParams.TextFilterName = null;
            }
        }
    }
});