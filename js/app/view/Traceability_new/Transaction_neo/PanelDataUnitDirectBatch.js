Ext.define('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnitDirectBatch', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnitDirectBatch',
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

                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnitDirectBatch-MainGrid').getSelectionModel().getSelection()[0];

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
            id: 'Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnitDirectBatch-MainGrid',
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
                    id: 'Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnitDirectBatch-ButtonAdd',
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
                id: 'Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnitDirectBatch-ButtonActionGrid',
                text: ' ',
                xtype: 'actioncolumn',
                width: '10%',
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
                width:'20%',
            },{
                text: lang('Gross (KG)'),
                dataIndex: 'VolumeBruto',
                width:'20%',
            }, {
                text: lang('Netto (KG)'),
                dataIndex: 'VolumeNetto',
                width:'20%',
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

            if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentDirectBatch').getValue() === null) {
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentDirectBatch').setValue(0);
            }

            if (records.length > 0) {
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnitDirectBatch-ButtonAdd').hide()
            }
            
            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentDirectBatch').setValue(parseFloat(store.sum('TotalPayment')));

        }, this);

        this.callParent(arguments);
    }
});