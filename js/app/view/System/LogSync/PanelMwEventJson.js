Ext.define('Koltiva.view.System.LogSync.PanelMwEventJson' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.System.LogSync.PanelMwEventJson',
    width: '100%',
    minHeight: 250,
    title: lang('Mw2 Event JSON : Tabel log JSON setelah proses validation general selesai dan siap untuk di masukkan ke proses pull_engine'),
    style: 'border:1px solid #CCC;',
    renderTo: 'ext-content',
    loadMask: true,
    selType: 'rowmodel',
    submitOnEnterGrid: function(field, event){
    	if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson').setFilterLs();
            Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson').getStore().loadPage(1);
        }
    },
    setFilterLs: function(){
    	localStorage.setItem('ct_mweventjson_ls', JSON.stringify({
    		opsiCall: 'simple',
            pTextSearch: Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-TextSearch').getValue(),
        }));
    },
    SetGridColumnHideAll: function(){
        
    },
    SetGridColumnDefault: function(){
    	
        
    	
    },
    listeners: {
        afterRender: function(component, eOpts){
        	var thisObj = this;

        	var ct_mweventjson_ls = JSON.parse(localStorage.getItem('ct_mweventjson_ls'));
        	if(ct_mweventjson_ls != null){
        		if(ct_mweventjson_ls.opsiCall != undefined){
	            	if(ct_mweventjson_ls.opsiCall == "advanced"){
	            		Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-BtnSimplifiedGrid').setVisible(true);
	            	}else{
	            		Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-BtnSimplifiedGrid').setVisible(false);
	            	}
	            }

	            if(ct_mweventjson_ls.pTextSearch != undefined){
	            	Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-TextSearch').setValue(ct_mweventjson_ls.pTextSearch);
                }
        	}

            var ct_mweventjson_grid_ls = JSON.parse(localStorage.getItem('ct_mweventjson_grid_ls'));
            if(ct_mweventjson_grid_ls != null){
            	if(ct_mweventjson_grid_ls.opsiShow != undefined){
	            	if(ct_mweventjson_grid_ls.opsiShow == "custom"){
	            		//Sesuaikan
                        thisObj.SetGridColumnCustom(ct_mweventjson_grid_ls.ColDisplayArr);
                        Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-BtnSimplifiedGrid').setVisible(true);
	            	}else{
	            		//Tampilan Grid Column Default
                        thisObj.SetGridColumnDefault();
                        Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-BtnSimplifiedGrid').setVisible(false);
	            	}
	            }
            }
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Define Store Main Grid
        thisObj.StoreGridMain       = Ext.create('Koltiva.store.System.LogSync.StoreMainGridLogMwEventJson');

        //Context Menu
        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson').getSelectionModel().getSelection()[0];
                        if (!sm) {
                            Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                            return false;
                        }else{
                            var id = sm.get('LogID');
                            var win = Ext.create('widget.window', {
                                title: lang('Log Detail'),
                                id: 'Koltiva.view.System.LogSync.PanelMwEventJson-GridLogDetail',
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
                                        fieldLabel: lang('JSON'),
                                        allowBlank: false,
                                        value: sm.get('event_json'),
                                        width: '100%',
                                        minHeight: 350,
                                        labelAlign: 'left',
                                        labelWidth: 30,
                                        readOnly: true
                                    },{
                                        xtype: 'textfield',
                                        fieldLabel: lang('Event UID'),
                                        allowBlank: false,
                                        value: sm.get('event_uid'),
                                        width: '100%',
                                        labelAlign: 'left',
                                        labelWidth: 30,
                                        readOnly: true
                                    },{
                                        xtype: 'textfield',
                                        fieldLabel: lang('Program UID'),
                                        allowBlank: false,
                                        value: sm.get('program_uid'),
                                        width: '100%',
                                        labelAlign: 'left',
                                        labelWidth: 30,
                                        readOnly: true
                                    },{
                                        xtype: 'textfield',
                                        fieldLabel: lang('Date Created'),
                                        allowBlank: false,
                                        value: sm.get('date_created'),
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
            id: 'Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson',
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
            dockedItems: [
            {
                xtype: 'pagingtoolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true,
                displayMsg: 'Showing {0} to {1} of {2} entries'
            },
            {
            	xtype: 'toolbar',
                dock:'top',
                items: [
                {
                	name: 'key',
                    id: 'Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-TextSearch',
                    xtype: 'textfield',
                    baseCls:'Sfr_TxtfieldSearchGrid',
                    emptyText: lang('Search eventuid - seperate by comma')+', '+lang('press_enter_search'),
                    width: 500,
                    allowBlank:false,
                    listeners: {
                        specialkey: thisObj.submitOnEnterGrid
                    }
                },{
                	xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/visible-field.png',
                	text:lang('Default Grid'),
                    hidden: true,
                    cls:'Sfr_BtnGridPaleBlue',
                    overCls:'Sfr_BtnGridPaleBlue-Hover',
                	id:'Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-BtnSimplifiedGrid',
                    handler: function() {
                    	localStorage.setItem('ct_mweventjson_ls', JSON.stringify({
				    		opsiCall: 'simple',
				            pTextSearch: Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-TextSearch').getValue()
				        }));

				        localStorage.setItem('ct_mweventjson_grid_ls', JSON.stringify({
				    		opsiShow: 'default',
                            pTextSearch: Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-TextSearch').getValue()
				        }));
				        thisObj.SetGridColumnDefault();

                    	thisObj.StoreGridMain.load();

                    	//Hilangkan Tombol
                    	Ext.getCmp('Koltiva.view.main.MainGridLogMwEventJson-GridLogMwEventJson-BtnSimplifiedGrid').setVisible(false);
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

                                var TextSearch = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-TextSearch').getValue();
                                

                                Ext.Ajax.request({
                                    url: m_api + '/logsync/mw2_event_json_export_excel',
                                    method: 'POST',
                                    waitMsg: lang('Please Wait'),
                                    params: {
                                        pTextSearch: TextSearch
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
                        Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson').getStore().loadPage(1);
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
            	text: lang('JSON'),
                dataIndex: 'event_json',
                flex: 4
            },{
                text: lang('Event UID'),
                dataIndex: 'event_uid',
                flex: 2
            },{
            	text: lang('Program UID'),
                dataIndex: 'program_uid',
                flex: 2
            },{
            	text: lang('Date Created'),
                dataIndex: 'date_created',
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