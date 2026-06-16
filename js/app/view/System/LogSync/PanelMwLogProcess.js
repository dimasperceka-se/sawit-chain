Ext.define('Koltiva.view.System.LogSync.PanelMwLogProcess' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.System.LogSync.PanelMwLogProcess',
    width: '100%',
    minHeight: 250,
    title: lang('Mw2 Log Process : Tabel log ketika mulai memproses tahap validation general'),
    style: 'border:1px solid #CCC;',
    renderTo: 'ext-content',
    loadMask: true,
    selType: 'rowmodel',
    // submitOnEnterGrid: function(field, event){
    // 	if (event.getKey() == event.ENTER) {
    //         Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess').setFilterLs();
    //         Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess').getStore().loadPage(1);
    //     }
    // },
    setFilterLs: function(){
    	localStorage.setItem('ct_mwlogprocess_ls', JSON.stringify({
    		opsiCall: 'simple',
            pDateStartMw2: Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateStart').getValue(),
            pDateEndMw2: Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd').getValue(),
        }));
    },
    SetGridColumnHideAll: function(){
        
    },
    SetGridColumnDefault: function(){
    	
        
    	
    },
    listeners: {
        afterRender: function(component, eOpts){
        	var thisObj = this;

        	var ct_mwlogprocess_ls = JSON.parse(localStorage.getItem('ct_mwlogprocess_ls'));
        	if(ct_mwlogprocess_ls != null){
        		if(ct_mwlogprocess_ls.opsiCall != undefined){
	            	if(ct_mwlogprocess_ls.opsiCall == "advanced"){
	            		Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-BtnSimplifiedGrid').setVisible(true);
	            	}else{
	            		Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-BtnSimplifiedGrid').setVisible(false);
	            	}
	            }

	            if(ct_mwlogprocess_ls.pDateStartMw2 != undefined){
	            	Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateStart').setValue(ct_mwlogprocess_ls.pDateStartMw2);
                }

                if(ct_mwlogprocess_ls.pDateEndMw2 != undefined){
	            	Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd').setValue(ct_mwlogprocess_ls.pDateEndMw2);
                }
        	}

            var ct_mwlogprocess_grid_ls = JSON.parse(localStorage.getItem('ct_mwlogprocess_grid_ls'));
            if(ct_mwlogprocess_grid_ls != null){
            	if(ct_mwlogprocess_grid_ls.opsiShow != undefined){
	            	if(ct_mwlogprocess_grid_ls.opsiShow == "custom"){
	            		//Sesuaikan
                        thisObj.SetGridColumnCustom(ct_mwlogprocess_grid_ls.ColDisplayArr);
                        Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-BtnSimplifiedGrid').setVisible(true);
	            	}else{
	            		//Tampilan Grid Column Default
                        thisObj.SetGridColumnDefault();
                        Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-BtnSimplifiedGrid').setVisible(false);
	            	}
	            }
            }
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Define Store Main Grid
        thisObj.StoreGridMain       = Ext.create('Koltiva.store.System.LogSync.StoreMainGridLogMwLogProcess');

        //Context Menu
        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess').getSelectionModel().getSelection()[0];
                        if (!sm) {
                            Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                            return false;
                        }else{
                            var id = sm.get('LogID');
                            var win = Ext.create('widget.window', {
                                title: lang('Log Detail'),
                                id: 'Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogDetail',
                                cls: 'Sfr_LayoutPopupWindows',
                                modal: true,
                                width: '60%',
                                height: 680,
                                layout: 'fit',
                                items: Ext.create('Ext.form.Panel', {
                                    height: 490,
                                    width: '100%',
                                    bodyPadding: 5,
                                    autoScroll: true,
                                    // id: 'frm-edit-translation',
                                    items: [{
                                        xtype: 'textareafield',
                                        fieldLabel: lang('Log'),
                                        allowBlank: false,
                                        value: sm.get('log'),
                                        width: '100%',
                                        minHeight: 350,
                                        labelAlign: 'left',
                                        labelWidth: 30,
                                        readOnly: true
                                    },{
                                        xtype: 'textareafield',
                                        fieldLabel: lang('Proc Name'),
                                        allowBlank: false,
                                        value: sm.get('proc_name'),
                                        width: '100%',
                                        labelAlign: 'left',
                                        labelWidth: 30,
                                        readOnly: true
                                    },
                                    /* {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Event UID'),
                                        allowBlank: false,
                                        value: sm.get('eventuid'),
                                        width: '100%',
                                        labelAlign: 'left',
                                        labelWidth: 30,
                                    } */
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Date Created'),
                                        allowBlank: false,
                                        value: sm.get('timestamp'),
                                        width: '100%',
                                        labelAlign: 'left',
                                        labelWidth: 30,
                                        readOnly: true
                                    }
                                    ],
                                    buttons: [{
                                        icon: varjs.config.base_url + 'images/icons/new/close.png',
                                        text: lang('Close'),
                                        cls:'Sfr_BtnFormGrey',
                                        overCls:'Sfr_BtnFormGrey-Hover',
                                        handler: function () {
                                            win.close();
                                        }
                                    }]
                                })
                            }).show()
                        }

                        
                    }
                }]
        });

        thisObj.items = [{
        	xtype: 'grid',
            id: 'Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            height: 450,
            // autoScroll: true,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true,
                displayMsg: 'Showing {0} to {1} of {2} entries'
            },{
            	xtype: 'toolbar',
                dock:'top',
                items: [
                {
                	name: 'startDate',
                    id: 'Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateStart',
                    xtype: 'datetimefield',
                    // baseCls:'Sfr_TxtfieldSearchGrid',
                    format: 'Y-m-d H:i',
                    width: 200,
                    emptyText: lang('Start Date'),
                    hourText: lang('Time'),
                    minuteText: lang('Minutes'),
                    todayText: lang('Current Date'),
                    allowBlank:false,
                    listeners: {
                        change: function(){
                            var nilai = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateStart').getValue();
                            var dt = new Date(nilai);
                            dt.setHours( dt.getHours() + 6 );
                            // console.log(dt)
                            Ext.getCmp("Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd").setMinValue(nilai);
                            Ext.getCmp("Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd").setMaxValue(dt);
                            Ext.getCmp("Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd").setValue(dt);
                            // el.set({
                            //     "maxValue" : dt
                            // });
                            // el.setMaxValue(dt);
                        }
                    }
                },{
                	name: 'endDate',
                    id: 'Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd',
                    xtype: 'datetimefield',
                    // baseCls:'Sfr_TxtfieldSearchGrid',
                    format: 'Y-m-d H:i',
                    width: 200,
                    emptyText: lang('End Date'),
                    hourText: lang('Time'),
                    minuteText: lang('Minutes'),
                    todayText: lang('Current Date'),
                    allowBlank:false,
                    listeners:{
                        change:function(){
                            var start = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateStart').getValue();
                            var dt = new Date(start);
                            dt.setHours( dt.getHours() + 6 );
                            var end = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd').getValue();

                            if((end > dt) || (end < start) || (end == '')){
                                Ext.getCmp('btn_search_mw2logprocess').setDisabled(true);
                            }else{
                                Ext.getCmp('btn_search_mw2logprocess').setDisabled(false);
                            }
                        }
                    }
                },{
                	xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/visible-field.png',
                	text:lang('Default Grid'),
                    hidden: true,
                    cls:'Sfr_BtnGridPaleBlue',
                    overCls:'Sfr_BtnGridPaleBlue-Hover',
                	id:'Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-BtnSimplifiedGrid',
                    handler: function() {
                    	localStorage.setItem('ct_mwlogprocess_ls', JSON.stringify({
				    		opsiCall: 'simple',
				            pDateStartMw2: Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateStart').getValue(),
                            pDateEndMw2: Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd').getValue()
				        }));

				        localStorage.setItem('ct_mwlogprocess_grid_ls', JSON.stringify({
				    		opsiShow: 'default',
                            pDateStartMw2: Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateStart').getValue(),
                            pDateEndMw2: Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd').getValue()
				        }));
				        thisObj.SetGridColumnDefault();

                    	thisObj.StoreGridMain.load();

                    	//Hilangkan Tombol
                    	Ext.getCmp('Koltiva.view.main.MainGridLogMwLogProcess-GridLogMwLogProcess-BtnSimplifiedGrid').setVisible(false);
                    }
                }, {
                    xtype: 'button',
                    margin: '0px 0px 0px 6px',
                    id:'btn_search_mw2logprocess',
                    text: lang('Search'),
                    handler: function() {
                        Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess').getStore().loadPage(1);
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/export.png',
                    text: lang('Export to Excel'),
                    cls: 'Sfr_BtnGridPaleBlue',
                    overCls: 'Sfr_BtnGridPaleBlue-Hover',
                    handler: function () {
                        Ext.MessageBox.confirm(lang('Message'), lang('Export data ?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.MessageBox.show({
                                    msg: lang('Please wait...'),
                                    progressText: lang('Exporting...'),
                                    width: 300,
                                    wait: true,
                                    waitConfig: {
                                        interval: 200
                                    },
                                    icon: 'ext-mb-download', //custom class in msg-box.html
                                    animateTarget: 'mb7'
                                });

                                var DateStartMw2 = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateStart').getValue();
                                var DateEndMw2 = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd').getValue();
                                

                                Ext.Ajax.request({
                                    url: m_api + '/logsync/mw2_log_process_export_excel',
                                    method: 'POST',
                                    waitMsg: lang('Please Wait'),
                                    params: {
                                        
                                        pDateStartMw2: DateStartMw2,
                                        pDateEndMw2: DateEndMw2
                                    },
                                    success: function (data) {
                                        Ext.MessageBox.hide();
                                        if (!testJSON(data.responseText)) {
                                            Ext.MessageBox.show({
                                                title: 'Failed',
                                                msg: 'Connection Failed',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                            return false;
                                        }

                                        var jsonResp = JSON.parse(data.responseText);
                                        if (jsonResp.success == true) {
                                            window.location = jsonResp.filenya;
                                        } else if (jsonResp.message == 'Empty') {
                                            Ext.MessageBox.show({
                                                title: lang('Success'),
                                                msg: lang(jsonResp.filenya),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-info'
                                            });
                                            return false;
                                        }
                                    },
                                    failure: function () {
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
                },{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/reload.png',
                    cls:'Sfr_BtnGridBlue',
                    overCls:'Sfr_BtnGridBlue-Hover',
                    handler: function() {
                        Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess').getStore().loadPage(1);
                    }
                }]
            }],
            columns:[
            {
                text: '',
                xtype: 'actioncolumn',
                width: '4%',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                        handler: function (grid, rowIndex, colIndex, item, e, record) {
                            thisObj.ContextMenuGrid.showAt(e.getXY());
                        }
                    }]
            },{
                text: 'No',
                xtype: 'rownumberer',
                width:'4%'
            },{
            	text: lang('ID'),
	            dataIndex: 'id',
	            hidden: true
            },{
            	text: lang('Log'),
                dataIndex: 'log',
                flex: 4
            },{
                text: lang('Proc Name'),
                dataIndex: 'proc_name',
                flex: 2
            },
           /* {
            	text: lang('Event UID'),
                dataIndex: 'eventuid',
                flex: 2
            }, */
            {
            	text: lang('Date Created'),
                dataIndex: 'timestamp',
                flex: 1
            }]
        }];

        this.callParent(arguments);
    }
});

function testJSON(text){
    try{
        JSON.parse(text);
        return true;
    }
    catch (error){
        return false;
    }
}