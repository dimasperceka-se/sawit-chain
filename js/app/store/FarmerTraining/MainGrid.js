Ext.define('Koltiva.store.FarmerTraining.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.MainGrid',
    fields: ['id', 'training', 'batch', 'tot', 'participant', 'start', 'end', 'days','TrainingStatus'],
    autoLoad: true,
    pageSize: 50,
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
            let cof_gridfarmertraining_params = JSON.parse(localStorage.getItem('cof_gridfarmertraining_params'));
            if(cof_gridfarmertraining_params != null){
                store.proxy.extraParams.ArrFilter = cof_gridfarmertraining_params.ArrFilter.join(',');
                store.proxy.extraParams.CmbFilterProvince = cof_gridfarmertraining_params.CmbFilterProvince;
                store.proxy.extraParams.CmbFilterDistrict = cof_gridfarmertraining_params.CmbFilterDistrict;
                store.proxy.extraParams.CmbFilterSubDistrict = cof_gridfarmertraining_params.CmbFilterSubDistrict;
                store.proxy.extraParams.CmbFilterVillage = cof_gridfarmertraining_params.CmbFilterVillage;
                store.proxy.extraParams.TextFilterID = cof_gridfarmertraining_params.TextFilterID;
                store.proxy.extraParams.TextFilterName = cof_gridfarmertraining_params.TextFilterName;
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