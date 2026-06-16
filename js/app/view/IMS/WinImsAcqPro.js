/*
* @Author: nikolius
* @Date:   2018-03-15 13:57:57
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-27 14:38:17
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinImsAcqPro' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsAcqPro',
    title: lang('IMS - Acquisition Process'),
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

        //store (Begin)
        var store_tab_farmer_identification = Ext.create('Koltiva.store.IMS.AcqProGridFarmerIdentification', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        var store_tab_socialization = Ext.create('Koltiva.store.IMS.AcqProGridSocialization', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        var store_tab_selection = Ext.create('Koltiva.store.IMS.AcqProGridSelection', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        var store_tab_selection_approved = Ext.create('Koltiva.store.IMS.AcqProGridSelectionApproved', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        var store_tab_training = Ext.create('Koltiva.store.IMS.AcqProGridTraining', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        var store_tab_training_approved = Ext.create('Koltiva.store.IMS.AcqProGridTrainingApproved', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        var store_tab_candidate_preics = Ext.create('Koltiva.store.IMS.AcqProGridCandidatePreICS', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });
        //store (End)

        thisObj.contextMenuGridFarmerIdentification = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-FarmerIdentification').getSelectionModel().getSelection()[0];

                        var WinRegisterAppForm = Ext.create('Koltiva.view.application_form.WinRegisterAppFormViewOnly', {
                            viewVar: {
                                opsiDisplay: 'view',
                                typeStatus: 'recomended',
                                ApplicantID: sm.get('ApplicantID')
                            }
                        });
                        if (!WinRegisterAppForm.isVisible()) {
                            WinRegisterAppForm.center();
                            WinRegisterAppForm.show();
                        } else {
                            WinRegisterAppForm.close();
                        }
                    }
                }]
        });

        thisObj.contextMenuGridSocialization = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Socialization').getSelectionModel().getSelection()[0];

                        var WinRegisterAppForm = Ext.create('Koltiva.view.application_form.WinRegisterAppFormViewOnly', {
                            viewVar: {
                                opsiDisplay: 'view',
                                socializationInfo: true,
                                IMSSocID: sm.get('IMSSocID'),
                                typeStatus: 'recomended',
                                ApplicantID: sm.get('ApplicantID'),
                                ParticipateInSocialization: sm.get('ParticipateInSocialization')
                            }
                        });
                        if (!WinRegisterAppForm.isVisible()) {
                            WinRegisterAppForm.center();
                            WinRegisterAppForm.show();
                        } else {
                            WinRegisterAppForm.close();
                        }
                    }
                }]
        });

        thisObj.contextMenuGridSelection = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection').getSelectionModel().getSelection()[0];

                        var WinRegisterAppForm = Ext.create('Koltiva.view.application_form.WinRegisterAppFormViewOnly', {
                            viewVar: {
                                opsiDisplay: 'view',
                                socializationInfo: true,
                                selectionInfo: true,
                                IMSSocID: sm.get('IMSSocID'),
                                typeStatus: 'recomended',
                                ApplicantID: sm.get('ApplicantID'),
                                ParticipantID: sm.get('ParticipantID')
                            }
                        });
                        if (!WinRegisterAppForm.isVisible()) {
                            WinRegisterAppForm.center();
                            WinRegisterAppForm.show();
                        } else {
                            WinRegisterAppForm.close();
                        }
                    }
                }]
        });

        thisObj.contextMenuGridTraining = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('Detail'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training').getSelectionModel().getSelection()[0];

                        var WinDetailTrainingAcq = Ext.create('Koltiva.view.IMS.WinDetailTrainingAcq', {
                            viewVar: {
                                IMSID: thisObj.viewVar.IMSID,
                                FarmerID: sm.get('FarmerID'),
                                ApplicantDisplayID: sm.get('DisplayID'),
                                CallerStore: store_tab_training
                            }
                        });
                        if (!WinDetailTrainingAcq.isVisible()) {
                            WinDetailTrainingAcq.center();
                            WinDetailTrainingAcq.show();
                        } else {
                            WinDetailTrainingAcq.close();
                        }
                    }
                }]
        });

        thisObj.items = [{
                xtype: 'form',
                id: 'Koltiva.view.IMS.WinImsAcqPro-Form',
                fileUpload: true,
                padding: '5 25 5 8',
                items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                                columnWidth: 1,
                                layout: 'form',
                                style: '',
                                items: [{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 0.495,
                                                style: 'padding-right:25px;',
                                                layout: 'form',
                                                fieldDefaults: {
                                                    labelWidth: 275
                                                },
                                                items: [{
                                                        xtype: 'hiddenfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-SigningLockSocSelBy',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-SigningLockSocSelBy'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertEventName',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertEventName',
                                                        fieldLabel: lang('Event Name'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-IMSID',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-IMSID',
                                                        fieldLabel: lang('Event ID'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-Location',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-Location',
                                                        fieldLabel: lang('Location'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-Year',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-Year',
                                                        fieldLabel: lang('Year of Certification'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-SocSelPeriodLabel',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-SocSelPeriodLabel',
                                                        fieldLabel: lang('Socialization Selection Period'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }]
                                            }, {
                                                columnWidth: 0.5,
                                                style: 'padding-right:25px;',
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertificateHolder',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertificateHolder',
                                                        fieldLabel: lang('Certificate Holders'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-ProgramName',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-ProgramName',
                                                        fieldLabel: lang('Program Name'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertificationBody',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-CertificationBody',
                                                        fieldLabel: lang('Certification Body'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-FirstBuyer',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-FirstBuyer',
                                                        fieldLabel: lang('First Buyer'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsAcqPro-Form-TrainingPeriodLabel',
                                                        name: 'Koltiva.view.IMS.WinImsAcqPro-Form-TrainingPeriodLabel',
                                                        fieldLabel: lang('Training Period'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }]
                                            }]
                                    }, {
                                        xtype: 'tabpanel',
                                        flex: 1,
                                        margin: 0,
                                        activeTab: 0,
                                        plain: true,
                                        items: [{
                                                xtype: 'gridpanel',
                                                title: lang('Farmer Identification'),
                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-FarmerIdentification',
                                                style: 'border:1px solid #CCC;padding-right:3px;',
                                                store: store_tab_farmer_identification,
                                                cls: 'Sfr_GridNew',
                                                width: '100%',
                                                minHeight:125,
                                                loadMask: true,
                                                selType: 'rowmodel',
                                                viewConfig: {
                                                    deferEmptyText: false,
                                                    emptyText: lang('No data Available')
                                                },
                                                dockedItems: [{
                                                        xtype: 'pagingtoolbar',
                                                        store: store_tab_farmer_identification,
                                                        dock: 'bottom',
                                                        displayInfo: true
                                                    }, {
                                                        xtype: 'toolbar',
                                                        items: [{
                                                                xtype: 'textfield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-FarmerIdentification-StringSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-FarmerIdentification-StringSearch',
                                                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                                                emptyText: lang('Applicant ID / Applicant Name'),
                                                                width: 280
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                                margin: '0px 10px 0px 6px',
                                                                text: lang('Search'),
                                                                cls: 'Sfr_BtnGridBlue',
                                                                overCls: 'Sfr_BtnGridBlue-Hover',
                                                                handler: function () {
                                                                    store_tab_farmer_identification.setStoreVar({
                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                        StringSearch: Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-FarmerIdentification-StringSearch').getValue()
                                                                    });
                                                                    store_tab_farmer_identification.load();
                                                                }
                                                            },
                                                            {
                                                                icon: varjs.config.base_url + 'images/icons/new/export.png',
                                                                text: lang('Export All'),
                                                                cls: 'Sfr_BtnGridPaleBlue',
                                                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                                                handler: function () {
                                                                    var IMSID = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-IMSID').getValue();
                                                                    var title = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-CertEventName').getValue();
                                                                    url = m_api + '/ims/exportFarmeridentification/' + IMSID;
                                                                    if (window.open(url, 'cetak', "height=200,width=200")) {
                                                                        Ext.MessageBox.hide();
                                                                    }
                                                                }
                                                            }]
                                                    }],
                                                columns: [{
                                                        text: '',
                                                        xtype: 'actioncolumn',
                                                        width: '4%',
                                                        items: [{
                                                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                                                    thisObj.contextMenuGridFarmerIdentification.showAt(e.getXY());
                                                                }
                                                            }]
                                                    }, {
                                                        dataIndex: 'ApplicantID',
                                                        hidden: true
                                                    }, {
                                                        text: 'No',
                                                        xtype: 'rownumberer',
                                                        align: 'center',
                                                        width: '3%'
                                                    }, {
                                                        text: lang('ID'),
                                                        width: '7%',
                                                        dataIndex: 'DisplayID'
                                                    }, {
                                                        text: lang('Name'),
                                                        flex: 3,
                                                        dataIndex: 'ApplicantName'
                                                    }, {
                                                        text: lang('Gender'),
                                                        width: '7%',
                                                        dataIndex: 'Gender'
                                                    }, {
                                                        text: lang('District'),
                                                        flex: 2,
                                                        dataIndex: 'District'
                                                    }, {
                                                        text: lang('SubDistrict'),
                                                        flex: 2,
                                                        dataIndex: 'SubDistrict'
                                                    }, {
                                                        text: lang('Village'),
                                                        flex: 2,
                                                        dataIndex: 'Village'
                                                    }, {
                                                        text: lang('Farmer Group'),
                                                        flex: 3,
                                                        dataIndex: 'FarmerGroup'
                                                    }, {
                                                        text: lang('Status'),
                                                        width: '9%',
                                                        dataIndex: 'ApplicantStatus'
                                                    }]
//                                                , listeners: {
//                                                    itemclick: function (view, record, item, index, e) {
//                                                        thisObj.contextMenuGridFarmerIdentification.showAt(e.getXY());
//                                                    }
//                                                }
                                            }, {
                                                xtype: 'gridpanel',
                                                title: lang('Socialization'),
                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Socialization',
                                                style: 'border:1px solid #CCC;padding-right:3px;',
                                                store: store_tab_socialization,
                                                cls: 'Sfr_GridNew',
                                                width: '100%',
                                                minHeight:125,
                                                loadMask: true,
                                                selType: 'rowmodel',
                                                viewConfig: {
                                                    deferEmptyText: false,
                                                    emptyText: lang('No data Available')
                                                },
                                                dockedItems: [{
                                                        xtype: 'pagingtoolbar',
                                                        store: store_tab_socialization,
                                                        dock: 'bottom',
                                                        displayInfo: true
                                                    }, {
                                                        xtype: 'toolbar',
                                                        items: [{
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/success-white.png',
                                                                margin: '0px 0px 0px 6px',
                                                                text: lang('Approval'),
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Socialization-BtnApprove',
                                                                hidden: true,
                                                                handler: function () {
                                                                    var WinFormApprovalSocia = Ext.create('Koltiva.view.IMS.WinFormApprovalSocia', {
                                                                        viewVar: {
                                                                            IMSID: thisObj.viewVar.IMSID
                                                                        }
                                                                    });
                                                                    if (!WinFormApprovalSocia.isVisible()) {
                                                                        WinFormApprovalSocia.center();
                                                                        WinFormApprovalSocia.show();
                                                                    } else {
                                                                        WinFormApprovalSocia.close();
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/user_tick_white.png',
                                                                margin: '0px 10px 0px 6px',
                                                                text: lang('Generate Candidate for Socialization Selection'),
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnGenSocSel',
                                                                hidden: m_act_gen_soc_sel,
                                                                handler: function () {
                                                                    var myMsgBox = new Ext.window.MessageBox();
                                                                    myMsgBox.textField.width = 300;
                                                                    myMsgBox.textField.center();
                                                                    myMsgBox.prompt(lang('Information'), lang('Please fill in information on who processing this action') + ':', function (btn, RemarkText) {
                                                                        if (btn == 'ok') {
                                                                            if (RemarkText != '') {
                                                                                Ext.MessageBox.show({
                                                                                    msg: 'Please wait...',
                                                                                    progressText: 'Generating...',
                                                                                    width: 300,
                                                                                    wait: true,
                                                                                    waitConfig: {
                                                                                        interval: 200
                                                                                    },
                                                                                    icon: 'ext-mb-info', //custom class in msg-box.html
                                                                                    animateTarget: 'mb9'
                                                                                });

                                                                                Ext.Ajax.request({
                                                                                    url: m_api + '/ims/acq_process_generate_soc_sel',
                                                                                    method: 'POST',
                                                                                    params: {
                                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                                        RemarkText: RemarkText
                                                                                    },
                                                                                    success: function (response, action) {
                                                                                        Ext.MessageBox.hide();
                                                                                        try {
                                                                                            JSON.parse(response.responseText);
                                                                                        } catch (error) {
                                                                                            Ext.MessageBox.show({
                                                                                                title: 'Failed',
                                                                                                msg: 'Network Connection Error',
                                                                                                buttons: Ext.MessageBox.OK,
                                                                                                animateTarget: 'mb9',
                                                                                                icon: 'ext-mb-error'
                                                                                            });
                                                                                            return false;
                                                                                        }

                                                                                        var objRet = Ext.decode(response.responseText);
                                                                                        switch (objRet.success) {
                                                                                            case true:
                                                                                                Ext.MessageBox.show({
                                                                                                    title: 'Information',
                                                                                                    msg: objRet.message,
                                                                                                    buttons: Ext.MessageBox.OK,
                                                                                                    animateTarget: 'mb9',
                                                                                                    icon: 'ext-mb-success'
                                                                                                });
                                                                                                store_tab_socialization.load();
                                                                                                store_tab_selection.load();
                                                                                                break;
                                                                                            case false:
                                                                                                Ext.MessageBox.show({
                                                                                                    title: 'Failed',
                                                                                                    msg: objRet.message,
                                                                                                    buttons: Ext.MessageBox.OK,
                                                                                                    animateTarget: 'mb9',
                                                                                                    icon: 'ext-mb-error'
                                                                                                });
                                                                                                break;
                                                                                        }
                                                                                    },
                                                                                    failure: function (response, action) {
                                                                                        Ext.MessageBox.hide();
                                                                                        Ext.MessageBox.show({
                                                                                            title: 'Failed',
                                                                                            msg: 'Network Connection Error',
                                                                                            buttons: Ext.MessageBox.OK,
                                                                                            animateTarget: 'mb9',
                                                                                            icon: 'ext-mb-error'
                                                                                        });
                                                                                    }
                                                                                });
                                                                            } else {
                                                                                Ext.MessageBox.show({
                                                                                    title: 'Information',
                                                                                    msg: lang('Information must be filled'),
                                                                                    buttons: Ext.MessageBox.OK,
                                                                                    animateTarget: 'mb9',
                                                                                    icon: 'ext-mb-info'
                                                                                });
                                                                            }
                                                                        }
                                                                    });
                                                                }
                                                            }, {
                                                                xtype: 'tbseparator',
                                                                style: 'margin:0px 20px;'
                                                            }, {
                                                                xtype: 'textfield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Socialization-StringSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Socialization-StringSearch',
                                                                emptyText: lang('Applicant ID / Applicant Name'),
                                                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                                                width: 280
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                                cls: 'Sfr_BtnGridBlue',
                                                                overCls: 'Sfr_BtnGridBlue-Hover',
                                                                margin: '0px 10px 0px 6px',
                                                                text: lang('Search'),
                                                                handler: function () {
                                                                    store_tab_socialization.setStoreVar({
                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                        StringSearch: Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Socialization-StringSearch').getValue()
                                                                    });
                                                                    store_tab_socialization.load();
                                                                    store_tab_selection.load();
                                                                }
                                                            }, {
                                                                icon: varjs.config.base_url + 'images/icons/new/export.png',
                                                                text: lang('Export All'),
                                                                cls: 'Sfr_BtnGridPaleBlue',
                                                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                                                handler: function () {
                                                                    var IMSID = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-IMSID').getValue();
                                                                    var title = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-CertEventName').getValue();
                                                                    var StringSearch = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Socialization-StringSearch').getValue();
                                                                    url = m_api + '/ims/exportFarmersocialization/' + IMSID + '/' + title + '/' + StringSearch;
                                                                    if (window.open(url, 'cetak', "height=200,width=200")) {
                                                                        Ext.MessageBox.hide();
                                                                    }
                                                                }
                                                            }]
                                                    }],
                                                columns: [{
                                                        text: 'No',
                                                        xtype: 'rownumberer',
                                                        align: 'center',
                                                        width: '3%'
                                                    }, {
                                                        text: lang('ID'),
                                                        flex: 1,
                                                        dataIndex: 'DisplayID'
                                                    }, {
                                                        text: lang('Farmer ID'),
                                                        flex: 1,
                                                        dataIndex: 'DestObjID'
                                                    }, {
                                                        text: lang('Name'),
                                                        flex: 3,
                                                        dataIndex: 'Name'
                                                    }, {
                                                        text: lang('Gender'),
                                                        flex: 0.5,
                                                        dataIndex: 'Gender'
                                                    }, {
                                                        text: lang('SubDistrict'),
                                                        flex: 2,
                                                        dataIndex: 'SubDistrict'
                                                    }, {
                                                        text: lang('Village'),
                                                        flex: 2,
                                                        dataIndex: 'Village'
                                                    }, {
                                                        text: lang('Farmer Group'),
                                                        flex: 3,
                                                        dataIndex: 'FarmerGroup'
                                                    }, {
                                                        text: lang('Socialization Event Name'),
                                                        flex: 3,
                                                        dataIndex: 'SocEventName'
                                                    }, {
                                                        text: lang('Date of Socialization'),
                                                        flex: 1,
                                                        dataIndex: 'DateOfSocialization'
                                                    }, {
                                                        text: lang('Date Generated'),
                                                        flex: 1,
                                                        dataIndex: 'DateGenerated'
                                                    }],
                                                listeners: {
                                                    itemclick: function (view, record, item, index, e) {
                                                        //Sementara Hilangkan Dulu
                                                        //thisObj.contextMenuGridSocialization.showAt(e.getXY());
                                                    }
                                                }
                                            }, {
                                                xtype: 'gridpanel',
                                                title: lang('Selection'),
                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection',
                                                style: 'border:1px solid #CCC;padding-right:3px;',
                                                cls: 'Sfr_GridNew',
                                                store: store_tab_selection,
                                                width: '100%',
                                                minHeight:125,
                                                loadMask: true,
                                                selType: 'rowmodel',
                                                viewConfig: {
                                                    deferEmptyText: false,
                                                    emptyText: lang('No data Available')
                                                },
                                                dockedItems: [{
                                                        xtype: 'pagingtoolbar',
                                                        store: store_tab_selection,
                                                        dock: 'bottom',
                                                        displayInfo: true
                                                    }, {
                                                        xtype: 'toolbar',
                                                        items: [{
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                                                margin: '0px 10px 0px 6px',
                                                                text: lang('Import Existing Certified Farmer (Excel)'),
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-BtnImportCertFarmer',
                                                                hidden: m_act_add,
                                                                handler: function () {
                                                                    var WinImsAcqProImportCertFarmer = Ext.create('Koltiva.view.IMS.WinImsAcqProImportCertFarmer', {
                                                                        viewVar: {
                                                                            IMSID: thisObj.viewVar.IMSID,
                                                                            CallerStore: store_tab_selection
                                                                        }
                                                                    });
                                                                    if (!WinImsAcqProImportCertFarmer.isVisible()) {
                                                                        WinImsAcqProImportCertFarmer.center();
                                                                        WinImsAcqProImportCertFarmer.show();
                                                                    } else {
                                                                        WinImsAcqProImportCertFarmer.close();
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/success-white.png',
                                                                margin: '0px 10px 0px 6px',
                                                                text: lang('Approval'),
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-BtnApprove',
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                hidden: m_act_approval,
                                                                handler: function () {
                                                                    var WinFormApprovalSelec = Ext.create('Koltiva.view.IMS.WinFormApprovalSelec', {
                                                                        viewVar: {
                                                                            IMSID: thisObj.viewVar.IMSID,
                                                                            CallerStoreSocialization: store_tab_socialization,
                                                                            CallerStoreSelection: store_tab_selection,
                                                                            CallerStoreSelectionApproved: store_tab_selection_approved
                                                                        }
                                                                    });
                                                                    if (!WinFormApprovalSelec.isVisible()) {
                                                                        WinFormApprovalSelec.center();
                                                                        WinFormApprovalSelec.show();
                                                                    } else {
                                                                        WinFormApprovalSelec.close();
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'tbseparator',
                                                                style: 'margin:0px 10px;',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-BtnApproveSeparator',
                                                                hidden: m_act_approval
                                                            }, {
                                                                xtype: 'textfield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-StringSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-StringSearch',
                                                                emptyText: lang('Applicant ID / Applicant Name'),
                                                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                                                width: 280
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                                cls: 'Sfr_BtnGridBlue',
                                                                overCls: 'Sfr_BtnGridBlue-Hover',
                                                                margin: '0px 10px 0px 6px',
                                                                text: lang('Search'),
                                                                handler: function () {
                                                                    store_tab_selection.setStoreVar({
                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                        StringSearch: Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-StringSearch').getValue()
                                                                    });
                                                                    store_tab_selection.load();
                                                                }
                                                            },
                                                            {
                                                                xtype: 'splitbutton',
                                                                icon: varjs.config.base_url + 'images/icons/new/export.png',
                                                                text: lang('Export'),
                                                                cls: 'Sfr_BtnGridPaleBlue',
                                                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                                                menu: {
                                                                    items: [{
                                                                            text: lang('Data Grid'),
                                                                            scope: this,
                                                                            handler: function () {
                                                                                var IMSID = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-IMSID').getValue();
                                                                                var title = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-CertEventName').getValue();
                                                                                var StringSearch = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-StringSearch').getValue();
                                                                                var Participate = 0;
                                                                                var Recommendation = 0;
                                                                                var Selection = 0;

                                                                                if (Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-cbParticipateSearch').checked == true) {
                                                                                    Participate = 1;
                                                                                }
                                                                                if (Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-cbRecommendationSearch').checked == true) {
                                                                                    Recommendation = 1;
                                                                                }
                                                                                if (Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-cbSelectionSearch').checked == true) {
                                                                                    Selection = 1;
                                                                                }

                                                                                url = m_api + '/ims/exportFarmerSelection/' + IMSID + '/' + title + '/' + Participate + '/' + Recommendation + '/' + Selection + '/' + StringSearch;
                                                                                if (window.open(url, 'cetak', "height=200,width=200")) {
                                                                                    Ext.MessageBox.hide();
                                                                                }
                                                                            }
                                                                        }, {
                                                                            text: lang('Participants in Socialization'),
                                                                            scope: this,
                                                                            handler: function () {
                                                                                url = m_api + '/ims/exportFarmerSelectionParticipateInSocialization/' + thisObj.viewVar.IMSID + '/'
                                                                                if (window.open(url, 'cetak', "height=200,width=200")) {
                                                                                    Ext.MessageBox.hide();
                                                                                }
                                                                            }
                                                                        }]
                                                                }
                                                            }, {
                                                                xtype: 'tbspacer',
                                                                flex: 1
                                                            }, {
                                                                xtype: 'checkboxfield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-cbParticipateSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-cbParticipateSearch',
                                                                boxLabel: lang('Participate in Socialization'),
                                                                style: 'margin-right:15px;',
                                                                listeners: {
                                                                    change: function () {
                                                                        if (this.checked == true) {
                                                                            store_tab_selection.storeVar.Participate = '1';
                                                                        } else {
                                                                            store_tab_selection.storeVar.Participate = '0';
                                                                        }
                                                                        store_tab_selection.load();
                                                                        return false;
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'checkboxfield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-cbRecommendationSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-cbRecommendationSearch',
                                                                boxLabel: lang('Recommendation'),
                                                                style: 'margin-right:15px;',
                                                                listeners: {
                                                                    change: function () {
                                                                        if (this.checked == true) {
                                                                            store_tab_selection.storeVar.Recommendation = '1';
                                                                        } else {
                                                                            store_tab_selection.storeVar.Recommendation = '0';
                                                                        }
                                                                        store_tab_selection.load();
                                                                        return false;
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'checkboxfield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-cbSelectionSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-cbSelectionSearch',
                                                                boxLabel: lang('Selection Status'),
                                                                listeners: {
                                                                    change: function () {
                                                                        if (this.checked == true) {
                                                                            store_tab_selection.storeVar.Selection = '1';
                                                                        } else {
                                                                            store_tab_selection.storeVar.Selection = '0';
                                                                        }
                                                                        store_tab_selection.load();
                                                                        return false;
                                                                    }
                                                                }
                                                            }]
                                                    }],
                                                columns: [{
                                                        dataIndex: 'IMSSocID',
                                                        hidden: true
                                                    }, {
                                                        text: 'No',
                                                        xtype: 'rownumberer',
                                                        align: 'center',
                                                        width: '3%'
                                                    }, {
                                                        text: lang('ID'),
                                                        flex: 1,
                                                        dataIndex: 'DisplayID'
                                                    }, {
                                                        text: lang('Farmer ID'),
                                                        flex: 1,
                                                        dataIndex: 'DestObjID'
                                                    }, {
                                                        text: lang('Name'),
                                                        flex: 3,
                                                        dataIndex: 'Name'
                                                    }, {
                                                        text: lang('Gender'),
                                                        width: '5%',
                                                        dataIndex: 'Gender'
                                                    }, {
                                                        text: lang('SubDistrict'),
                                                        flex: 2,
                                                        dataIndex: 'SubDistrict'
                                                    }, {
                                                        text: lang('Village'),
                                                        flex: 2,
                                                        dataIndex: 'Village'
                                                    }, {
                                                        text: lang('Farmer Group'),
                                                        flex: 3,
                                                        dataIndex: 'FarmerGroup'
                                                    }, {
                                                        text: lang('Participate in Socialization'),
                                                        flex: 2,
                                                        dataIndex: 'ParticipateInSocialization'
                                                    }, {
                                                        text: lang('Recommendation'),
                                                        flex: 1,
                                                        dataIndex: 'Recommendation'
                                                    }, {
                                                        text: lang('Selection Status'),
                                                        flex: 1,
                                                        dataIndex: 'SelectionStatus'
                                                    }, {
                                                        text: lang('Type'),
                                                        flex: 1,
                                                        dataIndex: 'ParticipantType'
                                                    }, {
                                                        text: lang('Date Generated'),
                                                        flex: 1,
                                                        dataIndex: 'DateGenerated'
                                                    }],
                                                listeners: {
                                                    itemclick: function (view, record, item, index, e) {
                                                        //Sementara Hilangkan Dulu
                                                        //thisObj.contextMenuGridSelection.showAt(e.getXY());
                                                    }
                                                }
                                            }, {
                                                xtype: 'gridpanel',
                                                title: lang('Selection Approved Participants'),
                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved',
                                                style: 'border:1px solid #CCC;padding-right:3px;',
                                                store: store_tab_selection_approved,
                                                cls: 'Sfr_GridNew',
                                                width: '100%',
                                                minHeight:125,
                                                loadMask: true,
                                                selType: 'rowmodel',
                                                viewConfig: {
                                                    deferEmptyText: false,
                                                    emptyText: lang('No data Available')
                                                },
                                                dockedItems: [{
                                                        xtype: 'pagingtoolbar',
                                                        store: store_tab_selection_approved,
                                                        dock: 'bottom',
                                                        displayInfo: true
                                                    }, {
                                                        xtype: 'toolbar',
                                                        items: [{
                                                                icon: varjs.config.base_url + 'images/icons/new/bookmark_go_white.png',
                                                                text: lang('Process to ICS Candidate From Selection'),
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved-BtnProcessCandidateSelection',
                                                                hidden: m_act_process_to_candidate_selection,
                                                                handler: function () {
                                                                    var myMsgBox = new Ext.window.MessageBox();
                                                                    myMsgBox.textField.width = 300;
                                                                    myMsgBox.textField.center();
                                                                    myMsgBox.prompt(lang('Information'), lang('Please fill in information on who processing this action') + ':', function (btn, RemarkText) {
                                                                        if (btn == 'ok') {
                                                                            if (RemarkText != '') {
                                                                                Ext.MessageBox.show({
                                                                                    msg: 'Please wait...',
                                                                                    progressText: 'Generating...',
                                                                                    width: 300,
                                                                                    wait: true,
                                                                                    waitConfig: {
                                                                                        interval: 200
                                                                                    },
                                                                                    icon: 'ext-mb-info', //custom class in msg-box.html
                                                                                    animateTarget: 'mb9'
                                                                                });

                                                                                Ext.Ajax.request({
                                                                                    url: m_api + '/ims/acq_process_to_candidate_from_selection',
                                                                                    method: 'POST',
                                                                                    params: {
                                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                                        RemarkText: RemarkText
                                                                                    },
                                                                                    success: function (response, action) {
                                                                                        Ext.MessageBox.hide();
                                                                                        try {
                                                                                            JSON.parse(response.responseText);
                                                                                        } catch (error) {
                                                                                            Ext.MessageBox.show({
                                                                                                title: 'Failed',
                                                                                                msg: 'Network Connection Error',
                                                                                                buttons: Ext.MessageBox.OK,
                                                                                                animateTarget: 'mb9',
                                                                                                icon: 'ext-mb-error'
                                                                                            });
                                                                                            return false;
                                                                                        }

                                                                                        var objRet = Ext.decode(response.responseText);
                                                                                        switch (objRet.success) {
                                                                                            case true:
                                                                                                Ext.MessageBox.show({
                                                                                                    title: 'Information',
                                                                                                    msg: objRet.message,
                                                                                                    buttons: Ext.MessageBox.OK,
                                                                                                    animateTarget: 'mb9',
                                                                                                    icon: 'ext-mb-success'
                                                                                                });
                                                                                                break;
                                                                                            case false:
                                                                                                Ext.MessageBox.show({
                                                                                                    title: 'Failed',
                                                                                                    msg: objRet.message,
                                                                                                    buttons: Ext.MessageBox.OK,
                                                                                                    animateTarget: 'mb9',
                                                                                                    icon: 'ext-mb-error'
                                                                                                });
                                                                                                break;
                                                                                        }
                                                                                    },
                                                                                    failure: function (response, action) {
                                                                                        Ext.MessageBox.hide();
                                                                                        Ext.MessageBox.show({
                                                                                            title: 'Failed',
                                                                                            msg: 'Network Connection Error',
                                                                                            buttons: Ext.MessageBox.OK,
                                                                                            animateTarget: 'mb9',
                                                                                            icon: 'ext-mb-error'
                                                                                        });
                                                                                    }
                                                                                });
                                                                            } else {
                                                                                Ext.MessageBox.show({
                                                                                    title: 'Information',
                                                                                    msg: lang('Information must be filled'),
                                                                                    buttons: Ext.MessageBox.OK,
                                                                                    animateTarget: 'mb9',
                                                                                    icon: 'ext-mb-info'
                                                                                });
                                                                            }
                                                                        }
                                                                    });
                                                                }
                                                            }, {
                                                                icon: varjs.config.base_url + 'images/icons/new/lock_edit_white.png',
                                                                text: lang('Signing Lock'),
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved-BtnSigningLock',
                                                                hidden: m_act_signing_lock_soc_sel,
                                                                handler: function () {
                                                                    var WinFormSigningSocSel = Ext.create('Koltiva.view.IMS.WinFormSigningSocSel', {
                                                                        viewVar: {
                                                                            IMSID: thisObj.viewVar.IMSID
                                                                        }
                                                                    });
                                                                    if (!WinFormSigningSocSel.isVisible()) {
                                                                        WinFormSigningSocSel.center();
                                                                        WinFormSigningSocSel.show();
                                                                    } else {
                                                                        WinFormSigningSocSel.close();
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'tbseparator',
                                                                style: 'margin:0px 10px;'
                                                            }, {
                                                                xtype: 'textfield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved-StringSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved-StringSearch',
                                                                emptyText: lang('ID / Name'),
                                                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                                                width: 280
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                                margin: '0px 10px 0px 6px',
                                                                text: lang('Search'),
                                                                cls: 'Sfr_BtnGridBlue',
                                                                overCls: 'Sfr_BtnGridBlue-Hover',
                                                                handler: function () {
                                                                    store_tab_selection_approved.setStoreVar({
                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                        StringSearch: Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved-StringSearch').getValue()
                                                                    });
                                                                    store_tab_selection_approved.load();
                                                                }
                                                            },
                                                            {
                                                                icon: varjs.config.base_url + 'images/icons/new/export.png',
                                                                text: lang('Export All'),
                                                                cls: 'Sfr_BtnGridPaleBlue',
                                                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                                                handler: function () {
                                                                    var IMSID = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-IMSID').getValue();
                                                                    var title = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-CertEventName').getValue();
                                                                    var StringSearch = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-StringSearch').getValue();
                                                                    url = m_api + '/ims/exportFarmerSelectionApproved/' + IMSID + '/' + title + '/' + StringSearch;
                                                                    if (window.open(url, 'cetak', "height=200,width=200")) {
                                                                        Ext.MessageBox.hide();
                                                                    }
                                                                }
                                                            }]
                                                    }],
                                                columns: [{
                                                        dataIndex: 'IMSSocID',
                                                        hidden: true
                                                    }, {
                                                        text: 'No',
                                                        xtype: 'rownumberer',
                                                        align: 'center',
                                                        width: '3%'
                                                    }, {
                                                        text: lang('ID'),
                                                        flex: 1,
                                                        dataIndex: 'DisplayID'
                                                    }, {
                                                        text: lang('Farmer ID'),
                                                        flex: 1,
                                                        dataIndex: 'DestObjID'
                                                    }, {
                                                        text: lang('Name'),
                                                        flex: 3,
                                                        dataIndex: 'Name'
                                                    }, {
                                                        text: lang('Gender'),
                                                        flex: 1,
                                                        dataIndex: 'Gender'
                                                    }, {
                                                        text: lang('District'),
                                                        flex: 2,
                                                        dataIndex: 'District'
                                                    }, {
                                                        text: lang('SubDistrict'),
                                                        flex: 2,
                                                        dataIndex: 'SubDistrict'
                                                    }, {
                                                        text: lang('Village'),
                                                        flex: 2,
                                                        dataIndex: 'Village'
                                                    }, {
                                                        text: lang('Type'),
                                                        flex: 2,
                                                        dataIndex: 'ParticipantType'
                                                    }, {
                                                        text: lang('Approval Remark'),
                                                        flex: 3,
                                                        dataIndex: 'ApprovalRemark'
                                                    }, {
                                                        text: lang('Approval By'),
                                                        flex: 3,
                                                        dataIndex: 'ApprovalBy'
                                                    }, {
                                                        text: lang('Date Approval'),
                                                        flex: 1,
                                                        dataIndex: 'DateApproval'
                                                    }]
                                            }, {
                                                xtype: 'gridpanel',
                                                title: lang('GAP & CoC Training'),
                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Training',
                                                style: 'border:1px solid #CCC;padding-right:3px;',
                                                store: store_tab_training,
                                                cls: 'Sfr_GridNew',
                                                width: '100%',
                                                minHeight:125,
                                                loadMask: true,
                                                selType: 'rowmodel',
                                                viewConfig: {
                                                    deferEmptyText: false,
                                                    emptyText: lang('No data Available')
                                                },
                                                dockedItems: [{
                                                        xtype: 'pagingtoolbar',
                                                        store: store_tab_training,
                                                        dock: 'bottom',
                                                        displayInfo: true
                                                    }, {
                                                        xtype: 'toolbar',
                                                        items: [{
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/user_tick_white.png',
                                                                margin: '0px 15px 0px 6px',
                                                                text: lang('Process All Data to Candidate'),
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnBulkProcessCandidate',
                                                                //hidden: m_act_approval,
                                                                hidden: true,
                                                                handler: function () {
                                                                    Ext.MessageBox.confirm('Message', lang('Are you sure want to process all the data ?'), function (btn) {
                                                                        if (btn == 'yes') {

                                                                            Ext.Ajax.request({
                                                                                waitMsg: 'Please Wait',
                                                                                url: m_api + '/ims/acq_process_to_candidate_bulk',
                                                                                method: 'POST',
                                                                                params: {
                                                                                    IMSID: thisObj.viewVar.IMSID
                                                                                },
                                                                                success: function (response, opts) {
                                                                                    var r = Ext.decode(response.responseText);
                                                                                    //console.log(r);

                                                                                    Ext.MessageBox.show({
                                                                                        title: lang('Success'),
                                                                                        msg: lang(r.message),
                                                                                        buttons: Ext.MessageBox.OK,
                                                                                        animateTarget: 'mb9',
                                                                                        icon: 'ext-mb-success'
                                                                                    });

                                                                                    store_tab_training.load();
                                                                                },
                                                                                failure: function (response, o) {
                                                                                    var r = Ext.decode(response.responseText);
                                                                                    //console.log(r);

                                                                                    var pesanNya;
                                                                                    if (r.message != undefined) {
                                                                                        pesanNya = r.message;
                                                                                    } else {
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
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/user_tick_white.png',
                                                                margin: '0px 10px 0px 6px',
                                                                text: lang('Generate Candidate for Approval'),
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnGenTrainCandidate',
                                                                hidden: m_act_gen_gap_coc,
                                                                handler: function () {

                                                                    var myMsgBox = new Ext.window.MessageBox();
                                                                    myMsgBox.textField.width = 300;
                                                                    myMsgBox.textField.center();
                                                                    myMsgBox.prompt(lang('Information'), lang('Please fill in information on who processing this action') + ':', function (btn, RemarkText) {
                                                                        if (btn == 'ok') {
                                                                            if (RemarkText != '') {
                                                                                Ext.MessageBox.show({
                                                                                    msg: 'Please wait...',
                                                                                    progressText: 'Generating...',
                                                                                    width: 300,
                                                                                    wait: true,
                                                                                    waitConfig: {
                                                                                        interval: 200
                                                                                    },
                                                                                    icon: 'ext-mb-info', //custom class in msg-box.html
                                                                                    animateTarget: 'mb9'
                                                                                });

                                                                                Ext.Ajax.request({
                                                                                    url: m_api + '/ims/acq_process_generate_training_candidate',
                                                                                    method: 'POST',
                                                                                    params: {
                                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                                        RemarkText: RemarkText
                                                                                    },
                                                                                    success: function (response, action) {
                                                                                        Ext.MessageBox.hide();
                                                                                        try {
                                                                                            JSON.parse(response.responseText);
                                                                                        } catch (error) {
                                                                                            Ext.MessageBox.show({
                                                                                                title: 'Failed',
                                                                                                msg: 'Network Connection Error',
                                                                                                buttons: Ext.MessageBox.OK,
                                                                                                animateTarget: 'mb9',
                                                                                                icon: 'ext-mb-error'
                                                                                            });
                                                                                            return false;
                                                                                        }

                                                                                        var objRet = Ext.decode(response.responseText);
                                                                                        switch (objRet.success) {
                                                                                            case true:
                                                                                                Ext.MessageBox.show({
                                                                                                    title: 'Information',
                                                                                                    msg: objRet.message,
                                                                                                    buttons: Ext.MessageBox.OK,
                                                                                                    animateTarget: 'mb9',
                                                                                                    icon: 'ext-mb-success'
                                                                                                });
                                                                                                store_tab_training.load();
                                                                                                break;
                                                                                            case false:
                                                                                                Ext.MessageBox.show({
                                                                                                    title: 'Failed',
                                                                                                    msg: objRet.message,
                                                                                                    buttons: Ext.MessageBox.OK,
                                                                                                    animateTarget: 'mb9',
                                                                                                    icon: 'ext-mb-error'
                                                                                                });
                                                                                                break;
                                                                                        }
                                                                                    },
                                                                                    failure: function (response, action) {
                                                                                        Ext.MessageBox.hide();
                                                                                        Ext.MessageBox.show({
                                                                                            title: 'Failed',
                                                                                            msg: 'Network Connection Error',
                                                                                            buttons: Ext.MessageBox.OK,
                                                                                            animateTarget: 'mb9',
                                                                                            icon: 'ext-mb-error'
                                                                                        });
                                                                                    }
                                                                                });
                                                                            } else {
                                                                                Ext.MessageBox.show({
                                                                                    title: 'Information',
                                                                                    msg: lang('Information must be filled'),
                                                                                    buttons: Ext.MessageBox.OK,
                                                                                    animateTarget: 'mb9',
                                                                                    icon: 'ext-mb-info'
                                                                                });
                                                                            }
                                                                        }
                                                                    });

                                                                }
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/success-white.png',
                                                                margin: '0px 0px 0px 6px',
                                                                text: lang('Approval'),
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnApprove',
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                hidden: m_act_approval,
                                                                handler: function () {
                                                                    var WinFormApprovalTrain = Ext.create('Koltiva.view.IMS.WinFormApprovalTrain', {
                                                                        viewVar: {
                                                                            IMSID: thisObj.viewVar.IMSID,
                                                                            callerStore: store_tab_training,
                                                                            callerStoreTrainApprove: store_tab_training_approved
                                                                        }
                                                                    });
                                                                    if (!WinFormApprovalTrain.isVisible()) {
                                                                        WinFormApprovalTrain.center();
                                                                        WinFormApprovalTrain.show();
                                                                    } else {
                                                                        WinFormApprovalTrain.close();
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'tbseparator',
                                                                style: 'margin:0px 10px;',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnApproveSeparator',
                                                                hidden: m_act_approval
                                                            }, {
                                                                xtype: 'textfield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Training-StringSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-Training-StringSearch',
                                                                emptyText: lang('ID / Name'),
                                                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                                                width: 280
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                                margin: '0px 10px 0px 6px',
                                                                text: lang('Search'),
                                                                cls: 'Sfr_BtnGridBlue',
                                                                overCls: 'Sfr_BtnGridBlue-Hover',
                                                                handler: function () {
                                                                    store_tab_training.setStoreVar({
                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                        StringSearch: Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-StringSearch').getValue()
                                                                    });
                                                                    store_tab_training.load();
                                                                }
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/export.png',
                                                                text: lang('Export All'),
                                                                cls: 'Sfr_BtnGridPaleBlue',
                                                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                                                handler: function () {
                                                                    var IMSID = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-IMSID').getValue();
                                                                    var title = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-CertEventName').getValue();
                                                                    var StringSearch = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-StringSearch').getValue();
                                                                    url = m_api + '/ims/exportFarmerGapCoc/' + IMSID + '/' + title + '/' + StringSearch;
                                                                    if (window.open(url, 'cetak', "height=200,width=200")) {
                                                                        Ext.MessageBox.hide();
                                                                    }
                                                                }
                                                            }]
                                                    }],
                                                columns: [{
                                                        text: '',
                                                        xtype: 'actioncolumn',
                                                        width: '4%',
                                                        items: [{
                                                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                                                    thisObj.contextMenuGridTraining.showAt(e.getXY());
                                                                }
                                                            }]
                                                    }, {
                                                        dataIndex: 'ApplicantID',
                                                        hidden: true
                                                    }, {
                                                        text: 'No',
                                                        xtype: 'rownumberer',
                                                        align: 'center',
                                                        width: '3%'
                                                    }, {
                                                        text: lang('ApplicantID'),
                                                        flex: 1,
                                                        dataIndex: 'DisplayID',
                                                        hidden: true
                                                    }, {
                                                        text: lang('Farmer ID'),
                                                        flex: 1,
                                                        dataIndex: 'FarmerID'
                                                    }, {
                                                        text: lang('Farmer Name'),
                                                        flex: 3,
                                                        dataIndex: 'FarmerName'
                                                    }, {
                                                        text: lang('Gender'),
                                                        flex: 1,
                                                        dataIndex: 'Gender'
                                                    }, {
                                                        text: lang('SubDistrict'),
                                                        flex: 2,
                                                        dataIndex: 'SubDistrict'
                                                    }, {
                                                        text: lang('Village'),
                                                        flex: 2,
                                                        dataIndex: 'Village'
                                                    }, {
                                                        text: lang('Farmer Group'),
                                                        flex: 3,
                                                        dataIndex: 'FarmerGroup'
                                                    }, {
                                                        text: lang('Training Requirement'),
                                                        flex: 2,
                                                        dataIndex: 'TrainingReq'
                                                    }, {
                                                        text: lang('Percentage Attendance') + ' (%)',
                                                        flex: 1,
                                                        dataIndex: 'PercentageAttendance'
                                                    }, {
                                                        text: lang('Eligible'),
                                                        flex: 1,
                                                        dataIndex: 'EligibleStatus'
                                                    }, {
                                                        text: lang('Date generated'),
                                                        flex: 1,
                                                        dataIndex: 'DateGenerated'
                                                    }]
//                                                ,
//                                                listeners: {
//                                                    itemclick: function (view, record, item, index, e) {
//                                                        thisObj.contextMenuGridTraining.showAt(e.getXY());
//                                                    }
//                                                }
                                            }, {
                                                xtype: 'gridpanel',
                                                title: lang('Training Approved Participants'),
                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved',
                                                style: 'border:1px solid #CCC;padding-right:3px;',
                                                store: store_tab_training_approved,
                                                cls: 'Sfr_GridNew',
                                                width: '100%',
                                                minHeight:125,
                                                loadMask: true,
                                                selType: 'rowmodel',
                                                viewConfig: {
                                                    deferEmptyText: false,
                                                    emptyText: lang('No data Available')
                                                },
                                                dockedItems: [{
                                                        xtype: 'pagingtoolbar',
                                                        store: store_tab_training_approved,
                                                        dock: 'bottom',
                                                        displayInfo: true
                                                    }, {
                                                        xtype: 'toolbar',
                                                        items: [{
                                                                icon: varjs.config.base_url + 'images/icons/new/bookmark_go_white.png',
                                                                text: lang('Process to ICS Candidate From Selection'),
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-BtnProcessCandidateTraining',
                                                                hidden: m_act_process_to_candidate_training,
                                                                handler: function () {
                                                                    var myMsgBox = new Ext.window.MessageBox();
                                                                    myMsgBox.textField.width = 300;
                                                                    myMsgBox.textField.center();
                                                                    myMsgBox.prompt(lang('Information'), lang('Please fill in information on who processing this action') + ':', function (btn, RemarkText) {
                                                                        if (btn == 'ok') {
                                                                            if (RemarkText != '') {
                                                                                Ext.MessageBox.show({
                                                                                    msg: 'Please wait...',
                                                                                    progressText: 'Generating...',
                                                                                    width: 300,
                                                                                    wait: true,
                                                                                    waitConfig: {
                                                                                        interval: 200
                                                                                    },
                                                                                    icon: 'ext-mb-info', //custom class in msg-box.html
                                                                                    animateTarget: 'mb9'
                                                                                });

                                                                                Ext.Ajax.request({
                                                                                    url: m_api + '/ims/acq_process_to_candidate_from_training',
                                                                                    method: 'POST',
                                                                                    params: {
                                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                                        RemarkText: RemarkText
                                                                                    },
                                                                                    success: function (response, action) {
                                                                                        Ext.MessageBox.hide();
                                                                                        try {
                                                                                            JSON.parse(response.responseText);
                                                                                        } catch (error) {
                                                                                            Ext.MessageBox.show({
                                                                                                title: 'Failed',
                                                                                                msg: 'Network Connection Error',
                                                                                                buttons: Ext.MessageBox.OK,
                                                                                                animateTarget: 'mb9',
                                                                                                icon: 'ext-mb-error'
                                                                                            });
                                                                                            return false;
                                                                                        }

                                                                                        var objRet = Ext.decode(response.responseText);
                                                                                        switch (objRet.success) {
                                                                                            case true:
                                                                                                Ext.MessageBox.show({
                                                                                                    title: 'Information',
                                                                                                    msg: objRet.message,
                                                                                                    buttons: Ext.MessageBox.OK,
                                                                                                    animateTarget: 'mb9',
                                                                                                    icon: 'ext-mb-success'
                                                                                                });
                                                                                                break;
                                                                                            case false:
                                                                                                Ext.MessageBox.show({
                                                                                                    title: 'Failed',
                                                                                                    msg: objRet.message,
                                                                                                    buttons: Ext.MessageBox.OK,
                                                                                                    animateTarget: 'mb9',
                                                                                                    icon: 'ext-mb-error'
                                                                                                });
                                                                                                break;
                                                                                        }
                                                                                    },
                                                                                    failure: function (response, action) {
                                                                                        Ext.MessageBox.hide();
                                                                                        Ext.MessageBox.show({
                                                                                            title: 'Failed',
                                                                                            msg: 'Network Connection Error',
                                                                                            buttons: Ext.MessageBox.OK,
                                                                                            animateTarget: 'mb9',
                                                                                            icon: 'ext-mb-error'
                                                                                        });
                                                                                    }
                                                                                });
                                                                            } else {
                                                                                Ext.MessageBox.show({
                                                                                    title: 'Information',
                                                                                    msg: lang('Information must be filled'),
                                                                                    buttons: Ext.MessageBox.OK,
                                                                                    animateTarget: 'mb9',
                                                                                    icon: 'ext-mb-info'
                                                                                });
                                                                            }
                                                                        }
                                                                    });
                                                                }
                                                            }, {
                                                                icon: varjs.config.base_url + 'images/icons/new/lock_edit_white.png',
                                                                text: lang('Signing Lock'),
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-BtnSigningLock',
                                                                hidden: m_act_signing_lock_gap_coc,
                                                                handler: function () {
                                                                    //Cek dl, Signing Lock untuk Soc Sel sudah belum
                                                                    var SigningLockSocSelBy = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-SigningLockSocSelBy').getValue();
                                                                    if (SigningLockSocSelBy != undefined && SigningLockSocSelBy != null && SigningLockSocSelBy != '') {
                                                                        var WinFormSigningGapCoc = Ext.create('Koltiva.view.IMS.WinFormSigningGapCoc', {
                                                                            viewVar: {
                                                                                IMSID: thisObj.viewVar.IMSID
                                                                            }
                                                                        });
                                                                        if (!WinFormSigningGapCoc.isVisible()) {
                                                                            WinFormSigningGapCoc.center();
                                                                            WinFormSigningGapCoc.show();
                                                                        } else {
                                                                            WinFormSigningGapCoc.close();
                                                                        }
                                                                    } else {
                                                                        Ext.MessageBox.show({
                                                                            title: 'Information',
                                                                            msg: lang('Socialization and Selection must be signed first'),
                                                                            buttons: Ext.MessageBox.OK,
                                                                            animateTarget: 'mb9',
                                                                            icon: 'ext-mb-info'
                                                                        });
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'tbseparator',
                                                                style: 'margin:0px 10px;'
                                                            }, {
                                                                xtype: 'textfield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-StringSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-StringSearch',
                                                                emptyText: lang('ID / Name'),
                                                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                                                width: 280
                                                            }, {
                                                                xtype: 'datefield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-DateApprovalSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-DateApprovalSearch',
                                                                emptyText: lang('Date Approval'),
                                                                width: 125,
                                                                format: 'Y-m-d'
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                                cls: 'Sfr_BtnGridBlue',
                                                                overCls: 'Sfr_BtnGridBlue-Hover',
                                                                margin: '0px 10px 0px 6px',
                                                                text: lang('Search'),
                                                                handler: function () {
                                                                    store_tab_training_approved.setStoreVar({
                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                        StringSearch: Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-StringSearch').getValue(),
                                                                        DateApprovalSearch: Ext.Date.format(Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-DateApprovalSearch').getValue(), 'Y-m-d')
                                                                    });
                                                                    store_tab_training_approved.load();
                                                                }
                                                            }, {
                                                                icon: varjs.config.base_url + 'images/icons/new/export.png',
                                                                text: lang('Export All'),
                                                                cls: 'Sfr_BtnGridPaleBlue',
                                                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                                                handler: function () {
                                                                    var IMSID = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-IMSID').getValue();
                                                                    var title = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-CertEventName').getValue();
                                                                    var StringSearch = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-StringSearch').getValue();
                                                                    var DateApprovalSearch = Ext.Date.format(Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-DateApprovalSearch').getValue(), 'Y-m-d');

                                                                    if (StringSearch == '')
                                                                        StringSearch = 'kosong';
                                                                    if (DateApprovalSearch == '')
                                                                        DateApprovalSearch = 'kosong';
                                                                    url = m_api + '/ims/exportFarmerGapCocApproved/' + IMSID + '/' + title + '/' + StringSearch + '/' + DateApprovalSearch;
                                                                    if (window.open(url, 'cetak', "height=200,width=200")) {
                                                                        Ext.MessageBox.hide();
                                                                    }
                                                                }
                                                            }]
                                                    }],
                                                columns: [{
                                                        text: 'No',
                                                        xtype: 'rownumberer',
                                                        align: 'center',
                                                        width: '3%'
                                                    }, {
                                                        text: lang('Farmer ID'),
                                                        flex: 1,
                                                        dataIndex: 'FarmerID'
                                                    }, {
                                                        text: lang('Farmer Name'),
                                                        flex: 3,
                                                        dataIndex: 'FarmerName'
                                                    }, {
                                                        text: lang('Gender'),
                                                        flex: 1,
                                                        dataIndex: 'Gender'
                                                    }, {
                                                        text: lang('Farmer Group'),
                                                        flex: 3,
                                                        dataIndex: 'FarmerGroup'
                                                    }, {
                                                        text: lang('Training Requirement'),
                                                        flex: 2,
                                                        dataIndex: 'TrainingReq'
                                                    }, {
                                                        text: lang('Percentage Attendance') + ' (%)',
                                                        flex: 1,
                                                        dataIndex: 'PercentageAttendance'
                                                    }, {
                                                        text: lang('Approval Remark'),
                                                        flex: 3,
                                                        dataIndex: 'AppRemark'
                                                    }, {
                                                        text: lang('Approval By'),
                                                        flex: 2,
                                                        dataIndex: 'AppBy'
                                                    }, {
                                                        text: lang('Date Approval'),
                                                        flex: 1,
                                                        dataIndex: 'DateApproval'
                                                    }]
                                            }, {
                                                xtype: 'gridpanel',
                                                title: lang('Candidate - Pre ICS'),
                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-CandidatePreICS',
                                                style: 'border:1px solid #CCC;padding-right:3px;',
                                                store: store_tab_candidate_preics,
                                                cls: 'Sfr_GridNew',
                                                minHeight:125,
                                                width: '100%',
                                                loadMask: true,
                                                selType: 'rowmodel',
                                                viewConfig: {
                                                    deferEmptyText: false,
                                                    emptyText: lang('No data Available')
                                                },
                                                dockedItems: [{
                                                        xtype: 'pagingtoolbar',
                                                        store: store_tab_candidate_preics,
                                                        dock: 'bottom',
                                                        displayInfo: true
                                                    }, {
                                                        xtype: 'toolbar',
                                                        items: [{
                                                                xtype: 'textfield',
                                                                name: 'Koltiva.view.IMS.WinImsAcqPro-Tab-CandidatePreICS-StringSearch',
                                                                id: 'Koltiva.view.IMS.WinImsAcqPro-Tab-CandidatePreICS-StringSearch',
                                                                emptyText: lang('ID / Name'),
                                                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                                                width: 280
                                                            }, {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                                margin: '0px 10px 0px 6px',
                                                                cls: 'Sfr_BtnGridBlue',
                                                                overCls: 'Sfr_BtnGridBlue-Hover',
                                                                text: lang('Search'),
                                                                handler: function () {
                                                                    store_tab_candidate_preics.setStoreVar({
                                                                        IMSID: thisObj.viewVar.IMSID,
                                                                        StringSearch: Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-CandidatePreICS-StringSearch').getValue()
                                                                    });
                                                                    store_tab_candidate_preics.load();
                                                                }
                                                            }, {
                                                                icon: varjs.config.base_url + 'images/icons/new/export.png',
                                                                text: lang('Export'),
                                                                cls: 'Sfr_BtnGridPaleBlue',
                                                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                                                handler: function () {
                                                                    var IMSID = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-IMSID').getValue();
                                                                    var StringSearch = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-CandidatePreICS-StringSearch').getValue();
                                                                    if (StringSearch == '')
                                                                        StringSearch = 'kosong';

                                                                    url = m_api + '/ims/exportFarmerCandidatePreICS/' + IMSID + '/' + StringSearch;
                                                                    if (window.open(url, 'cetak', "height=200,width=200")) {
                                                                        Ext.MessageBox.hide();
                                                                    }
                                                                }
                                                            }]
                                                    }],
                                                columns: [{
                                                        text: 'No',
                                                        xtype: 'rownumberer',
                                                        align: 'center',
                                                        width: '3%'
                                                    }, {
                                                        text: lang('Farmer ID'),
                                                        flex: 1,
                                                        dataIndex: 'FarmerID'
                                                    }, {
                                                        text: lang('Farmer Name'),
                                                        flex: 3,
                                                        dataIndex: 'FarmerName'
                                                    }, {
                                                        text: lang('Gender'),
                                                        width: '7%',
                                                        dataIndex: 'Gender'
                                                    }, {
                                                        text: lang('Farmer Group'),
                                                        flex: 1,
                                                        dataIndex: 'FarmerGroup'
                                                    }, {
                                                        text: lang('Training Percentage'),
                                                        flex: 2,
                                                        dataIndex: 'TrainingPercentage'
                                                    }, {
                                                        text: lang('Eligible for Audit'),
                                                        flex: 2,
                                                        dataIndex: 'StatusComply'
                                                    }, {
                                                        text: lang('Remark'),
                                                        flex: 2,
                                                        dataIndex: 'AuditRemark'
                                                    }]
                                            }],
                                        listeners: {
                                            'tabchange': function (tabPanel, tab) {
                                                switch (tab.id) {
                                                    case 'Koltiva.view.IMS.WinImsAcqPro-Tab-FarmerIdentification':
                                                        store_tab_farmer_identification.load();
                                                        break;
                                                    case 'Koltiva.view.IMS.WinImsAcqPro-Tab-Socialization':
                                                        store_tab_socialization.load();
                                                        break;
                                                    case 'Koltiva.view.IMS.WinImsAcqPro-Tab-Selection':
                                                        store_tab_selection.load();
                                                        break;
                                                    case 'Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved':
                                                        store_tab_selection_approved.load();
                                                        break;
                                                    case 'Koltiva.view.IMS.WinImsAcqPro-Tab-Training':
                                                        store_tab_training.load();
                                                        break;
                                                    case 'Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved':
                                                        store_tab_training_approved.load();
                                                        break;
                                                    case 'Koltiva.view.IMS.WinImsAcqPro-Tab-CandidatePreICS':
                                                        store_tab_candidate_preics.load();
                                                        break;
                                                }
                                            }
                                        }
                                    }]
                            }]
                    }]
            }];

        thisObj.buttons = [{
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form');
            formNya.getForm().reset();

            //load nilainya
            formNya.getForm().load({
                url: m_api + '/ims/acq_pro_get_form',
                method: 'GET',
                params: {
                    IMSID: thisObj.viewVar.IMSID,
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                    //console.log(r);

                    if(r.data.TrainStatus == "1"){
                    	//hide tombol generate
                        Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnBulkProcessCandidate').setVisible(false);
                    }

                    //Jika sudah Signing Lock Soc Sel
                    if(r.data.SigningLockSocSelBy != undefined && r.data.SigningLockSocSelBy != null && r.data.SigningLockSocSelBy != ''){
                    	Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-BtnApprove').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnGenSocSel').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-BtnImportCertFarmer').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved-BtnProcessCandidateSelection').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Form-SigningLockSocSelBy').setValue(r.data.SigningLockSocSelBy);
                    }

					//Jika sudah Signing Lock Gap Coc
                    if(r.data.SigningLockGapCocBy != undefined && r.data.SigningLockGapCocBy != null && r.data.SigningLockGapCocBy != ''){
                    	Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnGenTrainCandidate').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnApprove').setVisible(false);
	                	Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-BtnProcessCandidateTraining').setVisible(false);
					}
					
					//Pengecekan terakhir jika Ims Event sudah completed
					if(r.data.CertEventStatus == "2"){
						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnGenSocSel').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Socialization-BtnApprove').setDisabled(true);

						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-BtnApprove').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-BtnImportCertFarmer').setDisabled(true);
						
						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved-BtnSigningLock').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved-BtnProcessCandidateSelection').setDisabled(true);
						
						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnApprove').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnGenTrainCandidate').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnBulkProcessCandidate').setDisabled(true);
						
						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-BtnSigningLock').setDisabled(true);
						Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-BtnProcessCandidateTraining').setDisabled(true);
					}
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