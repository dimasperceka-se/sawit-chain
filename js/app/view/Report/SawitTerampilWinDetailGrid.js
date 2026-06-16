/*
* @Author: Gitandi Nadzari
* @Date:   2018-09-19 15:30:00
* @Last Modified by:   Gitandi Nadzari
* @Last Modified time: 2018-09-19 15:30:00
*/

/*
    Param2 yg diperlukan ketika load View ini
    - ***
*/

Ext.define('Koltiva.view.Report.SawitTerampilWinDetailGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Report.SawitTerampilWinDetailGrid',
    renderTo: 'ext-content',
    style:'padding:0 7px 7px 7px;margin:5px 0 0 0;',
    // viewVar: false,
    // setViewVar: function(value){
    //     this.viewVar = value;
    // },
    listeners: {
        afterRender: function(component, eOpts){
            var thisObj = this;
            Ext.getCmp('filterMonthYears').store.on('load',function(ds,records,o){
                Ext.getCmp('filterMonthYears').setValue(records[0].data.DateProcess);
                Ext.getCmp('Koltiva.view.Report.SawitTerampil-MainGrid').store.load();
                // Ext.getCmp('ckbReported').setValue(records[0].data.ReportStatus);
                // Ext.getCmp('srcSelect').el.dom.click();
            });
            // Ext.getCmp('srcSelect').el.dom.click();
        }
    },
    initComponent: function() {
        Ext.util.CSS.createStyleSheet([
            '.publishedItem {',
            '    color:#ffffff;',
            ' background-color:#589C14',
            '}'
        ].join('\n'));
        var thisObj = this;
        
        //Define Store Main Grid
        thisObj.SawitTerampilGridMain = Ext.create('Koltiva.store.Report.SawitTerampilMainFormGrid',{
            autoLoad:false            
        	// storeVar: {
            //     filterYears: thisObj.viewVar.filterYears
            // }
        });
        // thisObj.ComboYear = Ext.create('Koltiva.store.Report.CmbYear');
        // thisObj.ComboMonth = Ext.create('Koltiva.store.Report.CmbMonth');
        thisObj.ComboMonthYears = Ext.create('Koltiva.store.Report.SawitTerampilMainFormGrid.CmbMonthYears',{
            storeVar: {
                showProcessDate: m_act_calculate_kpi?'reported':'all'
            }
        });
        thisObj.Classification = Ext.create('Koltiva.store.Report.SawitTerampilMainFormGrid.Classification');
        
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.3,
                layout: 'form',
                items:[{
                    
                }]
            },{
                columnWidth: 0.7,
                xtype: 'panel',
                frame: false,
                id: 'Koltiva.view.Report.SawitTerampilMainFormGrid-gridInformation',
                html: ''
            }]
        },{
        	xtype: 'grid',
            id: 'Koltiva.view.Report.SawitTerampil-MainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.SawitTerampilGridMain,
            enableColumnHide: false,
            height: 550,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.SawitTerampilGridMain,
                dock: 'bottom',
                displayInfo: true
            },{
            	xtype: 'toolbar',
                dock:'top',
                items: [{
                    xtype: 'combobox',
                    id: 'filterMonthYears',
                    emptyText: lang('Process Date'),
                    store: thisObj.ComboMonthYears,
                    queryMode: 'local',
                    displayField: 'monthnmyears',
                    valueField: 'DateProcess',
                    allowBlank: false,
                    tpl: Ext.create('Ext.XTemplate',
                        '<tpl for=".">',
                        '<div class="{[this.getClass(values)]}">{monthnmyears}</div>',
                        '</tpl>',
                            {
                                getClass: function (rec) {
                                    if(m_act_calculate_kpi==false){
                                        return rec.ReportStatus == '1' ? 'x-boundlist-item publishedItem' : 'x-boundlist-item';
                                    } else {
                                        return 'x-boundlist-item';
                                    }
                                }
                            }
                    ),
                    listeners: {
                        change: function() {
                            
                        }
                    }
                // },{
                //     xtype: 'combobox',
                //     id: 'filterMonths',
                //     emptyText: lang('Month'),
                //     store: thisObj.ComboMonth,
                //     queryMode: 'local',
                //     displayField: 'month',
                //     valueField: 'month'
                // },{
                //     xtype: 'combobox',
                //     id: 'filterYears',
                //     emptyText: lang('Year'),
                //     store: thisObj.ComboYear,
                //     queryMode: 'local',
                //     displayField: 'year',
                //     valueField: 'year'
                },{
                    xtype: 'button',
                    id: 'srcSelect',
                    icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    text: lang('Search'),
                    handler: function () {
                        thisObj.filterRecord();
                    }
                },{
                    xtype: 'button',
                    id: 'srcCalculateKPI',
                    hidden: true, 
                    icon: varjs.config.base_url + 'images/icons/new/process.png',
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    text: lang('Calculate KPI'),
                    handler: function () {
                        var SawitTerampilWinCalculateKPI = Ext.create('Koltiva.view.Report.SawitTerampilWinCalculateKPI');
                        if (!SawitTerampilWinCalculateKPI.isVisible()) {
                            SawitTerampilWinCalculateKPI.center();
                            SawitTerampilWinCalculateKPI.show();
                        } else {
                            SawitTerampilWinCalculateKPI.close();
                        }
                    }
                // },{
                //     xtype: 'button',
                //     id: 'srcTesting',
                //     margin: '0px 0px 0px 6px',
                //     text: 'Test',
                //     handler: function () {
                //         Ext.MessageBox.confirm('Message', 'Testing?', function(btn){
                            
                //             if(btn == 'yes')
                //             {   Ext.Ajax.request({
                //                         waitMsg: 'Please wait...',
                //                         url: m_do_testcalc,
                //                         method : 'POST',                                        
                //                     success: function(response, opts){
                //                          var obj = Ext.decode(response.responseText);
                //                          switch(obj.success){
                //                              case true:
                //                              console.log(obj.success);
                //                          }
                //                     }
                //                 });
                //             }
                //         });
                //     }
                },{
                    xtype: 'button',
                    id: 'srcCalculateKPINew',
                    hidden: m_act_calculate_kpi, 
                    icon: varjs.config.base_url + 'images/icons/new/process.png',
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    text: lang('Calculate KPI New'),
                    handler: function () {
                        var UtzCertificationWinCalculateKPINew = Ext.create('Koltiva.view.Report.SawitTerampilWinCalculateKPINew');
                        if (!UtzCertificationWinCalculateKPINew.isVisible()) {
                            UtzCertificationWinCalculateKPINew.center();
                            UtzCertificationWinCalculateKPINew.show();
                        } else {
                            UtzCertificationWinCalculateKPINew.close();
                        }
                    }
                },{
                    xtype:'container',
                    flex:1
                },{
                    xtype: 'combobox',
                    id: 'filterClassification',
                    emptyText: lang('Classification'),
                    store: thisObj.Classification,
                    hidden:true,
                    queryMode: 'local',
                    displayField: 'classification',
                    valueField: 'classificationValue',
                    allowBlank: false,
                    width: 200
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/export.png',
                    text: lang('Export Detail'),
                    cls:'Sfr_BtnGridPaleBlue',
                    overCls:'Sfr_BtnGridPaleBlue-Hover',
                    hidden: true,
                    handler: function () {
                        var filterMonthYears = Ext.getCmp('filterMonthYears').getSubmitValue();
                        var filterClassification = Ext.getCmp('filterClassification').getSubmitValue();
                        if(filterMonthYears==''){
                            Ext.MessageBox.alert({
                                title:'Alert',
                                msg: lang('Process date report still not selected, please select the process date and try again.'),
                                buttons: Ext.Msg.OK,
                                icon: Ext.Msg.WARNING
                            });
                            Ext.getCmp('filterMonthYears').focus(true, 10);
                        } else if(filterClassification==''){
                            Ext.MessageBox.alert({
                                title:'Alert',
                                msg: lang('Classification report still not selected, please select the classification and try again.'),
                                buttons: Ext.Msg.OK,
                                icon: Ext.Msg.WARNING
                            });
                            Ext.getCmp('filterClassification').focus(true, 10);
                        } else {
                            Ext.MessageBox.confirm(lang('Message'), lang('Export data ?') , function(btn){
                    
                                if(btn == 'yes')
                                {
                                    Ext.MessageBox.show({
                                        msg: 'Please wait...',
                                        progressText: 'Exporting...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {
                                            interval: 200
                                        },
                                        icon: 'ext-mb-info', //custom class in msg-box.html
                                        animateTarget: 'mb9'
                                    });

                                    Ext.Ajax.request({
                                        url: m_report + '_detail_progress_excel/',
                                        method: 'POST',
                                        params: {
                                            filterMonthYears: filterMonthYears,
                                            filterClassification: filterClassification
                                        },
                                        waitMsg: lang('Please Wait'),
                                        success: function(data) {
                                            Ext.MessageBox.hide();
                                            var jsonResp = JSON.parse(data.responseText);
                                            window.location = jsonResp.filedl;
                                        },
                                        failure: function() {
                                            Ext.MessageBox.hide();
                                            Ext.MessageBox.show({
                                                title: 'Notifications',
                                                msg: 'Failed to export, Please try again.',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });
                                }
                            });
                        }
                        
                    }
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/export.png',
                    text: lang('Export Summary'),
                    cls:'Sfr_BtnGridPaleBlue',
                    overCls:'Sfr_BtnGridPaleBlue-Hover',
                    hidden:true,
                    handler: function () {
                        var filterMonthYears = Ext.getCmp('filterMonthYears').getSubmitValue();
                        if(filterMonthYears==''){
                            Ext.MessageBox.alert({
                                title:'Alert',
                                msg: lang('Process date report still not selected, please select the process date and try again.'),
                                buttons: Ext.Msg.OK,
                                icon: Ext.Msg.WARNING
                            });
                            Ext.getCmp('filterMonthYears').focus(true, 10);
                        } else {
                            Ext.MessageBox.confirm(lang('Message'), lang('Export data ?') , function(btn){
                                if(btn == 'yes')
                                {
                                    Ext.MessageBox.show({
                                        msg: 'Please wait...',
                                        progressText: 'Exporting...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {
                                            interval: 200
                                        },
                                        icon: 'ext-mb-info', //custom class in msg-box.html
                                        animateTarget: 'mb9'
                                    });

                                    Ext.Ajax.request({
                                        url: m_report + '_progress_excel/',
                                        method: 'POST',
                                        params: {
                                            filterMonthYears: filterMonthYears
                                        },
                                        waitMsg: lang('Please Wait'),
                                        success: function(data) {
                                            Ext.MessageBox.hide();
                                            var jsonResp = JSON.parse(data.responseText);
                                            window.location = jsonResp.filedl;
                                        },
                                        failure: function() {
                                            Ext.MessageBox.hide();
                                            Ext.MessageBox.show({
                                                title: 'Notifications',
                                                msg: 'Failed to export, Please try again.',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });

                                    var excel_url = m_report + '_progress_excel' + '/'+ filterMonthYears;
                                    window.location = excel_url;
                                }
                            });
                        }
                    }
                }]
            }],
            columns:[{
                text: lang('Batch'),
                dataIndex: 'Batch',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColBatch',
                width: '5%'
            },{
            	text: lang('Year'),
	            dataIndex: 'Year',
	            // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColYear',
                width: '5%'
            },{
                text: lang('Cluster Name'),
                dataIndex: 'ClusterName',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColIMSID',
                width: '5%'
            },{
            	text: lang('DateUpdatedHis'),
	            dataIndex: 'DateUpdatedHis',
	            // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColDateUpdatedHis',
                width: '5%'
            },{
            	text: lang('Province'),
                dataIndex: 'Province',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColProvince',
                width: '10%'
            },{
            	text: lang('TargetedPalmoilMill'),
                dataIndex: 'TargetedPalmoilMill',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColTargetCertHa',
                width: '3%'
            },{
            	text: lang('AchievedPalmoilMill'),
                dataIndex: 'AchievedPalmoilMill',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColAchievedCertHa',
                width: '3%'
            },{
            	text: lang('Targeted Farmer Register'),
                dataIndex: 'TargetedFarmerReg',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColTargetedBuyingUnit',
                width: '3%'
            },{
            	text: lang('Achieved Farmer Register'),
                dataIndex: 'AchievedFarmerReg',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColAchievedBuyingUnit',
                width: '3%'
            },{
            	text: lang('Targeted Farm Register'),
                dataIndex: 'TargetedFarmReg',
                width: '3%'
            },{
            	text: lang('Achieved Farm Register'),
                dataIndex: 'AchievedFarmReg',
                width: '3%'
            },{
            	text: lang('Targeted Ha'),
                dataIndex: 'TargetedHa',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColTargetedIMSSupport',
                width: '5%'
            },{
            	text: lang('Achieved Ha'),
                dataIndex: 'AchievedHa',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColAchievedIMSSupport',
                width: '5%'
            },{
            	text: lang('Targeted SOC'),
                dataIndex: 'TargetedSocSel',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColTargetedSOC',
                width: '5%'
            },{
            	text: lang('Achieved SOC'),
                dataIndex: 'AchievedSocSel',
                // id: 'Koltiva.view.Report.SawitTerampil-MainGrid-ColAchievedSOC',
                width: '5%'
            },{
                text: lang('Targeted Farmer Survey'),
                dataIndex: 'TargetedFarmerSurveyBP',
                width: '5%'
            },{
                text: lang('Achieved Farmer Survey'),
                dataIndex: 'AchievedFarmerSurveyBP',
                width: '5%'
            },{
                text: lang('Targeted Farm Survey'),
                dataIndex: 'TargetedFarmSurvey',
                width: '5%'
            },{
                text: lang('Achieved Farm Survey'),
                dataIndex: 'AchievedFarmSurvey',
                width: '5%'
            },{
                text: lang('Targeted Polygon'),
                dataIndex: 'TargetedPolygon',
                width: '5%'
            },{
                text: lang('Achieved Polygon'),
                dataIndex: 'AchievedPolygon',
                width: '5%'
            },{
                text: lang('Targeted Farmer Coach'),
                dataIndex: 'TargetedFarmerCoach',
                width: '5%'
            },{
                text: lang('Achieved Farmer Coach'),
                dataIndex: 'AchievedFarmerCoach',
                width: '5%'
            },{
                text: lang('Targeted Coaching Session'),
                dataIndex: 'TargetedCoachingSess',
                width: '5%'
            },{
                text: lang('Achieved Coaching Session'),
                dataIndex: 'AchievedCoachingSess',
                width: '5%'
            },{
                text: lang('Targeted Sms'),
                dataIndex: 'TargetedSms',
                width: '5%'
            },{
                text: lang('Achieved Sms'),
                dataIndex: 'AchievedSms',
                width: '5%'
            },{
                text: lang('Targeted Id Card'),
                dataIndex: 'TargetedIdCard',
                width: '5%'
            },{
                text: lang('Achieved Id Card'),
                dataIndex: 'AchievedIdCard',
                width: '5%'
            },{
                text: lang('Targeted FarmX'),
                dataIndex: 'TargetedFarmX',
                width: '5%'
            },{
                text: lang('Achieved FarmX'),
                dataIndex: 'AchievedFarmX',
                width: '5%'
            },{
                text: lang('Targeted FarmG'),
                dataIndex: 'TargetedFarmG',
                width: '5%'
            },{
                text: lang('Achieved FarmG'),
                dataIndex: 'AchievedFarmG',
                width: '5%'
            },{
                text: lang('Targeted FarmR'),
                dataIndex: 'TargetedFarmR',
                width: '5%'
            },{
                text: lang('Achieved FarmR'),
                dataIndex: 'AchievedFarmR',
                width: '5%'
            },{
                text: lang('Targeted FarmC'),
                dataIndex: 'TargetedFarmC',
                width: '5%'
            },{
                text: lang('Achieved FarmC'),
                dataIndex: 'AchievedFarmC',
                width: '5%'
            }]
        }];

        this.callParent(arguments);
    },
    filterRecord: function(){
        var thisObj = this;        
        thisObj.SawitTerampilGridMain.load();
    },
    calculateKPI: function(){
        var thisObj = this;        
        Ext.MessageBox.confirm(lang('Message'), lang('Calculate KPI ?') , function(btn){
                    
            if(btn == 'yes')
            {                                  
                Ext.Msg.alert('Info', lang('Okay'));
            } else {
                Ext.Msg.alert('Info', lang('No'));
            }
        });
    }
});