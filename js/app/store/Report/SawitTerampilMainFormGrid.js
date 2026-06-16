/*
* @Author: Gitandi Nadzari
* @Date:   2018-09-19 15:30:00
* @Last Modified by:   Gitandi Nadzari
* @Last Modified time: 2018-09-19 15:30:00
*/

Ext.define('Koltiva.store.Report.SawitTerampilMainFormGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Report.SawitTerampilMainFormGrid',
    fields: ['Batch','Year','ClusterName','Province','AchievedPalmoilMill','AchievedFarmerReg','AchievedFarmReg','AchievedHa','AchievedSocSel',
    'AchievedFarmerSurveyBP','AchievedFarmSurvey','AchievedPolygon','AchievedFarmerCoach','AchievedCoachingSess','AchievedSms','AchievedIdCard',
    'AchievedFarmX','AchievedFarmG','AchievedFarmR','AchievedFarmC','DateUpdated','DateUpdatedHis','LastModifiedBy','TargetedPalmoilMill',
    'TargetedFarmerReg','TargetedFarmReg','TargetedHa','TargetedSocSel','TargetedFarmerSurveyBP','TargetedFarmSurvey','TargetedPolygon',
    'TargetedFarmerCoach','TargetedCoachingSess','TargetedSms','TargetedIdCard','TargetedFarmX','TargetedFarmG','TargetedFarmR','TargetedFarmC'],
    pageSize: 60,
    autoLoad: true,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/report_sawit_terampil/sawit_terampil_main_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
    	beforeload: function(store, operation, options){
            // store.proxy.extraParams.filterYears= Ext.getCmp('filterYears').getValue();
            // store.proxy.extraParams.filterMonths= Ext.getCmp('filterMonths').getValue();
            // Ext.getCmp('filterMonthYears').store.on('load',function(ds,records,o){
            //     Ext.getCmp('filterMonthYears').setValue(records[0].data.DateProcess);
            //     Ext.getCmp('srcSelect').el.dom.click();
            // });
            store.proxy.extraParams.filterMonthYears= Ext.getCmp('filterMonthYears').getValue();
        }
    }
});

Ext.define('Koltiva.store.Report.SawitTerampilMainFormGrid.CmbMonthYears', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Report.SawitTerampilMainFormGrid.CmbMonthYears',
    id: 'Koltiva.store.Report.SawitTerampilMainFormGrid.CmbMonthYears',
    fields: ['id','ReportName','monthnmyears','monthyears','DateProcess','ReportStatus'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_combo_monthyears,
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners:{
        beforeload: function(store){
            store.proxy.extraParams.showProcessDate = this.storeVar.showProcessDate;
        }
    }
});
// Ext.define('Koltiva.store.Report.CmbYear', {
//         extend: 'Ext.data.Store',
//         storeId: 'Koltiva.store.Report.CmbYear',
//         id: 'Koltiva.store.Report.CmbYear',
//         fields: ['year'],
//         autoLoad: true,
//         proxy: {
//             type: 'ajax',
//             url: m_combo_years,
//             reader: {
//                 type: 'json',
//                 root: 'data'
//             }
//         }
// });

// Ext.define('Koltiva.store.Report.CmbMonth', {
//     extend: 'Ext.data.Store',
//     storeId: 'Koltiva.store.Report.CmbMonth',
//     id: 'Koltiva.store.Report.CmbMonth',
//     fields: ['month'],
//     autoLoad: true,
//     proxy: {
//         type: 'ajax',
//         url: m_combo_months,
//         reader: {
//             type: 'json',
//             root: 'data'
//         }
//     }
// });
Ext.define('Koltiva.store.Report.SawitTerampilMainFormGrid.Classification', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Report.SawitTerampilMainFormGrid.Classification',
    id: 'Koltiva.store.Report.SawitTerampilMainFormGrid.Classification',
    fields: ['classification','classificationValue'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_classification,
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
Ext.define('Koltiva.store.Report.WaveJB', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Report.WaveJB',
    id: 'Koltiva.store.Report.WaveJB',
    fields: ['id','name'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_wave_jb,
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners:{
        beforeload: function(store){
            // store.proxy.extraParams.showProcessDate = this.storeVar.showProcessDate;
        }
    }
});


Ext.define('Koltiva.store.Report.CmbStoreProcedureJB', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Report.CmbStoreProcedureJB',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/report_sawit_terampil/cmb_store_procedure_sawit_terampil',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.ProgID = this.storeVar.ProgID;
        }
    }
});


Ext.define('Koltiva.store.Report.StoreMainGridCalculateJB', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Report.StoreMainGridCalculateJB',
    id: 'Koltiva.store.Report.StoreMainGridCalculateJB',
    fields: ['id', 'ProgID', 'StoreProcedureName', 'DateGenerated', 'OrderNo'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/report_sawit_terampil/calculate_sawit_grid_main',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function (store) {
            store.proxy.extraParams.ProgID = this.storeVar.ProgID;
        }
    }
});
