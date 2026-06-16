/*
 * @Author: nikolius
 * @Date:   2017-10-31 10:29:20
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-07-03 10:28:29
 */

/*=======================================Garden (Begin)===================================================*/
function winImsEventDetailFarmerGarden(FarmerID,IMSID) {
    //variabel =============================================================
    var contextMenuImsEventDetailGridFarmerGardenDetail = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/application_view_list.png',
                text: lang('View'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('winGridImsEventDetailFarmerGarden_MainGrid').getSelectionModel().getSelection()[0];
                    displayWinGridImsEventDetailFarmerGardenForm('view', smb.get('FarmerID'), smb.get('SurveyNr'), smb.get('GardenNr'));
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function () {
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

    //store =====================================================================
    var store_ims_event_detail_grid_farmer_garden_detail = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID', 'FarmerName', 'GardenNr', 'SurveyNr', 'ha', 'Production', 'CalculatedProduction'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_farmer_garden',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.FarmerID = FarmerID;
                store.proxy.extraParams.IMSID = IMSID;
            }
        }
    });

    var winGridImsEventDetailFarmerGarden = Ext.create('widget.window', {
        title: lang('Farmer Garden Detail'),
        id: 'winGridImsEventDetailFarmerGarden',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '85%',
        height: '60%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'gridpanel',
                id: 'winGridImsEventDetailFarmerGarden_MainGrid',
                style: 'border:2px solid #CCC;',
                cls: 'Sfr_GridNew',
                store: store_ims_event_detail_grid_farmer_garden_detail,
                width: '99%',
                loadMask: true,
                selType: 'rowmodel',
//                listeners: {
//                    itemclick: function (view, record, item, index, e) {
//                        contextMenuImsEventDetailGridFarmerGardenDetail.showAt(e.getXY());
//                    }
//                },
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No data Available')
                },
                columns: [{
                        text: lang('Action'),
                        xtype: 'actioncolumn',
                        width: '5%',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    contextMenuImsEventDetailGridFarmerGardenDetail.showAt(e.getXY());
                                }
                            }]
                    }, {
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '3%'
                    }, {
                        text: lang('Farmer ID'),
                        dataIndex: 'FarmerID',
                        hidden: true
                    }, {
                        text: lang('Farmer Name'),
                        dataIndex: 'FarmerName',
                        flex: 1
                    }, {
                        text: lang('Survey Number'),
                        dataIndex: 'SurveyNr',
                        width: '12%'
                    }, {
                        text: lang('Plot Number'),
                        dataIndex: 'GardenNr',
                        width: '10%'
                    }, {
                        text: lang('Area of Garden (Ha)'),
                        dataIndex: 'ha',
                        width: '15%'
                    }, {
                        text: lang('Estimated production (Kg/Year)'),
                        dataIndex: 'Production',
                        width: '17%'
                    }, {
                        text: lang('Calculated production (Kg/Year)'),
                        dataIndex: 'CalculatedProduction',
                        width: '17%'
                    }]
            }],
        buttons: [{
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                id: 'winGridImsEventDetailFarmerGardenBtnClose',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    winGridImsEventDetailFarmerGarden.close();
                }
            }]
    });

    //show windows
    if (!winGridImsEventDetailFarmerGarden.isVisible()) {
        winGridImsEventDetailFarmerGarden.center();
        winGridImsEventDetailFarmerGarden.show();
    } else {
        winGridImsEventDetailFarmerGarden.close();
    }
}

function displayWinGridImsEventDetailFarmerGardenForm(opsiDisplay, FarmerID, SurveyNr, GardenNr) {
    //atur hak akses
    var viewOnly = true;
    if (opsiDisplay == 'view') {
        viewOnly = true;
    } else {
        viewOnly = false;
    }

    //function & variabel2 (begin)
    function perkiraanProduksi() {
        Ext.getCmp('imsEditperkiraanproduktifitas').setValue(parseInt((parseFloat(Ext.getCmp('imsEditperkiraanproduksi').getValue()) || 0) / (parseFloat(Ext.getCmp('imsEditGardenHaUnCertified').getValue()) || 1)));
    }

    function TotalBulan() {
        Ext.getCmp('imsEditTotalBulan').setValue((parseFloat(Ext.getCmp('imsEditPanenTrekMonths').getValue()) || 0) + (parseFloat(Ext.getCmp('imsEditPanenBiasaMonths').getValue()) || 0) + (parseFloat(Ext.getCmp('imsEditPanenRayaMonths').getValue()) || 0));
        if (parseFloat(Ext.getCmp('imsEditTotalBulan').getValue()) != 12) Ext.getCmp('imsEditTotalBulan').addCls('notif-red');
        else Ext.getCmp('imsEditTotalBulan').removeCls('notif-red');
    }

    function KgPerTahunTrek(cb, nv, ov) {
        Ext.getCmp('imsEditPanenTrekKgThn').setValue(parseInt((parseFloat(Ext.getCmp('imsEditPanenTrekMonths').getValue()) || 0) * (parseFloat(Ext.getCmp('imsEditPanenTrekPanenMonth').getValue()) || 0) * (parseFloat(Ext.getCmp('imsEditPanenTrekKg').getValue()) || 0)));
        // console.log('KgPerTahunTrek');
        totalpanen(cb, nv, ov)
    }

    function KgPerTahunBiasa(cb, nv, ov) {
        Ext.getCmp('imsEditPanenBiasaKgThn').setValue(parseInt((parseFloat(Ext.getCmp('imsEditPanenBiasaMonths').getValue()) || 0) * (parseFloat(Ext.getCmp('imsEditPanenBiasaPanenMonth').getValue()) || 0) * (parseFloat(Ext.getCmp('imsEditPanenBiasaKg').getValue()) || 0)));
        // console.log('KgPerTahunBiasa');
        totalpanen(cb, nv, ov)
    }

    function KgPerTahunRaya(cb, nv, ov) {
        Ext.getCmp('imsEditPanenRayaKgThn').setValue(parseInt((parseFloat(Ext.getCmp('imsEditPanenRayaMonths').getValue()) || 0) * (parseFloat(Ext.getCmp('imsEditPanenRayaPanenMonth').getValue()) || 0) * (parseFloat(Ext.getCmp('imsEditPanenRayaKg').getValue()) || 0)));
        // console.log('KgPerTahunRaya');
        totalpanen(cb, nv, ov)
    }

    function totalpanen(cb, nv, ov) {
        Ext.getCmp('imsEdittotalpanen').setValue((parseFloat(Ext.getCmp('imsEditPanenTrekKgThn').getValue()) || 0) + (parseFloat(Ext.getCmp('imsEditPanenBiasaKgThn').getValue()) || 0) + (parseFloat(Ext.getCmp('imsEditPanenRayaKgThn').getValue()) || 0));
        Ext.getCmp('imsEdittotalproduktivitas').setValue(parseInt((parseFloat(Ext.getCmp('imsEdittotalpanen').getValue()) || 0) / (parseFloat(Ext.getCmp('imsEditGardenHaUnCertified').getValue()) || 1)));
        // Ext.getCmp('perkiraanproduksi').setValue(parseInt((parseFloat(Ext.getCmp('totalpanen').getValue()) || 0)));
        perkiraanProduksi();
        // console.log(parseFloat(Ext.getCmp('totalpanen').getValue()));
        if (parseFloat(Ext.getCmp('imsEdittotalproduktivitas').getValue()) < 0 || parseFloat(Ext.getCmp('imsEdittotalproduktivitas').getValue()) > 4000) {
            Ext.MessageBox.alert('Warning', lang('Total produktifitas maksimal 4000 kg'));
            cb.setValue(ov);
            // Ext.getCmp('totalpanen').addCls('notif-red');
        } else {
            // Ext.getCmp('totalpanen').removeCls('notif-red');
        }
    }

    //function & variabel2 (end)

    //store (begin)
    var surveys = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/surveys',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var programs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/programs',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.SupplychainID = Ext.getCmp('imsSupplychainID').getValue();
            }
        }
    });

    var status_audit = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": "Passed"
        }, {
            "id": "2",
            "label": "Not Passed"
        }, {
            "id": "3",
            "label": "Passed with condition"
        }]
    });

    var interval_panen = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "0",
            "label": lang("Tidak ada")
        }, {
            "id": "4",
            "label": lang("1 kali/minggu")
        }, {
            "id": "2",
            "label": lang("1 kali/2 minggu")
        }, {
            "id": "1",
            "label": lang("1 kali/bulan")
        }]
    });
    //store (end)

    var winGridImsEventDetailFarmerGardenForm = Ext.create('widget.window', {
        title: lang('Farmer Garden Certification Data'),
        id: 'winGridImsEventDetailFarmerGardenForm',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '50%',
        height: '80%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'form',
                id: 'winGridImsEventDetailFarmerGardenForm_Form',
                padding: '5 20 5 8',
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 250,
                    padding: 10
                },
                items: [{
                        xtype: 'panel',
                        items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                        columnWidth: 1,
                                        layout: 'form',
                                        items: [{
                                                xtype: 'textfield',
                                                id: 'imsEditType',
                                                name: 'EditType',
                                                hidden: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('FarmerID'),
                                                id: 'imsEditFarmerID',
                                                name: 'FarmerID',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Farmer Name'),
                                                id: 'imsEditFarmerName',
                                                name: 'FarmerName',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultSurveyNr',
                                                name: 'DefaultSurveyNr',
                                                hidden: true
                                            }, {
                                                id: 'imsEditSurveyNr',
                                                name: 'SurveyNr',
                                                xtype: 'combobox',
                                                fieldLabel: lang('Survey Number'),
                                                store: surveys,
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false,
                                                queryMode: 'local'
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultGardenNr',
                                                name: 'DefaultGardenNr',
                                                hidden: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Plot Nr'),
                                                id: 'imsEditGardenNr',
                                                name: 'GardenNr',
                                            }, {
                                                xtype: 'numberfield',
                                                fieldLabel: lang('Ukuran Kebun (Hektar)'),
                                                id: 'imsEditGardenHaUnCertified',
                                                name: 'GardenHaUncertified',
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Pohon TM'),
                                                id: 'imsEditPohonTM',
                                                name: 'PohonTM',
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Pohon TBM'),
                                                id: 'imsEditPohonTBM',
                                                name: 'PohonTBM',
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Pohon Rehab'),
                                                id: 'imsEditPohonRehab',
                                                name: 'PohonRehab',
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultCertification',
                                                name: 'DefaultCertification',
                                                hidden: true
                                            }, {
                                                id: 'imsEditCertification',
                                                name: 'Certification',
                                                xtype: 'combobox',
                                                fieldLabel: lang('Certification'),
                                                store: programs,
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false,
                                                queryMode: 'local'
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultICSDate',
                                                name: 'DefaultICSDate',
                                                hidden: true
                                            }, {
                                                xtype: 'datefield',
                                                fieldLabel: lang('ICS Date'),
                                                id: 'imsEditICSDate',
                                                name: 'ICSDate',
                                                format: 'Y-m-d',
                                                hidden: true
                                            }, {
                                                id: 'imsEditStatusAudit',
                                                name: 'StatusAudit',
                                                xtype: 'combobox',
                                                fieldLabel: lang('Status Audit'),
                                                store: status_audit,
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false,
                                                queryMode: 'local',
                                                hidden: true
                                            }]
                                    }]
                            }, {
                                xtype: 'fieldset',
                                id: 'imsEditPanelProduction1',
                                title: lang('Estimated Annual Production'),
                                fieldDefaults: {
                                    labelWidth: 350,
                                },
                                items: [{
                                        xtype: 'numberfield',
                                        id: 'imsEditProductionNext',
                                        name: 'ProductionNext',
                                        fieldLabel: lang('Perkiraan produksi setahun ke depan'),
                                    }, {
                                        xtype: 'numberfield',
                                        id: 'imsEditperkiraanproduksi',
                                        allowBlank: false,
                                        name: 'EstimatedProduction',
                                        fieldLabel: lang('Estimated Production'),
                                        listeners: {
                                            change: function (cb, nv, ov) {
                                                perkiraanProduksi();
                                            }
                                        }
                                    }, {
                                        xtype: 'numberfield',
                                        id: 'imsEditperkiraanproduktifitas',
                                        name: 'perkiraanproduktifitas',
                                        fieldLabel: lang('Estimated productivity') + ' ' + lang('(Kg/Ha/Tahun)'),
                                        readOnly: true
                                    }, ]
                            }, {
                                xtype: 'fieldset',
                                id: 'imsEditPanelProduction2',
                                title: lang('Produksi Kakao/tahun (jual kering)'),
                                items: [{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 0.25,
                                                layout: 'form',
                                                border: false,
                                                items: [{
                                                        xtype: 'tbspacer',
                                                        height: 22
                                                    }, {
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Panen Trek')
                                                    }, {
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Panen Biasa')
                                                    }, {
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Panen Raya')
                                                    }, {
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Total Bulan')
                                                    }]
                                            }, {
                                                columnWidth: 0.15,
                                                layout: 'form',
                                                border: false,
                                                padding: 1,
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Bulan')
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEditPanenTrekMonths',
                                                        name: 'PanenTrekMonths',
                                                        listeners: {
                                                            change: function (cb, nv, ov) {
                                                                KgPerTahunTrek(cb, nv, ov);
                                                                TotalBulan();
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEditPanenBiasaMonths',
                                                        name: 'PanenBiasaMonths',
                                                        listeners: {
                                                            change: function (cb, nv, ov) {
                                                                KgPerTahunBiasa(cb, nv, ov);
                                                                TotalBulan();
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEditPanenRayaMonths',
                                                        name: 'PanenRayaMonths',
                                                        listeners: {
                                                            change: function (cb, nv, ov) {
                                                                KgPerTahunRaya(cb, nv, ov);
                                                                TotalBulan();
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEditTotalBulan',
                                                        name: 'TotalBulan',
                                                        readOnly: true
                                                    }]
                                            }, {
                                                columnWidth: 0.3,
                                                layout: 'form',
                                                padding: 1,
                                                border: false,
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Interval Panen')
                                                    }, {
                                                        xtype: 'combo',
                                                        name: 'PanenTrekPanenMonth',
                                                        id: 'imsEditPanenTrekPanenMonth',
                                                        store: interval_panen,
                                                        queryMode: 'local',
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        listeners: {
                                                            change: function (cb, nv, ov) {
                                                                KgPerTahunTrek(cb, nv, ov);
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'combo',
                                                        name: 'PanenBiasaPanenMonth',
                                                        id: 'imsEditPanenBiasaPanenMonth',
                                                        store: interval_panen,
                                                        queryMode: 'local',
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        listeners: {
                                                            change: function (cb, nv, ov) {
                                                                KgPerTahunBiasa(cb, nv, ov);
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'combo',
                                                        name: 'PanenRayaPanenMonth',
                                                        id: 'imsEditPanenRayaPanenMonth',
                                                        store: interval_panen,
                                                        queryMode: 'local',
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        listeners: {
                                                            change: function (cb, nv, ov) {
                                                                KgPerTahunRaya(cb, nv, ov);
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Total Produksi')
                                                    }]
                                            }, {
                                                columnWidth: 0.15,
                                                layout: 'form',
                                                border: false,
                                                padding: 1,
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('kg/panen')
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEditPanenTrekKg',
                                                        name: 'PanenTrekKg',
                                                        listeners: {
                                                            change: function (cb, nv, ov) {
                                                                KgPerTahunTrek(cb, nv, ov);
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEditPanenBiasaKg',
                                                        name: 'PanenBiasaKg',
                                                        listeners: {
                                                            change: function (cb, nv, ov) {
                                                                KgPerTahunBiasa(cb, nv, ov);
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEditPanenRayaKg',
                                                        name: 'PanenRayaKg',
                                                        listeners: {
                                                            change: function (cb, nv, ov) {
                                                                KgPerTahunRaya(cb, nv, ov);
                                                            }
                                                        }
                                                    }]
                                            }, {
                                                columnWidth: 0.15,
                                                layout: 'form',
                                                border: false,
                                                padding: 1,
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('kg/thn')
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEditPanenTrekKgThn',
                                                        name: 'PanenTrekKgThn',
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEditPanenBiasaKgThn',
                                                        name: 'PanenBiasaKgThn',
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEditPanenRayaKgThn',
                                                        name: 'PanenRayaKgThn',
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'imsEdittotalpanen',
                                                        name: 'totalpanen',
                                                        readOnly: true
                                                    }]
                                            }]
                                    }]
                            }, {
                                xtype: 'fieldset',
                                id: 'imsEditPanelProduction3',
                                items: [{
                                        xtype: 'textfield',
                                        id: 'imsEdittotalproduktivitas',
                                        name: 'totalproduktivitas',
                                        fieldLabel: lang('Total Produktivitas') + ' ' + lang('(Kg/Ha/Tahun)'),
                                        labelWidth: 300,
                                        readOnly: true
                                    }]
                            }]
                    }]
            }],
        buttons: [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                id: 'winGridImsEventDetailFarmerGardenFormBtnClose',
                text: lang('Save'),
                margin: '5px',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                hidden: viewOnly,
                handler: function () {
                    Ext.MessageBox.show({
                        title: 'Information',
                        msg: lang('Under Development'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }, {
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                id: 'winGridImsEventDetailFarmerGardenFormBtnClose',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    winGridImsEventDetailFarmerGardenForm.close();
                }
            }]
    });

    //========================================= Isi Form (BEGIN) =====================================================================//
    if (opsiDisplay == 'view' || opsiDisplay == 'update') {
        Ext.getCmp('winGridImsEventDetailFarmerGardenForm_Form').getForm().load({
            url: m_api + '/ims/ims_event_detail_farmer_garden_fill_form',
            method: 'GET',
            params: {
                FarmerID: FarmerID,
                SurveyNr: SurveyNr,
                GardenNr: GardenNr
            },
            success: function(form, action) {
                var r = Ext.decode(action.response.responseText);
                //console.log(r);

                Ext.getCmp('winGridImsEventDetailFarmerGardenForm_Form').getForm().reset();
                Ext.getCmp('imsEditType').setValue('garden');
                Ext.getCmp('imsEditFarmerID').setValue(r.data.FarmerID);
                Ext.getCmp('imsEditFarmerName').setValue(r.data.FarmerName);
                Ext.getCmp('imsEditDefaultGardenNr').setValue(r.data.GardenNr);
                Ext.getCmp('imsEditGardenNr').setValue(r.data.GardenNr);
                Ext.getCmp('imsEditDefaultSurveyNr').setValue(r.data.SurveyNr);
                Ext.getCmp('imsEditSurveyNr').setValue(r.data.SurveyNr);
                Ext.getCmp('imsEditCertification').setValue(0);
                Ext.getCmp('imsEditGardenHaUnCertified').setValue(r.data.GardenHaUnCertified);

                Ext.getCmp('imsEditPanenBiasaMonths').setValue(r.data.PanenBiasaMonths);
                Ext.getCmp('imsEditPanenBiasaPanenMonth').setValue(r.data.PanenBiasaPanenMonth);
                Ext.getCmp('imsEditPanenBiasaKg').setValue(parseInt(r.data.PanenBiasaKg));
                Ext.getCmp('imsEditPanenTrekMonths').setValue(r.data.PanenTrekMonths);
                Ext.getCmp('imsEditPanenTrekPanenMonth').setValue(r.data.PanenTrekPanenMonth);
                Ext.getCmp('imsEditPanenTrekKg').setValue(parseInt(r.data.PanenTrekKg));
                Ext.getCmp('imsEditPanenRayaMonths').setValue(r.data.PanenRayaMonths);
                Ext.getCmp('imsEditPanenRayaPanenMonth').setValue(r.data.PanenRayaPanenMonth);
                Ext.getCmp('imsEditPanenRayaKg').setValue(parseInt(r.data.PanenRayaKg));
                Ext.getCmp('imsEditPohonTBM').setValue(r.data.PohonTBM);
                Ext.getCmp('imsEditPohonTM').setValue(r.data.PohonTM);
                Ext.getCmp('imsEditPohonRehab').setValue(r.data.PohonRehab);

                Ext.getCmp('imsEditperkiraanproduksi').setValue(r.data.Production);
                Ext.getCmp('imsEditProductionNext').setValue(r.data.ProductionNext);
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
    //========================================= Isi Form (END)   =====================================================================//

    //show windows
    if (!winGridImsEventDetailFarmerGardenForm.isVisible()) {
        winGridImsEventDetailFarmerGardenForm.center();
        winGridImsEventDetailFarmerGardenForm.show();
    } else {
        winGridImsEventDetailFarmerGardenForm.close();
    }
}
/*=======================================Garden   (End)===================================================*/



/*=======================================Certification   (Begin)===================================================*/

function winImsEventDetailFarmerCertification(FarmerID,IMSID){
    //variabel =============================================================
    var contextMenuImsEventDetailGridFarmerCertificationDetail = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/application_view_list.png',
                text: lang('View'),
                cls:'Sfr_BtnConMenuWhite',
                handler: function () {
                    var smb = Ext.getCmp('winGridImsEventDetailFarmerCertification_MainGrid').getSelectionModel().getSelection()[0];
                    displayWinGridImsEventDetailFarmerCertificationForm('view', smb.get('FarmerID'), smb.get('SurveyNr'), smb.get('GardenNr'), smb.get('CertificationID'));
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls:'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function () {
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

    //store =============================================================
    var store_ims_event_detail_grid_farmer_certification_detail = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID', 'FarmerName', 'GardenNr', 'SurveyNr', 'ICSDate', 'StatusAudit', 'Certification', 'FarmerSignature', 'CertificationID'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_farmer_cert',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.FarmerID = FarmerID;
                store.proxy.extraParams.IMSID = IMSID;
            }
        }
    });


    var winGridImsEventDetailFarmerCertification = Ext.create('widget.window', {
        title: lang('Farmer Certification Detail'),
        id: 'winGridImsEventDetailFarmerCertification',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '85%',
        height: '60%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'gridpanel',
                id: 'winGridImsEventDetailFarmerCertification_MainGrid',
                style: 'border:2px solid #CCC;',
                cls: 'Sfr_GridNew',
                store: store_ims_event_detail_grid_farmer_certification_detail,
                width: '99%',
                loadMask: true,
                selType: 'rowmodel',
//            listeners: {
//                itemclick: function(view, record, item, index, e) {
//                    contextMenuImsEventDetailGridFarmerCertificationDetail.showAt(e.getXY());
//                }
//            },
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No data Available')
                },
                columns: [{
                        text: lang('Action'),
                        xtype: 'actioncolumn',
                        width: '5%',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    contextMenuImsEventDetailGridFarmerCertificationDetail.showAt(e.getXY());
                                }
                            }]
                    }, {
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '3%'
                    }, {
                        text: lang('Farmer ID'),
                        dataIndex: 'FarmerID',
                        hidden: true
                    }, {
                        text: lang('Farmer Name'),
                        dataIndex: 'FarmerName',
                        flex: 1
                    }, {
                        text: lang('Survey Number'),
                        dataIndex: 'SurveyNr',
                        width: '10%'
                    }, {
                        text: lang('Plot Nr'),
                        dataIndex: 'GardenNr',
                        width: '10%'
                    }, {
                        text: lang('Certification'),
                        dataIndex: 'Certification',
                        width: '12.5%'
                    }, {
                        text: lang('ICS Date'),
                        dataIndex: 'ICSDate',
                        width: '12.5%'
                    }, {
                        text: lang('Status Audit'),
                        dataIndex: 'StatusAudit',
                        width: '12.5%'
                    }, {
                        text: lang('Farmer Signature'),
                        dataIndex: 'FarmerSignature',
                        width: '12%'
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
                    winGridImsEventDetailFarmerCertification.close();
                }
            }]
    });

    //show windows
    if (!winGridImsEventDetailFarmerCertification.isVisible()) {
        winGridImsEventDetailFarmerCertification.center();
        winGridImsEventDetailFarmerCertification.show();
    } else {
        winGridImsEventDetailFarmerCertification.close();
    }
}


function displayWinGridImsEventDetailFarmerCertificationForm(opsiDisplay, FarmerID, SurveyNr, GardenNr, CertificationID){
    //atur hak akses
    var viewOnly = true;
    if (opsiDisplay == 'view') {
        viewOnly = true;
    } else {
        viewOnly = false;
    }

    //function & variabel2 (begin)

    //function & variabel2 (end)

    //store & combo2 (begin)
    var surveys = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/surveys',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var programs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/programs',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.SupplychainID = Ext.getCmp('imsSupplychainID').getValue();
            }
        }
    });

    var status_audit = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": "Passed"
        }, {
            "id": "2",
            "label": "Not Passed"
        }, {
            "id": "3",
            "label": "Passed with condition"
        }]
    });
    //store & combo2 (end)


    var winGridImsEventDetailFarmerCertificationForm = Ext.create('widget.window', {
        title: lang('Farmer Certification Data'),
        id: 'winGridImsEventDetailFarmerCertificationForm',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '48%',
        height: '65%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'form',
                id: 'winGridImsEventDetailFarmerCertificationForm_Form',
                padding: '5 20 5 8',
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 250,
                    padding: 10
                },
                items: [{
                        xtype: 'panel',
                        items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                        columnWidth: 1,
                                        layout: 'form',
                                        items: [{
                                                xtype: 'textfield',
                                                id: 'imsEditType',
                                                name: 'EditType',
                                                hidden: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('FarmerID'),
                                                id: 'imsEditFarmerID',
                                                name: 'FarmerID',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Farmer Name'),
                                                id: 'imsEditFarmerName',
                                                name: 'FarmerName',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultSurveyNr',
                                                name: 'DefaultSurveyNr',
                                                hidden: true
                                            }, {
                                                id: 'imsEditSurveyNr',
                                                name: 'SurveyNr',
                                                xtype: 'combobox',
                                                fieldLabel: lang('Survey Number'),
                                                store: surveys,
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false,
                                                queryMode: 'local'
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultGardenNr',
                                                name: 'DefaultGardenNr',
                                                hidden: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Plot Nr'),
                                                id: 'imsEditGardenNr',
                                                name: 'GardenNr',
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultCertification',
                                                name: 'DefaultCertification',
                                                hidden: true
                                            }, {
                                                id: 'imsEditCertification',
                                                name: 'Certification',
                                                xtype: 'combobox',
                                                fieldLabel: lang('Certification'),
                                                store: programs,
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false,
                                                queryMode: 'local'
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultICSDate',
                                                name: 'DefaultICSDate',
                                                hidden: true
                                            }, {
                                                xtype: 'datefield',
                                                fieldLabel: lang('ICS Date'),
                                                id: 'imsEditICSDate',
                                                name: 'ICSDate',
                                                format: 'Y-m-d',
                                            }, {
                                                id: 'imsEditStatusAudit',
                                                name: 'StatusAudit',
                                                xtype: 'combobox',
                                                fieldLabel: lang('Status Audit'),
                                                store: status_audit,
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false,
                                                queryMode: 'local'
                                            }]
                                    }]
                            }]
                    }]
            }],
        buttons: [{
                id: 'winGridImsEventDetailFarmerCertificationFormBtnClose',
                text: lang('Save'),
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                margin: '5px',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                hidden: viewOnly,
                handler: function () {
                    Ext.MessageBox.show({
                        title: 'Information',
                        msg: lang('Under Development'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }, {
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                id: 'winGridImsEventDetailFarmerCertificationFormBtnClose',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    winGridImsEventDetailFarmerCertificationForm.close();
                }
            }]
    });

    //========================================= Isi Form (BEGIN) =====================================================================//
    if (opsiDisplay == 'view' || opsiDisplay == 'update') {
        Ext.Ajax.request({
            url: m_api + '/ims/certification_detail',
            method: 'GET',
            params: {
                FarmerID: FarmerID,
                SurveyNr: SurveyNr,
                GardenNr: GardenNr,
                Certification: CertificationID
            },
            success: function(fp, action) {
                var data = Ext.decode(fp.responseText);
                Ext.getCmp('winGridImsEventDetailFarmerCertificationForm_Form').getForm().reset();
                Ext.getCmp('imsEditType').setValue('cert');
                Ext.getCmp('imsEditFarmerID').setValue(data.FarmerID);
                Ext.getCmp('imsEditFarmerName').setValue(data.FarmerName);
                Ext.getCmp('imsEditDefaultGardenNr').setValue(data.GardenNr);
                Ext.getCmp('imsEditGardenNr').setValue(data.GardenNr);
                Ext.getCmp('imsEditDefaultSurveyNr').setValue(data.SurveyNr);
                Ext.getCmp('imsEditSurveyNr').setValue(data.SurveyNr);
                Ext.getCmp('imsEditCertification').setValue(data.Certification);
                Ext.getCmp('imsEditDefaultCertification').setValue(data.Certification);
                Ext.getCmp('imsEditDefaultICSDate').setValue(data.ICSDate);
                Ext.getCmp('imsEditICSDate').setValue(data.ICSDate);
                Ext.getCmp('imsEditStatusAudit').setValue(data.StatusAudit);
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
    //========================================= Isi Form (END)   =====================================================================//

    //show windows
    if (!winGridImsEventDetailFarmerCertificationForm.isVisible()) {
        winGridImsEventDetailFarmerCertificationForm.center();
        winGridImsEventDetailFarmerCertificationForm.show();
    } else {
        winGridImsEventDetailFarmerCertificationForm.close();
    }
}
/*=======================================Certification   (End)  ===================================================*/

/*======================================= Audit Log   (Begin)  ===================================================*/

function winImsEventDetailFarmerAuditLog(FarmerID,IMSID){
    //variabel =============================================================
    var contextMenuImsEventDetailGridFarmerAuditLogDetail = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/application_view_list.png',
                cls: 'Sfr_BtnConMenuWhite',
                text: lang('View'),
                handler: function () {
                    var smb = Ext.getCmp('winGridImsEventDetailFarmerAuditLog_MainGrid').getSelectionModel().getSelection()[0];
                    displayWinGridImsEventDetailFarmerAuditLogForm('view', smb.get('FarmerID'), smb.get('SurveyNr'), smb.get('GardenNr'), smb.get('CertificationID'), smb.get('ICSDate'));
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                cls: 'Sfr_BtnConMenuWhite',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function () {
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

    //store =====================================================================
    var store_ims_event_detail_grid_farmer_audit_log_detail = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID', 'FarmerName', 'GardenNr', 'SurveyNr', 'ICSDate', 'StatusAudit', 'Certification', 'CertificationID'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/ims_farmer_audit',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.FarmerID = FarmerID;
                store.proxy.extraParams.IMSID = IMSID;
            }
        }
    });


    var winGridImsEventDetailFarmerAuditLog = Ext.create('widget.window', {
        title: lang('Farmer Audit Log Detail'),
        id: 'winGridImsEventDetailFarmerAuditLog',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '85%',
        height: '60%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'gridpanel',
                id: 'winGridImsEventDetailFarmerAuditLog_MainGrid',
                style: 'border:2px solid #CCC;',
                cls: 'Sfr_GridNew',
                store: store_ims_event_detail_grid_farmer_audit_log_detail,
                width: '99%',
                loadMask: true,
                selType: 'rowmodel',
//                listeners: {
//                    itemclick: function (view, record, item, index, e) {
//                        contextMenuImsEventDetailGridFarmerAuditLogDetail.showAt(e.getXY());
//                    }
//                },
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No data Available')
                },
                columns: [{
                        text: lang('Action'),
                        xtype: 'actioncolumn',
                        width: '4%',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    contextMenuImsEventDetailGridFarmerAuditLogDetail.showAt(e.getXY());
                                }
                            }]
                    }, {
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '4%'
                    }, {
                        text: lang('Farmer ID'),
                        dataIndex: 'FarmerID',
                        hidden: true
                    }, {
                        text: lang('Farmer Name'),
                        dataIndex: 'FarmerName',
                        flex: 2
                    }, {
                        text: lang('Survey Number'),
                        dataIndex: 'SurveyNr',
                        flex: 1
                    }, {
                        text: lang('Plot Nr'),
                        dataIndex: 'GardenNr',
                        flex: 1
                    }, {
                        text: lang('Certification'),
                        dataIndex: 'Certification',
                        flex: 1
                    }, {
                        text: lang('ICS Date'),
                        dataIndex: 'ICSDate',
                        flex: 1
                    }, {
                        text: lang('Status Audit'),
                        dataIndex: 'StatusAudit',
                        flex: 2
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
                    winGridImsEventDetailFarmerAuditLog.close();
                }
            }]
    });

    //show windows
    if (!winGridImsEventDetailFarmerAuditLog.isVisible()) {
        winGridImsEventDetailFarmerAuditLog.center();
        winGridImsEventDetailFarmerAuditLog.show();
    } else {
        winGridImsEventDetailFarmerAuditLog.close();
    }
}


function displayWinGridImsEventDetailFarmerAuditLogForm(opsiDisplay, FarmerID, SurveyNr, GardenNr, CertificationID, ICSDate){
    //atur hak akses
    var viewOnly = true;
    if (opsiDisplay == 'view') {
        viewOnly = true;
    } else {
        viewOnly = false;
    }

    //function & variabel2 (begin)

    //function & variabel2 (end)

    //store & combo2 (begin)
    var surveys = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/surveys',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var programs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/programs',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.SupplychainID = Ext.getCmp('imsSupplychainID').getValue();
            }
        }
    });

    var status_audit = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": "Passed"
        }, {
            "id": "2",
            "label": "Not Passed"
        }, {
            "id": "3",
            "label": "Passed with condition"
        }]
    });
    //store & combo2 (end)

    var winGridImsEventDetailFarmerAuditLogForm = Ext.create('widget.window', {
        title: lang('Farmer Audit Log Data'),
        id: 'winGridImsEventDetailFarmerAuditLogForm',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '48%',
        height: '65%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'form',
                id: 'winGridImsEventDetailFarmerAuditLogForm_Form',
                padding: '5 20 5 8',
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 250,
                    padding: 10
                },
                items: [{
                        xtype: 'panel',
                        items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                        columnWidth: 1,
                                        layout: 'form',
                                        items: [{
                                                xtype: 'textfield',
                                                id: 'imsEditType',
                                                name: 'EditType',
                                                hidden: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('FarmerID'),
                                                id: 'imsEditFarmerID',
                                                name: 'FarmerID',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Farmer Name'),
                                                id: 'imsEditFarmerName',
                                                name: 'FarmerName',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultSurveyNr',
                                                name: 'DefaultSurveyNr',
                                                hidden: true
                                            }, {
                                                id: 'imsEditSurveyNr',
                                                name: 'SurveyNr',
                                                xtype: 'combobox',
                                                fieldLabel: lang('Survey Number'),
                                                store: surveys,
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false,
                                                queryMode: 'local'
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultGardenNr',
                                                name: 'DefaultGardenNr',
                                                hidden: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Plot Nr'),
                                                id: 'imsEditGardenNr',
                                                name: 'GardenNr',
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultCertification',
                                                name: 'DefaultCertification',
                                                hidden: true
                                            }, {
                                                id: 'imsEditCertification',
                                                name: 'Certification',
                                                xtype: 'combobox',
                                                fieldLabel: lang('Certification'),
                                                store: programs,
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false,
                                                queryMode: 'local'
                                            }, {
                                                xtype: 'textfield',
                                                id: 'imsEditDefaultICSDate',
                                                name: 'DefaultICSDate',
                                                hidden: true
                                            }, {
                                                xtype: 'datefield',
                                                fieldLabel: lang('ICS Date'),
                                                id: 'imsEditICSDate',
                                                name: 'ICSDate',
                                                format: 'Y-m-d',
                                            }, {
                                                id: 'imsEditStatusAudit',
                                                name: 'StatusAudit',
                                                xtype: 'combobox',
                                                fieldLabel: lang('Status Audit'),
                                                store: status_audit,
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false,
                                                queryMode: 'local'
                                            }]
                                    }]
                            }]
                    }]
            }],
        buttons: [{
                id: 'winGridImsEventDetailFarmerAuditLogFormBtnClose',
                text: lang('Save'),
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                margin: '5px',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                hidden: viewOnly,
                handler: function () {
                    Ext.MessageBox.show({
                        title: 'Information',
                        msg: lang('Under Development'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }, {
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                id: 'winGridImsEventDetailFarmerAuditLogFormBtnClose',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    winGridImsEventDetailFarmerAuditLogForm.close();
                }
            }]
    });

    //========================================= Isi Form (BEGIN) =====================================================================//
    if (opsiDisplay == 'view' || opsiDisplay == 'update') {
        Ext.Ajax.request({
            url: m_api + '/ims/audit_detail',
            method: 'GET',
            params: {
                FarmerID: FarmerID,
                SurveyNr: SurveyNr,
                GardenNr: GardenNr,
                ICSDate: ICSDate,
                Certification: CertificationID
            },
            success: function(fp, action) {
                var data = Ext.decode(fp.responseText);
                Ext.getCmp('winGridImsEventDetailFarmerAuditLogForm_Form').getForm().reset();

                Ext.getCmp('imsEditType').setValue('audit');
                Ext.getCmp('imsEditFarmerID').setValue(data.FarmerID);
                Ext.getCmp('imsEditFarmerName').setValue(data.FarmerName);
                Ext.getCmp('imsEditDefaultGardenNr').setValue(data.GardenNr);
                Ext.getCmp('imsEditGardenNr').setValue(data.GardenNr);
                Ext.getCmp('imsEditDefaultSurveyNr').setValue(data.SurveyNr);
                Ext.getCmp('imsEditSurveyNr').setValue(data.SurveyNr);
                Ext.getCmp('imsEditCertification').setValue(data.Certification);
                Ext.getCmp('imsEditDefaultCertification').setValue(data.Certification);
                Ext.getCmp('imsEditDefaultICSDate').setValue(data.ICSDate);
                Ext.getCmp('imsEditICSDate').setValue(data.ICSDate);
                Ext.getCmp('imsEditStatusAudit').setValue(data.StatusAudit);
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
    //========================================= Isi Form (END)   =====================================================================//

    //show windows
    if (!winGridImsEventDetailFarmerAuditLogForm.isVisible()) {
        winGridImsEventDetailFarmerAuditLogForm.center();
        winGridImsEventDetailFarmerAuditLogForm.show();
    } else {
        winGridImsEventDetailFarmerAuditLogForm.close();
    }
}
/*======================================= Audit Log   (End)  ===================================================*/


/*======================================= Status Eligible (Begin)  ===================================================*/
function winImsEventDetailFarmerEligible(FarmerID,IMSID,CallerStore){

    var winImsEventDetailFarmerEligibleForm = Ext.create('widget.window', {
        title: lang('Eligible Status Form'),
        id: 'winImsEventDetailFarmerEligibleForm',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '40%',
        height: '36%',
        overflowY: 'auto',
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'form',
                id: 'winImsEventDetailFarmerEligibleForm-Form',
                padding: '5 20 5 8',
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 250,
                    padding: 10
                },
                items: [{
                        xtype: 'panel',
                        items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                        columnWidth: 1,
                                        layout: 'form',
                                        items: [{
                                                fieldLabel: lang('Eligible for Audit'),
                                                labelWidth: 125,
                                                xtype: 'radiogroup',
                                                msgTarget: 'side',
                                                hidden: true,
                                                width: '100%',
                                                columns: 2,
                                                items: [{
                                                        boxLabel: lang('Yes'),
                                                        name: 'StatusAudit',
                                                        inputValue: '1',
                                                        id: 'StatusAudit_1',
                                                        listeners: {
                                                            change: function () {
                                                                return false;
                                                            }
                                                        }
                                                    }, {
                                                        boxLabel: lang('No'),
                                                        name: 'StatusAudit',
                                                        inputValue: '2',
                                                        id: 'StatusAudit_2',
                                                        listeners: {
                                                            change: function () {
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                            }, {
                                                xtype: 'textarea',
                                                id: 'NotEligibleReason',
                                                name: 'NotEligibleReason',
                                                width: '100%',
                                                fieldLabel: lang('Not Eligible Reason'),
                                                labelWidth: 125,
                                                hidden: true
                                            }, {
                                                fieldLabel: lang('Eligible for Audit'),
                                                labelWidth: 150,
                                                xtype: 'radiogroup',
                                                width: '100%',
                                                columns: 2,
                                                id: 'StatusComplyRadio',
                                                items: [{
                                                        boxLabel: lang('Yes'),
                                                        name: 'StatusComply',
                                                        inputValue: '1',
                                                        id: 'StatusComply_1',
                                                        listeners: {
                                                            change: function () {
                                                                if (this.checked == true) {
                                                                    Ext.getCmp('StatusComplyRemark').setDisabled(true);
                                                                    Ext.getCmp('winImsEventDetailFarmerEligibleForm-ICSDate').setDisabled(true);
                                                                    Ext.getCmp('winImsEventDetailFarmerEligibleForm-ICSDate').setValue('');
                                                                    Ext.getCmp('StatusComplyRemark').setValue('');
                                                                } else {
                                                                    Ext.getCmp('StatusComplyRemark').setDisabled(false);
                                                                    Ext.getCmp('winImsEventDetailFarmerEligibleForm-ICSDate').setDisabled(false);
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                    }, {
                                                        boxLabel: lang('No'),
                                                        name: 'StatusComply',
                                                        inputValue: '2',
                                                        id: 'StatusComply_2',
                                                        listeners: {
                                                            change: function () {
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                            }, {
                                                xtype: 'datefield',
                                                fieldLabel: lang('ICS Date'),
                                                labelWidth: 150,
                                                id: 'winImsEventDetailFarmerEligibleForm-ICSDate',
                                                name: 'ICSDate',
                                                format: 'Y-m-d'
                                            }, {
                                                xtype: 'textarea',
                                                id: 'StatusComplyRemark',
                                                name: 'StatusComplyRemark',
                                                width: '100%',
                                                fieldLabel: lang('Not Eligible Reason'),
                                                labelWidth: 150
                                            }]
                                    }]
                            }]
                    }]
            }],
        buttons: [{
                id: 'winImsEventDetailFarmerEligibleForm-BtnSave',
                text: lang('Save'),
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                margin: '5px',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                hidden: m_act_update,
                handler: function () {
                    var form = Ext.getCmp('winImsEventDetailFarmerEligibleForm-Form').getForm();

                    if (form.isValid()) {
                        form.submit({
                            url: m_api + '/ims/form_eligible_farmer',
                            method: 'POST',
                            params: {
                                FarmerID: FarmerID,
                                IMSID: IMSID
                            },
                            waitMsg: 'Saving data...',
                            success: function (fp, o) {
                                Ext.MessageBox.alert('Success', 'Data saved');
                                winImsEventDetailFarmerEligibleForm.close();
                                CallerStore.load();
                            },
                            failure: function (fp, o) {
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: 'Failed to save data',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    } else {
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Please fill the required field',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                }
            }, {
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                id: 'winImsEventDetailFarmerEligibleForm-BtnClose',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    winImsEventDetailFarmerEligibleForm.close();
                }
            }]
    });

    //isi form
    Ext.getCmp('winImsEventDetailFarmerEligibleForm-Form').getForm().load({
        url: m_api + '/ims/fill_form_eligible_farmer',
        method: 'GET',
        params: {
            FarmerID: FarmerID,
            IMSID: IMSID
        },
        success: function(form, action) {
            var r = Ext.decode(action.response.responseText);
            Ext.getCmp('winImsEventDetailFarmerEligibleForm-ICSDate').setValue(r.data.ICSDate);
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

    //show windows
    if (!winImsEventDetailFarmerEligibleForm.isVisible()) {
        winImsEventDetailFarmerEligibleForm.center();
        winImsEventDetailFarmerEligibleForm.show();
    } else {
        winImsEventDetailFarmerEligibleForm.close();
    }
}
/*======================================= Status Eligible (End)    ===================================================*/