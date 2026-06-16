Ext.define('Koltiva.store.Dboard.MainGridKpiTargetSawitTerampil', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Dboard.MainGridKpiTargetSawitTerampil',
    id: 'Koltiva.store.Dboard.MainGridKpiTargetSawitTerampil',
    fields: [
        'TargetID'
        ,'ClusterName'
        ,'ProgramName'
        ,'Province'
        ,'Year'
        ,{name:'KsMill',type:'integer'}
        ,{name:'StMill',type:'integer'}
        ,{name:'FarmerReg',type:'integer'}
        ,{name:'FarmReg',type:'integer'}
        ,{name:'Ha',type:'integer'}
        ,{name:'SocSel',type:'integer'}
        ,{name:'FarmerSurveyBP',type:'integer'}
        ,{name:'FarmSurvey',type:'integer'}
        ,{name:'Polygon', type:'integer'}
        ,{name:'FarmerCoach', type:'integer'}
        ,{name:'CoachingSess', type:'integer'}
        ,{name:'Sms', type:'integer'}
        ,{name:'IdCard', type:'integer'}
        ,{name:'FarmX', type:'integer'}
        ,{name:'FarmG', type:'integer'}
        ,{name:'FarmR', type:'integer'}
        ,{name:'FarmC', type:'integer'}
    ],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/kpi_general/kpi_target_sawit_terampil_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function(store, options) {
            store.proxy.extraParams.FilterYear = this.storeVar.FilterYear;
        }
    }
});