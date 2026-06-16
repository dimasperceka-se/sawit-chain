/*
* @Author: nikolius
* @Date:   2018-07-10 13:45:27
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-11 13:17:25
*/

/*
    Param2 yg diperlukan ketika load View ini
    - AuditIMSManager
    - FarmerID
    - GardenNr
    - SurveyNr
    - Certification
    - CallerStore
    - OpsiDisplay
    - ICSDate
*/

Ext.define('Koltiva.view.PlotSurvey.WinFormICSLog' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.PlotSurvey.WinFormICSLog',
    title: lang('ICS Log Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '44%',
    height: '80%',
    cls: 'Sfr_LayoutPopupWindows',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var FormNya = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-Form').getForm();
            FormNya.reset();

            //Set Default Value
            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-Form-FarmerID').setValue(thisObj.viewVar.FarmerID);
            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-Form-SurveyNr').setValue(thisObj.viewVar.SurveyNr);
            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-Form-GardenNr').setValue(thisObj.viewVar.GardenNr);
            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-Form-CertificationProgram').setValue(thisObj.viewVar.Certification);
            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-TabSummary').setDisabled(true);

            if(thisObj.viewVar.OpsiDisplay == 'update' || thisObj.viewVar.OpsiDisplay == 'view'){
            	//Set Default
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-Form-ICSDate').setReadOnly(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-TabSummary').setDisabled(false);

            	//Btn Save
            	if(thisObj.viewVar.OpsiDisplay == 'view'){
                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-Form-BtnSave').setVisible(false);
                }

                //load formnya
                FormNya.load({
                    url: m_api + '/plot_survey/ics_log_form_data',
                    method: 'GET',
                    params: {
                        FarmerID: thisObj.viewVar.FarmerID,
                        GardenNr: thisObj.viewVar.GardenNr,
                        SurveyNr: thisObj.viewVar.SurveyNr,
                        Certification: thisObj.viewVar.Certification,
                        ICSDate: thisObj.viewVar.ICSDate
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        thisObj.StoreGridGardenSummary.setStoreVar({
                            FarmerID: thisObj.viewVar.FarmerID,
                            GardenNr: thisObj.viewVar.GardenNr,
                            SurveyNr: thisObj.viewVar.SurveyNr,
                            Certification: thisObj.viewVar.Certification,
                            ICSDate: thisObj.viewVar.ICSDate
                        });
                        thisObj.StoreGridGardenSummary.load();
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

                //Cek akses darimana
                if(thisObj.viewVar.AuditIMSManager == '1'){
                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-Form-StatusAudit2').setVisible(true);
                }else{
                    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-Form-StatusAudit2').setVisible(false);
                }
            }
        }
    },
    initComponent: function() {
        var thisObj = this;

        // STORE ================================ (Begin)
        var CmbCertProgram = Ext.create('Koltiva.store.ComboGeneral.CmbCertProgramsGeneral');
        var ComboStaffCertification = Ext.create('Koltiva.store.ComboGeneral.CmbStaffCertification');
        thisObj.StoreGridGardenSummary = Ext.create('Koltiva.store.PlotSurvey.GridSurveyGardenSummary');
        // STORE ================================ (End)

        thisObj.ContextMenuGridSummary = Ext.create('Ext.menu.Menu',{
            cls: 'Sfr_ConMenu',
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/application_view_list.png',
                text: lang('View Issue'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-TabSummary-GridSummary').getSelectionModel().getSelection()[0];
                    var WinFormGardenSummaryIssue = Ext.create('Koltiva.view.PlotSurvey.WinFormGardenSummaryIssue',{
                        viewVar:{
                            DaconID: sm.get('DaconID')
                        }
                    });
                    if (!WinFormGardenSummaryIssue.isVisible()) {
                        WinFormGardenSummaryIssue.center();
                        WinFormGardenSummaryIssue.show();
                    } else {
                        WinFormGardenSummaryIssue.close();
                    }
                }
            }]
        });

        //Items -------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'tabpanel',
            flex: 1,
            activeTab: 0,
            plain: true,
            cls:'Sfr_TabForm',
            style:'padding:10px 4px;background-color:white;',
            id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Tab',
            items:[{
                xtype: 'panel',
                title: lang('Form'),
                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-TabForm',
                cls: 'Sfr_PanelSubLayoutForm',
                items:[{
                    xtype: 'form',
                    id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form',
                    fileUpload: true,
                    padding:'5 25 5 8',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 1,
                            layout:'form',
                            items:[{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-FarmerID',
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-FarmerID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-SurveyNr',
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-SurveyNr'
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-GardenNr',
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-GardenNr'
                            },{
                                xtype: 'combobox',
                                fieldLabel: lang('Certification Program'),
                                labelWidth: 325,
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-CertificationProgram',
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-CertificationProgram',
                                store: CmbCertProgram,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                readOnly: true,
                                allowBlank:false
                            },{
                                xtype: 'datefield',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-ICSDate',
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-ICSDate',
                                fieldLabel: lang('ICS Date'),
                                labelWidth: 325,
                                allowBlank: false,
                                format: 'Y-m-d'
                            },{
                                xtype: 'radiogroup',
                                allowBlank: false,
                                fieldLabel: lang('Penilaian dari internal inspektor'),
                                labelWidth: 325,
                                columns: 1,
                                items: [{
                                    boxLabel: lang('Lolos Audit'),
                                    inputValue: '1',
                                    id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-StatusAudit1',
                                    name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-StatusAudit'
                                }, {
                                    boxLabel: lang('Disahkan Dengan Syarat'),
                                    inputValue: '3',
                                    id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-StatusAudit3',
                                    name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-StatusAudit'
                                }, {
                                    boxLabel: lang('Tidak Lolos Audit'),
                                    inputValue: '2',
                                    id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-StatusAudit2',
                                    name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-StatusAudit'
                                }]
                            },{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Masuk wilayah hutan lindung'),
                                labelWidth: 325,
                                columns: 1,
                                items: [{
                                    boxLabel: lang('Yes'),
                                    inputValue: '1',
                                    id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-MasukHutanLindung1',
                                    name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-MasukHutanLindung'
                                }, {
                                    boxLabel: lang('No'),
                                    inputValue: '2',
                                    id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-MasukHutanLindung2',
                                    name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-MasukHutanLindung'
                                }]
                            },{
                                xtype: 'datefield',
                                format: 'Y-m-d',
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-DateRevisionAudit',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-DateRevisionAudit',
                                fieldLabel: lang('Tidak Lolos Audit, Paling lambat perbaikan tanggal'),
                                labelWidth: 325
                            },{
                                xtype: 'textarea',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-CommentAudit',
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-CommentAudit',
                                fieldLabel: lang('Komentar'),
                                labelWidth: 325,
                                allowBlank: false
                            },{
                                xtype: 'textarea',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-RecommendationAudit',
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-RecommendationAudit',
                                fieldLabel: lang('Rekomendasi'),
                                labelWidth: 325
                            },{
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-InspectorID',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-InspectorID',
                                xtype: 'combobox',
                                fieldLabel: lang('Inspector'),
                                store: ComboStaffCertification,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local'
                            },{
                                xtype:'textfield',
                                id:'Koltiva.view.PlotSurvey.WinFormICSLog-Form-InspectorName',
                                name:'Koltiva.view.PlotSurvey.WinFormICSLog-Form-InspectorName',
                                fieldLabel: lang('Inspector Name (If not registered above)'),
                                labelWidth: 325
                            },{
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-AuditCommiteeID',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-AuditCommiteeID',
                                xtype: 'combobox',
                                fieldLabel: lang('Audit Commitee'),
                                store: ComboStaffCertification,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local'
                            },{
                                xtype:'textfield',
                                id:'Koltiva.view.PlotSurvey.WinFormICSLog-Form-AuditCommiteeName',
                                name:'Koltiva.view.PlotSurvey.WinFormICSLog-Form-AuditCommiteeName',
                                fieldLabel: lang('Audit Commitee Name (If not registered above)'),
                                labelWidth: 325
                            },{
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-IMSManagerID',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-IMSManagerID',
                                xtype: 'combobox',
                                fieldLabel: lang('IMS Manager'),
                                store: ComboStaffCertification,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local'
                            },{
                                xtype:'textfield',
                                id:'Koltiva.view.PlotSurvey.WinFormICSLog-Form-IMSManagerName',
                                name:'Koltiva.view.PlotSurvey.WinFormICSLog-Form-IMSManagerName',
                                fieldLabel: lang('IMS Manager Name (If not registered above)'),
                                labelWidth: 325
                            },{
                                xtype: 'textarea',
                                fieldLabel: lang('Tanda Tangan Petani'),
                                labelWidth: 325,
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-FarmerSignature',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-FarmerSignature'
                            }, {
                                xtype: 'textarea',
                                fieldLabel: lang('Tanda Tangan Inspektor'),
                                labelWidth: 325,
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-InspectorSignature',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-InspectorSignature'
                            }, {
                                xtype: 'textarea',
                                fieldLabel: lang('Tanda Tangan Komite'),
                                labelWidth: 325,
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-AuditCommiteeSignature',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-AuditCommiteeSignature'
                            }, {
                                xtype: 'textarea',
                                fieldLabel: lang('Tanda Tangan IMS Manager'),
                                labelWidth: 325,
                                name: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-IMSManagerSignature',
                                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-IMSManagerSignature'
                            }]
                        }]
                    }]
                }]
            },{
                xtype: 'panel',
                title: lang('Summary'),
                id: 'Koltiva.view.PlotSurvey.WinFormICSLog-TabSummary',
                cls: 'Sfr_PanelSubLayoutForm',
                items:[{
                    xtype: 'grid',
                    id: 'Koltiva.view.PlotSurvey.WinFormICSLog-TabSummary-GridSummary',
                    style: 'border:1px solid #CCC;',
                    store: thisObj.StoreGridGardenSummary,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data available')
                    },
                    columns: [{
                        text: ' ',
                        xtype:'actioncolumn',
                        width: '6%',
                        items:[{
                            icon: varjs.config.base_url + 'images/icons/silk/download_arrow.png',
                            tooltip: 'Action',
                            handler: function(grid, rowIndex, colIndex, item, e, record) {
                                thisObj.ContextMenuGridSummary.showAt(e.getXY());
                            }
                        }]
                    },{
                        dataIndex: 'DaconID',
                        hidden:true
                    },{
                        text: lang('Remark Process'),
                        dataIndex: 'RemarkProcess',
                        width: '75%'
                    },{
                        text: lang('Nr of issue'),
                        dataIndex: 'NrOfIssue',
                        width: '18%'
                    }]
                }]
            }]
        }];
        //Items -------------------------------------------------------------- (End)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls:'Sfr_BtnFormBlue',
            overCls:'Sfr_BtnFormBlue-Hover',
            text: lang('Save'),
            id: 'Koltiva.view.PlotSurvey.WinFormICSLog-Form-BtnSave',
            handler: function () {
                var FormNya = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormICSLog-Form').getForm();
                var FormValidOrNot = FormNya.isValid();

                if (FormValidOrNot ==  true) {
                    FormNya.submit({
                        url: m_api + '/plot_survey/ics_log',
                        method:'POST',
                        params: {
                            OpsiDisplay: thisObj.viewVar.OpsiDisplay
                        },
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //form reset
                            FormNya.reset();

                            //refresh store yg manggil
                            thisObj.viewVar.CallerStore.load();

                            //tutup popup
                            thisObj.close();
                        },
                        failure: function(fp, o){
                            var pesanNya;
                            if(o.result.message != undefined){
                                pesanNya = o.result.message;
                            }else{
                                pesanNya = lang('Connection error');
                            }
                            Ext.MessageBox.show({
                                title: 'Attention',
                                msg: pesanNya,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: 'Attention',
                        msg: 'Form not valid yet',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }

            }
        },{
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
			text: lang('Close'),
			cls:'Sfr_BtnFormGrey',
			overCls:'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});