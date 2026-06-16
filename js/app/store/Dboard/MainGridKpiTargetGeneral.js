Ext.define('Koltiva.store.Dboard.MainGridKpiTargetGeneral', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Dboard.MainGridKpiTargetGeneral',
    id: 'Koltiva.store.Dboard.MainGridKpiTargetGeneral',
    fields: [
        'CountryID'
        ,'ProvinceID'
        ,'PartnerID'
        ,'DistrictID'
        ,'District'
        ,'ProvinceLabel'
        ,'Year'
        ,'Province'
        ,'CountryName'
        ,{name:'PalmOilFarmersRegistered',type:'integer'}
        ,{name:'PalmOilPlantationsMapped',type:'integer'}
        ,{name:'ConsentLettersSigned',type:'integer'}
        ,{name:'PalmOilPlantationsArea',type:'integer'}
        ,{name:'PalmOilMillsMapped',type:'integer'}
        ,{name:'PalmOilPlantationsMappedWithPolygon',type:'integer'}
        ,{name:'PalmOilSMEMapped',type:'integer'}
        ,{name:'PalmOilPlantationsHectareMappedWithPolygon',type:'integer'}
        ,{name:'TrainOrCoachFarmers', type:'integer'}
        ,{name:'RegisteredPlantation', type:'integer'}
        ,{name:'RegisteredPlantationHectares', type:'integer'}
        ,{name:'ResponSourcingFarmers', type:'integer'}
        ,{name:'TraceTransaction', type:'integer'}
        ,{name:'PlatformUsers', type:'integer'}
        ,{name:'RegisteredSME', type:'integer'}
        ,{name:'FarmXUsers', type:'integer'}
        ,{name:'FarmGateUsers', type:'integer'}
        ,{name:'FarmRetailUsers', type:'integer'}
        ,{name:'FarmCloudUsers', type:'integer'}
    ],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/kpi_general/kpi_target_general_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function(store, options) {
            store.proxy.extraParams.FilterYear = this.storeVar.FilterYear;
            store.proxy.extraParams.FilterCountry = this.storeVar.FilterCountry;
            store.proxy.extraParams.FilterProvince = this.storeVar.FilterProvince;
            store.proxy.extraParams.FilterPartnerID = this.storeVar.FilterPartnerID;
            store.proxy.extraParams.FilterDistrictID = this.storeVar.FilterDistrictID;
        }
    }
});