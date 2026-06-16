/*
* @Author: nikolius
* @Date:   2017-10-26 17:10:40
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-23 15:52:03
*/

function displayFormImsEventDetail(opsiDisplay, IMSMasterID, IMSID, CallerStore) {
	//atur hak akses
    var viewOnly = true;
    if (opsiDisplay == 'view') {
        viewOnly = true;
    } else {
        viewOnly = false;
    }

    /*============================================ Function & Other Var (Begin) ==================================================*/

    var contextMenuImsEventDetailGridFarmer = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/status_away.png',
                text: lang('Eligible Status'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridFarmer').getSelectionModel().getSelection()[0];
                    winImsEventDetailFarmerEligible(smb.get('FarmerID'), IMSID, store_img_event_detail_grid_farmer);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/folder.png',
                text: lang('Garden Detail'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridFarmer').getSelectionModel().getSelection()[0];
                    winImsEventDetailFarmerGarden(smb.get('FarmerID'), IMSID);
                }
            },
            {
                icon: varjs.config.base_url + 'images/icons/new/folder_table.png',
                text: lang('Certification Detail'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridFarmer').getSelectionModel().getSelection()[0];
                    winImsEventDetailFarmerCertification(smb.get('FarmerID'), IMSID);
                }
            },
            {
                icon: varjs.config.base_url + 'images/icons/new/folder_page.png',
                text: lang('Audit Log Detail'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridFarmer').getSelectionModel().getSelection()[0];
                    winImsEventDetailFarmerAuditLog(smb.get('FarmerID'), IMSID);
                }
            },
            {
                icon: varjs.config.base_url + 'images/icons/new/printout_black.png',
                text: lang('Certification Contract'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridFarmer').getSelectionModel().getSelection()[0];
                    preview_cetak_surat(m_print + '_contract/' + IMSID + '/' + smb.raw.FarmerID);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/document_link.png',
                text: lang('Input Certification Contract'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridFarmer').getSelectionModel().getSelection()[0];
                    var WinFormInputCertContract = Ext.create('Koltiva.view.IMS.WinFormInputCertContract');
                    WinFormInputCertContract.setViewVar({
                        IMSID: IMSID,
                        FarmerID: smb.raw.FarmerID
                    });
                    if (!WinFormInputCertContract.isVisible()) {
                        WinFormInputCertContract.center();
                        WinFormInputCertContract.show();
                    } else {
                        WinFormInputCertContract.close();
                    }

                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/printout_black.png',
                text: lang('Farmer Profile'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridFarmer').getSelectionModel().getSelection()[0];
                    var url_print_farmer_profile = m_api + '/farmer/cetak_beneficiary_profiles/CpgID/0/MemberID/' + smb.raw.FarmerID + '/IMSID/' + IMSID;
                    preview_cetak_surat(url_print_farmer_profile);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridFarmer').getSelectionModel().getSelection()[0];

                    //cek apakah sudah komplit
                    if (parseInt(Ext.ComponentQuery.query('[name=CertEventStatus]')[0].getGroupValue()) == 2) {
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Event already completed, cannot delete the data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });

                        return false;
                    }

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims/ims_farmer_candidate',
                                method: 'DELETE',
                                params: {
                                    FarmerID: smb.raw.FarmerID,
                                    IMSID: IMSID
                                },
                                success: function (response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    store_img_event_detail_grid_farmer.load();
                                },
                                failure: function (response, opts) {
                                    var pesanNya;
                                    if (o.result.message != undefined) {
                                        pesanNya = o.result.message;
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
            }]
    });

    var GridPanelDataReview = Ext.create('Koltiva.view.IMS.GridPanelDataReview', {
        IMSID: IMSID
    });

    var GridPanelDataReport = Ext.create('Koltiva.view.IMS.GridPanelDataReport', {
        IMSID: IMSID
    });
    var contextMenuImsEventDetailGridAFL = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/printout_black.png',
                text: lang('Farmer Profile'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridAFL').getSelectionModel().getSelection()[0];
                    var url_print_farmer_profile = m_api + '/farmer/cetak_beneficiary_profiles/CpgID/0/MemberID/' + smb.raw.FarmerID + '/IMSID/' + IMSID;
                    preview_cetak_surat(url_print_farmer_profile);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/printout_black.png',
                text: lang('GF Audit'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridAFL').getSelectionModel().getSelection()[0];
                    var url_print_p1_certification = m_api + '/farmer/cetak_p1_cert_ims/FarmerID/' + smb.raw.FarmerID + '/IMSID/' + IMSID;
                    preview_cetak_surat(url_print_p1_certification);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/printout_black.png',
                text: lang('GF Audit (2019)'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridAFL').getSelectionModel().getSelection()[0];
                    var url_print_p1_certification = m_api + '/farmerprint/cetak_p1_cert_ims_new/FarmerID/' + smb.raw.FarmerID + '/IMSID/' + IMSID;
                    preview_cetak_surat(url_print_p1_certification);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/silk/page_edit.png',
                text: lang('Regenerate Audit Summary'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_ims_regen_audit_summary,
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridAFL').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.show({
                        msg: 'Loading, please wait...',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200},
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        iconHeight: 50,
                        animateTarget: 'mb9'
                    });
                    //Ext.MessageBox.hide();

                    Ext.Ajax.request({
                        url: m_api + '/ims/ims_regenerate_audit_summary',
                        method: 'POST',
                        params: {
                            IMSID: smb.raw.IMSID,
                            FarmerID: smb.raw.FarmerID
                        },
                        success: function (rp, o) {
                            Ext.MessageBox.hide();
                            var r = Ext.decode(rp.responseText);

                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: r.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            store_img_event_detail_grid_afl.load();
                        },
                        failure: function (rp, o) {
                            Ext.MessageBox.hide();
                            try {
                                var r = Ext.decode(rp.responseText);
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            } catch (err) {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'Connection Error',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        }
                    });
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/page_edit.png',
                text: lang('Verify this Farmer (CL)'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_ims_afl_verify_cl,
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridAFL').getSelectionModel().getSelection()[0];

                    var WinFormICSVerifyFarmer = Ext.create('Koltiva.view.IMS.WinFormICSVerifyFarmer');
                    WinFormICSVerifyFarmer.setViewVar({
                        CallFrom: 'VerifyCL',
                        IMSID: smb.raw.IMSID,
                        FarmerID: smb.raw.FarmerID,
                        AFLStatus: smb.raw.AFLStatus,
                        CallerStore: store_img_event_detail_grid_afl
                    });
                    if (!WinFormICSVerifyFarmer.isVisible()) {
                        WinFormICSVerifyFarmer.center();
                        WinFormICSVerifyFarmer.show();
                    } else {
                        WinFormICSVerifyFarmer.close();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/page_edit.png',
                text: lang('Verify this Farmer (IMS Manager)'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_ims_afl_verify_imsmanager,
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridAFL').getSelectionModel().getSelection()[0];

                    var WinFormICSVerifyFarmer = Ext.create('Koltiva.view.IMS.WinFormICSVerifyFarmer');
                    WinFormICSVerifyFarmer.setViewVar({
                        CallFrom: 'VerifyIMSManager',
                        IMSID: smb.raw.IMSID,
                        FarmerID: smb.raw.FarmerID,
                        AFLStatus: smb.raw.AFLStatus,
                        CallerStore: store_img_event_detail_grid_afl
                    });
                    if (!WinFormICSVerifyFarmer.isVisible()) {
                        WinFormICSVerifyFarmer.center();
                        WinFormICSVerifyFarmer.show();
                    } else {
                        WinFormICSVerifyFarmer.close();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: true,
                handler: function () {
                    //cek apakah sudah komplit
                    if (parseInt(Ext.ComponentQuery.query('[name=CertEventStatus]')[0].getGroupValue()) == 2) {
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Event already completed, cannot delete the data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });

                        return false;
                    }

                    Ext.MessageBox.show({
                        title: 'Information',
                        msg: lang('Under Development'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }]
    });

    var contextMenuImsEventDetailGridAFLFinal = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Reset External Audit'),
                cls: 'Sfr_BtnConMenuWhite',
                itemId: 'contextMenuImsEventDetailGridAFLFinal.ResetExternalAudit',
                disabled: true,
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridAFLFinal').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Are you sure want to reset this farmer ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims/external_audit_reset',
                                method: 'POST',
                                params: {
                                    IMSID: IMSID,
                                    FarmerID: smb.raw.FarmerID
                                },
                                success: function (rp, o) {
                                    var r = Ext.decode(rp.responseText);
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
                                    store_img_event_detail_grid_afl_final.load();
                                },
                                failure: function (rp, o) {
                                    try {
                                        var r = Ext.decode(rp.responseText);
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    } catch (err) {
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: 'Connection Error',
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/printout_black.png',
                text: lang('GF Certification'),
                cls: 'Sfr_BtnConMenuWhite',
                itemId: 'contextMenuImsEventDetailGridAFLFinal.GFCert',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridAFLFinal').getSelectionModel().getSelection()[0];
                    var url_print_p1_certification_cert = m_api + '/farmer/cetak_p1_cert_ims/FarmerID/' + smb.raw.FarmerID + '/IMSID/' + IMSID + '/AFLFinal/1';
                    preview_cetak_surat(url_print_p1_certification_cert);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/printout_black.png',
                text: lang('GF Certification (2019)'),
                cls: 'Sfr_BtnConMenuWhite',
                itemId: 'contextMenuImsEventDetailGridAFLFinal.GFCert2019',
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridAFLFinal').getSelectionModel().getSelection()[0];
                    var url_print_p1_certification_cert = m_api + '/farmerprint/cetak_p1_cert_ims_new/FarmerID/' + smb.raw.FarmerID + '/IMSID/' + IMSID + '/AFLFinal/1';
                    preview_cetak_surat(url_print_p1_certification_cert);
                }
            }]
    });

    var contextMenuImsEventDetailGridStaff = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update staff work area on this event'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridStaff').getSelectionModel().getSelection()[0];
                    //console.log(smb);

                    var WinImsEventStaffEditWorkArea = Ext.create('Koltiva.view.IMS.WinImsEventStaffEditWorkArea', {
                        viewVar: {
                            IMSStaffID: smb.get('IMSStaffID'),
                            IMSMasterID: smb.get('IMSMasterID'),
                            IMSID: smb.get('IMSID'),
                            CallerStore: store_img_event_detail_grid_staff
                        }
                    });
                    if (!WinImsEventStaffEditWorkArea.isVisible()) {
                        WinImsEventStaffEditWorkArea.center();
                        WinImsEventStaffEditWorkArea.show();
                    } else {
                        WinImsEventStaffEditWorkArea.close();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridStaff').getSelectionModel().getSelection()[0];

                    //cek apakah sudah komplit
                    if (parseInt(Ext.ComponentQuery.query('[name=CertEventStatus]')[0].getGroupValue()) == 2) {
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Event already completed, cannot delete the data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });

                        return false;
                    }

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims/staff_ims_event',
                                method: 'DELETE',
                                params: {
                                    IMSStaffID: smb.get('IMSStaffID'),
                                    IMSMasterID: smb.get('IMSMasterID'),
                                    StaffID: smb.get('StaffID'),
                                    IMSID: IMSID
                                },
                                success: function (response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    store_img_event_detail_grid_staff.load();
                                },
                                failure: function (response, opts) {
                                    var pesanNya;
                                    if (o.result.message != undefined) {
                                        pesanNya = o.result.message;
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
            }]
    });

    var contextMenuImsEventDetailGridBuyingUnit = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/printout_black.png',
                cls: 'Sfr_BtnConMenuWhite',
                text: lang('Profile'),
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridBuyingUnit').getSelectionModel().getSelection()[0];
                    if (smb.raw.OrgType == 'Pedagang') {
                        var url_print_profile = m_api + '/trader/print_profiles/' + smb.raw.SupplychainID;
                        preview_cetak_surat(url_print_profile);
                    } else {
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Not Available Yet'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                cls: 'Sfr_BtnConMenuWhite',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridBuyingUnit').getSelectionModel().getSelection()[0];

                    //cek apakah sudah komplit
                    if (parseInt(Ext.ComponentQuery.query('[name=CertEventStatus]')[0].getGroupValue()) == 2) {
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Event already completed, cannot delete the data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });

                        return false;
                    }

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims/buying_unit_input',
                                method: 'DELETE',
                                params: {
                                    SupplychainID: smb.raw.SupplychainID,
                                    IMSID: IMSID
                                },
                                success: function (response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    store_img_event_detail_grid_buying_unit.load();
                                },
                                failure: function (response, opts) {
                                    var pesanNya;
                                    if (o.result.message != undefined) {
                                        pesanNya = o.result.message;
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
            }]
    });

    var contextMenuImsEventDetailGridFiles = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/download.png',
                cls: 'Sfr_BtnConMenuWhite',
                text: lang('Download'),
                handler: function () {
                    var sm = Ext.getCmp('imsEventDetailGridFiles').getSelectionModel().getSelection()[0];

                    var filenamepath = sm.get('FilePath');
                    if (filenamepath != '' && filenamepath != null) {
                        window.open(m_url_awss3 + '/' + filenamepath);
                    } else {
                        Ext.MessageBox.show({
                            title: lang('Information'),
                            msg: lang('File not found'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }, {
                text: lang('Delete File'),
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('imsEventDetailGridFiles').getSelectionModel().getSelection()[0];

                    //cek apakah sudah komplit
                    if (parseInt(Ext.ComponentQuery.query('[name=CertEventStatus]')[0].getGroupValue()) == 2) {
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Event already completed, cannot delete the data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });

                        return false;
                    }

                    Ext.MessageBox.confirm('Message', 'Are you sure want to delete this file ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims/ims_event_file_upload',
                                method: 'DELETE',
                                params: {
                                    IDCaller: sm.get('IMSFileID'),
                                    imsType: 'ims_event_detail'
                                },
                                success: function (response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('File deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    store_img_event_detail_grid_files.sorters.clear();
                                    store_img_event_detail_grid_files.load();
                                },
                                failure: function (response, opts) {
                                    var pesanNya;
                                    if (o.result.message != undefined) {
                                        pesanNya = o.result.message;
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
            }]
    });

    var contextMenuImsEventDetailGridSummaryFa = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/export.png',
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_export,
                text: lang('Export Detail Data'),
                handler: function () {
                    var sm = Ext.getCmp('imsEventDetailPanelSummary_fa').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.show({
                        msg: 'Loading, please wait...',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200},
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        iconHeight: 50,
                        animateTarget: 'mb9'
                    });

                    var url = m_api + '/ims/ims_event_detail_summary_fa_detail/' + IMSID + '/' + sm.get('UserID') + '/';
                    if (window.open(url, 'Export', "height=200,width=200")) {
                        Ext.MessageBox.hide();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/export.png',
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_export,
                text: lang('Export Progress Data Collection'),
                handler: function () {
                    var sm = Ext.getCmp('imsEventDetailPanelSummary_fa').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.show({
                        msg: 'Loading, please wait...',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200},
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        iconHeight: 50,
                        animateTarget: 'mb9'
                    });

                    var url = m_api + '/ims/ims_event_detail_summary_fa_progress/' + IMSID + '/' + sm.get('UserID') + '/';
                    if (window.open(url, 'Export', "height=200,width=200")) {
                        Ext.MessageBox.hide();
                    }
                }
            }, {
                text: '-----------------------------------------'
            }, {
                icon: varjs.config.base_url + 'images/icons/new/share.png',
                cls: 'Sfr_BtnConMenuWhite',
                text: lang('Update Data Farmer Target to DHIS'),
                handler: function () {
                    var sm = Ext.getCmp('imsEventDetailPanelSummary_fa').getSelectionModel().getSelection()[0];

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
                        url: m_api + '/ims/ims_farmer_target_to_dhis',
                        method: 'POST',
                        params: {
                            IMSID: IMSID,
                            UserID: sm.get('UserID')
                        },
                        success: function (response, action) {
                            Ext.MessageBox.hide();
                            var r = Ext.decode(response.responseText);

                            switch (r.success) {
                                case true:
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
                                    break;
                                case false:
                                    Ext.MessageBox.show({
                                        title: 'Failed',
                                        msg: r.message,
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
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/folder_user.png',
                cls: 'Sfr_BtnConMenuWhite',
                text: lang('Download Offline Data by FA Target'),
                handler: function () {
                    var sm = Ext.getCmp('imsEventDetailPanelSummary_fa').getSelectionModel().getSelection()[0];

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
                        url: m_api + '/ims/ims_farmer_target_offline_data',
                        method: 'POST',
                        params: {
                            IMSID: IMSID,
                            UserID: sm.get('UserID'),
                            StaffName: sm.get('FaLabel')
                        },
                        success: function (response, action) {
                            Ext.MessageBox.hide();
                            var r = Ext.decode(response.responseText);
                            //console.log(response.responseText);
                            //console.log(r);

                            switch (r.success) {
                                case true:
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //Download Filenya
                                    window.open(m_api_base_url + r.FilePathZip, 'Offline Data', "height=80,width=80");
                                    break;
                                case false:
                                    Ext.MessageBox.show({
                                        title: 'Failed',
                                        msg: r.message,
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
                }
            }]
    });

    var contextMenuImsEventDetailGridIcsReinspect = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                text: lang('Cancel Reinspection'),
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var smb = Ext.getCmp('imsEventDetailGridIcsReinspect').getSelectionModel().getSelection()[0];
                    //console.log(smb);

                    if (smb.raw.RegenerateICSStatus == "1") {
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Cannot cancel farmer that have been regenerate ics'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    } else {
                        Ext.MessageBox.confirm('Message', 'Are you sure want to cancel this farmer ?', function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/ims/ics_reinspect_farmer',
                                    method: 'DELETE',
                                    params: {
                                        FarmerID: smb.raw.FarmerID,
                                        GardenNr: smb.raw.CertGardenNr,
                                        IMSID: IMSID
                                    },
                                    success: function (rp, o) {
                                        var r = Ext.decode(rp.responseText);
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        store_ims_event_detail_grid_ics_reinspect.load();
                                    },
                                    failure: function (rp, o) {
                                        try {
                                            var r = Ext.decode(rp.responseText);
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: r.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        } catch (err) {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: 'Connection Error',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            }]
    });

    var panelImsEventDocuments = Ext.create('Koltiva.view.IMS.PanelImsEventDocuments', {
        viewVar: {
            IMSID: IMSID
        }
    });

    var PanelImsCoaching = Ext.create('Koltiva.view.IMS.PanelGridImsCoaching', {
        viewVar: {
            IMSID: IMSID
        }
    });
    /*============================================ Function & Other Var (End)   ==================================================*/

    //==================================================== store & combobox (begin) ===================================================//

    var cmb_search_status_audit = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        data: [{
            "id": "-",
            "label": lang("No Status Yet")
        }, {
            "id": "Comply",
            "label": lang("Comply")
        }, {
            "id": "Not Comply",
            "label": lang("Not Comply")
        }]
    });

    var cmb_search_status_verified = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        data: [{
            "id": "0",
            "label": lang("No Status Yet")
        }, {
            "id": "1",
            "label": lang("Verified by CL")
        }, {
            "id": "2",
            "label": lang("Verified by IMS Manager")
        }]
    });

    var cert_body = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/cert_body',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var cert_body_contact = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/cert_body_contact',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var first_buyer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/first_buyer',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var surveys = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/surveys',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var cmb_batch_training = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/cmb_batch_training',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_img_event_detail_grid_farmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSID','MemberDisplayID','FarmerID', 'FarmerName', 'StatusAudit','AuditRemark', 'SurveyNr', 'SurveyName', 'FarmerGroup', 'SubDistrict',  'Village', 'CGarden', 'CCertification', 'CAudit', 'CPostHarvest', 'CPPI', 'TotalFarm','CGardenPolygon','FarmerUpdated','FarmerVisited'],
        autoLoad: true,
        pageSize: 25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_detail_candidate',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
                store.proxy.extraParams.key = Ext.getCmp('imsEventDetailGridFarmer_SearchFarmerString').getValue();
            }
        }
    });

    var store_img_event_detail_grid_afl = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSID', 'FarmerID','MemberDisplayID', 'FarmerName', 'FarmerGroup', 'Village', 'AFLStatus', 'CertYear', 'CertFirstYear', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertFarmNr','ICSDate','TotalHa','TotalCocoaFarm','CertStatusVerified','AuditSummaryStatus'],
        id: 'StoreImgEventDetailGridAfl',
        autoLoad: false,
        pageSize: 25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/afls',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
                store.proxy.extraParams.key = Ext.getCmp('imsEventDetailGridAFL_SearchFarmerString').getValue();
                store.proxy.extraParams.StatusAudit = Ext.getCmp('imsEventDetailGridAFL_SearchStatusAudit').getValue();
                store.proxy.extraParams.StatusVerified = Ext.getCmp('imsEventDetailGridAFL_SearchStatusVerified').getValue();
            }
        }
    });

    var store_ims_event_detail_grid_ics_reinspect = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSID','FarmerID','FarmerName','FarmerGroup','Village','AFLStatus','CertGardenNr','RegenerateICSStatus','ICSDate','CertNextHarvest','CertHarvest','CertHectare'],
        id: 'StoreImgEventDetailGridIcsReinspect',
        autoLoad: false,
        pageSize: 25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ics_reinspect',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
                store.proxy.extraParams.key = Ext.getCmp('imsEventDetailGridIcsReinspect_SearchFarmerString').getValue();
            }
        }
    });

    var store_img_event_detail_grid_afl_final = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSID', 'FarmerID', 'MemberDisplayID','FarmerName', 'FarmerGroup', 'Village', 'AFLStatus', 'CertYear', 'CertFirstYear', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertFarmNr','ICSDate','TotalHa','SalesQuota','TotalCocoaFarm','ExternalAudit'],
        autoLoad: false,
        pageSize: 25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/afl_final',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
                store.proxy.extraParams.key = Ext.getCmp('imsEventDetailGridAFLFinal_SearchFarmerString').getValue();
                store.proxy.extraParams.ICSStatus = parseInt(Ext.ComponentQuery.query('[name=ICSStatus]')[0].getGroupValue());
                store.proxy.extraParams.StatusIcsReinspect = parseInt(Ext.ComponentQuery.query('[name=StatusIcsReinspect]')[0].getGroupValue());
            }
        }
    });

    var store_img_event_detail_grid_cfl = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSID', 'FarmerID', 'FarmerName', 'FarmerGroup', 'Village', 'AFLStatus', 'CertYear', 'CertFirstYear', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertFarmNr','ICSDate','TotalHa','SalesQuota','TotalCocoaFarm'],
        autoLoad: false,
        pageSize: 25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/cfls',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
                store.proxy.extraParams.key = Ext.getCmp('imsEventDetailGridCFL_SearchFarmerString').getValue();
            }
        }
    });

    var store_img_event_detail_grid_staff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSStaffID','IMSMasterID', 'IMSID', 'StaffID','StaffRoleType', 'StaffName', 'StaffEmail', 'StaffWorkArea', 'Gender'],
        autoLoad: false,
        pageSize: 25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/staffs',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
                store.proxy.extraParams.key = Ext.getCmp('imsEventDetailGridStaff_SearchStaffString').getValue();
            }
        }
    });

    var store_img_event_detail_grid_buying_unit = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSID', 'SupplychainID', 'Name', 'OrgType', 'Company', 'District', 'Status'],
        autoLoad: false,
        pageSize: 25,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/buying_units',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
                store.proxy.extraParams.key = Ext.getCmp('imsEventDetailGridBuyingUnit_SearchBUString').getValue();
            }
        }
    });

    var store_img_event_detail_grid_files = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSFileID', 'IMSID', 'FileName', 'FilePath','FileDesc'],
        autoLoad: false,
        pageSize: 25,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/files',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
            }
        }
    });

    var store_ims_event_detail_grid_summary_kpi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['rowLabel','FarmerVisited', 'Farmer', 'Garden', 'GardenWithPolygon', 'PostHarvest', 'Certification', 'AuditLog', 'PPI'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_event_detail_summary_kpi',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
            }
        }
    });

    var store_ims_event_detail_grid_summary_fa = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['UserID','FaLabel','FarmerVisited','FarmerTarget', 'Farmer','FarmerWithPhoto','FarmerFamilyLabour', 'Garden','GardenWithPolygon', 'PostHarvest', 'Certification', 'AuditLog', 'PPI'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_event_detail_summary_fa',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
            }
        }
    });

    var store_ims_event_detail_grid_summary_afl = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['Data','Comply','NotComply','NoStatusYet','LastUpdated'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_event_detail_summary_afl',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = IMSID;
            }
        }
    });

    //==================================================== store & combobox (end)   ===================================================//


	var winFormImsEventDetail = Ext.create('widget.window', {
        title: lang('Form IMS Event Detail'),
        id: 'imsCertWinFormImsEventDetail',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '98%',
        height: '96%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 35,
        items: [{
            xtype: 'form',
            id: 'imsCertFormImsEventDetail',
            padding: '5 35 5 8',
            items: [{
            	layout: 'column',
                border: false,
                items: [{
                    columnWidth: 0.49,
                    padding: 4,
                    layout: 'form',
                    items: [{
                        xtype: 'hiddenfield',
                        id: 'imsIMSID',
                        name: 'IMSID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'imsIMSMasterID',
                        name: 'IMSMasterID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'imsSupplychainID',
                        name: 'SupplychainID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'imsCertHolderID',
                        name: 'CertHolderID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'imsStatusImsFinalPeriod',
                        name: 'StatusImsFinalPeriod'
                    },{
                        xtype: 'textfield',
                        id: 'imsCertEventName',
                        name: 'CertEventName',
                        fieldLabel: lang('Event Name'),
                        allowBlank: false,
                        labelWidth: 225
                    },{
                        xtype: 'textfield',
                        id: 'imsCertEventID',
                        name: 'CertEventID',
                        fieldLabel: lang('Event ID'),
                        labelWidth: 225,
                        readOnly: true
                    },{
                        xtype: 'datefield',
                        fieldLabel: lang('Event Start Date'),
                        labelWidth: 225,
                        id: 'imsCertEventDate',
                        name: 'CertEventDate',
                        format: 'Y-m-d',
                        //allowBlank: false,
                        hidden:true
                    },{
                    	xtype: 'textfield',
                        id: 'imsSupplychainLabel',
                        name: 'SupplychainLabel',
                        fieldLabel: lang('Holder Name'),
                        labelWidth: 225,
                        readOnly: true
                    },{
                    	xtype: 'textfield',
                        id: 'imsCertHolderProgramLabel',
                        name: 'CertHolderProgramLabel',
                        fieldLabel: lang('Program Name'),
                        labelWidth: 225,
                        readOnly: true
                    },{
                        xtype: 'textfield',
                        id: 'imsGIPNumber',
                        name: 'GIPNumber',
                        fieldLabel: lang('GIP Number'),
                        labelWidth: 225,
                        readOnly: true,
                        hidden: true
                    }, {
                        xtype: 'textfield',
                        id: 'imsCertProgMemberID',
                        name: 'CertProgMemberID',
                        fieldLabel: lang('Program Member ID'),
                        labelWidth: 225,
                        readOnly: true
                    }, {
                        xtype: 'textfield',
                        id: 'imsCertProgMemberDate',
                        name: 'CertProgMemberDate',
                        fieldLabel: lang('Program Member Date'),
                        labelWidth: 225,
                        readOnly: true
                    },{
                    	xtype: 'textfield',
                        id: 'imsLocation',
                        name: 'Location',
                        fieldLabel: lang('Location'),
                        labelWidth: 225
                    },{
                        xtype: 'combobox',
                        fieldLabel: lang('Certification Body'),
                        labelWidth: 225,
                        id: 'imsCertBodyID',
                        name: 'CertBodyID',
                        store: cert_body,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        listeners: {
                            change: function (cb, nv, ov) {
                                cert_body_contact.load({
                                    params: {
                                        CertBodyID: Ext.getCmp('imsCertBodyID').getValue()
                                    }
                                });
                            }
                        }
                    }, {
                        xtype: 'combobox',
                        fieldLabel: lang('Contact / Staff'),
                        labelWidth: 225,
                        id: 'imsCertBodyContactID',
                        name: 'CertBodyContactID',
                        store: cert_body_contact,
                        //allowBlank: false,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.Ajax.request({
                                    url: m_api + '/ims/set_body',
                                    method: 'GET',
                                    params: {CertBodyContactID: Ext.getCmp('imsCertBodyContactID').getValue()},
                                    success: function (fp, o) {
                                        var data = Ext.decode(fp.responseText);
                                        Ext.getCmp('imsContactEmail').setValue(data.ContactEmail);
                                        Ext.getCmp('imsContactPhone').setValue(data.ContactPhone);
                                        Ext.getCmp('imsContactAddress').setValue(data.ContactAddress);
                                    },
                                    failure: function (response, opts) {
                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        }
                    }, {
                        xtype: 'textfield',
                        id: 'imsContactEmail',
                        name: 'ContactEmail',
                        fieldLabel: lang('Contact Email'),
                        labelWidth: 225,
                        readOnly: true,
                        hidden:true
                    }, {
                        xtype: 'textfield',
                        id: 'imsContactPhone',
                        name: 'ContactPhone',
                        fieldLabel: lang('Contact Phone'),
                        labelWidth: 225,
                        readOnly: true,
                        hidden:true
                    }, {
                        xtype: 'textfield',
                        id: 'imsContactAddress',
                        name: 'ContactAddress',
                        fieldLabel: lang('Contact Address'),
                        labelWidth: 225,
                        readOnly: true,
                        hidden:true
                    },{
	                    xtype: 'combobox',
	                    fieldLabel: lang('First Buyer'),
	                    labelWidth: 225,
	                    id: 'imsFirstBuyerID',
	                    name: 'FirstBuyerID',
	                    store: first_buyer,
	                    //allowBlank: false,
	                    queryMode: 'local',
	                    displayField: 'label',
	                    valueField: 'id'
	                }, {
	                    xtype: 'combobox',
	                    fieldLabel: lang('Survey Number (Including Baseline)'),
	                    labelWidth: 225,
	                    id: 'imsSurveyNr',
	                    name: 'SurveyNr',
	                    store: surveys,
	                    allowBlank: false,
	                    queryMode: 'local',
	                    displayField: 'label',
	                    valueField: 'id'
	                }, {
	                    xtype: 'hiddenfield',
	                    id: 'imsdefaultSurveyNr',
	                    name: 'defaultSurveyNr',
	                }, {
	                    xtype: 'numberfield',
	                    fieldLabel: lang('Year Number'),
	                    labelWidth: 225,
	                    minValue: 1,
	                    id: 'imsYear',
	                    name: 'Year',
	                    format: 'Y-m-d'
	                }]
                },{
                	columnWidth: 0.49,
                    padding: 4,
                    layout: 'form',
                    items: [{
                        xtype:'combobox',
                        fieldLabel: lang('Batch Training'),
                        labelWidth: 225,
                        id: 'CpgBatchID',
                        name: 'CpgBatchID',
                        store: cmb_batch_training,
                        allowBlank: false,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id'
                    },{
                    	xtype: 'radiogroup',
	                	fieldLabel: lang('Event Status'),
	                	labelWidth: 225,
	                    columns: 3,
                        id:'rowCertEventStatus',
                        hidden:true,
	                    items:[{
	                        boxLabel: lang('Ongoing'),
	                        name: 'CertEventStatus',
	                        inputValue: '1',
	                        id: 'imsCertEventStatus1',
	                        listeners:{
	                            change: function(){
	                                return false;
	                            }
	                        }
	                    },{
	                        boxLabel: lang('Completed'),
	                        name: 'CertEventStatus',
	                        inputValue: '2',
	                        id: 'imsCertEventStatus2',
	                        listeners:{
	                            change: function(){
	                                return false;
	                            }
	                        }
	                    },{
                            boxLabel: lang('Canceled'),
	                        name: 'CertEventStatus',
	                        inputValue: '3',
	                        id: 'imsCertEventStatus3',
	                        listeners:{
	                            change: function(){
	                                return false;
	                            }
	                        }
                        }]
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('ICS Status'),
                        labelWidth: 225,
                        columns: 2,
                        id:'rowICSStatus',
                        items:[{
                            boxLabel: lang('Lock'),
                            name: 'ICSStatus',
                            inputValue: '1',
                            id: 'ICSStatus1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Open'),
                            name: 'ICSStatus',
                            inputValue: '2',
                            id: 'ICSStatus2',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('ICS Reinspection'),
                        labelWidth: 225,
                        columns: 2,
                        id:'rowStatusIcsReinspect',
                        hidden:true, //Component ini penting, jangan dihapus
                        items:[{
                            boxLabel: lang('Ongoing'),
                            name: 'StatusIcsReinspect',
                            inputValue: '1',
                            id: 'StatusIcsReinspect1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Completed'),
                            name: 'StatusIcsReinspect',
                            inputValue: '2',
                            id: 'StatusIcsReinspect2',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('External Audit'),
                        labelWidth: 225,
                        columns: 2,
                        id:'rowExternalAuditStatus',
                        items:[{
                            boxLabel: lang('Ongoing'),
                            name: 'ExternalAuditStatus',
                            inputValue: '1',
                            id: 'ExternalAuditStatus1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Completed'),
                            name: 'ExternalAuditStatus',
                            inputValue: '2',
                            id: 'ExternalAuditStatus2',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        style:'margin-top:-10px;',
                        items:[{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype:'label',
                                cls: 'x-form-item-label',
                                text: lang('Certification')
                            }]
                        },{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype: 'datefield',
                                //fieldLabel: lang('Certification Start'),
                                //labelWidth: 225,
                                id: 'imsCertificationStart',
                                name: 'CertificationStart',
                                //disabled: true,
                                format: 'Y-m-d'
                            }]
                        },{
                            columnWidth: 0.1,
                            layout:'form',
                            items:[{
                                xtype:'label',
                                cls: 'x-form-item-label',
                                style:'text-align:center;',
                                text: lang('to')
                            }]
                        },{
                            columnWidth: 0.3,
                            style:'padding-left:5px;',
                            layout:'form',
                            items:[{
                                xtype: 'datefield',
                                //fieldLabel: lang('Certification End'),
                                //labelWidth: 225,
                                id: 'imsCertificationEnd',
                                name: 'CertificationEnd',
                                //disabled: true,
                                format: 'Y-m-d'
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        style:'margin-top:-15px;',
                        items:[{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype:'label',
                                cls: 'x-form-item-label',
                                text: lang('Internal Audit')
                            }]
                        },{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype: 'datefield',
                                //fieldLabel: lang('Internal Audit Start'),
                                //labelWidth: 225,
                                id: 'imsInternalStart',
                                name: 'InternalStart',
                                //disabled: true,
                                format: 'Y-m-d'
                            }]
                        },{
                            columnWidth: 0.1,
                            layout:'form',
                            items:[{
                                xtype:'label',
                                cls: 'x-form-item-label',
                                style:'text-align:center;',
                                text: lang('to')
                            }]
                        },{
                            columnWidth: 0.3,
                            style:'padding-left:5px;',
                            layout:'form',
                            items:[{
                                xtype: 'datefield',
                                //fieldLabel: lang('Internal Audit End'),
                                //labelWidth: 225,
                                id: 'imsInternalEnd',
                                name: 'InternalEnd',
                                //disabled: true,
                                format: 'Y-m-d'
                            }]
                        }]
                    },{
                        layout: 'column',
                        border: false,
                        style:'margin-top:-15px;',
                        items:[{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype:'label',
                                cls: 'x-form-item-label',
                                text: lang('External Audit')
                            }]
                        },{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype: 'datefield',
                                //fieldLabel: lang('External Audit Start'),
                                //labelWidth: 225,
                                id: 'imsExternalStart',
                                name: 'ExternalStart',
                                format: 'Y-m-d'
                            }]
                        },{
                            columnWidth: 0.1,
                            layout:'form',
                            items:[{
                                xtype:'label',
                                cls: 'x-form-item-label',
                                style:'text-align:center;',
                                text: lang('to')
                            }]
                        },{
                            columnWidth: 0.3,
                            style:'padding-left:5px;',
                            layout:'form',
                            items:[{
                                xtype: 'datefield',
                                //fieldLabel: lang('External Audit End'),
                                //labelWidth: 225,
                                id: 'imsExternalEnd',
                                name: 'ExternalEnd',
                                //disabled: true,
                                format: 'Y-m-d'
                            }]
                        }]
                    }, {
                        layout: 'column',
                        border: false,
                        style:'margin-top:-15px;',
                        items:[{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype:'label',
                                cls: 'x-form-item-label',
                                text: lang('Extension Date')
                            }]
                        },{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype: 'datefield',
                                //fieldLabel: lang('Extension Date'),
                                //labelWidth: 225,
                                id: 'imsExtensionDate',
                                name: 'ExtensionDate',
                                //disabled: true,
                                format: 'Y-m-d'
                            }]
                        }]
	                }, {
                        layout: 'column',
                        border: false,
                        style:'margin-top:-15px;',
                        items:[{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype:'label',
                                cls: 'x-form-item-label',
                                text: lang('Validity')
                            }]
                        },{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype: 'datefield',
                                //fieldLabel: lang('Validity Start'),
                                //labelWidth: 225,
                                id: 'imsValidityStart',
                                name: 'ValidityStart',
                                //disabled: true,
                                format: 'Y-m-d'
                            }]
                        },{
                            columnWidth: 0.1,
                            layout:'form',
                            items:[{
                                xtype:'label',
                                cls: 'x-form-item-label',
                                style:'text-align:center;',
                                text: lang('to')
                            }]
                        },{
                            columnWidth: 0.3,
                            style:'padding-left:5px;',
                            layout:'form',
                            items:[{
                                xtype: 'datefield',
                                //fieldLabel: lang('Validity End'),
                                //labelWidth: 225,
                                id: 'imsValidityEnd',
                                name: 'ValidityEnd',
                                //disabled: true,
                                format: 'Y-m-d'
                            }]
                        }]
                    }, {
                        layout: 'column',
                        border: false,
                        style:'margin-top:-15px;',
                        items:[{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype:'label',
                                cls: 'x-form-item-label',
                                text: lang('Certification Issued Date')
                            }]
                        },{
                            columnWidth: 0.3,
                            style:'padding-right:10px;',
                            layout:'form',
                            items:[{
                                xtype: 'datefield',
                                //fieldLabel: lang('Certification Issued Date'),
                                //labelWidth: 225,
                                id: 'imsExternalDate',
                                name: 'ExternalDate',
                                format: 'Y-m-d'
                            }]
                        }]
	                }]
                }]
            },{
                xtype: 'tabpanel',
                flex: 1,
                margin: 2,
                activeTab: 0,
                plain: true,
                items: [{
                	xtype: 'gridpanel',
                    title: lang('Candidate'),
                    id: 'imsEventDetailGridFarmer',
                    cls: 'Sfr_GridNew',
                    style: 'border:2px solid #CCC;',
                    store: store_img_event_detail_grid_farmer,
                    width: '99%',
                    minHeight:300,
                    loadMask: true,
                    selType: 'rowmodel',
//                    listeners: {
//	                    itemclick: function(view, record, item, index, e){
//	                       contextMenuImsEventDetailGridFarmer.showAt(e.getXY());
//	                    }
//	                },
	                viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data Available')
                    },
	                dockedItems: [{
	                	xtype: 'pagingtoolbar',
                        store: store_img_event_detail_grid_farmer,
                        dock: 'bottom',
                        displayInfo: true
	                },{
                                        xtype: 'toolbar',
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                                cls: 'Sfr_BtnGridGreen',
                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                hidden: m_act_add,
                                                text: lang('Add'),
                                                id: 'imsCandidateTabBtnAddFarmer',
                                                scope: this,
                                                handler: function () {
                                                    $prosesCek = cekSaveDulu(IMSID);
                                                    if ($prosesCek == true) {
                                                        var WinFormCandidate = Ext.create('Koltiva.view.IMS.WinFormSelectCandidate', {
                                                            viewVar: {
                                                                IMSID: IMSID,
                                                                CallerStore: store_img_event_detail_grid_farmer
                                                            }
                                                        });
                                                        if (!WinFormCandidate.isVisible()) {
                                                            WinFormCandidate.center();
                                                            WinFormCandidate.show();
                                                        } else {
                                                            WinFormCandidate.close();
                                                        }
                                                    }
                                                }
                                            }, {
                                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                                    cls:'Sfr_BtnGridGreen',
                                    overCls:'Sfr_BtnGridGreen-Hover',
		                    hidden: m_act_import,
		                    text: lang('Import Excel'),
		                    id:'imsCandidateTabBtnImportExcel',
		                    scope: this,
		                    handler: function() {
                                $prosesCek = cekSaveDulu(IMSID);
                                if($prosesCek == true){
                                    displayWinUploadFarmerList(IMSID,store_img_event_detail_grid_farmer);
                                }
		                    }
		                },{
                            xtype: 'splitbutton',
                            id:'imsCandidateTabBtnImportMappingGroup',
                            icon: varjs.config.base_url + 'images/icons/new/group_add.png',
                            hidden: m_act_import,
                            cls:'Sfr_BtnGridPaleBlue',
                            overCls:'Sfr_BtnGridPaleBlue-Hover',
                            text: lang('FA Target Mapping'),
                            menu: {
                                items: [{
                                    text: lang('Target Farmer'),
                                    id:'imsCandidateTabBtnImportMappingFA',
                                    scope: this,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
                                            var WinCandidateMapFA = Ext.create('Koltiva.view.IMS.WinCandidateMapFA', {
                                                viewVar: {
                                                    IMSID: IMSID
                                                }
                                            });
                                            if (!WinCandidateMapFA.isVisible()) {
                                                WinCandidateMapFA.center();
                                                WinCandidateMapFA.show();
                                            } else {
                                                WinCandidateMapFA.close();
                                            }
                                        }
                                    }
                                }, {
                                    text: lang('Target Coaching'),
                                    id:'imsCandidateTabBtnImportMappingFACoaching',
                                    scope: this,
                                    hidden:true,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
                                            var WinFarmerMapFA = Ext.create('Koltiva.view.IMS.WinFarmerMapFA', {
                                                viewVar: {
                                                    IMSID: IMSID
                                                }
                                            });
                                            if (!WinFarmerMapFA.isVisible()) {
                                                WinFarmerMapFA.center();
                                                WinFarmerMapFA.show();
                                            } else {
                                                WinFarmerMapFA.close();
                                            }
                                        }
                                    }
                                }]
                            }
                        },/*{
		                	icon: varjs.config.base_url + 'images/icons/new/add.png',
	                        hidden: m_act_add,
	                        text: lang('Add'),
	                        id:'imsCandidateTabBtnAdd',
	                        scope: this,
	                        handler: function() {
	                            Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: lang('Under Development'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-info'
                                });
	                        }
		                },*/{
                            xtype: 'splitbutton',
                            icon: varjs.config.base_url + 'images/icons/new/export.png',
                            hidden: m_act_export,
                            cls:'Sfr_BtnGridPaleBlue',
                            overCls:'Sfr_BtnGridPaleBlue-Hover',
                            text: lang('Export'),
                            menu: {
                                items: [{
                                    text: lang('Data Grid'),
                                    scope: this,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
                                            Ext.MessageBox.show({
                                                msg: 'Loading, please wait...',
                                                progressText: 'Saving...',
                                                width:300,
                                                wait:true,
                                                waitConfig: {interval:200},
                                                icon:'ext-mb-download', //custom class in msg-box.html
                                                iconHeight: 50,
                                                animateTarget: 'mb7'
                                            });

                                            $.get(m_api+'/ims/ims_event_detail_farmer_pre_afl_grid/'+IMSID, function(data) {
                                                if (data) {
                                                    //console.log(data);
                                                    if(data.count_data == 0){
                                                        Ext.MessageBox.show({
                                                            title: lang('Attention'),
                                                            msg: lang('No data found'),
                                                            buttons: Ext.MessageBox.OK,
                                                            animateTarget: 'mb9',
                                                            icon: 'ext-mb-info'
                                                        });
                                                    }else{
                                                        Ext.MessageBox.hide();
                                                        window.location = m_api_base_url+'/'+data.UrlFilenya;
                                                    }
                                                }
                                            });
                                        }
                                    }
                                },{
                                    text: lang('Pre ICS Per Garden'),
                                    scope: this,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
                                            Ext.MessageBox.show({
                                                msg: 'Loading, please wait...',
                                                progressText: 'Saving...',
                                                width:300,
                                                wait:true,
                                                waitConfig: {interval:200},
                                                icon:'ext-mb-download', //custom class in msg-box.html
                                                iconHeight: 50,
                                                animateTarget: 'mb7'
                                            });

                                            $.get(m_api+'/ims/ims_event_detail_farmer_pre_afl_garden/'+IMSID, function(data) {
                                                if (data) {
//                                                    Ext.MessageBox.hide();
                                                    //console.log(data);
                                                    if(data.count_data == 0){
                                                        Ext.MessageBox.show({
                                                            title: lang('Attention'),
                                                            msg: lang('No data found'),
                                                            buttons: Ext.MessageBox.OK,
                                                            animateTarget: 'mb9',
                                                            icon: 'ext-mb-info'
                                                        });
                                                    }else{
                                                        Ext.MessageBox.hide();
                                                        window.location = m_api_base_url+'/'+data.UrlFilenya;
                                                    }
                                                }
                                            });
                                        }
                                    }
                                }]
                            }
                        },{
                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                            cls:'Sfr_BtnGridPaleBlue',
                            overCls:'Sfr_BtnGridPaleBlue-Hover',
                            hidden: m_act_import,
                            id:'imsCandidateTabBtnUpdateGardenPreAFL',
                            text: lang('Update Garden Candidates'),
                            scope: this,
                            handler: function() {
                                $prosesCek = cekSaveDulu(IMSID);
                                if($prosesCek == true){
                                    Ext.MessageBox.show({
                                        msg: 'Please wait...',
                                        progressText: 'Updating...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {
                                            interval: 200
                                        },
                                        icon: 'ext-mb-info', //custom class in msg-box.html
                                        animateTarget: 'mb9'
                                    });

                                    Ext.Ajax.request({
                                        url: m_api + '/ims/gen_pre_afl_garden',
                                        method: 'POST',
                                        params: {
                                            IMSID: IMSID
                                        },
                                        success: function(response, action) {
                                            Ext.MessageBox.hide();
                                            try{
                                                JSON.parse(response.responseText);
                                            }
                                            catch (error){
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
                                            switch(objRet.success){
                                                case true:
                                                    Ext.MessageBox.show({
                                                        title: 'Information',
                                                        msg: objRet.message,
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-success'
                                                    });
                                                    store_img_event_detail_grid_farmer.load();
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
                                        failure: function(response, action){
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

                                }
                            }
                        },{
		                	xtype: 'textfield',
	                        name: 'imsEventDetailGridFarmer_SearchFarmerString',
	                        id: 'imsEventDetailGridFarmer_SearchFarmerString',
	                        emptyText: lang('Farmer ID / Farmer Name'),
                                baseCls:'Sfr_TxtfieldSearchGrid',
	                        width: 280
		                },{
	                        xtype: 'button',
	                        icon: varjs.config.base_url + 'images/icons/new/search_white.png',
	                        margin: '0px 0px 0px 6px',
	                        text: lang('Search'),
                                cls:'Sfr_BtnGridBlue',
                                overCls:'Sfr_BtnGridBlue-Hover',
	                        handler: function() {
	                        	store_img_event_detail_grid_farmer.load();
	                        }
	                    }]
		            }],
	            	columns: [{
                            text: '',
                            xtype:'actioncolumn',
                            width:'4%',
                            items:[{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function(grid, rowIndex, colIndex, item, e, record) {
                                    contextMenuImsEventDetailGridFarmer.showAt(e.getXY());
                                }
                            }]
                        },{
	                    text: 'ID',
	                    dataIndex: 'IMSID',
	                    hidden: true
	                }, {
	                    text: 'FarmerID',
	                    dataIndex: 'FarmerID',
	                    hidden: true
	                }, {
	                    text: 'SurveyNr',
	                    dataIndex: 'SurveyNr',
	                    hidden: true
	                }, {
	                    text: 'No',
	                    xtype: 'rownumberer',
	                    align: 'center',
	                    width: '3%'
	                }, {
	                    text: lang('Farmer ID'),
                            flex: 1,
	                    dataIndex: 'MemberDisplayID'
	                }, {
	                    text: lang('Farmer Name'),
                            flex: 2,
	                    dataIndex: 'FarmerName'
	                }, {
	                    text: lang('Farmer Group'),
                            flex: 2,
	                    dataIndex: 'FarmerGroup'
	                }, {
	                    text: lang('Village'),
                            flex: 1,
	                    dataIndex: 'Village'
	                }, {
	                    text: lang('Farmer Updated'),
                            flex: 1,
	                    dataIndex: 'FarmerUpdated'
	                },{
                        text: lang('Farmer Visited'),
                            flex: 1,
                        dataIndex: 'FarmerVisited'
                    },{
                        text: lang('Audit Remark'),
                        hidden: true,
                        dataIndex: 'AuditRemark'
                    },{
                        text: lang('Total Farm'),
                            flex: 1,
                        dataIndex: 'TotalFarm'
                    },{
	                    text: lang('Progress')+' '+lang('Garden'),
                            flex: 1,
	                    dataIndex: 'CGarden'
	                }, {
                        text: lang('Progress')+' '+lang('Garden Polygon'),
                            flex: 1,
                        dataIndex: 'CGardenPolygon'
                    },{
	                    text: lang('Progress')+' '+lang('Post Harvest'),
                            flex: 1,
	                    dataIndex: 'CPostHarvest'
	                }, {
	                    text: lang('Progress')+' '+lang('PPI'),
                            flex: 1,
	                    dataIndex: 'CPPI'
	                }, {
	                    text: lang('Progress')+' '+lang('Certification'),
                            flex: 1,
	                    dataIndex: 'CCertification'
	                }, {
	                    text: lang('Progress')+' '+lang('Audit Log'),
                            flex: 1,
	                    dataIndex: 'CAudit'
	                }]
                },{
                    xtype: 'gridpanel',
                    title: lang('ICS'),
                    minHeight:300,
                    id: 'imsEventDetailGridAFL',
                    style: 'border:2px solid #CCC;',
                    cls: 'Sfr_GridNew',
                    store: store_img_event_detail_grid_afl,
                    width: '99%',
                    loadMask: true,
                    selType: 'rowmodel',
                    listeners: {
                        itemclick: function(view, record, item, index, e){
                           contextMenuImsEventDetailGridAFL.showAt(e.getXY());
                        }
                    },
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data Available')
                    },
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png',
                            hidden: m_act_add,
                            text: lang('Generate ICS'),
                            id:'imsAFLTabBtnGenAFL',
                            cls:'Sfr_BtnGridGreen',
                            overCls:'Sfr_BtnGridGreen-Hover',
                            scope: this,
                            handler: function() {

                                $prosesCek = cekSaveDulu(IMSID);
                                if($prosesCek == true){
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
                                        url: m_api + '/ims/gen_afl_farmer_and_garden',
                                        method: 'POST',
                                        params: {
                                            IMSID: IMSID
                                        },
                                        success: function(response, action) {
                                            Ext.MessageBox.hide();
                                            try{
                                                JSON.parse(response.responseText);
                                            }
                                            catch (error){
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
                                            switch(objRet.success){
                                                case true:
                                                    Ext.MessageBox.show({
                                                        title: 'Information',
                                                        msg: objRet.message,
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-success'
                                                    });
                                                    store_img_event_detail_grid_afl.load();
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
                                        failure: function(response, action){
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
                                }

                            }
                        },{
                            xtype: 'splitbutton',
                            icon: varjs.config.base_url + 'images/icons/new/export.png',
                            hidden: m_act_export,
                            cls:'Sfr_BtnGridPaleBlue',
                            overCls:'Sfr_BtnGridPaleBlue-Hover',
                            text: 'Export',
                            menu: {
                                items: [{
                                    text: lang('Farmer ICS'),
                                    scope: this,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
                                            Ext.MessageBox.show({
                                                msg: 'Loading, please wait...',
                                                progressText: 'Saving...',
                                                width:300,
                                                wait:true,
                                                waitConfig: {interval:200},
                                                icon:'ext-mb-download', //custom class in msg-box.html
                                                iconHeight: 50,
                                                animateTarget: 'mb7'
                                            });

                                            Ext.Ajax.request({
                                                //url: m_api_direct + '/report_sql_view/sql_view_export_excel',
                                                url:  m_api + '/ims/ims_event_detail_afl_farmer_export/'+IMSID,
                                                method: 'GET',
                                                waitMsg: lang('Please Wait'),
                                                success: function(data) {
                                                    Ext.MessageBox.hide();
                                                    if(!testJSON(data.responseText)){
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
                                                    window.location = jsonResp.filenya;
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
                                    }
                                },{
                                    text: lang('Audit Summary Garden'),
                                    scope: this,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
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
                                                url: m_api + '/ims/ims_expot_audit_summary_garden/',
                                                method: 'POST',
                                                params: {
                                                    IMSID: IMSID
                                                },
                                                waitMsg: lang('Please Wait'),
                                                success: function(data) {
                                                    Ext.MessageBox.hide();
                                                    if(!testJSON(data.responseText)){
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

                                            /*var excel_url = m_api + '/ims/ims_expot_audit_summary_garden/'+IMSID;
                                            window.location = excel_url;
                                            Ext.MessageBox.hide();*/
                                        }
                                    }
                                },{
                                    text: lang('Audit Summary Garden (Y1)'),
                                    scope: this,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
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
                                                url: m_api + '/ims/ims_expot_audit_summary_garden_year1/',
                                                method: 'POST',
                                                params: {
                                                    IMSID: IMSID
                                                },
                                                waitMsg: lang('Please Wait'),
                                                success: function(data) {
                                                    Ext.MessageBox.hide();
                                                    if(!testJSON(data.responseText)){
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

                                            // var excel_url = m_api + '/ims/ims_expot_audit_summary_garden_year1/'+IMSID;
                                            // window.location = excel_url;
                                            // Ext.MessageBox.hide();
                                        }
                                    }
                                },{
                                    text: lang('Audit Summary Garden (Y2)'),
                                    scope: this,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
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
                                                url: m_api + '/ims/ims_expot_audit_summary_garden_year2/',
                                                method: 'POST',
                                                params: {
                                                    IMSID: IMSID
                                                },
                                                waitMsg: lang('Please Wait'),
                                                success: function(data) {
                                                    Ext.MessageBox.hide();
                                                    if(!testJSON(data.responseText)){
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

                                            // var excel_url = m_api + '/ims/ims_expot_audit_summary_garden_year2/'+IMSID;
                                            // window.location = excel_url;
                                            // Ext.MessageBox.hide();
                                        }
                                    }
                                },{
                                    text: lang('P1 Summary'),
                                    scope: this,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){

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
                                                url: m_api + '/ims/ims_event_detail_afl_p1_summary/',
                                                method: 'POST',
                                                waitMsg: lang('Please Wait'),
                                                params: {
                                                    IMSID: IMSID
                                                },
                                                success: function(data) {
                                                    Ext.MessageBox.hide();
                                                    if(!testJSON(data.responseText)){
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
                                                    window.location = jsonResp.filenya;
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

                                            /*Ext.MessageBox.show({
                                                msg: 'Loading, please wait...',
                                                progressText: 'Saving...',
                                                width:300,
                                                wait:true,
                                                waitConfig: {interval:200},
                                                icon:'ext-mb-download', //custom class in msg-box.html
                                                iconHeight: 50,
                                                animateTarget: 'mb7'
                                            });

                                            var url = m_api + '/ims/ims_event_detail_afl_p1_summary/'+IMSID;
                                            if(window.open(url, 'Export', "height=200,width=200")){
                                                Ext.MessageBox.hide();
                                            }*/
                                        }
                                    }
                                },{
                                    text: lang('Pre ICS - Not Comply Detail Data'),
                                    scope: this,
                                    hidden:true,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
                                            Ext.MessageBox.show({
                                                msg: 'Loading, please wait...',
                                                progressText: 'Saving...',
                                                width:300,
                                                wait:true,
                                                waitConfig: {interval:200},
                                                icon:'ext-mb-download', //custom class in msg-box.html
                                                iconHeight: 50,
                                                animateTarget: 'mb7'
                                            });

                                            var url = m_api + '/ims/ims_event_detail_not_comply/'+IMSID;
                                            if(window.open(url, 'Export', "height=200,width=200")){
                                                Ext.MessageBox.hide();
                                            }
                                        }
                                    }
                                }]
                            }


                        },{
                            xtype: 'textfield',
                            name: 'imsEventDetailGridAFL_SearchFarmerString',
                            id: 'imsEventDetailGridAFL_SearchFarmerString',
                            emptyText: lang('Farmer ID / Farmer Name'),
                            baseCls:'Sfr_TxtfieldSearchGrid',
                            width: 280
                        }, {
                            id: 'imsEventDetailGridAFL_SearchStatusAudit',
                            name: 'imsEventDetailGridAFL_SearchStatusAudit',
                            xtype: 'combo',
                            width: 190,
                            store: cmb_search_status_audit,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            selectOnFocus: true,
                            baseCls:'Sfr_TxtfieldSearchGrid',
                            emptyText: lang('Audit Status')
                        },{
                            id: 'imsEventDetailGridAFL_SearchStatusVerified',
                            name: 'imsEventDetailGridAFL_SearchStatusVerified',
                            xtype: 'combo',
                            width: 190,
                            store: cmb_search_status_verified,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            selectOnFocus: true,
                            baseCls:'Sfr_TxtfieldSearchGrid',
                            emptyText: lang('Status Verified')
                        },{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                            margin: '0px 0px 0px 6px',
                            text: lang('Search'),
                            cls:'Sfr_BtnGridBlue',
                            overCls:'Sfr_BtnGridBlue-Hover',
                            handler: function() {
                                store_img_event_detail_grid_afl.load();
                            }
                        }]
                    },{
                        xtype: 'pagingtoolbar',
                        store: store_img_event_detail_grid_afl,
                        dock: 'bottom',
                        displayInfo: true
                    }],
                    columns: [{
            	text: '',
                xtype:'actioncolumn',
                        width:'4%',
                        items:[{
                            icon: varjs.config.base_url + 'images/icons/new/action.png',
                            handler: function(grid, rowIndex, colIndex, item, e, record) {
                                contextMenuImsEventDetailGridAFL.showAt(e.getXY());
                            }
                        }]
                    },{
                        text: 'ID',
                        dataIndex: 'IMSID',
                        hidden: true
                    }, {
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '3%'
                    },{
                        text: 'FarmerID',
                        dataIndex: 'FarmerID',
                            flex: 1,
                    }, {
                        text: lang('Farmer Name'),
                            flex: 2,
                        dataIndex: 'FarmerName'
                    },{
                        text: lang('Farmer Group'),
                            flex: 2,
                        dataIndex: 'FarmerGroup'
                    },{
                        text: lang('Location'),
                            flex: 1,
                        dataIndex: 'Village'
                    },{
                        text: lang('Audit Summary'),
                            flex: 1,
                        dataIndex: 'AuditSummaryStatus'
                    },{
                        text: lang('Status'),
                            flex: 1,
                        dataIndex: 'AFLStatus'
                    },{
                        text: lang('Verified'),
                            flex: 1,
                        dataIndex: 'CertStatusVerified',
                        renderer: function (value) {
                            var RetVal;

                            switch(parseInt(value)){
                                case 1:
                                    RetVal = lang('Verified by CL');
                                break;
                                case 2:
                                    RetVal = lang('Verified by IMS Manager');
                                break;
                                default:
                                    RetVal = '-';
                                break;
                            }

                            return RetVal;
                        }
                    },{
                        text: lang('First Year of Certification'),
                            flex: 1,
                        dataIndex: 'CertFirstYear'
                    },{
                        text: lang('Internal Inspection Date'),
                            flex: 1,
                        dataIndex: 'ICSDate'
                    }, {
                        text: lang('Estimated Harvest Present Year (Kg)'),
                            flex: 1,
                        dataIndex: 'CertNextHarvest'
                    }, {
                        text: lang('Previous Years Harvest (Kg)'),
                            flex: 1,
                        dataIndex: 'CertHarvest'
                    }, {
                        text: lang('Total Certified Crop Area (Ha)'),
                            flex: 1,
                        dataIndex: 'CertHectare'
                    },{
                        text: lang('Nr of Cert Plots'),
                            flex: 1,
                        dataIndex: 'CertFarmNr'
                    },{
                        text: lang('Total Palmoil Farm'),
                            flex: 1,
                        dataIndex: 'TotalCocoaFarm'
                    },{
                        text: lang('Total Farm Area (Ha)'),
                            flex: 1,
                        dataIndex: 'TotalHa'
                    }]
                },{
                    xtype: 'gridpanel',
                    title: lang('AFL'),
                    minHeight:300,
                    id: 'imsEventDetailGridAFLFinal',
                    style: 'border:2px solid #CCC;',
                    cls: 'Sfr_GridNew',
                    store: store_img_event_detail_grid_afl_final,
                    width: '99%',
                    loadMask: true,
                    selType: 'rowmodel',
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data Available')
                    },
//                    listeners: {
//                        itemclick: function(view, record, item, index, e){
//                            contextMenuImsEventDetailGridAFLFinal.showAt(e.getXY());
//                            let ExternalAuditEvent = Ext.ComponentQuery.query('[name=ExternalAuditStatus]')[0].getGroupValue();
//
//                            var sm = record;
//                            if(sm.data.ExternalAudit == '3' && ExternalAuditEvent == '1') {
//                                contextMenuImsEventDetailGridAFLFinal.getComponent('contextMenuImsEventDetailGridAFLFinal.ResetExternalAudit').setDisabled(false);
//                            } else {
//                                contextMenuImsEventDetailGridAFLFinal.getComponent('contextMenuImsEventDetailGridAFLFinal.ResetExternalAudit').setDisabled(true);
//                            }
//                        }
//                    },
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                            text: lang('External Audit'),
                            id:'imsAflExternalAuditBtn',
                            cls:'Sfr_BtnGridGreen',
                            overCls:'Sfr_BtnGridGreen-Hover',
                            disabled:true,
                            handler: function() {
                                //Cek apa reinspection active
                                let StatusIcsReinspect = Ext.ComponentQuery.query('[name=StatusIcsReinspect]')[0].getGroupValue();

                                if(StatusIcsReinspect != "1") {
                                    var WinAflExternalAuditInput = Ext.create('Koltiva.view.IMS.WinAflExternalAuditInput', {
                                        viewVar: {
                                            IMSID: IMSID
                                        }
                                    });
                                    if (!WinAflExternalAuditInput.isVisible()) {
                                        WinAflExternalAuditInput.center();
                                        WinAflExternalAuditInput.show();
                                    } else {
                                        WinAflExternalAuditInput.close();
                                    }
                                } else {
                                    Ext.MessageBox.show({
                                        title: lang('Attention'),
                                        msg: lang('Reinspection is ongoing'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-info'
                                    });
                                }
                            }
                        },{
                            icon: varjs.config.base_url + 'images/icons/new/group_add.png',
//                            hidden: m_act_import,
                            text: lang('Farmer Coaching Mapping'),
                            cls:'Sfr_BtnGridPaleBlue',
                            overCls:'Sfr_BtnGridPaleBlue-Hover',
                            id:'imsFarmerCoachingMapping',
                            handler: function() {
                                console.log(IMSID);
                                $prosesCek = cekSaveDulu(IMSID);
                                if($prosesCek == true){
                                    var WinFarmerCoachingMapping = Ext.create('Koltiva.view.IMS.WinFarmerCoachingMapping', {
                                        viewVar: {
                                            IMSID: IMSID
                                        }
                                    });
                                    if (!WinFarmerCoachingMapping.isVisible()) {
                                        WinFarmerCoachingMapping.center();
                                        WinFarmerCoachingMapping.show();
                                    } else {
                                        WinFarmerCoachingMapping.close();
                                    }
                                }
                            }
                        }, {
                            xtype: 'splitbutton',
                            icon: varjs.config.base_url + 'images/icons/new/export.png',
                            hidden: m_act_export,
                            text: 'Export',
                            cls:'Sfr_BtnGridPaleBlue',
                            overCls:'Sfr_BtnGridPaleBlue-Hover',
                            menu: {
                                items: [{
                                    text: lang('Farmer AFL'),
                                    scope: this,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
                                            Ext.MessageBox.show({
                                                msg: 'Loading, please wait...',
                                                progressText: 'Saving...',
                                                width:300,
                                                wait:true,
                                                waitConfig: {interval:200},
                                                icon:'ext-mb-download', //custom class in msg-box.html
                                                iconHeight: 50,
                                                animateTarget: 'mb7'
                                            });

                                            Ext.Ajax.request({
                                                url:  m_api + '/ims/ims_event_detail_afl_final_farmer_export/'+IMSID+'/'+parseInt(Ext.ComponentQuery.query('[name=ICSStatus]')[0].getGroupValue())+'/',
                                                method: 'GET',
                                                waitMsg: lang('Please Wait'),
                                                success: function(data) {
                                                    Ext.MessageBox.hide();
                                                    if(!testJSON(data.responseText)){
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
                                                    window.location = jsonResp.filenya;
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
                                    }
                                }]
                            }
                        },{
                            xtype: 'textfield',
                            name: 'imsEventDetailGridAFLFinal_SearchFarmerString',
                            id: 'imsEventDetailGridAFLFinal_SearchFarmerString',
                            emptyText: lang('Farmer ID / Farmer Name'),
                            baseCls:'Sfr_TxtfieldSearchGrid',
                            width: 280
                        }, {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                            margin: '0px 0px 0px 6px',
                            text: lang('Search'),
                            cls:'Sfr_BtnGridBlue',
                            overCls:'Sfr_BtnGridBlue-Hover',
                            handler: function() {
                                store_img_event_detail_grid_afl_final.load();
                            }
                        }]
                    },{
                        xtype: 'pagingtoolbar',
                        store: store_img_event_detail_grid_afl_final,
                        dock: 'bottom',
                        displayInfo: true
                    }],
                    columns: [{
                        text: '',
                        xtype:'actioncolumn',
                        width:'4%',
                        items:[{
                            icon: varjs.config.base_url + 'images/icons/new/action.png',
                            handler: function(grid, rowIndex, colIndex, item, e, record) {
                                contextMenuImsEventDetailGridAFLFinal.showAt(e.getXY());
                            var ExternalAuditEvent = Ext.ComponentQuery.query('[name=ExternalAuditStatus]')[0].getGroupValue();

                            var sm = record;
                            if(sm.data.ExternalAudit == '3' && ExternalAuditEvent == '1') {
                                contextMenuImsEventDetailGridAFLFinal.getComponent('contextMenuImsEventDetailGridAFLFinal.ResetExternalAudit').setDisabled(false);
                            } else {
                                contextMenuImsEventDetailGridAFLFinal.getComponent('contextMenuImsEventDetailGridAFLFinal.ResetExternalAudit').setDisabled(true);
                            }
                            }
                        }]
                    },{
                        text: 'ID',
                        dataIndex: 'IMSID',
                        hidden: true
                    },
                    {
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '3%'
                    },{
                        text: 'FarmerID',
                        flex: 1,
                        dataIndex: 'MemberDisplayID'
                    },
                    {
                        text: lang('Farmer Name'),
                        flex: 2,
                        dataIndex: 'FarmerName'
                    },{
                        text: lang('Farmer Group'),
                        flex: 2,
                        dataIndex: 'FarmerGroup'
                    },{
                        text: lang('Location'),
                        flex: 1,
                        dataIndex: 'Village'
                    },{
                        text: lang('Status'),
                        flex: 1,
                        dataIndex: 'AFLStatus'
                    },{
                        text: lang('External Audit'),
                        flex: 1,
                        dataIndex: 'ExternalAudit',
                        renderer: function (value) {
                            var RetVal;

                            if(value != null && value != ''){
                                switch(value){
                                    case '1':
                                        RetVal = lang('No Status');
                                    break;
                                    case '2':
                                        RetVal = lang('Pass');
                                    break;
                                    case '3':
                                        RetVal = lang('Not Pass');
                                    break;
                                    default:
                                        RetVal = '-';
                                    break;
                                }
                            }else{
                                RetVal = '-';
                            }

                            return RetVal;
                        }
                    },{
                        text: lang('First Year of Certification'),
                        flex: 1,
                        dataIndex: 'CertFirstYear'
                    },{
                        text: lang('Internal Inspection Date'),
                        flex: 1,
                        dataIndex: 'ICSDate'
                    },
                    {
                        text: lang('Estimated Harvest Present Year (Kg)'),
                        flex: 1,
                        dataIndex: 'CertNextHarvest'
                    },
                    {
                        text: lang('Previous Year\'s Harvest (Kg)'),
                        flex: 1,
                        dataIndex: 'CertHarvest'
                    },/*{
                        text: lang('Sales Quota'),
                        width: '5%',
                        dataIndex: 'SalesQuota'
                    },*/
                    {
                        text: lang('Total Certified Crop Area (Ha)'),
                        flex: 1,
                        dataIndex: 'CertHectare'
                    },{
                        text: lang('Nr of Cert Plots'),
                        flex: 1,
                        dataIndex: 'CertFarmNr'
                    },{
                        text: lang('Total Palmoil Farm'),
                        flex: 1,
                        dataIndex: 'TotalCocoaFarm'
                    },{
                        text: lang('Total Farm Area (Ha)'),
                        flex: 1,
                        dataIndex: 'TotalHa'
                    }]
                },{
                    xtype: 'panel',
                    title: lang('Coaching'),
                    id: 'imsEventDetailGridCoachingFinal',
                    items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: 1,
                            layout:'form',
                            style:'padding: 0',
                            items:[PanelImsCoaching]
                        }]
                    }]
                },{
                    xtype: 'gridpanel',
                    title: lang('CFL'),
                    minHeight:300,
                    id: 'imsEventDetailGridCFL',
                    style: 'border:2px solid #CCC;',
                    cls: 'Sfr_GridNew',
                    store: store_img_event_detail_grid_cfl,
                    width: '99%',
                    loadMask: true,
                    selType: 'rowmodel',
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data Available')
                    },
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            xtype: 'splitbutton',
                            icon: varjs.config.base_url + 'images/icons/new/export.png',
                            hidden: m_act_export,
                            cls:'Sfr_BtnGridPaleBlue',
                            overCls:'Sfr_BtnGridPaleBlue-Hover',
                            text: lang('Export'),
                            menu: {
                                items: [{
                                    text: lang('Farmer CFL'),
                                    scope: this,
                                    handler: function() {
                                        $prosesCek = cekSaveDulu(IMSID);
                                        if($prosesCek == true){
                                            Ext.MessageBox.show({
                                                msg: 'Loading, please wait...',
                                                progressText: 'Saving...',
                                                width:300,
                                                wait:true,
                                                waitConfig: {interval:200},
                                                icon:'ext-mb-download', //custom class in msg-box.html
                                                iconHeight: 50,
                                                animateTarget: 'mb7'
                                            });

                                            Ext.Ajax.request({
                                                //url: m_api_direct + '/report_sql_view/sql_view_export_excel',
                                                url:  m_api + '/ims/ims_event_detail_cfl_farmer_export/'+IMSID,
                                                method: 'GET',
                                                waitMsg: lang('Please Wait'),
                                                success: function(data) {
                                                    Ext.MessageBox.hide();
                                                    if(!testJSON(data.responseText)){
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
                                                    window.location = jsonResp.filenya;
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
                                    }
                                }]
                            }
                        },{
                            xtype: 'textfield',
                            name: 'imsEventDetailGridCFL_SearchFarmerString',
                            id: 'imsEventDetailGridCFL_SearchFarmerString',
                            emptyText: lang('Farmer ID / Farmer Name'),
                            baseCls:'Sfr_TxtfieldSearchGrid',
                            width: 280
                        }, {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                            margin: '0px 0px 0px 6px',
                            text: lang('Search'),
                            cls:'Sfr_BtnGridGreen',
                            overCls:'Sfr_BtnGridGreen-Hover',
                            handler: function() {
                                store_img_event_detail_grid_cfl.load();
                            }
                        },{
                            xtype:'tbspacer',
                    		flex:1
                        },{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/status_busy.png',
                            margin: '0px 10px 0px 6px',
                            text: lang('Take Out Farmer'),
                            id:'imsEventDetailGridCFL_BtnTakeOutFarmer',
                            cls:'Sfr_BtnGridGreen',
                            overCls:'Sfr_BtnGridGreen-Hover',
                            handler: function() {
                                var WinImsCflTakeOutFarmer = Ext.create('Koltiva.view.IMS.WinImsCflTakeOutFarmer', {
                                    viewVar: {
                                        IMSID: IMSID,
                                        CallerStore: store_img_event_detail_grid_cfl
                                    }
                                });
                                if (!WinImsCflTakeOutFarmer.isVisible()) {
                                    WinImsCflTakeOutFarmer.center();
                                    WinImsCflTakeOutFarmer.show();
                                } else {
                                    WinImsCflTakeOutFarmer.close();
                                }
                            }
                        }]
                    },{
                        xtype: 'pagingtoolbar',
                        store: store_img_event_detail_grid_cfl,
                        dock: 'bottom',
                        displayInfo: true
                    }],
                    columns: [
                    {
                        text: 'ID',
                        dataIndex: 'IMSID',
                        hidden: true
                    },
                    {
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '3%'
                    },{
                        text: 'FarmerID',
                        dataIndex: 'FarmerID',
                        width: '7%'
                    },
                    {
                        text: lang('Farmer Name'),
                        flex: 1,
                        dataIndex: 'FarmerName'
                    },{
                        text: lang('Farmer Group'),
                        flex: 1,
                        dataIndex: 'FarmerGroup'
                    },{
                        text: lang('Location'),
                        width: '8%',
                        dataIndex: 'Village'
                    },{
                        text: lang('Status'),
                        width: '6%',
                        dataIndex: 'AFLStatus'
                    },
                    {
                        text: lang('First Year of Certification'),
                        width: '6%',
                        dataIndex: 'CertFirstYear'
                    },{
                        text: lang('Internal Inspection Date'),
                        width: '5%',
                        dataIndex: 'ICSDate'
                    },
                    {
                        text: lang('Estimated Harvest Present Year (Kg)'),
                        width: '6%',
                        dataIndex: 'CertNextHarvest'
                    },
                    {
                        text: lang('Previous Year\'s Harvest (Kg)'),
                        width: '5%',
                        dataIndex: 'CertHarvest'
                    },{
                        text: lang('Sales Quota'),
                        width: '5%',
                        dataIndex: 'SalesQuota'
                    },
                    {
                        text: lang('Total Certified Crop Area (Ha)'),
                        width: '5%',
                        dataIndex: 'CertHectare'
                    },
                    {
                        text: lang('Nr of Cert Plots'),
                        width: '6%',
                        dataIndex: 'CertFarmNr'
                    },{
                        text: lang('Total Palmoil Farm'),
                        width: '6%',
                        dataIndex: 'TotalCocoaFarm'
                    },{
                        text: lang('Total Farm Area (Ha)'),
                        width: '6%',
                        dataIndex: 'TotalHa'
                    }]

                }],
                listeners: {
                    'tabchange': function (tabPanel, tab) {
                        switch(tab.id){
                            case 'imsEventDetailGridAFL':
                                store_img_event_detail_grid_afl.load();
                            break;
                            case 'imsEventDetailGridIcsReinspect':
                                store_ims_event_detail_grid_ics_reinspect.load();
                            break;
                            case 'imsEventDetailGridAFLFinal':
                                store_img_event_detail_grid_afl_final.load();
                            break;
                        }
                    }
                }
            }]
            }],
        buttons: [{
                id: 'imsCertFormImsEventDetailBtnSave',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                margin: '5px',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                hidden: viewOnly,
                handler: function () {
                    var objThisBtn = this;

                    //status IMS
                    objThisBtn.StatusIMS = parseInt(Ext.ComponentQuery.query('[name=CertEventStatus]')[0].getGroupValue());
                    var FormFieldTanggal = true;
                    objThisBtn.pesanFormSubmit = lang('Are you sure to set this IMS Event to Complete ?') + '<br /><p>' + lang('These field need to be filled to set this to complete') + '</p><ul><li>' + lang('Certification Start') + '</li><li>' + lang('Certification End') + '</li><li>' + lang('Internal Audit Start') + '</li><li>' + lang('Internal Audit End') + '</li><li>' + lang('External Audit Start') + '</li><li>' + lang('External Audit End') + '</li><li>' + lang('Validity Start') + '</li><li>' + lang('Validity End') + '</li><li>' + lang('Certification Issue Date') + '</li></ul>';
                    objThisBtn.FormSubmitGo = true;
                    objThisBtn.arrMsgTgl = [];
                            let ExternalAuditStatus = parseInt(Ext.ComponentQuery.query('[name=ExternalAuditStatus]')[0].getGroupValue());
                            let ICSStatus = parseInt(Ext.ComponentQuery.query('[name=ICSStatus]')[0].getGroupValue());
                            let
                    StatusIcsReinspect = parseInt(Ext.ComponentQuery.query('[name=StatusIcsReinspect]')[0].getGroupValue());
                            var formNya = Ext.getCmp('imsCertFormImsEventDetail').getForm();
                    if (formNya.isValid()) {
                        if (objThisBtn.StatusIMS == 2) {

                            Ext.MessageBox.confirm('Message', objThisBtn.pesanFormSubmit, function (btn) {
                                if (btn == 'yes') {
                                    //Cek Tanggal2 yg Mandatory
                                    if (Ext.getCmp('imsCertificationStart').getValue() == null) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('Certification Start is required'));
                                    }
                                    if (Ext.getCmp('imsCertificationEnd').getValue() == null) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('Certification End is required'));
                                    }
                                    if (Ext.getCmp('imsInternalStart').getValue() == null) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('Internal Audit Start is required'));
                                    }
                                    if (Ext.getCmp('imsInternalEnd').getValue() == null) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('Internal Audit End is required'));
                                    }
                                    if (Ext.getCmp('imsExternalStart').getValue() == null) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('External Audit Start is required'));
                                    }
                                    if (Ext.getCmp('imsExternalEnd').getValue() == null) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('External Audit End is required'));
                                    }
                                    if (Ext.getCmp('imsValidityStart').getValue() == null) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('Validity Start is required'));
                                    }
                                    if (Ext.getCmp('imsValidityEnd').getValue() == null) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('Validity End is required'));
                                    }
                                    if (Ext.getCmp('imsExternalDate').getValue() == null) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('Certification Issued Date is required'));
                                    }

                                    if (StatusIcsReinspect == 1) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('ICS Reinspection is still ongoing'));
                                    }

                                    if (ExternalAuditStatus == 1 || isNaN(ExternalAuditStatus)) {
                                        objThisBtn.FormSubmitGo = false;
                                        objThisBtn.arrMsgTgl.push(lang('External Audit is not set or still ongoing'));
                                    }

                                    if (objThisBtn.FormSubmitGo == false) {
                                        var pesanCompleteNotValidForm = '<p>' + lang('These requirement needs to be meet') + '</p><ul><li>' + objThisBtn.arrMsgTgl.join('</li><li>') + '</li></ul>';
                                        Ext.MessageBox.show({
                                            title: 'Attention',
                                            msg: lang('Form not complete yet') + pesanCompleteNotValidForm,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-info'
                                        });
                                    } else {
                                        //submit formnya
                                        formNya.submit({
                                            url: m_api + '/ims/ims_event_detail',
                                            method: 'POST',
                                            waitMsg: 'Saving data...',
                                            success: function (fp, o) {
                                                Ext.MessageBox.show({
                                                    title: 'Information',
                                                    msg: lang('Data saved'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-success'
                                                });

                                                //load store caller
                                                CallerStore.load();

                                                //tutup popup
                                                winFormImsEventDetail.close();
                                            },
                                            failure: function (fp, o) {
                                                var pesanNya;
                                                if (o.result.message != undefined) {
                                                    pesanNya = o.result.message;
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
                                }
                            });

                        } else {
                            if (objThisBtn.StatusIMS == 3) {
                                objThisBtn.pesanFormSubmit = lang('Are you sure want to canceled this event ?');
                            } else {
                                objThisBtn.pesanFormSubmit = lang('Are you sure want to update this event ?');
                            }
                            Ext.MessageBox.confirm('Message', objThisBtn.pesanFormSubmit, function (btn) {
                                if (btn == 'yes') {
                                    formNya.submit({
                                        url: m_api + '/ims/ims_event_detail',
                                        method: 'POST',
                                        waitMsg: 'Saving data...',
                                        success: function (fp, o) {
                                            Ext.MessageBox.show({
                                                title: 'Information',
                                                msg: lang('Data saved'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-success'
                                            });

                                            //load store caller
                                            CallerStore.load();

                                            //tutup popup
                                            winFormImsEventDetail.close();
                                        },
                                        failure: function (fp, o) {
                                            var pesanNya;
                                            if (o.result.message != undefined) {
                                                pesanNya = o.result.message;
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

                    } else {
                        Ext.MessageBox.show({
                            title: 'Attention',
                            msg: lang('Form not complete yet'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }, {
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                id: 'imsCertFormImsEventDetailBtnClose',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    winFormImsEventDetail.close();
                }
            }]
    });


	// ==================================== Isi Form (BEGIN) ===============================================================//
    Ext.getCmp('imsIMSMasterID').setValue(IMSMasterID);

    if (opsiDisplay == 'insert') {
        //load info CertHolder dan Program Name
        Ext.Ajax.request({
            url: m_api + '/ims/cert_holder_prog_by_ims_master',
            method: 'GET',
            params: {
                IMSMasterID: IMSMasterID
            },
            success: function(fp, action) {
                var data = Ext.decode(fp.responseText);

                Ext.getCmp('imsCertHolderProgramLabel').setValue(data.CertHolderProgramLabel);
                Ext.getCmp('imsSupplychainLabel').setValue(data.SupplychainLabel);
                Ext.getCmp('imsCertProgMemberID').setValue(data.CertProgMemberID);
                Ext.getCmp('imsCertProgMemberDate').setValue(data.CertProgMemberDate);
                Ext.getCmp('imsGIPNumber').setValue(data.GIPNumber);
                Ext.getCmp('imsCertHolderID').setValue(data.CertHolderID);
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

    if (opsiDisplay == 'view' || opsiDisplay == 'update') {
        Ext.getCmp('imsCertFormImsEventDetail').getForm().load({
            url: m_api + '/ims/ims_event_detail_fill_form',
            method: 'GET',
            params: {
                IMSID: IMSID
            },
            success: function(form, action) {
                var r = Ext.decode(action.response.responseText);

                // Ext.getCmp('tbGridAflSummaryTableTitle').setText(lang('Field Agent Data Collection')+' (last update : '+r.data.DateUpdatedSummary+')');

                //Status Event
                Ext.getCmp('rowCertEventStatus').setVisible(true);

                //cek apakah sudah Completed IMS nya
                if(r.data.CertEventStatus == "2" || r.data.CertEventStatus == "3"){
                	Ext.getCmp('imsCandidateTabBtnImportExcel').setVisible(false);
	                Ext.getCmp('imsCandidateTabBtnUpdateGardenPreAFL').setVisible(false);
                    Ext.getCmp('imsCandidateTabBtnImportMappingFA').setVisible(false);
	                Ext.getCmp('imsAFLTabBtnGenAFL').setVisible(false);
	                Ext.getCmp('imsStaffTabBtnAddStaff').setVisible(false);
	                Ext.getCmp('imsBUTabBtnAdd').setVisible(false);
	                Ext.getCmp('imsSummaryTabBtnUpdateDateSum').setVisible(false);

                    //hilangkan btn savenya
                    Ext.getCmp('imsCertFormImsEventDetailBtnSave').setVisible(false);

                    //Button Set Status Reinspection
                    Ext.getCmp('imsEventDetailGridIcsReinspect_BtnIcsReinspectStatus').setDisabled(true);

                    //Disabled kan
                    Ext.getCmp('rowCertEventStatus').setDisabled(true);
                }

                if(r.data.ICSStatus == "1"){
                    //Hilangkan tombol kalau ICS sudah di lock
                    Ext.getCmp('imsAFLTabBtnGenAFL').setVisible(false);
                    Ext.getCmp('imsCandidateTabBtnImportExcel').setVisible(false);
                    Ext.getCmp('imsCandidateTabBtnUpdateGardenPreAFL').setVisible(false);
                    Ext.getCmp('imsCandidateTabBtnImportMappingFA').setVisible(false);
                    Ext.getCmp('imsEventDetailGridIcsReinspect').setDisabled(false);

                    Ext.getCmp('rowICSStatus').setDisabled(true);
                    Ext.getCmp('rowExternalAuditStatus').setVisible(true);
                }else{
                    //Hilangkan tombol kalau ICS masih ongoing
                    // Ext.getCmp('imsEventDetailGridIcsReinspect').setDisabled(true);
                }

                if(r.data.StatusImsFinalPeriod == "1"){ //Jika IMS Finalization Period "Ongoing"
                    Ext.getCmp('imsEventDetailGridCFL_BtnTakeOutFarmer').setVisible(true);
                }else{
                    Ext.getCmp('imsEventDetailGridCFL_BtnTakeOutFarmer').setVisible(false);

                    if(r.data.StatusImsFinalPeriod == "2"){
                        Ext.getCmp('imsFilesTabBtnUploadFile').setVisible(false);
                    }
                }

                if(r.data.StatusIcsReinspect == "1"){ //Status Reinspection Ongoing
                    Ext.getCmp('imsEventDetailGridIcsReinspect_BtnAddFarmer').setDisabled(false);
                    Ext.getCmp('imsEventDetailGridIcsReinspect_BtnRegenerateIcs').setDisabled(false);

                    //Set Title
                    Ext.getCmp('imsEventDetailGridIcsReinspect').setTitle(lang('ICS Reinspection (Ongoing)'));
                }else{
                    // Ext.getCmp('imsEventDetailGridIcsReinspect_BtnAddFarmer').setDisabled(true);
                    // Ext.getCmp('imsEventDetailGridIcsReinspect_BtnRegenerateIcs').setDisabled(true);

                    // if(r.data.StatusIcsReinspect == "2"){
                    //     Ext.getCmp('imsEventDetailGridIcsReinspect').setTitle(lang('ICS Reinspection (Completed)'));
                    // }
                }

                if(r.data.ExternalAuditStatus == "1") {
                    Ext.getCmp('imsAflExternalAuditBtn').setDisabled(false);
                }
                if(r.data.ExternalAuditStatus == "2") {
                    Ext.getCmp('rowExternalAuditStatus').setDisabled(true);
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
    // ==================================== Isi Form (END) =================================================================//

    //show windows
    if (!winFormImsEventDetail.isVisible()) {
        winFormImsEventDetail.center();
        winFormImsEventDetail.show();
    } else {
        winFormImsEventDetail.close();
    }

}


function displayWinUploadFarmerList(IMSID, callerStore){

    var winImsEventUploadFarmerList = Ext.create('widget.window', {
        title: lang('Form IMS - Import Farmer'),
        id: 'winImsEventUploadFarmerList',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '40%',
        height: '25%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
            xtype: 'form',
            id: 'winImsEventUploadFarmerListForm',
            fileUpload: true,
            layout:'form',
            items:[{
                xtype: 'fileuploadfield',
                fieldLabel: lang('File')+' (type: xlsx)',
                labelWidth: 125,
                id: 'imsfile',
                name: 'file',
                buttonText: 'Browse',
                allowBlank: false,
                listeners: {
                    'change': function(fb, v){
                        var form = Ext.getCmp('winImsEventUploadFarmerListForm').getForm();
                        form.submit({
                            url: m_api+'/ims/import_farmer',
                            waitMsg: 'Sending and insert file...',
                            params: {IMSID: IMSID},
                            success: function(fp, o) {
                                var r = Ext.decode(o.response.responseText);
                                
                                if(r.status){
                                    Ext.MessageBox.alert('Success','Proses Import Farmer selesai.<br> '+o.result.berhasil+' berhasil ditambahkan <br> '+o.result.gagal+' gagal ditambahkan / sudah terdaftar.');

                                    callerStore.load();
                                    winImsEventUploadFarmerList.close();
                                }else{
                                    Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                }
                            },
                            failure: function (fp, o) {
                                var r = Ext.decode(o.response.responseText);
                                
                                if(r.status){
                                    Ext.MessageBox.alert('Success','Proses Import Farmer selesai.<br> '+o.result.berhasil+' berhasil ditambahkan <br> '+o.result.gagal+' gagal ditambahkan / sudah terdaftar.');
                                    callerStore.load();
                                    winImsEventUploadFarmerList.close();
                                }else{
                                    Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                }
                            }
                        });
                    }
                }
            },{
                //id:'Koltiva.view.Grower.FormMainGrower-ConsentLetterUrl',
                //html:'<a style="text-decoration:underline;" href="'+varjs.config.base_url+'api/files/template-import-ims-candidate-map-fa.xlsx" target="_blank">Download Template File for Import</a>'
                html: '<a style="text-decoration:underline;" href="' + varjs.config.base_url + 'api/ims/ims_import_farmer/' + IMSID + '" target="_blank">Download Template File for Import</a>'
            }]
        }],
        buttons: [{
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    winImsEventUploadFarmerList.close();
                }
            }]
    });


    //show windows
    if (!winImsEventUploadFarmerList.isVisible()) {
        winImsEventUploadFarmerList.center();
        winImsEventUploadFarmerList.show();
    } else {
        winImsEventUploadFarmerList.close();
    }
}

function testJSON(text){
    try{
        JSON.parse(text);
        return true;
    }
    catch (error){
        return false;
    }
}