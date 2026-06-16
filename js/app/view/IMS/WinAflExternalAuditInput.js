/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Oct 25 2019
 *  File : WinAflExternalAuditInput.js
 *******************************************/

Ext.define('Koltiva.view.IMS.WinAflExternalAuditInput', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinAflExternalAuditInput',
    title: lang('External Audit - Choose Farmer'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '90%',
    height: '88%',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    overflowY: 'auto',
    initComponent: function () {
        var thisObj = this;

        thisObj.MainGrid = Ext.create('Koltiva.store.IMS.GridExternalAuditInput', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID,
                TextSearch: null
            }
        });

        //items -------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinAflExternalAuditInput-Form',
            columnWidth: 1,
            padding: '5 20 5 10',
            items: [{
                xtype: 'grid',
                id: 'Koltiva.view.IMS.WinAflExternalAuditInput-MainGrid',
                style: 'border:1px solid #CCC;margin-top:4px;',
                title: 'Farmer',
                cls: 'Sfr_GridNew',
                loadMask: true,
                selType: 'checkboxmodel',
                store: thisObj.MainGrid,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No data Available')
                },
                dockedItems: [{
                    xtype: 'pagingtoolbar',
                    store: thisObj.MainGrid,
                    dock: 'bottom',
                    displayInfo: true
                },{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                        name: 'Koltiva.view.IMS.WinAflExternalAuditInput-TextSearch',
                        id: 'Koltiva.view.IMS.WinAflExternalAuditInput-TextSearch',
                        xtype: 'textfield',
                        baseCls: 'Sfr_TxtfieldSearchGrid',
                        width: 400,
                        emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('press_enter_search'),
                        listeners: {
                            specialkey: function (f, e) {
                                if (e.getKey() == e.ENTER) {
                                    thisObj.MainGrid.storeVar.TextSearch = Ext.getCmp('Koltiva.view.IMS.WinAflExternalAuditInput-TextSearch').getValue();
                                    thisObj.MainGrid.load();
                                }
                            }
                        }
                    }]
                }],
                columns: [{
                    HeaderCheckbox: true,
                    dataIndex: 'CheckData',
                    flex: 0.2
                }, {
                    text: 'No',
                    xtype: 'rownumberer',
                    align: 'center',
                    flex: 0.2
                },{
                    text: 'FarmerID',
                    dataIndex: 'FarmerID',
                    flex: 0.75
                },
                {
                    text: lang('Farmer Name'),
                    flex: 1.5,
                    dataIndex: 'FarmerName'
                },{
                    text: lang('CPG'),
                    flex: 1.5,
                    dataIndex: 'FarmerGroup'
                },{
                    text: lang('Location'),
                    flex: 1.5,
                    dataIndex: 'Village'
                },{
                    text: lang('Status'),
                    flex: 0.75,
                    dataIndex: 'AFLStatus'
                },
                {
                    text: lang('First Year of Certification'),
                    flex: 0.75,
                    dataIndex: 'CertFirstYear'
                },{
                    text: lang('Internal Inspection Date'),
                    flex: 0.75,
                    dataIndex: 'ICSDate'
                },
                {
                    text: lang('Estimated Harvest Present Year (Kg)'),
                    flex: 0.75,
                    dataIndex: 'CertNextHarvest'
                },{
                    text: lang('Previous Year\'s Harvest (Kg)'),
                    flex: 0.75,
                    dataIndex: 'CertHarvest'
                },{
                    text: lang('Total Certified Crop Area (Ha)'),
                    flex: 0.75,
                    dataIndex: 'CertHectare'
                },{
                    text: lang('Nr of Cert Plots'),
                    flex: 0.75,
                    dataIndex: 'CertFarmNr'
                },{
                    text: lang('Total Cocoa Farm'),
                    flex: 0.75,
                    dataIndex: 'TotalCocoaFarm'
                },{
                    text: lang('Total Farm Area (Ha)'),
                    flex: 0.75,
                    dataIndex: 'TotalHa'
                }]
            }]
        }];
        //items -------------------------------------------------------------- (End)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinAflExternalAuditInput-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinAflExternalAuditInput-Form').getForm();
                    var GridSelected = Ext.getCmp('Koltiva.view.IMS.WinAflExternalAuditInput-MainGrid').getSelectionModel().getSelection();

                    var IdSelectedArr = [];
                    for (var i = GridSelected.length - 1; i >= 0; i--) {
                        IdSelectedArr.push(GridSelected[i].get('FarmerID'));
                    }

                    if (IdSelectedArr.length > 0) {
                        var FarmerIDSel = IdSelectedArr.join(',');

                        Ext.MessageBox.confirm('Message', lang('Are you sure to choose this farmers?'), function (btn) {
                            if (btn == 'yes') {
                                FormNya.submit({
                                    url: m_api + '/ims/external_audit_input',
                                    method: 'POST',
                                    params: {
                                        FarmerIDSel: FarmerIDSel,
                                        IMSID: thisObj.viewVar.IMSID
                                    },
                                    waitMsg: 'Saving data...',
                                    success: function (fp, o) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data saved'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //form reset
                                        FormNya.reset();

                                        //refresh page content
                                        Ext.getCmp('imsEventDetailGridAFLFinal').getStore().loadPage(1);

                                        //tutup popup
                                        thisObj.close();
                                    },
                                    failure: function (fp, o) {
                                        var pesanNya;
                                        if (o.result.message != undefined) {
                                            pesanNya = o.result.message;
                                        } else {
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
                            }
                        });
                    } else {
                        Ext.MessageBox.show({
                            title: lang('Attention'),
                            msg: lang('No farmer selected'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }, {
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});