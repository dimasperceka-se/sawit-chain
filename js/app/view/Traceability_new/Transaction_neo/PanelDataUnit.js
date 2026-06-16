Ext.define('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit',
    title: lang('Weight & Unit'),
    cls: 'Sfr_PanelSubLayoutFormRoundedGray',
    viewVar: false,
    hidden: true,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
        }
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.MainGrid = Ext.create('Koltiva.store.Traceability_new.Transaction_neo.MainGridDataUnit', {
            storeVar: {
                SupplyTransID: thisObj.viewVar.SupplyTransID
            }
        });

        thisObj.ContextMenu = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                scope: this,
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function () {

                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit-MainGrid').getSelectionModel().getSelection()[0];

                    var WinFormDataUnit = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit');
                    WinFormDataUnit.setViewVar({
                        OpsiDisplay: 'update',
                        StoreGridMain: thisObj.MainGrid,
                        SupplyTransID: sessionStorage.getItem('setSupplyTransID') == null ? thisObj.viewVar.SupplyTransID : sessionStorage.getItem('setSupplyTransID'),
                        TransDetailID: sm.get('TransDetailID')
                    });
                    if (!WinFormDataUnit.isVisible()) {
                        WinFormDataUnit.center();
                        WinFormDataUnit.show();
                    } else {
                        WinFormDataUnit.close();
                    }
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit-MainGrid',
            cls: 'Sfr_GridNew',
            loadMask: true,
            height: 300,
            selType: 'rowmodel',
            style: 'border:1px solid #CCC;',
            store: thisObj.MainGrid,
            features: [{
                ftype: 'summary'
            }],
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    id: 'Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit-ButtonAdd',
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add'),
                    hidden: m_act_add,
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    handler: function () {

                        var WinFormDataUnit = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit');
                        WinFormDataUnit.setViewVar({
                            OpsiDisplay: 'insert',
                            StoreGridMain: thisObj.MainGrid,
                            SupplyTransID: sessionStorage.getItem('setSupplyTransID') == null ? thisObj.viewVar.SupplyTransID : sessionStorage.getItem('setSupplyTransID'),
                            TransDetailID: null,
                            MemberID : thisObj.viewVar.MemberID
                        });
                        if (!WinFormDataUnit.isVisible()) {
                            WinFormDataUnit.center();
                            WinFormDataUnit.show();
                        } else {
                            WinFormDataUnit.close();
                        }
                    }
                }]
            }],
            columns: [{
                id: 'Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit-ButtonActionGrid',
                text: ' ',
                xtype: 'actioncolumn',
                width: '15%',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenu.showAt(e.getXY());
                    }
                }]
            },
            {
                text: lang('Janjang'),
                dataIndex: 'Bunches',
                width:'15%',
            },{
                text: lang('Gross (KG)'),
                dataIndex: 'VolumeBruto',
                width:'15%',
            }, {
                text: lang('Netto (KG)'),
                dataIndex: 'VolumeNetto',
                width:'15%',
            },{
                text: lang('Deduction (%)'),
                dataIndex: 'DeductionPercentage',
                width:'20%',
            },{
                text: lang('Price per Kilo'),
                dataIndex: 'ContractPrice',
                width:'20%',
            }]
        }];

        // mendapatkan value total berat yang di grid panel unit
        thisObj.MainGrid.on('load', function(store, records){
            sessionStorage.removeItem('totalPaymentTransactionDetail');
            sessionStorage.removeItem('totalPackageTotalTransactionDetail');

            // if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Price').getValue() === null) {
            //     Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Price').setValue(0);
            // }

            if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPayment').getValue() === null) {
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPayment').setValue(0);
            }

            if (records.length > 0) {
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit-ButtonAdd').hide()
            }
            // if (records.length > 0) {
            //     sessionStorage.setItem('totalPaymentTransactionDetail', parseFloat(store.sum('ContractPrice')));
            //     sessionStorage.setItem('totalPackageTotalTransactionDetail', parseFloat(store.sum('PackageTotal')));

            //     // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalWeightPanelUnit').setValue(parseFloat(store.sum('NettWeight')));
            // } else {
            //     sessionStorage.setItem('totalPaymentTransactionDetail', 0);
            //     sessionStorage.setItem('totalPackageTotalTransactionDetail', 0);

            //     // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalWeightPanelUnit').setValue(0);
            // }

            // let Price        = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Price').getValue()
            // let ContractPrice = parseFloat(Price) * parseFloat(store.sum('NettWeight'));

            // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-ContractPrice').setValue(ContractPrice);

            // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Price').setValue(parseFloat(store.sum('ContractPrice')));
            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPayment').setValue(parseFloat(store.sum('TotalPayment')));
            // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-UnitTotal').setValue(parseFloat(store.sum('PackageTotal')));

        }, this);

        this.callParent(arguments);
    }
});