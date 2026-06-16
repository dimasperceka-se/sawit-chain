Ext.define('Koltiva.view.Traceability.Transaction.TransactionForm', {
    extend: 'Ext.TabPanel',
    id: 'Koltiva.view.Traceability.Transaction.TransactionForm',
    flex: 1,
    padding: 5,
    activeTab: 0,
    plain: true,
    margin: '0 0 0 0',
    setInputFieldOn: function(){
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DisplaySupplyID').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-FakturNumber').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stDateWeight').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stTimeWeight').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndDateWeight').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndTimeWeight').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stWeight').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeight').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight1').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight2').setReadOnly(false);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Price').setReadOnly(false);
    },
    setInputFieldOff: function(){
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DisplaySupplyID').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-FakturNumber').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stDateWeight').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stTimeWeight').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndDateWeight').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndTimeWeight').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stWeight').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeight').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight1').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight2').setReadOnly(true);
        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Price').setReadOnly(true);
    },
    initComponent: function () {
        function ResetFormTransaction() {
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyTransID').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyID').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyType').setValue('Farmer');
            var dt = new Date();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DateTransaction').setValue(Ext.Date.format(dt, 'Y-m-d H:i:s'));
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DisplaySupplyID').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Village').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-GroupName').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-FakturNumber').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stDateWeight').setValue(Ext.Date.format(dt, 'Y-m-d'));
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stTimeWeight').setValue(Ext.Date.format(dt, 'H:i'));
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndDateWeight').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndTimeWeight').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stWeight').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeight').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Netto').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight1').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight2').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustNetto').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Price').setValue();
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-TotalPayment').setValue();
            for (i = 1; i < 9; i++) { 
                if(i==8){
                    var i = "Total";
                }
                Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan'+i).setValue();
                Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Persen'+i).setValue();
                Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Denda'+i).setValue();
                Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Potongan'+i).setValue();
            }
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Save').hide();
        }

        function HitungNetto() {
            var bruto1 = isNaN(parseFloat(Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stWeight').getValue())) ? 0 : parseFloat(Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stWeight').getValue());
            var bruto2 = isNaN(parseFloat(Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeight').getValue())) ? 0 : parseFloat(Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeight').getValue());
            var netto = parseFloat(bruto1) - parseFloat(bruto2);
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Netto').setValue(netto);
            var AdjustWeight1 = isNaN(parseFloat(Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight1').getValue())) ? 0 : parseFloat(Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight1').getValue());
            var AdjustWeight2 = isNaN(parseFloat(Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight2').getValue())) ? 0 : parseFloat(Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight2').getValue());
            var AdjustNetto = netto - (parseFloat(AdjustWeight1) * netto / 100) - AdjustWeight2;
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustNetto').setValue(AdjustNetto.toFixed(2));
            var price = isNaN(parseFloat(Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Price').getValue())) ? 0 : parseFloat(Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Price').getValue());
            var TotalPayment = parseFloat(AdjustNetto) * parseFloat(price);
            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-TotalPayment').setValue(TotalPayment.toFixed(2));
        }

        var thisObj = this;
        var cmbSupplyType = Ext.create('Koltiva.store.Traceability.Transaction.ComboSupplyType');
        cmbSupplyType.on('load', function (store, records, options) {
            var combo = Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyType');
            combo.select(combo.getStore().getAt(0));
        });
        var storeGridMainTransaction = Ext.create('Koltiva.store.Traceability.Transaction.MainGridTransaction');
        var cmbSupplyID = Ext.create('Koltiva.store.Traceability.Transaction.ComboSupplyID');
        

        // Penerimaan 
        //var objFormPenerimaan = Ext.create('Koltiva.view.Traceability_new.Transaction.FormPenerimaan');
        //thisObj.objFormPenerimaan = objFormPenerimaan;

        thisObj.items = [{
                xtype: 'form',
                viewVar: false,
                setViewVar: function (value) {
                    this.viewVar = value;
                },
                frame: true,
                collapsible: true,
                margin: '0 0 0 0',
                padding: 5,
                title: lang('Form Transaksi'),
                items: [{
                        columnWidth: 0.5,
                        layout: 'form',
                        id: 'Koltiva.view.Traceability.Transaction.Form',
                        padding: 5,
                        items: [{
                                    xtype: 'container',
                                    flex: 1
                                }, {
                                xtype: 'toolbar',
                                dock:'top',
                                //style: 'border-style: none',
                                style: 'margin-top: -10px',
                                items: [{
                                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                        text: lang('Add New Transaction'),
                                        handler: function() {
                                            ResetFormTransaction();
                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm').setInputFieldOn();
                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Save').show();
                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Save').setText(lang('Save New Transaction'));
                                            var dt = new Date();
                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DateTransaction').setValue(Ext.Date.format(dt, 'Y-m-d H:i:s'));
                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stDateWeight').setValue(Ext.Date.format(dt, 'Y-m-d'));
                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stTimeWeight').setValue(Ext.Date.format(dt, 'H:i'));
                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DisplaySupplyID').el.dom.focus();
                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DisplaySupplyID').focus();
                                    }
                                }]
                            }, {
                                layout: 'column',
                                items: [{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        hidden: true,
                                        padding: 5,
                                        items: [{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplychainID',
                                                name: 'SupplychainID',
                                                hidden: true
                                            }, {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyTransID',
                                                name: 'SupplyTransID',
                                                hidden: true
                                            }, {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyID',
                                                name: 'SupplyID',
                                                hidden: true
                                            }]
                                    }, {
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        bodyPadding: 5,
                                        fieldDefaults: {
                                            labelAlign: 'left',
                                            labelWidth: 175,
                                            anchor: '100%'
                                        },
                                        items: [{
                                                xtype: 'textfield',
                                                fieldLabel: lang('Transaction Date'),
                                                labelWidth: 175,
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-DateTransaction',
                                                name: 'DateTransaction',
                                                value: m_now,
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Deivery Date'),
                                                labelWidth: 175,
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-DeliveryDate',
                                                name: 'DeliveryDate',
                                                value: m_now,
                                                readOnly: true
                                            }, {
                                                xtype: 'combo',
                                                readOnly: true,
                                                store: cmbSupplyID,
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-DisplaySupplyID',
                                                name: 'DisplaySupplyID',
                                                displayField: 'displayid',
                                                fieldLabel: lang('Farmer ID'),
                                                typeAhead: false,
                                                hideTrigger: true,
                                                queryCaching: false,
                                                allowBlank: false,
                                                minChars: 3,
                                                emptyText: lang('Search by Name/ID'),
                                                anchor: '100%',
                                                listConfig: {
                                                    loadingText: lang('Searching...'),
                                                    emptyText: lang('No data found'),
                                                    getInnerTpl: function () {
                                                        return '<div class="search-item">' +
                                                                '<b>{displayid}</b> - <b>{name}</b><br> NIK : <b>{noktp}</b> <br>Village : <b>{village}</b><br>GroupName : <b>{groupname}</b>' + '<hr></div>';
                                                    }
                                                },
                                                pageSize: 10,
                                                // override default onSelect to do redirect
                                                listeners: {
                                                    select: function (combo, selection) {
                                                        var post = selection[0];
                                                        if (post) {
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyID').setValue(post.get('id'));
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyName').setValue(post.get('name'));
                                                            //Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AgentName').setValue(post.get('agentname'));
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Village').setValue(post.get('village'));
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-GroupName').setValue(post.get('groupname'));
                                                        }
                                                    }
                                                }
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Village'),
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Village',
                                                name: 'Village',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('No. Faktur'),
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-FakturNumber',
                                                name: 'FakturNumber',
                                                readOnly: true
                                            }]
                                    }, {
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        padding: 5,
                                        items: [{
                                                xtype: 'combo',
                                                fieldLabel: lang('Type'),
                                                store: cmbSupplyType,
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyType',
                                                name: 'SupplyType',
                                                queryMode: 'local',
                                                displayField: 'label',
                                                valueField: 'id',
                                                value: 'Farmer',
                                                readOnly: true,
                                                listeners: {
                                                    change: function (dv, record, item, index, e) {
                                                        if (this.value == 'Farmer') {
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DisplaySupplyID').labelEl.update('Farmer ID');
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-GroupName').show();
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyID').setReadOnly(false);
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DestWeight').hide();
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DestNumberPackage').hide();
                                                        } else if (this.value == 'Batch') {
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DisplaySupplyID').labelEl.update('ID');
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-GroupName').hide();
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-FakturNumber').hide();
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyID').setReadOnly(false);
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DestWeight').show();
                                                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DestNumberPackage').show();
                                                        }
                                                    }
                                                }
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Agent'),
                                                labelWidth: 175,
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-AgentName',
                                                name: 'SupplyName',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('DO'),
                                                labelWidth: 175,
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyName',
                                                name: 'SupplyName',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Farmer Group'),
                                                labelWidth: 175,
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-GroupName',
                                                name: 'GroupName',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Destination Weight'),
                                                labelWidth: 175,
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-DestWeight',
                                                name: 'DestWeight',
                                                readOnly: true,
                                                hidden: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Tandan'),
                                                labelWidth: 175,
                                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-DestNumberPackage',
                                                name: 'SupplyName',
                                                readOnly: true,
                                                hidden: true
                                            }]
                                    }]
                            }, {
                                xtype: 'panel',
                                title: lang('Penimbangan'),
                                items: [{
                                        layout: 'column',
                                        items: [{
                                                columnWidth: 0.5,
                                                layout: 'form',
                                                bodyPadding: 0,
                                                fieldDefaults: {
                                                    labelAlign: 'left',
                                                    labelWidth: 175,
                                                    anchor: '100%',

                                                },
                                                items: [{
                                                        layout: 'column',
                                                        items: [{
                                                                columnWidth: 0.7,
                                                                layout: 'form',
                                                                bodyPadding: 5,
                                                                style: 'margin-top: -10px',
                                                                fieldDefaults: {
                                                                    labelAlign: 'left',
                                                                    labelWidth: 175,
                                                                    anchor: '100%'
                                                                },
                                                                items: [{
                                                                        xtype: 'datefield',
                                                                        readOnly: true,
                                                                        fieldLabel: lang('1st Date'),
                                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stDateWeight',
                                                                        name: '1stDateWeight',
                                                                        format: 'Y-m-d',
                                                                        value: m_date
                                                                    }]
                                                            }, {
                                                                columnWidth: 0.3,
                                                                layout: 'form',
                                                                padding: 5,
                                                                style: 'margin-top: -10px',
                                                                fieldDefaults: {
                                                                    labelAlign: 'left',
                                                                    labelWidth: 175,
                                                                    anchor: '100%'
                                                                },
                                                                items: [{
                                                                        xtype: 'timefield',
                                                                        readOnly: true,
                                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stTimeWeight',
                                                                        name: '1stTimeWeight',
                                                                        format: 'H:i',
                                                                        value: m_time,
                                                                        listeners: {
                                                                            change: function (c, v) {
                                                                                
                                                                            }
                                                                        }
                                                                    }]
                                                            }]
                                                    }, {
                                                        layout: 'column',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeighing',
                                                        items: [{
                                                                columnWidth: 0.7,
                                                                layout: 'form',
                                                                bodyPadding: 5,
                                                                style: 'margin-top: -20px',
                                                                fieldDefaults: {
                                                                    labelAlign: 'left',
                                                                    labelWidth: 175,
                                                                    anchor: '100%'
                                                                },
                                                                items: [{
                                                                        xtype: 'datefield',
                                                                        readOnly: true,
                                                                        fieldLabel: lang('2nd Date'),
                                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndDateWeight',
                                                                        name: '2ndDateWeight',
                                                                        format: 'Y-m-d',
                                                                    }]
                                                            }, {
                                                                columnWidth: 0.3,
                                                                layout: 'form',
                                                                bodyPadding: 5,
                                                                style: 'margin-top: -20px',
                                                                fieldDefaults: {
                                                                    labelAlign: 'left',
                                                                    labelWidth: 175,
                                                                    anchor: '100%'
                                                                },
                                                                items: [{
                                                                        xtype: 'timefield',
                                                                        readOnly: true,
                                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndTimeWeight',
                                                                        name: '2ndTimeWeight',
                                                                        format: 'H:i',
                                                                        listeners: {
                                                                            change: function (c, v) {
                                                                                
                                                                            }
                                                                        }
                                                                    }]
                                                            }]
                                                    }]
                                            }, {
                                                columnWidth: 0.5,
                                                layout: 'form',
                                                padding: 5,
                                                fieldDefaults: {
                                                    labelAlign: 'left',
                                                    labelWidth: 175,
                                                    anchor: '100%'
                                                },
                                                items: [{
                                                        xtype: 'numberfield', minValue: 0, fieldStyle: 'text-align: right', readOnly: true,
                                                        labelWidth: 175,
                                                        fieldLabel: lang('1st Weight'),
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stWeight',
                                                        name: '1stWeight',
                                                        allowBlank: false,
                                                        listeners: {
                                                            change: function (c, v) {
                                                                HitungNetto();
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield', minValue: 0, fieldStyle: 'text-align: right', readOnly: true,
                                                        labelWidth: 175,
                                                        fieldLabel: lang('2nd Weight'),
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeight',
                                                        name: '2ndWeight',
                                                        listeners: {
                                                            change: function (c, v) {
                                                                HitungNetto();
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield', minValue: 0, fieldStyle: 'text-align: right', readOnly: true,
                                                        fieldLabel: lang('Total Tandan'),
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan',
                                                        name: 'Tandan',
                                                    }, {
                                                        xtype: 'numberfield', minValue: 0, fieldStyle: 'text-align: right',
                                                        labelWidth: 175,
                                                        fieldLabel: lang('Netto'),
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Netto',
                                                        name: 'Netto',
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'numberfield', minValue: 0, fieldStyle: 'text-align: right', readOnly: true,
                                                        labelWidth: 175,
                                                        fieldLabel: lang('Adjust Weght (%)'),
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight1',
                                                        name: 'AdjustWeight1',
                                                        listeners: {
                                                            change: function (c, v) {
                                                                HitungNetto();
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield', minValue: 0, fieldStyle: 'text-align: right', readOnly: true,
                                                        labelWidth: 175,
                                                        fieldLabel: lang('Adjust Weght (Kg)'),
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight2',
                                                        name: 'AdjustWeight2',
                                                        listeners: {
                                                            change: function (c, v) {
                                                                HitungNetto();
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield', minValue: 0, fieldStyle: 'text-align: right',
                                                        labelWidth: 175,
                                                        fieldLabel: lang('Adjust Netto'),
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustNetto',
                                                        name: 'AdjustNetto',
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'numberfield', minValue: 0, fieldStyle: 'text-align: right', readOnly: true,
                                                        labelWidth: 175,
                                                        fieldLabel: lang('Price'),
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Price',
                                                        name: 'Price',
                                                        allowBlank: false,
                                                        listeners: {
                                                            change: function (c, v) {
                                                                HitungNetto();
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield', minValue: 0, fieldStyle: 'text-align: right',
                                                        labelWidth: 175,
                                                        fieldLabel: lang('Total Payment'),
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-TotalPayment',
                                                        name: 'TotalPayment',
                                                        readOnly: true

                                                    }]
                                            }]
                                    }]
                            }, {
                                xtype: 'panel',
                                title: lang('Quality'),
                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-PanelQuality',
                                viewVar: false,
                                setViewVar: function (value) {
                                    this.viewVar = value;
                                },
                                collapsible: true,
                                collapsed: true,
                                items: [{
                                        layout: 'column',
                                        items: [{
                                                columnWidth: 0.4,
                                                layout: 'form',
                                                padding: 5,
                                                items: [{
                                                        xtype: 'label',
                                                        text: lang('Jenis')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Jenis1',
                                                        name: 'Jenis1',
                                                        readOnly: true,
                                                        value: lang('Fraksi ( 00 ) Sangat Matang')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Jenis2',
                                                        name: 'Jenis2',
                                                        readOnly: true,
                                                        value: lang('Fraksi ( 0 ) Matang')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Jenis3',
                                                        name: 'Jenis4',
                                                        readOnly: true,
                                                        value: lang('Fraksi ( 1:2 ) Matang & Matang l')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Jenis4',
                                                        name: 'Jenis4',
                                                        readOnly: true,
                                                        value: lang('Fraksi ( 3:4 ) Matang ll & L. Matang')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Jenis5',
                                                        name: 'Jenis5',
                                                        readOnly: true,
                                                        value: lang('Fraksi ( 5 ) Lewat Matang')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Jenis6',
                                                        name: 'Jenis6',
                                                        readOnly: true,
                                                        value: lang('Fraksi ( 6 ) Tandan Kosong')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Jenis7',
                                                        name: 'Jenis7',
                                                        readOnly: true,
                                                        value: lang('TOTAL')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Jenis8',
                                                        name: 'Jenis8',
                                                        readOnly: true,
                                                        value: lang('Tangkai Panjang')
                                                    }]
                                            }, {
                                                columnWidth: 0.15,
                                                layout: 'form',
                                                padding: 5,
                                                items: [{
                                                        xtype: 'label',
                                                        text: lang('Total Tandan')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan1',
                                                        name: 'Tandan1'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan2',
                                                        name: 'Tandan2'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan3',
                                                        name: 'Tandan3'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan4',
                                                        name: 'Tandan4'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan5',
                                                        name: 'Tandan5'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan6',
                                                        name: 'Tandan6'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-TandanTotal',
                                                        name: 'TandanTotal'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan7',
                                                        name: 'Tandan7'
                                                    }]
                                            }, {
                                                columnWidth: 0.15,
                                                layout: 'form',
                                                padding: 5,
                                                items: [{
                                                        xtype: 'label',
                                                        text: lang('%')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Persen1',
                                                        name: 'Persen1'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Persen2',
                                                        name: 'Persen2'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Persen3',
                                                        name: 'Persen3'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Persen4',
                                                        name: 'Persen4'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Persen5',
                                                        name: 'Persen5'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Persen6',
                                                        name: 'Persen6'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-PersenTotal',
                                                        name: 'PersenTotal'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Persen7',
                                                        name: 'Persen7'
                                                    }]
                                            }, {
                                                columnWidth: 0.15,
                                                layout: 'form',
                                                padding: 5,
                                                items: [{
                                                        xtype: 'label',
                                                        text: lang('Denda (Kg)')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Denda1',
                                                        name: 'Denda1'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Denda2',
                                                        name: 'Denda2'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Denda3',
                                                        name: 'Denda3'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Denda4',
                                                        name: 'Denda4'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Denda5',
                                                        name: 'Denda5'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Denda6',
                                                        name: 'Denda6'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-DendaTotal',
                                                        name: 'DendaTotal'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Denda7',
                                                        name: 'Denda7'
                                                    }]
                                            }, {
                                                columnWidth: 0.15,
                                                layout: 'form',
                                                padding: 5,
                                                items: [{
                                                        xtype: 'label',
                                                        text: lang('% Potongan')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Potongan1',
                                                        name: 'Potongan1'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Potongan2',
                                                        name: 'Potongan2'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Potongan3',
                                                        name: 'Potongan3'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Potongan4',
                                                        name: 'Potongan4'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Potongan5',
                                                        name: 'Potongan5'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Potongan6',
                                                        name: 'Potongan6'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-PotonganTotal',
                                                        name: 'PotonganTotal'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Potongan7',
                                                        name: 'Potongan7'
                                                    }]
                                            }]
                                    }]
                            }],
                        buttons: [{
                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-Save',
                                hidden: true,
                                text: lang('Save New Transaction'),
                                margin: '5px',
                                scale: 'large',
                                ui: 's-button',
                                cls: 's-blue',
                                handler: function () {
                                    var form = this.up('form').getForm();
                                    var methode;
                                    if (Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyTransID').getValue() != '')
                                        methode = 'PUT';
                                    else
                                        methode = 'POST';
                                    form.submit({
                                        url: m_api + '/tc_transaction/transaction',
                                        method: methode,
                                        waitMsg: lang('Sending data...'),
                                        success: function (fp, o) {
                                            var flds = JSON.parse(o.response.responseText);  
                                            if(flds.success==true){
                                                Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridToolbar-BtnSearch').el.dom.click();
                                                ResetFormTransaction();
                                                Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm').setInputFieldOff();
                                            }
                                            //Ext.MessageBox.alert(flds.info, lang(flds.message));
                                            Ext.MessageBox.show({
                                                title: lang(flds.info),
                                                msg: lang(flds.message),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: flds.icon
                                            });
                                        },
                                        failure: function(response, opts) {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: lang('Could not connect to the database. Retry later'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });
                                }
                            }, {
                                text: lang('Cancel'),
                                id: 'Koltiva.view.Traceability.Transaction.TransactionForm-Form-ButtonReset',
                                margin: '5px',
                                scale: 'large',
                                ui: 's-button',
                                cls: 's-grey',
                                disabled: false,
                                handler: function () {
                                    ResetFormTransaction();
                                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm').setInputFieldOff();
                                }
                            }]
                    }]
            },{
                xtype: 'form',
                viewVar: false,
                setViewVar: function (value) {
                    this.viewVar = value;
                },
                frame: true,
                collapsible: true,
                margin: '0 0 0 0',
                padding: 5,
                hidden : false,
                title: lang('Form Penerimaan'),
                id: 'Koltiva.view.Traceability.Transaction.Form-panelPenerimaan',
                //items: [thisObj.objFormPenerimaan]
            }];
        this.callParent(arguments);
    }
});