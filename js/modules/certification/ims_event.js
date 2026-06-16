/*
 * @Author: nikolius
 * @Date:   2017-10-26 17:09:12
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-07-13 11:21:13
 */

function cekSaveDulu(checkerID){
    if(checkerID != null){
        return true;
    }else{
        Ext.MessageBox.show({
            title: 'Information',
            msg: lang('Data need to be saved first'),
            buttons: Ext.MessageBox.OK,
            animateTarget: 'mb9',
            icon: 'ext-mb-info'
        });

        return false;
    }
}

function displayFormImsEvent(opsiDisplay, IMSMasterID, CallerStore) {
    //atur hak akses
    var viewOnly = true;
    if (opsiDisplay == 'view') {
        viewOnly = true;
    } else {
        viewOnly = false;
    }

    var contextMenuImsEventGridFiles = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/download.png',
                text: lang('Download'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridFiles').getSelectionModel().getSelection()[0];

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
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete File'),
                hidden: m_act_delete,
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridFiles').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Are you sure want to delete this file ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims/ims_event_file_upload',
                                method: 'DELETE',
                                params: {
                                    IDCaller: sm.get('IMSMasterFileID'),
                                    imsType: 'ims_event'
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
                                    store_grid_files.sorters.clear();
                                    store_grid_files.load();
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

    var contextMenuGridMasterStaff = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridStaff').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims/ims_master_staff',
                                method: 'DELETE',
                                params: {
                                    StaffID: sm.get('StaffID'),
                                    IMSMasterID: IMSMasterID
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
                                    store_grid_staff.load();
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

    var contextMenuGridAnnualCert = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                itemId: 'contextMenuGridAnnualCert.View',
                cls: 'Sfr_BtnConMenuWhite',
                text: lang('View'),
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridAnnualCert').getSelectionModel().getSelection()[0];
                    displayFormImsEventDetail('view', IMSMasterID, sm.get('IMSID'), store_grid_annual_certificate);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/date_add.png',
                itemId: 'contextMenuGridAnnualCert.Acquisition',
                text: lang('Acquisition Process'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_acq,
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridAnnualCert').getSelectionModel().getSelection()[0];

                    var WinImsAcqPro = Ext.create('Koltiva.view.IMS.WinImsAcqPro', {
                        viewVar: {
                            IMSID: sm.get('IMSID')
                        }
                    });
                    if (!WinImsAcqPro.isVisible()) {
                        WinImsAcqPro.center();
                        WinImsAcqPro.show();
                    } else {
                        WinImsAcqPro.close();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/user_star.png',
                itemId: 'contextMenuGridAnnualCert.ImsTraining',
                text: lang('Training'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_training,
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridAnnualCert').getSelectionModel().getSelection()[0];

                    var WinImsTraining = Ext.create('Koltiva.view.IMS.WinImsTraining', {
                        viewVar: {
                            IMSID: sm.get('IMSID'),
                            CertEventStatus: sm.get('EventStatusRaw')
                        }
                    });
                    if (!WinImsTraining.isVisible()) {
                        WinImsTraining.center();
                        WinImsTraining.show();
                    } else {
                        WinImsTraining.close();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/user.png',
                itemId: 'contextMenuGridCoaching.Activity',
                cls: 'Sfr_BtnConMenuWhite',
                text: lang('Coaching Activity'),
                // hidden: m_act_acq,
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridAnnualCert').getSelectionModel().getSelection()[0];

                    var WinImsCoachAct = Ext.create('Koltiva.view.IMS.WinImsCoachAct', {
                        viewVar: {
                            IMSID: sm.get('IMSID')
                        }
                    });
                    if (!WinImsCoachAct.isVisible()) {
                        WinImsCoachAct.center();
                        WinImsCoachAct.show();
                    } else {
                        WinImsCoachAct.close();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/page_edit.png',
                itemId: 'contextMenuGridAnnualCert.AssetReceipt',
                text: lang('Asset Receipt'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_asset_receipt,
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridAnnualCert').getSelectionModel().getSelection()[0];
                    var WinImsAssetRcp = Ext.create('Koltiva.view.IMS.WinImsAssetRcp', {
                        viewVar: {
                            IMSID: sm.get('IMSID'),
                            CertEventStatus: sm.get('EventStatusRaw')
                        }
                    });
                    if (!WinImsAssetRcp.isVisible()) {
                        WinImsAssetRcp.center();
                        WinImsAssetRcp.show();
                    } else {
                        WinImsAssetRcp.close();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                itemId: 'contextMenuGridAnnualCert.Update',
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridAnnualCert').getSelectionModel().getSelection()[0];
                    displayFormImsEventDetail('update', IMSMasterID, sm.get('IMSID'), store_grid_annual_certificate);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/script_link.png',
                text: lang('IMS Finalization Period'),
                itemId: 'contextMenuGridAnnualCert.Ifp',
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_ims_finalization_period,
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridAnnualCert').getSelectionModel().getSelection()[0];
                    var WinImsFormFinalizationPeriod = Ext.create('Koltiva.view.IMS.WinImsFormFinalizationPeriod', {
                        viewVar: {
                            IMSID: sm.get('IMSID'),
                            CallerStore: store_grid_annual_certificate
                        }
                    });
                    if (!WinImsFormFinalizationPeriod.isVisible()) {
                        WinImsFormFinalizationPeriod.center();
                        WinImsFormFinalizationPeriod.show();
                    } else {
                        WinImsFormFinalizationPeriod.close();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                itemId: 'contextMenuGridAnnualCert.Delete',
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('imsEventGridAnnualCert').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims/ims_event_detail',
                                method: 'DELETE',
                                params: {
                                    IMSID: sm.get('IMSID')
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
                                    store_grid_annual_certificate.load();
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

    var panelImsMasterDocuments = Ext.create('Koltiva.view.IMS.PanelImsMasterDocuments', {
        viewVar: {
            IMSMasterID: IMSMasterID
        }
    });

    /*============================================ Function (Begin) ==================================================*/
    function imsEventGridAnnualCert_submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store_grid_annual_certificate.load({
                params: {
                    page: 1,
                    start: 0,
                    limit: 50
                }
            });
            store_grid_annual_certificate.loadPage(1);
        }
    }

    function imsEventGridStaff_submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store_grid_staff.load({
                params: {
                    page: 1,
                    start: 0,
                    limit: 50
                }
            });
            store_grid_staff.loadPage(1);
        }
    }
    /*============================================ Function (End)   ==================================================*/

    //==================================================== store & combobox (begin) ===================================================//
    var cmb_certificate_holder = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/combo_certificate_holder',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_grid_annual_certificate = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSID', 'EventName', 'EventYear', 'EventProgram','FirstBuyer', 'Location', 'CertificationStart', 'CertificationEnd', 'DateOfCertification', 'DateValid','NrOfFarmerCert', 'EventStatus','EventStatusRaw','Quota', 'FileKML','Status','TotVolApp','TotHectare'],
        autoLoad: true,
        pageSize: 10,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/grid_annual_certificate',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSMasterID = IMSMasterID;
                store.proxy.extraParams.SearchEventName = Ext.getCmp('imsEventGridAnnualCert_SearchEvent').getValue();
            }
        }
    });

    var store_grid_staff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSStaffID','StaffID', 'StaffName', 'StaffRoleType', 'Gender', 'Email', 'WorkAreaLabel'],
        autoLoad: true,
        pageSize: 20,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_event_grid_staff',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSMasterID = IMSMasterID;
                store.proxy.extraParams.SearchStaffName = Ext.getCmp('imsEventGridStaff_SearchStaffName').getValue();
            }
        }
    });

    var store_grid_summary = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['EventName','EventYear','CFarmer', 'CGarden', 'CPostHarvest', 'CPPI', 'CCert', 'CAuditLog'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_event_grid_summary',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSMasterID = IMSMasterID;
            }
        }
    });

    var store_grid_files = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSMasterFileID','FileName','FilePath', 'FileDesc'],
        autoLoad: false,
        pageSize: 20,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_event_grid_files',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSMasterID = IMSMasterID;
            }
        }
    });
    //==================================================== store & combobox (end)   ===================================================//

    var winFormImsEvent = Ext.create('widget.window', {
        title: lang('Form IMS Event'),
        id: 'imsCertWinFormImsEvent',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '96%',
        height: '90%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'form',
                id: 'imsCertFormImsEvent',
                padding: '5 20 5 8',
                items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                                columnWidth: 0.49,
                                padding: 4,
                                layout: 'form',
                                items: [{
                                        xtype: 'hiddenfield',
                                        id: 'imsCertFormImsEvent_IMSMasterID',
                                        name: 'imsCertFormImsEvent_IMSMasterID'
                                    }, {
                                        xtype: 'combobox',
                                        labelWidth: 225,
                                        fieldLabel: lang('Certificate Holder & Program'),
                                        allowBlank: false,
                                        store: cmb_certificate_holder,
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                        id: 'imsCertFormImsEvent_cmbCertHolder',
                                        name: 'imsCertFormImsEvent_cmbCertHolder',
                                        listeners: {
                                            change: function (cb, nv, ov) {
                                                Ext.Ajax.request({
                                                    url: m_api + '/ims/certification_program_label',
                                                    method: 'GET',
                                                    waitMsg: lang('Sending data...'),
                                                    params: {
                                                        CertHolderID: nv
                                                    },
                                                    success: function (response, opts) {
                                                        var obj = Ext.decode(response.responseText);
                                                        Ext.getCmp('imsCertFormImsEvent_CertHolderProgram').setValue(obj.label);
                                                    },
                                                    failure: function (response, opts) {
                                                        Ext.getCmp('imsCertFormImsEvent_CertHolderProgram').setValue('');
                                                    }
                                                });
                                            }
                                        }
                                    }, {
                                        xtype: 'textfield',
                                        id: 'imsCertFormImsEvent_CertHolderProgram',
                                        fieldLabel: lang('Program Name'),
                                        labelWidth: 225,
                                        readOnly: true
                                    }, {
                                        xtype: 'datefield',
                                        fieldLabel: lang('Date Established'),
                                        labelWidth: 225,
                                        allowBlank: false,
                                        id: 'imsCertFormImsEvent_DateEstablished',
                                        name: 'imsCertFormImsEvent_DateEstablished',
                                        format: 'Y-m-d'
                                    }]
                            }, {
                                columnWidth: 0.49,
                                padding: 4,
                                layout: 'form',
                                items: [{
                                        xtype: 'textareafield',
                                        labelWidth: 225,
                                        id: 'imsCertFormImsEvent_Description',
                                        name: 'imsCertFormImsEvent_Description',
                                        fieldLabel: lang('Description'),
                                        anchor: '100%'
                                    }]
                            }]
                    }, {
                        xtype: 'tabpanel',
                        flex: 1,
                        margin: 2,
                        activeTab: 0,
                        plain: true,
                        items: [{
                                xtype: 'gridpanel',
                                title: lang('Annual Certificate'),
                                id: 'imsEventGridAnnualCert',
                                style: 'border:1px solid #CCC;',
                                cls: 'Sfr_GridNew',
                                store: store_grid_annual_certificate,
                                width: '100%',
                                minHeight:300,
                                loadMask: true,
                                selType: 'rowmodel',
//                                listeners: {
//                                    itemclick: function (view, record, item, index, e) {
//                                        contextMenuGridAnnualCert.showAt(e.getXY());
//
//                                        var sm = record;
//                                        if (sm.data.Status == '2') { //CertEventStatus
//                                            //Jika Complete, Maka tidak boleh Update / Delete
//                                            contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Update').setVisible(false);
//                                            contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Delete').setVisible(false);
//                                            contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Ifp').setDisabled(false);
//                                        } else {
//                                            if (m_act_update == false)
//                                                contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Update').setVisible(true);
//                                            else
//                                                contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Update').setVisible(false);
//                                            if (m_act_delete == false)
//                                                contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Delete').setVisible(true);
//                                            else
//                                                contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Delete').setVisible(false)
//
//                                            contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Ifp').setDisabled(true);
//                                        }
//                                    }
//                                },
                                dockedItems: [{
                                        xtype: 'pagingtoolbar',
                                        store: store_grid_annual_certificate,
                                        dock: 'bottom',
                                        displayInfo: true
                                    }, {
                                        xtype: 'toolbar',
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                                text: lang('Add'),
                                                cls: 'Sfr_BtnGridGreen',
                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                hidden: m_act_add,
                                                scope: this,
                                                handler: function () {
                                                    $prosesCek = cekSaveDulu(IMSMasterID);
                                                    if ($prosesCek == true) {
                                                        displayFormImsEventDetail('insert', IMSMasterID, null, store_grid_annual_certificate);
                                                    }
                                                }
                                            }, {
                                                xtype: 'textfield',
                                                emptyText: lang('Event Name'),
                                                width: 300,
                                                baseCls:'Sfr_TxtfieldSearchGrid',
                                                name: 'imsEventGridAnnualCert_SearchEvent',
                                                id: 'imsEventGridAnnualCert_SearchEvent',
                                                listeners: {
                                                    specialkey: imsEventGridAnnualCert_submitOnEnter
                                                }
                                            }, {
                                                xtype: 'button',
                                                margin: '0px 0px 0px 6px',
                                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                text: lang('Search'),
                                                cls:'Sfr_BtnGridBlue',
                                                overCls:'Sfr_BtnGridBlue-Hover',
                                                handler: function () {
                                                    store_grid_annual_certificate.load({
                                                        params: {
                                                            page: 1,
                                                            start: 0,
                                                            limit: 50
                                                        }
                                                    });
                                                    store_grid_annual_certificate.loadPage(1);
                                                }
                                            }]
                                    }],
                                viewConfig: {
                                    deferEmptyText: false,
                                    emptyText: lang('No data Available')
                                },
                                columns: [{
                                        text: '',
                                        xtype: 'actioncolumn',
                                        width: '4%',
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                                    contextMenuGridAnnualCert.showAt(e.getXY());
                                                    var sm = record;
                                                    if (sm.data.Status == '2') { //CertEventStatus
                                                        //Jika Complete, Maka tidak boleh Update / Delete
                                                        contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Update').setVisible(false);
                                                        contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Delete').setVisible(false);
                                                        contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Ifp').setDisabled(false);
                                                    } else {
                                                        if (m_act_update == false)
                                                            contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Update').setVisible(true);
                                                        else
                                                            contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Update').setVisible(false);
                                                        if (m_act_delete == false)
                                                            contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Delete').setVisible(true);
                                                        else
                                                            contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Delete').setVisible(false)

                                                        contextMenuGridAnnualCert.getComponent('contextMenuGridAnnualCert.Ifp').setDisabled(true);
                                                    }
                                                }
                                            }]
                                    }, {
                                        dataIndex: 'FileKML',
                                        hidden: true
                                    }, {
                                        dataIndex: 'Status',
                                        hidden: true
                                    }, {
                                        text: 'No',
                                        xtype: 'rownumberer',
                                        width: '3%'
                                    }, {
                                        text: lang('IMSID'),
                                        dataIndex: 'IMSID',
                                        width: '4%',
                                    }, {
                                        dataIndex: 'EventName',
                                        text: lang('Event Name'),
                                        flex: 1
                                    }, {
                                        dataIndex: 'EventProgram',
                                        text: lang('Year of Certification'),
                                        width: '6%'
                                    }, {
                                        dataIndex: 'FirstBuyer',
                                        text: lang('First Buyer'),
                                        width: '8%'
                                    }, {
                                        dataIndex: 'Location',
                                        text: lang('Location'),
                                        width: '9%'
                                    }, {
                                        dataIndex: 'EventStatus',
                                        text: lang('Event Status'),
                                        width: '6%'
                                    }, {
                                        dataIndex: 'EventStatusRaw',
                                        text: lang('Event Status Raw'),
                                        hidden: true
                                    }, {
                                        dataIndex: 'DateValid',
                                        text: lang('Validity of Certificate'),
                                        width: '12%'
                                    }, {
                                        xtype: 'numbercolumn',
                                        dataIndex: 'NrOfFarmerCert',
                                        text: lang('# Farmers Certified'),
                                        width: '8%',
                                        format: '0,000'
                                    }, {
                                        xtype: 'numbercolumn',
                                        dataIndex: 'TotHectare',
                                        text: lang('Total Certified Area (Ha)'),
                                        width: '8%',
                                        format: '0,000'
                                    }, {
                                        xtype: 'numbercolumn',
                                        dataIndex: 'TotVolApp',
                                        text: lang('Total Volume Approve (MT)'),
                                        width: '10%',
                                        format: '0,000.00'
                                    }, {
                                        xtype: 'numbercolumn',
                                        dataIndex: 'Quota',
                                        text: lang('Sales Quota (MT)'),
                                        width: '8%',
                                        format: '0,000.00'
                                    }]
                            }, {
                                xtype: 'gridpanel',
                                title: lang('Staff'),
                                id: 'imsEventGridStaff',
                                style: 'border:1px solid #CCC;',
                                store: store_grid_staff,
                                cls: 'Sfr_GridNew',
                                width: '100%',
                                minHeight:300,
                                loadMask: true,
                                selType: 'rowmodel',
                                listeners: {
                                    itemclick: function (view, record, item, index, e) {
                                        contextMenuGridMasterStaff.showAt(e.getXY());
                                    }
                                },
                                dockedItems: [{
                                        xtype: 'pagingtoolbar',
                                        store: store_grid_staff,
                                        dock: 'bottom',
                                        displayInfo: true
                                    }, {
                                        xtype: 'toolbar',
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                                text: lang('Add'),
                                                hidden: m_act_add,
                                                cls: 'Sfr_BtnGridGreen',
                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                scope: this,
                                                handler: function () {
                                                    $prosesCek = cekSaveDulu(IMSMasterID);
                                                    if ($prosesCek == true) {
                                                        displayWinFormImsStaff(IMSMasterID, store_grid_staff);
                                                    }
                                                }
                                            }, {
                                                xtype: 'textfield',
                                                emptyText: lang('Staff Name'),
                                                width: 300,
                                                baseCls:'Sfr_TxtfieldSearchGrid',
                                                name: 'imsEventGridStaff_SearchStaffName',
                                                id: 'imsEventGridStaff_SearchStaffName',
                                                listeners: {
                                                    specialkey: imsEventGridStaff_submitOnEnter
                                                }
                                            }, {
                                                xtype: 'button',
                                                margin: '0px 0px 0px 6px',
                                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                text: lang('Search'),
                                                cls:'Sfr_BtnGridBlue',
                                                overCls:'Sfr_BtnGridBlue-Hover',
                                                handler: function () {
                                                    store_grid_staff.load({
                                                        params: {
                                                            page: 1,
                                                            start: 0,
                                                            limit: 50
                                                        }
                                                    });
                                                    store_grid_staff.loadPage(1);
                                                }
                                            }]
                                    }],
                                viewConfig: {
                                    deferEmptyText: false,
                                    emptyText: lang('No data Available')
                                },
                                columns: [{
                                        text: '',
                                        xtype: 'actioncolumn',
                                        width: '4%',
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                                    contextMenuGridMasterStaff.showAt(e.getXY());
                                                }
                                            }]
                                    }, {
                                        dataIndex: 'IMSStaffID',
                                        hidden: true
                                    }, {
                                        dataIndex: 'StaffID',
                                        hidden: true
                                    }, {
                                        text: 'No',
                                        xtype: 'rownumberer',
                                        width: '5%'
                                    }, {
                                        dataIndex: 'StaffName',
                                        text: lang('Staff Name'),
                                        flex: 2
                                    }, {
                                        dataIndex: 'StaffRoleType',
                                        text: lang('Type'),
                                        flex: 1
                                    }, {
                                        dataIndex: 'Gender',
                                        text: lang('Gender'),
                                        flex: 1
                                    }, {
                                        dataIndex: 'Email',
                                        text: lang('Email'),
                                        flex: 2
                                    }, {
                                        dataIndex: 'WorkAreaLabel',
                                        text: lang('Work Area'),
                                        flex: 1
                                    }]
                            }, {
                                xtype: 'gridpanel',
                                title: lang('Summary'),
                                cls: 'Sfr_GridNew',
                                hidden: true,
                                id: 'imsEventGridSummary',
                                style: 'border:1px solid #CCC;',
                                store: store_grid_summary,
                                width: '100%',
                                minHeight:300,
                                loadMask: true,
                                selType: 'rowmodel',
                                dockedItems: [{
                                        xtype: 'toolbar',
                                        items: [{
                                                xtype: 'button',
                                                icon: varjs.config.base_url + 'images/icons/new/reload.png',
                                                margin: '0px 0px 0px 6px',
                                                cls: 'Sfr_BtnGridGreen',
                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                text: lang('Refresh'),
                                                handler: function () {
                                                    store_grid_summary.load();
                                                }
                                            }]
                                    }],
                                viewConfig: {
                                    deferEmptyText: false,
                                    emptyText: lang('No Summary')
                                },
                                columns: [{
                                        text: 'No',
                                        xtype: 'rownumberer',
                                        width: '4%',
                                    }, {
                                        text: lang('Event Name'),
                                        dataIndex: 'EventName',
                                        flex: 1
                                    }, {
                                        text: lang('Year'),
                                        width: '6%',
                                        dataIndex: 'EventYear'
                                    }, {
                                        text: lang('Farmer'),
                                        dataIndex: 'CFarmer',
                                        flex: 1
                                    }, {
                                        text: lang('Garden'),
                                        width: '10%',
                                        dataIndex: lang('CGarden')
                                    }, {
                                        text: lang('Post Harvest'),
                                        width: '10%',
                                        dataIndex: 'CPostHarvest'
                                    }, {
                                        text: lang('PPI'),
                                        width: '10%',
                                        dataIndex: 'CPPI'
                                    }, {
                                        text: lang('Certification'),
                                        width: '10%',
                                        dataIndex: 'CCert'
                                    }, {
                                        text: lang('Audit Log'),
                                        width: '9%',
                                        dataIndex: 'CAuditLog'
                                    }]
                            }, {
                                xtype: 'gridpanel',
                                title: lang('Files'),
                                id: 'imsEventGridFiles',
                                style: 'border:1px solid #CCC;',
                                store: store_grid_files,
                                cls: 'Sfr_GridNew',
                                width: '100%',
                                minHeight:300,
                                loadMask: true,
                                selType: 'rowmodel',
                                dockedItems: [{
                                        xtype: 'pagingtoolbar',
                                        store: store_grid_files,
                                        dock: 'bottom',
                                        displayInfo: true
                                    }, {
                                        xtype: 'toolbar',
                                        items: [{
                                                xtype: 'button',
                                                icon: varjs.config.base_url + 'images/icons/new/reload.png',
                                                margin: '0px 0px 0px 6px',
                                                text: lang('Refresh'),
                                                cls:'Sfr_BtnGridBlue',
                                                overCls:'Sfr_BtnGridBlue-Hover',
                                                handler: function () {
                                                    store_grid_files.sorters.clear();
                                                    store_grid_files.load();
                                                }
                                            }, {
                                                xtype: 'button',
                                                icon: varjs.config.base_url + 'images/icons/new/upload_white.png',
                                                margin: '0px 0px 0px 6px',
                                                text: lang('Upload File'),
                                                cls:'Sfr_BtnGridGreen',
                                                overCls:'Sfr_BtnGridGreen-Hover',
                                                hidden: m_act_add,
                                                handler: function () {
                                                    $prosesCek = cekSaveDulu(IMSMasterID);
                                                    if ($prosesCek == true) {
                                                        displayWinImsEventFileUpload('ims_event', IMSMasterID, store_grid_files);
                                                    }
                                                }
                                            }]
                                    }],
                                viewConfig: {
                                    deferEmptyText: false,
                                    emptyText: lang('No Files')
                                },
                                columns: [ {
                                        text: lang('Action'),
                                        xtype: 'actioncolumn',
                                        width: '4%',
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                                    contextMenuImsEventGridFiles.showAt(e.getXY());
                                                }
                                            }]
                                    }, {
                                        dataIndex: 'IMSMasterFileID',
                                        hidden: true
                                    }, {
                                        text: 'No',
                                        xtype: 'rownumberer',
                                        width: '3%'
                                    }, {
                                        text: lang('Files'),
                                        dataIndex: 'FileName',
                                        flex: 1
                                    }, {
                                        dataIndex: 'FilePath',
                                        hidden: true
                                    }, {
                                        text: lang('Remark'),
                                        dataIndex: 'FileDesc',
                                        flex: 1
                                    }]
                            }, {
                                xtype: 'panel',
                                title: lang('Documents'),
                                id: 'imsEventGridDocuments',
                                items: [{
                                    layout: 'column',
                                    border: false,
                                    items: [{
                                        columnWidth: 1,
                                        layout: 'form',
                                        style: 'padding: 0',
                                        items: [panelImsMasterDocuments]
                                    }]
                                }]
                            }],
                        listeners: {
                            'tabchange': function (tabPanel, tab) {
                                switch (tab.id) {
                                    case 'imsEventGridSummary':
                                        store_grid_summary.load();
                                        break;
                                    case 'imsEventGridFiles':
                                        store_grid_files.load();
                                        break;
                                    case 'imsEventGridDocuments':
                                        panelImsMasterDocuments.store.load();
                                        break;
                                }
                            }
                        }
                    }]
            }],
        buttons: [{
                id: 'imsCertFormImsEventBtnSave',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                margin: '5px',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                hidden: viewOnly,
                handler: function () {

                    var formNya = Ext.getCmp('imsCertFormImsEvent').getForm();
                    if (formNya.isValid()) {

                        formNya.submit({
                            url: m_api + '/ims/ims_event',
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
                                winFormImsEvent.close();
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

                    } else {
                        Ext.MessageBox.show({
                            title: 'Attention',
                            msg: lang('Form not valid yet'),
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
                id: 'imsCertFormImsEventBtnClose',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    winFormImsEvent.close();
                }
            }]
    });
    // ====================================================== Isi Form (BEGIN) ========================================================================================================//
    if (opsiDisplay == 'view' || opsiDisplay == 'update') {
        Ext.getCmp('imsCertFormImsEvent').getForm().load({
            url: m_api + '/ims/ims_master_fill_form',
            method: 'GET',
            params: {
                IMSMasterID: IMSMasterID
            },
            success: function(form, action) {
                var r = Ext.decode(action.response.responseText);

                if(opsiDisplay == 'view'){
                    Ext.getCmp('imsCertFormImsEvent_cmbCertHolder').setDisabled(true);
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
    // ====================================================== Isi Form (END) ========================================================================================================//

    //show windows
    if (!winFormImsEvent.isVisible()) {
        winFormImsEvent.center();
        winFormImsEvent.show();
    } else {
        winFormImsEvent.close();
    }
}


// ======================================== Form Upload Files (BEGIN) ===========================================================================//
function displayWinImsEventFileUpload(callForm,IDCaller,StoreCaller){

    var winImsEventFileUpload = Ext.create('widget.window', {
        title: lang('Form IMS - File Upload'),
        id: 'winImsEventFileUpload',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '40%',
        height: '50%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'form',
                id: 'winImsEventFileUploadForm',
                fileUpload: true,
                layout: 'form',
                items: [{
                        xtype: 'fileuploadfield',
                        fieldLabel: lang('File'),
                        labelWidth: 125,
                        id: 'winImsEventFileUploadForm_File',
                        name: 'winImsEventFileUploadForm_File',
                        buttonText: 'Browse',
                        allowBlank: false
                    }, {
                        xtype: 'textareafield',
                        labelWidth: 125,
                        id: 'winImsEventFileUploadForm_Description',
                        name: 'winImsEventFileUploadForm_Description',
                        fieldLabel: lang('Description'),
                        anchor: '100%'
                    }, {
                        xtype: 'hiddenfield',
                        id: 'winImsEventFileUploadForm_callForm',
                        name: 'winImsEventFileUploadForm_callForm',
                        value: callForm
                    }, {
                        xtype: 'hiddenfield',
                        id: 'winImsEventFileUploadForm_IDCaller',
                        name: 'winImsEventFileUploadForm_IDCaller',
                        value: IDCaller
                    }]
            }],
        buttons: [{
                id: 'winImsEventFileUploadFormBtnSave',
                text: lang('Upload'),
                margin: '5px',
                cls:'Sfr_BtnFormBlue',
                overCls:'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    var form = Ext.getCmp('winImsEventFileUploadForm').getForm();
                    form.submit({
                        url: m_api + '/ims/ims_event_file_upload',
                        waitMsg: 'Uploding File...',
                        success: function (fp, o) {
                            //console.log(fp);

                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('File Uploaded'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });


                            StoreCaller.sorters.clear();
                            StoreCaller.load();
                            winImsEventFileUpload.close();
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
            }, {
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                id: 'winImsEventFileUploadFormBtnClose',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    winImsEventFileUpload.close();
                }
            }]
    });

    //show windows
    if (!winImsEventFileUpload.isVisible()) {
        winImsEventFileUpload.center();
        winImsEventFileUpload.show();
    } else {
        winImsEventFileUpload.close();
    }
}
// ========================================== Form Upload Files (END) ===========================================================================//