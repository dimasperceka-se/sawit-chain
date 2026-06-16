/*
* @Author: nikolius
* @Date:   2018-07-09 15:20:01
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-11 13:23:04
*/

/*
    Param2 yg diperlukan ketika load View ini
    - FarmerID
    - GardenNr
    - SurveyNr
    - Certification
*/

Ext.define('Koltiva.view.PlotSurvey.GridICSLog' ,{
    extend: 'Ext.grid.Panel',
    id: 'Koltiva.view.PlotSurvey.GridICSLog',
    style:'border:1px solid #CCC;',
    viewConfig: {
        deferEmptyText: false,
        emptyText: lang('No data Available')
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    StatusIsLock: null,
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //Cek StatusIsLock
            Ext.Ajax.request({
                url: m_api + '/plot_survey/grid_ics_log_status_lock',
                method: 'GET',
                params: {
                    MemberID: thisObj.viewVar.FarmerID,
                    PlotNr: thisObj.viewVar.GardenNr,
                    SurveyNr: thisObj.viewVar.SurveyNr,
                },
                success: function(response, action) {
                    var r = Ext.decode(response.responseText);
                    thisObj.StatusIsLock = r.LockStatus;
                    if(r.LockStatus == "Yes"){
                        Ext.getCmp('Koltiva.view.PlotSurvey.GridICSLog-BtnAdd').setVisible(false);
                    }else{
                        Ext.getCmp('Koltiva.view.PlotSurvey.GridICSLog-BtnAdd').setVisible(true);
                    }
                }
            });
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.store = Ext.create('Koltiva.store.PlotSurvey.GridICSLog',{
        	storeVar: {
                FarmerID: thisObj.viewVar.MemberID,
                GardenNr: thisObj.viewVar.PlotNr,
                SurveyNr: thisObj.viewVar.SurveyNr,
                Certification: thisObj.viewVar.Certification
            }
        });

        thisObj.contextMenuGridICSLog = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/silk/application_view_list.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.PlotSurvey.GridICSLog').getSelectionModel().getSelection()[0];
                    var WinFormICSLog = Ext.create('Koltiva.view.PlotSurvey.WinFormICSLog');

                    WinFormICSLog.setViewVar({
                        OpsiDisplay:'view',
                        AuditIMSManager:'0',
                        CallerStore: thisObj.store,
                        FarmerID:thisObj.viewVar.FarmerID,
                        SurveyNr:thisObj.viewVar.SurveyNr,
                        GardenNr:thisObj.viewVar.GardenNr,
                        Certification:thisObj.viewVar.Certification,
                        ICSDate:sm.get('ICSDate')
                    });
                    if (!WinFormICSLog.isVisible()) {
                        WinFormICSLog.center();
                        WinFormICSLog.show();
                    } else {
                        WinFormICSLog.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/silk/pencil.png',
                text: lang('Update'),
                hidden: m_act_update,
                itemId: 'Koltiva.view.PlotSurvey.GridICSLog-CMMenu-Update',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.PlotSurvey.GridICSLog').getSelectionModel().getSelection()[0];
                    var WinFormICSLog = Ext.create('Koltiva.view.PlotSurvey.WinFormICSLog');

                    WinFormICSLog.setViewVar({
                        OpsiDisplay:'update',
                        AuditIMSManager:'0',
                        CallerStore: thisObj.store,
                        FarmerID:thisObj.viewVar.FarmerID,
                        SurveyNr:thisObj.viewVar.SurveyNr,
                        GardenNr:thisObj.viewVar.GardenNr,
                        Certification:thisObj.viewVar.Certification,
                        ICSDate:sm.get('ICSDate')
                    });
                    if (!WinFormICSLog.isVisible()) {
                        WinFormICSLog.center();
                        WinFormICSLog.show();
                    } else {
                        WinFormICSLog.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/silk/pencil.png',
                text: lang('Update Audit IMS Manager'),
                itemId: 'Koltiva.view.PlotSurvey.GridICSLog-CMMenu-UpdateImsManager',
                hidden: m_act_update_audit_imsmanager,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.PlotSurvey.GridICSLog').getSelectionModel().getSelection()[0];
                    var WinFormICSLog = Ext.create('Koltiva.view.PlotSurvey.WinFormICSLog');

                    WinFormICSLog.setViewVar({
                        OpsiDisplay:'update',
                        AuditIMSManager:'1',
                        CallerStore: thisObj.store,
                        FarmerID:thisObj.viewVar.FarmerID,
                        SurveyNr:thisObj.viewVar.SurveyNr,
                        GardenNr:thisObj.viewVar.GardenNr,
                        Certification:thisObj.viewVar.Certification,
                        ICSDate:sm.get('ICSDate')
                    });
                    if (!WinFormICSLog.isVisible()) {
                        WinFormICSLog.center();
                        WinFormICSLog.show();
                    } else {
                        WinFormICSLog.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/silk/delete.png',
                text: lang('Delete'),
                itemId: 'Koltiva.view.PlotSurvey.GridICSLog-CMMenu-Delete',
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.PlotSurvey.GridICSLog').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/farmers/ics_log',
                                method: 'DELETE',
                                params: {
                                    FarmerID:thisObj.viewVar.FarmerID,
			                        SurveyNr:thisObj.viewVar.SurveyNr,
			                        GardenNr:thisObj.viewVar.GardenNr,
			                        Certification:thisObj.viewVar.Certification,
			                        ICSDate:sm.get('ICSDate')
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
                                    thisObj.store.load();
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

        thisObj.dockedItems = [{
            xtype: 'toolbar',
            baseCls: 'bgToolbarTitlePanel',
            dock: 'top',
            items:[{
                xtype: 'tbtext',
                style:'font-weight:bold;text-decoration:underline;line-height:25px;',
                text: lang("List of ICS Audit Logs")
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/silk/lock_edit.png',
                text: lang('Lock ICS Audit Log'),
                hidden: m_act_ics_audit_log_lock,
                style:'margin-right:20px;',
                handler: function() {
                    var WinFormICSLogLock = Ext.create('Koltiva.view.Farmer.WinFormICSLogLock');
                    WinFormICSLogLock.setViewVar({
                        FarmerID:thisObj.viewVar.FarmerID,
                        SurveyNr:thisObj.viewVar.SurveyNr,
                        GardenNr:thisObj.viewVar.GardenNr
                    });
                    if (!WinFormICSLogLock.isVisible()) {
                        WinFormICSLogLock.center();
                        WinFormICSLogLock.show();
                    } else {
                        WinFormICSLogLock.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/silk/add.png',
                text: lang('New Audit Log'),
                hidden: m_act_farmer_new_audit_log,
                id: 'Koltiva.view.PlotSurvey.GridICSLog-BtnAdd',
                handler: function() {
                	var WinFormICSLog = Ext.create('Koltiva.view.PlotSurvey.WinFormICSLog');

                    WinFormICSLog.setViewVar({
                        OpsiDisplay:'insert',
                        AuditIMSManager:'1',
                        CallerStore: thisObj.store,
                        FarmerID:thisObj.viewVar.FarmerID,
                        SurveyNr:thisObj.viewVar.SurveyNr,
                        GardenNr:thisObj.viewVar.GardenNr,
                        Certification:thisObj.viewVar.Certification
                    });
                    if (!WinFormICSLog.isVisible()) {
                        WinFormICSLog.center();
                        WinFormICSLog.show();
                    } else {
                        WinFormICSLog.close();
                    }
                }
            }]
        }];

        thisObj.columns = [{
            text: lang('Action'),
            xtype:'actioncolumn',
            width: '6%',
            items:[{
                icon: varjs.config.base_url + 'images/icons/silk/download_arrow.png',
                tooltip: 'Action',
                handler: function(grid, rowIndex, colIndex, item, e, record) {
                    thisObj.contextMenuGridICSLog.showAt(e.getXY());

                    if(thisObj.StatusIsLock == "Yes"){
                        thisObj.contextMenuGridICSLog.items.get('Koltiva.view.PlotSurvey.GridICSLog-CMMenu-UpdateImsManager').setDisabled(true);
                        thisObj.contextMenuGridICSLog.items.get('Koltiva.view.PlotSurvey.GridICSLog-CMMenu-Update').setDisabled(true);
                        thisObj.contextMenuGridICSLog.items.get('Koltiva.view.PlotSurvey.GridICSLog-CMMenu-Delete').setDisabled(true);
                    }else{
                        //Peraturan defaultnya
                        if(m_act_update == false){
                            thisObj.contextMenuGridICSLog.items.get('Koltiva.view.PlotSurvey.GridICSLog-CMMenu-Update').setDisabled(false);
                        }else{
                            thisObj.contextMenuGridICSLog.items.get('Koltiva.view.PlotSurvey.GridICSLog-CMMenu-Update').setDisabled(true);
                        }
                        if(m_act_delete == false){
                            thisObj.contextMenuGridICSLog.items.get('Koltiva.view.PlotSurvey.GridICSLog-CMMenu-Delete').setDisabled(false);
                        }else{
                            thisObj.contextMenuGridICSLog.items.get('Koltiva.view.PlotSurvey.GridICSLog-CMMenu-Delete').setDisabled(true);
                        }
                        if(m_act_update_audit_imsmanager == false){
                            thisObj.contextMenuGridICSLog.items.get('Koltiva.view.PlotSurvey.GridICSLog-CMMenu-UpdateImsManager').setDisabled(false);
                        }else{
                            thisObj.contextMenuGridICSLog.items.get('Koltiva.view.PlotSurvey.GridICSLog-CMMenu-UpdateImsManager').setDisabled(true);
                        }
                    }
                }
            }]
        },{
            text: 'No',
            xtype: 'rownumberer',
            width: '4%'
        },{
            hidden: true,
            dataIndex: 'Certification'
        },{
            xtype: 'gridcolumn',
            dataIndex: 'CertProgram',
            text: lang('Certification Program'),
            width:'15%'
        },{
            xtype: 'gridcolumn',
            dataIndex: 'ICSDate',
            text: lang('ICS Date'),
            width:'10%'
        },{
            xtype: 'gridcolumn',
            dataIndex: 'StatusAuditName',
            text: lang('Status Audit'),
            width:'14%'
        },{
            xtype: 'gridcolumn',
            dataIndex: 'DateRevisionAudit',
            text: lang('Date Revision'),
            width:'10%'
        },{
        	xtype: 'gridcolumn',
            dataIndex: 'CommentAudit',
            text: lang('Comment'),
            width:'20%'
        },{
        	xtype: 'gridcolumn',
            dataIndex: 'StaffName',
            text: lang('Inspector'),
            width:'19%'
        }];

        this.callParent(arguments);
    }
});