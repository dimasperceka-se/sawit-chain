/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 28 2018
 *  File : WinImsAssetRcp.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinImsAssetRcp' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsAssetRcp',
    title: lang('IMS - Asset Receipt'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '94%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //Ini hak akses button ================================================= (Begin)
        if(thisObj.viewVar.CertEventStatus == '2'){ //Ims Status Completed
            thisObj.m_act_add = true;
            thisObj.m_act_update = true;
            thisObj.m_act_delete = true;
        }else{
            thisObj.m_act_add = m_act_add;
            thisObj.m_act_update = m_act_update;
            thisObj.m_act_delete = m_act_delete;
        }
        //Ini hak akses button ================================================= (End)

        //=== STORE ========================== (Begin)
        thisObj.store_tab_ch = Ext.create('Koltiva.store.IMS.ImsAssetRcpGridRcpChBs',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        thisObj.store_tab_ch_bs_summary = Ext.create('Koltiva.store.IMS.ImsAssetRcpGridRcpChBsSummary',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        thisObj.store_tab_farmer_apd = Ext.create('Koltiva.store.IMS.ImsAssetRcpGridFarmerAPD',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        thisObj.store_tab_farmer_card_rcp = Ext.create('Koltiva.store.IMS.ImsAssetRcpGridFarmerCardRcp',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        thisObj.store_tab_farmer_card_rcp_summary = Ext.create('Koltiva.store.IMS.ImsAssetRcpGridFarmerCardRcpSummary',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID,
                FilterReceivedStatus: 1
            }
        });
        /*Tambah Cek GIT */
        //=== STORE ========================== (End)

        //=== Content Menu ========================== (Begin)
        thisObj.ContextMenuGridCHBS = Ext.create('Ext.menu.Menu',{
            cls: 'Sfr_ConMenu',
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Tab-CHBS').getSelectionModel().getSelection()[0];                                        
                    var WinFormImsAssetRcpChBs = Ext.create('Koltiva.view.IMS.WinFormImsAssetRcpChBs');

                    WinFormImsAssetRcpChBs.setViewVar({
                        OpsiDisplay:'view',
                        CallerStore: thisObj.store_tab_ch,
                        IMSID:thisObj.viewVar.IMSID,
                        RcpID:sm.get('RcpID')
                    });
                    if (!WinFormImsAssetRcpChBs.isVisible()) {
                        WinFormImsAssetRcpChBs.center();
                        WinFormImsAssetRcpChBs.show();
                    } else {
                        WinFormImsAssetRcpChBs.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: thisObj.m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Tab-CHBS').getSelectionModel().getSelection()[0];
                    var WinFormImsAssetRcpChBs = Ext.create('Koltiva.view.IMS.WinFormImsAssetRcpChBs');

                    WinFormImsAssetRcpChBs.setViewVar({
                        OpsiDisplay:'update',
                        CallerStore: thisObj.store_tab_ch,
                        IMSID:thisObj.viewVar.IMSID,
                        RcpID:sm.get('RcpID')
                    });
                    if (!WinFormImsAssetRcpChBs.isVisible()) {
                        WinFormImsAssetRcpChBs.center();
                        WinFormImsAssetRcpChBs.show();
                    } else {
                        WinFormImsAssetRcpChBs.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/user_suit.png',
                text: lang('Receiver Status'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: thisObj.m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Tab-CHBS').getSelectionModel().getSelection()[0];
                    var WinFormImsAssetRcpChBsReceiverStatus = Ext.create('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus');

                    WinFormImsAssetRcpChBsReceiverStatus.setViewVar({
                        CallerStore: thisObj.store_tab_ch,
                        IMSID:thisObj.viewVar.IMSID,                        
                        RcpID:sm.get('RcpID')
                    });
                    if (!WinFormImsAssetRcpChBsReceiverStatus.isVisible()) {
                        WinFormImsAssetRcpChBsReceiverStatus.center();
                        WinFormImsAssetRcpChBsReceiverStatus.show();
                    } else {
                        WinFormImsAssetRcpChBsReceiverStatus.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: thisObj.m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Tab-CHBS').getSelectionModel().getSelection()[0];                    
                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims_asset_rcp/rcp_ch_bs',
                                method: 'DELETE',
                                params: {                                    
                                    RcpID: sm.get('RcpID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    thisObj.store_tab_ch.load();
                                },
                                failure: function(response, o) {
                                    var pesanNya;
                                    if(o.result.message != undefined){
                                        pesanNya = o.result.message;
                                    }else{
                                        pesanNya = lang('Connection error');
                                    }
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: pesanNya,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        }
                    });                    
                }
            }]
        });

        thisObj.ContextMenuGridFarmerAPD = Ext.create('Ext.menu.Menu',{
            cls: 'Sfr_ConMenu',
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/page_edit.png',
                text: lang('Receipt'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerAPD').getSelectionModel().getSelection()[0];
                    var WinFormImsAssetFarmerAPDRcp = Ext.create('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcp',{
                        viewVar: {
                            CallerStore: thisObj.store_tab_farmer_apd,
                            IMSID:thisObj.viewVar.IMSID,
                            FarmerID: sm.get('FarmerID'),
                            FarmerName: sm.get('FarmerName')
                        }
                    });
                    
                    if (!WinFormImsAssetFarmerAPDRcp.isVisible()) {
                        WinFormImsAssetFarmerAPDRcp.center();
                        WinFormImsAssetFarmerAPDRcp.show();
                    } else {
                        WinFormImsAssetFarmerAPDRcp.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: thisObj.m_act_delete,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerAPD').getSelectionModel().getSelection()[0];
                    
                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims_asset_rcp/farmer_apd_sample',
                                method: 'DELETE',
                                params: {
                                    IMSID: thisObj.viewVar.IMSID,
                                    FarmerID: sm.get('FarmerID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    thisObj.store_tab_farmer_apd.load();
                                },
                                failure: function(response, o) {
                                    var pesanNya;
                                    if(o.result.message != undefined){
                                        pesanNya = o.result.message;
                                    }else{
                                        pesanNya = lang('Connection error');
                                    }
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: pesanNya,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        }
                    });
                }
            }]
        });

        thisObj.ContextMenuGridFarmerCardRcp = Ext.create('Ext.menu.Menu',{
            cls: 'Sfr_ConMenu',
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcp').getSelectionModel().getSelection()[0];
                    
                    var WinFormImsAssetFarmerCardRcp = Ext.create('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp',{
                        viewVar:{
                            IMSID : thisObj.viewVar.IMSID,
                            CallerStore: thisObj.store_tab_farmer_card_rcp,
                            OpsiDisplay: 'view',
                            RcpID: sm.get('RcpID')
                        }
                    });
                    if (!WinFormImsAssetFarmerCardRcp.isVisible()) {
                        WinFormImsAssetFarmerCardRcp.center();
                        WinFormImsAssetFarmerCardRcp.show();
                    } else {
                        WinFormImsAssetFarmerCardRcp.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: thisObj.m_act_update,
                cls: 'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcp').getSelectionModel().getSelection()[0];
                    
                    var WinFormImsAssetFarmerCardRcp = Ext.create('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp',{
                        viewVar:{
                            IMSID : thisObj.viewVar.IMSID,
                            CallerStore: thisObj.store_tab_farmer_card_rcp,
                            OpsiDisplay: 'update',
                            RcpID: sm.get('RcpID')
                        }
                    });
                    if (!WinFormImsAssetFarmerCardRcp.isVisible()) {
                        WinFormImsAssetFarmerCardRcp.center();
                        WinFormImsAssetFarmerCardRcp.show();
                    } else {
                        WinFormImsAssetFarmerCardRcp.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/printout_black.png',
                text: lang('Print Receipt'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcp').getSelectionModel().getSelection()[0];
                    preview_cetak_surat(m_api+'/ims_asset_rcp/farmer_card_rcp_printout/'+sm.get('RcpID')+'/'+thisObj.viewVar.IMSID);
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: thisObj.m_act_delete,
                cls: 'Sfr_BtnConMenuWhite',
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcp').getSelectionModel().getSelection()[0];                    
                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims_asset_rcp/farmer_card_rcp',
                                method: 'DELETE',
                                params: {
                                    RcpID: sm.get('RcpID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    thisObj.store_tab_farmer_card_rcp.load();
                                },
                                failure: function(response, o) {
                                    var pesanNya;
                                    if(o.result.message != undefined){
                                        pesanNya = o.result.message;
                                    }else{
                                        pesanNya = lang('Connection error');
                                    }
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: pesanNya,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        }
                    });
                }
            }]
        });
        //=== Content Menu ========================== (End)

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinImsAssetRcp-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',                    
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                        	columnWidth: 0.495,
                            style:'padding-right:25px;',
                            layout:'form',
                            fieldDefaults: {
					            labelWidth: 275
					        },
                            items:[{
                            	xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinImsAssetRcp-Form-CertEventName',
                                name: 'Koltiva.view.IMS.WinImsAssetRcp-Form-CertEventName',
                                fieldLabel: lang('Event Name'),
                                labelWidth: 200,
                                readOnly: true
                            },{
                            	xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinImsAssetRcp-Form-IMSID',
                                name: 'Koltiva.view.IMS.WinImsAssetRcp-Form-IMSID',
                                fieldLabel: lang('Event ID'),
                                labelWidth: 200,
                                readOnly: true
                            },{
                            	xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinImsAssetRcp-Form-Location',
                                name: 'Koltiva.view.IMS.WinImsAssetRcp-Form-Location',
                                fieldLabel: lang('Location'),
                                labelWidth: 200,
                                readOnly: true
                            },{
                            	xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinImsAssetRcp-Form-Year',
                                name: 'Koltiva.view.IMS.WinImsAssetRcp-Form-Year',
                                fieldLabel: lang('Year of Certification'),
                                labelWidth: 200,
                                readOnly: true
                            }]
                        },{
                        	columnWidth: 0.5,
                            style:'padding-right:25px;',
                            layout:'form',
                            items:[{
                            	xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinImsAssetRcp-Form-CertificateHolder',
                                name: 'Koltiva.view.IMS.WinImsAssetRcp-Form-CertificateHolder',
                                fieldLabel: lang('Certificate Holders'),
                                labelWidth: 200,
                                readOnly: true
                            },{
                            	xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinImsAssetRcp-Form-ProgramName',
                                name: 'Koltiva.view.IMS.WinImsAssetRcp-Form-ProgramName',
                                fieldLabel: lang('Program Name'),
                                labelWidth: 200,
                                readOnly: true
                            },{
                            	xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinImsAssetRcp-Form-CertificationBody',
                                name: 'Koltiva.view.IMS.WinImsAssetRcp-Form-CertificationBody',
                                fieldLabel: lang('Certification Body'),
                                labelWidth: 200,
                                readOnly: true
                            },{
                            	xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinImsAssetRcp-Form-FirstBuyer',
                                name: 'Koltiva.view.IMS.WinImsAssetRcp-Form-FirstBuyer',
                                fieldLabel: lang('First Buyer'),
                                labelWidth: 200,
                                readOnly: true
                            }]
                        }]
                    },{
                    	xtype: 'tabpanel',
		                flex: 1,
		                margin: 0,
		                activeTab: 0,
		                plain: true,
		                items: [{
                            xtype: 'gridpanel',
		                    title: lang('CH & BS Receipt'),
		                    id: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-CHBS',
		                    style: 'border:1px solid #CCC;padding-right:3px;',
		                    store: thisObj.store_tab_ch,
                                    cls: 'Sfr_GridNew',
		                    width: '100%',
		                    loadMask: true,
		                    selType: 'rowmodel',
		                    viewConfig: {
		                        deferEmptyText: false,
		                        emptyText: lang('No Data Available')
		                    },
			                dockedItems: [{
			                	xtype: 'pagingtoolbar',
		                        store: thisObj.store_tab_ch,
		                        dock: 'bottom',
		                        displayInfo: true
			                },{
			                	xtype: 'toolbar',
		                		items: [{
                                                    xtype: 'button',
                                                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                                                    margin: '0px 0px 0px 6px',
                                                    hidden: thisObj.m_act_add,
                                                    text: lang('Add Receipt'),
                                                    cls: 'Sfr_BtnGridGreen',
                                                    overCls: 'Sfr_BtnGridGreen-Hover',
                                                    handler: function() {
                                                        var WinFormImsAssetRcpChBs = Ext.create('Koltiva.view.IMS.WinFormImsAssetRcpChBs',{
                                                                                viewVar:{
                                                                IMSID : thisObj.viewVar.IMSID,
                                                                OpsiDisplay: 'insert',
                                                                CallerStore: thisObj.store_tab_ch
                                                                                }
                                                                            });
                                                                            if (!WinFormImsAssetRcpChBs.isVisible()) {
                                                                                WinFormImsAssetRcpChBs.center();
                                                                                WinFormImsAssetRcpChBs.show();
                                                                            } else {
                                                                                WinFormImsAssetRcpChBs.close();
                                                                            }
                                    }
                                }]
                            }],
                            columns: [{
			                    dataIndex: 'RcpID',
			                    hidden: true
			                },{
                                text: lang('Action'),
                                xtype:'actioncolumn',
                                width: '6%',
                                items:[{
                                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                                    tooltip: 'Action',
                                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                                        thisObj.ContextMenuGridCHBS.showAt(e.getXY());
                                    }
                                }]
                            },{
                                text: lang('Transaction Number'),
                                flex: 1,
			                    dataIndex: 'RcpTransNumber'
                            },{
                                text: lang('Receipt Created'),
                                flex: 1,
			                    dataIndex: 'RcpDate'
                            },{
                                text: lang('Remark'),
                                flex: 3,
                                //dataIndex: 'Remark'
                                renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    return '<div style="white-space: normal;">'+data.Remark+'</div>';
                                }
                            },{
                                text: lang('Total Items (Qty)'),
                                flex: 1,
			                    dataIndex: 'TotalItem'
                            },{
                                text: lang('Items Owner'),
                                flex: 3,
			                    renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    var DisplayColumn;

                                    if(data.ItemOwnerLabel != '-'){
                                        var StrProcess = data.ItemOwnerLabel.split("@");

                                        DisplayColumn = '<ul style="margin:0px;padding:0px;">';
                                        for (let i = 0; i < StrProcess.length; i++) {                                            
                                            DisplayColumn += '<li>'+StrProcess[i]+'</li>';    
                                        }
                                        DisplayColumn += '</ul>';
                                    }else{
                                        DisplayColumn = data.ItemOwnerLabel;
                                    }
                                    
                                    return '<div style="white-space: normal;">'+DisplayColumn+'</div>';
                                }
                            },{
                                text: lang('Receiver Status'),
                                flex: 1,              
                                renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    var DisplayColumn = '-';

                                    switch(data.ReceiverStatusRaw){
                                        case '1':
                                            DisplayColumn = '<span style="color:green;">'+data.ReceiverStatus+'</span>';
                                        break;
                                        case '2':
                                            DisplayColumn = '<span style="color:red;">'+data.ReceiverStatus+'</span>';
                                        break;
                                    }

                                    return '<div>'+DisplayColumn+'</div>';
                                }
                            },{
                                text: lang('Created By'),
                                flex: 1,
                                dataIndex: 'CreatedBy'
                            }]
                        },{
                            xtype: 'gridpanel',
		                    title: lang('CH & BS Summary Assets'),
		                    id: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-CHBSSumAss',
		                    style: 'border:1px solid #CCC;padding-right:3px;',
                                    cls: 'Sfr_GridNew',
		                    store: thisObj.store_tab_ch_bs_summary,
		                    width: '100%',
		                    loadMask: true,
		                    selType: 'rowmodel',
		                    viewConfig: {
		                        deferEmptyText: false,
		                        emptyText: lang('No Data Available')
		                    },			                
                            columns: [{
                                text: lang('Type'),
                                width: '6%',
                                dataIndex: 'UserAssType'
			                },{
                                text: lang('Asset User'),
                                flex: 1,
                                dataIndex: 'UserAssLabel'
                            },{
                                text: lang('Total Items'),
                                width: '15%',
                                dataIndex: 'TotalItems'
                            },{
                                text: lang('Detail Items'),
                                flex: 1,
                                //dataIndex: 'DetailItems'
                                renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    var DisplayColumn;

                                    if(data.DetailItems != '-'){
                                        var StrProcess = data.DetailItems.split("@");

                                        DisplayColumn = '<ul style="margin:0px;padding:0px;">';
                                        for (let i = 0; i < StrProcess.length; i++) {                                            
                                            DisplayColumn += '<li>'+StrProcess[i]+'</li>';    
                                        }
                                        DisplayColumn += '</ul>';
                                    }else{
                                        DisplayColumn = data.DetailItems;
                                    }
                                    
                                    return '<div style="white-space: normal;">'+DisplayColumn+'</div>';
                                }
                            }]
                        },{
                            xtype: 'gridpanel',
		                    title: lang('Farmer APD'),
		                    id: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerAPD',
		                    style: 'border:1px solid #CCC;padding-right:3px;',
                                    cls: 'Sfr_GridNew',
		                    store: thisObj.store_tab_farmer_apd,
		                    width: '100%',
		                    loadMask: true,
		                    selType: 'rowmodel',
		                    viewConfig: {
		                        deferEmptyText: false,
		                        emptyText: lang('No Data Available')
                            },
                            dockedItems: [{
                                xtype: 'toolbar',
                                    items: [{
                                    xtype: 'button',
                                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                                    margin: '0px 0px 0px 6px',
                                    cls: 'Sfr_BtnFormGreen',
                                    overCls: 'Sfr_BtnFormGreen-Hover',
                                    hidden: thisObj.m_act_add,
                                    text: lang('Add Farmer Sample'),
                                    handler: function() {
                                        var WinFormImsAssetFarmerAPDAddSample = Ext.create('Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample',{
					                    	viewVar:{
                                                IMSID : thisObj.viewVar.IMSID,
                                                CallerStore: thisObj.store_tab_farmer_apd
					                    	}
					                    });
					                    if (!WinFormImsAssetFarmerAPDAddSample.isVisible()) {
					                        WinFormImsAssetFarmerAPDAddSample.center();
					                        WinFormImsAssetFarmerAPDAddSample.show();
					                    } else {
					                        WinFormImsAssetFarmerAPDAddSample.close();
					                    }
                                    }
                                }]
                            }],
                            columns: [{
                                text: lang('Action'),
                                xtype:'actioncolumn',
                                width: '6%',
                                items:[{
                                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                                    tooltip: 'Action',
                                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                                        thisObj.ContextMenuGridFarmerAPD.showAt(e.getXY());
                                    }
                                }]
                            },{
                                text: lang('Farmer ID'),
                                flex: 1,
			                    dataIndex: 'FarmerID'
                            },{
                                text: lang('Name'),
                                flex: 2,
			                    dataIndex: 'FarmerName'
                            },{
                                text: lang('Gender'),
                                flex: 1,
			                    dataIndex: 'Gender'
                            },{
                                text: lang('Farmer Group'),
                                flex: 2,
			                    dataIndex: 'FarmerGroup'
                            },{
                                text: lang('APD - Kotak Pestisida'),
                                flex: 1,
                                //dataIndex: 'KotakPestisida'
                                renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    var DisplayColumn;
            
                                    if(parseInt(data.KotakPestisida) > 0){
                                        DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                                    }else{
                                        DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                                    }
            
                                    return DisplayColumn;
                                }
                            },{
                                text: lang('APD - Masker'),
                                flex: 1,
			                    //dataIndex: 'Masker'
                                renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    var DisplayColumn;
            
                                    if(parseInt(data.Masker) > 0){
                                        DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                                    }else{
                                        DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                                    }
            
                                    return DisplayColumn;
                                }
                            },{
                                text: lang('APD - Sarung Tangan'),
                                flex: 1,
                                //dataIndex: 'SarungTangan'
                                renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    var DisplayColumn;
            
                                    if(parseInt(data.SarungTangan) > 0){
                                        DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                                    }else{
                                        DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                                    }
            
                                    return DisplayColumn;
                                }
                            },{
                                text: lang('APD - Goggles'),
                                flex: 1,
                                //dataIndex: 'Goggles'
                                renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    var DisplayColumn;
            
                                    if(parseInt(data.Goggles) > 0){
                                        DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                                    }else{
                                        DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                                    }
            
                                    return DisplayColumn;
                                }
                            },{
                                text: lang('APD - Boots'),
                                flex: 1,
                                //dataIndex: 'Boots'
                                renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    var DisplayColumn;
            
                                    if(parseInt(data.Boots) > 0){
                                        DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                                    }else{
                                        DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                                    }
            
                                    return DisplayColumn;
                                }
                            },{
                                text: lang('APD - Mantel'),
                                flex: 1,
                                //dataIndex: 'Mantel'
                                renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    var DisplayColumn;
            
                                    if(parseInt(data.Mantel) > 0){
                                        DisplayColumn = '<div align="center" style="white-space: normal;"><input type="checkbox" checked="" disabled /></div>';
                                    }else{
                                        DisplayColumn = '<div align="center" style="white-space: normal;">-</div>';
                                    }
            
                                    return DisplayColumn;
                                }
                            }]
                        },{
                            xtype: 'gridpanel',
		                    title: lang('Farmer ID Card Receipt'),
		                    id: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcp',
		                    style: 'border:1px solid #CCC;padding-right:3px;',
                                    cls: 'Sfr_GridNew',
		                    store: thisObj.store_tab_farmer_card_rcp,
		                    width: '100%',
		                    loadMask: true,
		                    selType: 'rowmodel',
		                    viewConfig: {
		                        deferEmptyText: false,
		                        emptyText: lang('No Data Available')
                            },
                            dockedItems: [{
                                xtype: 'toolbar',
		                		items: [{
                                    xtype: 'button',
                                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                                    margin: '0px 0px 0px 6px',
                                    hidden: thisObj.m_act_add,
                                    text: lang('Add Receipt'),
                                    cls: 'Sfr_BtnGridGreen',
                                    overCls: 'Sfr_BtnGridGreen-Hover',
                                    handler: function() {
                                        var WinFormImsAssetFarmerCardRcp = Ext.create('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp',{
					                    	viewVar:{
                                                IMSID : thisObj.viewVar.IMSID,
                                                CallerStore: thisObj.store_tab_farmer_card_rcp,
                                                OpsiDisplay: 'insert'
					                    	}
					                    });
					                    if (!WinFormImsAssetFarmerCardRcp.isVisible()) {
					                        WinFormImsAssetFarmerCardRcp.center();
					                        WinFormImsAssetFarmerCardRcp.show();
					                    } else {
					                        WinFormImsAssetFarmerCardRcp.close();
					                    }
                                    }
                                }]
                            }],
                            columns: [{
                                text: lang('Action'),
                                xtype:'actioncolumn',
                                width: '6%',
                                items:[{
                                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                                    tooltip: 'Action',
                                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                                        thisObj.ContextMenuGridFarmerCardRcp.showAt(e.getXY());
                                    }
                                }]
                            },{
                                dataIndex: 'RcpID',
			                    hidden: true
                            },{
                                text: lang('Transaction Number'),
                                flex: 1,
			                    dataIndex: 'RcpTransNumber'
                            },{
                                text: lang('Receipt Date'),
                                flex: 1,
			                    dataIndex: 'RcpDate'
                            },{
                                text: lang('Remark'),
                                flex: 2,
                                //dataIndex: 'Remark'
                                renderer: function (t, meta, record) {
                                    var data = record.getData();
                                    return '<div style="white-space: normal;">'+data.Remark+'</div>';
                                }
                            },{
                                text: lang('Total Farmers'),
                                flex: 1,
			                    dataIndex: 'TotalFarmers'
                            },{
                                text: lang('Total Farmers Received Card'),
                                flex: 2,
			                    dataIndex: 'TotalFarmersRecCard'
                            },{
                                text: lang('File Upload Status'),
                                flex: 1,
			                    dataIndex: 'StatusFileUpload'
                            }]
                        },{
                            xtype: 'gridpanel',
		                    title: lang('Farmer ID Card Receipt Summary'),
		                    id: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcpSum',
		                    style: 'border:1px solid #CCC;padding-right:3px;',
                                    cls: 'Sfr_GridNew',
		                    store: thisObj.store_tab_farmer_card_rcp_summary,
		                    width: '100%',
		                    loadMask: true,
		                    selType: 'rowmodel',
		                    viewConfig: {
		                        deferEmptyText: false,
		                        emptyText: lang('No Data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
		                        store: thisObj.store_tab_farmer_card_rcp_summary,
		                        dock: 'bottom',
		                        displayInfo: true
                            },{
                                xtype: 'toolbar',
                                items: [{
                                    xtype:'tbspacer',
                                    flex:1
                                },{
                                    xtype:'radiofield',
                                    name: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcpSum-RdRecStatus',
                                    id: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcpSum-RdRecStatus1',
                                    boxLabel: lang('Received ID'),
                                    checked:true,
                                    style:'margin-right:25px;',
                                    value:'1',
                                    listeners:{
                                        change: function(){
                                            if(this.checked == true){
                                                thisObj.store_tab_farmer_card_rcp_summary.storeVar.FilterReceivedStatus = '1';
                                                thisObj.store_tab_farmer_card_rcp_summary.load();
                                            }
                                        }
                                    }
                                },{
                                    xtype:'radiofield',
                                    name: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcpSum-RdRecStatus',
                                    id: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcpSum-RdRecStatus2',
                                    boxLabel: lang('Not Received Yet'),
                                    value:'2',
                                    style:'margin-right:25px;',
                                    listeners:{
                                        change: function(){
                                            if(this.checked == true){
                                                thisObj.store_tab_farmer_card_rcp_summary.storeVar.FilterReceivedStatus = '2';
                                                thisObj.store_tab_farmer_card_rcp_summary.load();
                                            }
                                        }
                                    }
                                },{
                                    xtype:'radiofield',
                                    name: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcpSum-RdRecStatus',
                                    id: 'Koltiva.view.IMS.WinImsAssetRcp-Tab-FarmerCardRcpSum-RdRecStatus3',
                                    boxLabel: lang('Not Assign to any Receipt'),
                                    value:'null',
                                    listeners:{
                                        change: function(){
                                            if(this.checked == true){
                                                thisObj.store_tab_farmer_card_rcp_summary.storeVar.FilterReceivedStatus = 'null';
                                                thisObj.store_tab_farmer_card_rcp_summary.load();
                                            }
                                        }
                                    }
                                }]
                            }],
                            columns: [{
			                    text: lang('Farmer ID'),
                                flex: 1,
                                dataIndex: 'FarmerID'
			                },{
                                text: lang('Name'),
                                flex: 2,
                                dataIndex: 'FarmerName'
                            },{
                                text: lang('Gender'),
                                flex: 1,
                                dataIndex: 'Gender'
                            },{
                                text: lang('Farmer Group'),
                                flex: 2,
                                dataIndex: 'FarmerGroup'
                            },{
                                text: lang('Sub District'),
                                flex: 1,
                                dataIndex: 'SubDistrict'
                            },{
                                text: lang('Village'),
                                flex: 1,
                                dataIndex: 'Village'
                            },{
                                text: lang('Received Status'),
                                flex: 1,
                                dataIndex: 'ReceivedStatus'
                            }]
                        }]
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function() {
                    thisObj.close();
                }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.IMS.WinImsAssetRcp-Form');
            formNya.getForm().reset();

            //load nilainya
            formNya.getForm().load({
                url: m_api + '/ims_asset_rcp/asset_rcp_get_form',
                method: 'GET',
                params: {
                    IMSID: thisObj.viewVar.IMSID,
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                    //console.log(r);                    
                },
                failure: function(form, action) {
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Failed to retrieve data',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });
        }
    }
});