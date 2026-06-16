Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['transactionNumber', 'transactionType', 'transactionName', 'transactionDate', 'debet', 'credit'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var storeWithdrawalTrans = Ext.create('Ext.data.Store', {
        fields: ['date', 'type', 'amount'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_lasttrans,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_savingtype = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_savingtype,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });


    var mc_status = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_status,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_member = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_member,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function displayFormWindow() {
        if (!win.isVisible()) {
            //            resetForm();
            DataForm.getForm().reset();
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: '70%',
        width: '70%',
        bodyPadding: 5,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
            layout: 'column',
            border: false,
            items: [{
                xtype: 'fieldset',
                title: lang('Applicant'),
                margin: '0 5 0 0',
                columnWidth: .5,
                items: [{
                    id: 'memberID',
                    name: 'memberID',
                    xtype: 'combo',
                    emptyText: '-- Select --',
                    fieldLabel: lang('Member'),
                    multiSelect: false,
                    store: mc_member,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    listeners: {
                        change: function() {
                            generateMemberData();
                        }
                    }
                }, {
                    xtype: 'textfield',
                    readOnly: true,
                    fieldLabel: lang('Name'),
                    id: 'name',
                    name: 'name'
                }, {
                    xtype: 'textarea',
                    readOnly: true,
                    fieldLabel: lang('Address'),
                    id: 'address',
                    name: 'address'
                }]
            }, {
                xtype: 'fieldset',
                layout: 'anchor',
                title: lang('Transaction'),
                margin: '0 5 0 0',
                columnWidth: .5,
                items: [{
                    xtype: 'textfield',
                    id: 'id',
                    name: 'id',
                    inputType: 'hidden'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Saving Number'),
                    id: 'memberSavingNo',
                    name: 'memberSavingNo'
                }, {
                    id: 'savingTypeID',
                    name: 'savingTypeID',
                    xtype: 'combo',
                    emptyText: '-- Select --',
                    fieldLabel: lang('Saving Type'),
                    multiSelect: false,
                    store: mc_savingtype,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    listeners: {
                        change: function() {
                            generateSavingType();
                        }
                    }
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Interest'),
                    readOnly: true,
                    id: 'savingTypeInterestRate',
                    name: 'savingTypeInterestRate'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Min Payment'),
                    readOnly: true,
                    id: 'savingTypeMinAmount',
                    name: 'savingTypeMinAmount'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Min Transaction'),
                    readOnly: true,
                    id: 'savingTypeMinTrans',
                    name: 'savingTypeMinTrans'
                }, {
                    xtype: 'textarea',
                    fieldLabel: lang('Remark'),
                    id: 'memberSavingRemark',
                    name: 'memberSavingRemark'

                }]
            }]
        }, ],
        buttons: [{
            id: 'saveButton',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('id').getValue() == '')
                    methode = 'POST';
                else
                    methode = 'PUT';
                form.submit({
                    url: m_crud,
                    method: methode,
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                win.hide(this, function() {
                    store.load();
                });
            }
        }, {
            text: 'Close',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                win.hide();
            }
        }]
    });
    var win = Ext.create('widget.window', {
        title: 'Data Saving',
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: '70%',
        minWidth: 370,
        height: '70%',
        layout: 'fit',
        items: [DataForm]
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue(),
                    status: Ext.getCmp('filterStatus').getValue()
                }
            });
        }
    }

    function generateSavingType() {
        Ext.Ajax.request({
            url: m_crud + '_savingtype',
            method: 'GET',
            params: {
                id: Ext.getCmp('savingTypeID').getValue()
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('savingTypeInterestRate').setValue(r.savingTypeInterestRate);
                Ext.getCmp('savingTypeMinAmount').setValue(r.savingTypeMinAmount);
                Ext.getCmp('savingTypeMinTrans').setValue(r.savingTypeMinTrans);
            }
        });
    }

    function generateMemberData() {
        Ext.Ajax.request({
            url: m_crud + '_member',
            method: 'GET',
            params: {
                id: Ext.getCmp('memberID').getValue()
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('name').setValue(r.name);
                Ext.getCmp('address').setValue(r.address);
            }
        });
    }

    var form_teller = Ext.create('Ext.Container', {
        width: '100%',
        height: '100%',
        renderTo: 'ext-content',
        listeners: {
            'render': function() {
                // setNoTransaction();
            }
        },
        items: [{
            xtype: 'container',
            layout: {
                type: 'column'
            },
            defaults: {
                margin: 5
            },
            items: [{
                xtype: 'container',
                columnWidth: .05
            }, {
                xtype: 'container',
                columnWidth: .4,
                layout: {
                    type: 'fit'
                },
                items: [{
                    xtype: 'button',
                    flex: true,
                    ui: 's-button',
                    cls: 's-grey',
                    padding: 10,
                    disabled: true,
                    disabledCls: 's-green',
                    scale: 'large',
                    text: lang('SETORAN'),
                    id: 'btn-toggle-add-deposit',
                    enableToggle: true,
                    toggleHandler: function(button, state) {
                        if (state === true) {
                            button.disable();
                            Ext.getCmp('btn-toggle-add-withdrawal').enable();
                            Ext.getCmp('frm-add-deposit').getForm().reset();
                            Ext.getCmp('btn-toggle-add-withdrawal').toggle(false);
                            Ext.getCmp('pnl-card-coop-transaction').getLayout().setActiveItem(0);

                            setNoTransaction();
                        }
                    }
                }]
            }, {
                xtype: 'container',
                columnWidth: .1
            }, {
                xtype: 'panel',
                columnWidth: .4,
                layout: {
                    type: 'fit'
                },
                items: [{
                    xtype: 'button',
                    scale: 'large',
                    text: lang('PENARIKAN'),
                    ui: 's-button',
                    cls: 's-grey',
                    disabledCls: 's-red',
                    padding: 10,
                    id: 'btn-toggle-add-withdrawal',
                    enableToggle: true,
                    toggleHandler: function(button, state) {
                        console.log(state);
                        if (state === true) {
                            button.disable();
                            Ext.getCmp('btn-toggle-add-deposit').enable();
                            Ext.getCmp('frm-add-withdrawal').getForm().reset();
                            Ext.getCmp('btn-toggle-add-deposit').toggle(false);
                            Ext.getCmp('pnl-card-coop-transaction').getLayout().setActiveItem(1);
                        }
                    }
                }]
            }, {
                xtype: 'panel',
                columnWidth: .05,
                layout: {
                    type: 'fit'
                },
                items: [{
                    xtype: 'button',
                    scale: 'large',
                    hidden: true,
                    text: 'AUTHORIZED',
                    ui: 's-button',
                    cls: 's-red',
                    padding: 10,
                    id: 'btn-authorized-add-withdrawal',
                    handler: function(button, state) {

                        var win = Ext.create('Ext.Window', {
                            title: 'Transaction List',
                            width: 1000,
                            height: 500,
                            modal: true,
                            layout: {
                                type: 'fit'
                            },
                            items: [{
                                xtype: 'grid',
                                store: Ext.create('Ext.data.Store', {
                                    storeId: 'store-grid-authorized-trans-list',
                                    fields: ['id', 'memberTransactionNumber', 'memberTransactionType', 'memberTransactionDate', 'cashSourceName', 'memberSavingNo', 'memberTransactionAmount', 'memberTransactionRemark', 'debet', 'credit'],
                                    autoLoad: true,
                                    pageSize: 10,
                                    proxy: {
                                        type: 'ajax',
                                        url: 'api/transaction/coop_transactions',
                                        reader: {
                                            type: 'json',
                                            root: 'data'
                                        }
                                    }
                                }),
                                dockedItems: [{
                                    xtype: 'pagingtoolbar',
                                    store: Ext.data.StoreManager.lookup('store-grid-authorized-trans-list'),
                                    dock: 'bottom',
                                    displayInfo: true
                                }],
                                columns: [{
                                    text: 'ID',
                                    dataIndex: 'id',
                                    hidden: true
                                }, {
                                    text: 'No',
                                    xtype: 'rownumberer',
                                    width: '5%'
                                }, {
                                    text: lang('Transaction Number'),
                                    width: '15%',
                                    dataIndex: 'memberTransactionNumber'
                                }, {
                                    text: lang('Name'),
                                    flex: true,
                                    dataIndex: 'memberSavingNo'
                                }, {
                                    text: lang('Trans Type'),
                                    width: 100,
                                    dataIndex: 'memberTransactionType'
                                }, {
                                    text: lang('Date [d/m/Y]'),
                                    width: 100,
                                    xtype: 'datecolumn',
                                    format: 'd/m/Y',
                                    dataIndex: 'memberTransactionDate'
                                }, {
                                    text: lang('Debet'),
                                    width: 200,
                                    align: 'right',
                                    style: 'font-family:courier new',
                                    xtype: 'numbercolumn',
                                    format: '0,000.00',
                                    dataIndex: 'debet'
                                }, {
                                    text: lang('Kredit'),
                                    width: 200,
                                    style: 'font-family:courier new',
                                    align: 'right',
                                    xtype: 'numbercolumn',
                                    format: '0,000.00',
                                    dataIndex: 'credit'
                                }, {
                                    xtype: 'actioncolumn',
                                    width: 40,
                                    items: [{
                                        icon: varjs.config.base_url + 'images/icons/silk/check_error.png', // Use a URL in the icon config
                                        tooltip: 'Cancel this transaction',
                                        handler: function(grid, rowIndex, colIndex) {
                                            var rec = grid.getStore().getAt(rowIndex);

                                            Ext.MessageBox.confirm('Cancel', 'Apakah anda mau membatalkan transaksi ini ?', function(btn) {
                                                if (btn == 'yes') {

                                                    Ext.Ajax.request({
                                                        waitMsg: 'Please Wait',
                                                        url: 'api/transaction/cancel_trans',
                                                        method: 'POST',
                                                        params: {
                                                            id: rec.get('id')
                                                        },
                                                        success: function(response, opts) {

                                                        },
                                                        failure: function(response, opts) {

                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    }]
                                }]
                            }]
                        }).show();
                    }
                }]
            }]
        }, {
            xtype: 'container',
            id: 'pnl-card-coop-transaction',
            layout: {
                type: 'card'
            },
            items: [{
                xtype: 'panel',
                frame: true,
                hidden: true,
                id: 'pnl-add-deposit',
                style: 'border:6px solid #799143',
                bodyStyle: 'background:#799143;',
                header: {
                    style: 'background:#799143;border-color:#799143;text-align:center; font-size:25px'
                },
                title: 'S E T O R A N',
                items: [
                    Ext.create('Ext.form.Panel', {
                        bodyPadding: 5,
                        disabled: true,
                        id: 'frm-add-deposit',
                        fieldDefaults: {
                            labelAlign: 'left',
                            labelWidth: 120
                        },
                        layout: {
                            type: 'column'
                        },
                        getInvalidFields: function() {
                            var invalidFields = [];
                            Ext.suspendLayouts();
                            this.form.getFields().filterBy(function(field) {
                                if (field.validate()) return;
                                invalidFields.push(field);
                            });
                            Ext.resumeLayouts(true);
                            return invalidFields;
                        },
                        items: [{
                            xtype: 'container',
                            columnWidth: .5,
                            layout: {
                                type: 'fit'
                            },
                            items: [{
                                xtype: 'hiddenfield',
                                name: 'UserId',
                                id: 'ApprovalIdDeposit'
                            }, {
                                xtype: 'fieldset',
                                id: 'fieldset-saving-acc',
                                title: lang('Informasi Simpanan'),
                                style: 'padding-bottom:2px',
                                items: [{
                                        xtype: 'datefield',
                                        fieldLabel: lang('Tanggal'),
                                        name: 'MemberTransactionDate'
                                    }, {
                                        xtype: 'checkbox',
                                        fieldStyle: 'margin-left:125px',
                                        boxLabel: lang('Akun Sendiri'),
                                        submitValue: false,
                                        checked: false,
                                        hidden: true,
                                        id: 'check-deposit-own-acc',
                                        listeners: {
                                            change: function(c, v) {

                                                if (v === true) {
                                                    Ext.getCmp('receiverID').setValue(1);
                                                    Ext.getCmp('recPrimaryNo').reset();
                                                    Ext.getCmp('receiverid_container').disable();
                                                } else {
                                                    Ext.getCmp('receiverid_container').enable();
                                                }
                                            }
                                        }
                                    }, {
                                        xtype: 'fieldcontainer',
                                        fieldLabel: lang('No. Anggota'),
                                        id: 'receiverid_container',
                                        hidden: false,
                                        layout: 'hbox',
                                        align: 'stretch',
                                        bodyStyle: 'padding: 10px',
                                        disabled: false,
                                        items: [{
                                            xtype: 'textfield',
                                            id: 'recPrimaryNo',
                                            name: 'recPrimaryNo',
                                            submitValue: false,
                                            readOnly: true
                                        }, {
                                            xtype: 'hidden',
                                            id: 'receiverID',
                                            name: 'receiverID'
                                        }, {
                                            xtype: 'hidden',
                                            id: 'nameTmp',
                                            name: 'nameTmp'
                                        }, {
                                            xtype: 'hidden',
                                            id: 'addressTmp',
                                            name: 'addressTmp'
                                        }, {
                                            iconCls: 'search',
                                            cls: 's-grey',
                                            xtype: 'button',
                                            id: 'isReceiver',
                                            style: 'margin-left:5px',
                                            handler: function() {
                                                var idSearchMember = Ext.id();

                                                var storeMember = Ext.create('Ext.data.Store', {
                                                    extend: 'Ext.data.Model',
                                                    storeId: 'store-grid-member',
                                                    fields: ['id', 'farmerID', 'primaryNo', 'name', 'Village', 'identityNumber', 'address', 'status', 'GroupName'],
                                                    // autoLoad: true,
                                                    pageSize: 10,
                                                    proxy: {
                                                        type: 'ajax',
                                                        url: m_all_member,
                                                        reader: {
                                                            type: 'json',
                                                            root: 'data'
                                                        }
                                                    }
                                                });
                                                var winreceipt = Ext.create('Ext.Window', {
                                                    title: 'Member List',
                                                    width: 750,
                                                    height: 400,
                                                    modal: true,
                                                    layout: 'fit',
                                                    items: [{
                                                        xtype: 'grid',
                                                        store: storeMember,
                                                        dockedItems: [{
                                                            xtype: 'toolbar',
                                                            items: [{
                                                                xtype: 'textfield',
                                                                // hidden:true,
                                                                fieldLabel: 'Member Name',
                                                                name: 'memberName',
                                                                id: idSearchMember
                                                            }, {
                                                                xtype: 'button',
                                                                margin: '0px 0px 0px 6px',
                                                                text: 'Search',
                                                                handler: function() {
                                                                    storeMember.load({
                                                                        params: {
                                                                            key: Ext.getCmp(idSearchMember).getValue(),
                                                                            // status: 1
                                                                        }
                                                                    });
                                                                }
                                                            }]
                                                        }, {
                                                            xtype: 'pagingtoolbar',
                                                            store: Ext.data.StoreManager.lookup('store-grid-member'),
                                                            dock: 'bottom',
                                                            displayInfo: true
                                                        }],
                                                        columns: [{
                                                            text: 'ID',
                                                            dataIndex: 'id',
                                                            hidden: true
                                                        }, {
                                                            text: 'No',
                                                            xtype: 'rownumberer',
                                                            width: 40
                                                        }, {
                                                            text: 'Member Number',
                                                            width: 150,
                                                            dataIndex: 'primaryNo'
                                                        }, {
                                                            text: 'Name',
                                                            flex: true,
                                                            dataIndex: 'name'
                                                        }, {
                                                            text: 'CPG',
                                                            flex: true,
                                                            dataIndex: 'GroupName'
                                                        }, {
                                                            text: 'Village',
                                                            width: 200,
                                                            dataIndex: 'Village'
                                                        }, {
                                                            text: 'Status',
                                                            width: 75,
                                                            dataIndex: 'status',
                                                            renderer: function(value) {
                                                                if (value == '1') {
                                                                    return lang('Active');
                                                                } else if (value == '2') {
                                                                    return lang('Inactive');
                                                                } else {
                                                                    return lang('Candidate');
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'actioncolumn',
                                                            width: 40,
                                                            items: [{
                                                                icon: varjs.config.base_url + 'images/icons/silk/check_error.png', // Use a URL in the icon config
                                                                tooltip: 'Select this member 1',
                                                                handler: function(grid, rowIndex, colIndex) {
                                                                    
                                                                    var rec = grid.getStore().getAt(rowIndex);
                                                                    
                                                                    var num = rec.get('primaryNo');
                                                                    var memid = rec.get('id');
                                                                    var name = rec.get('name');
                                                                    var address = rec.get('address');
                                                                    var identity = rec.get('identityNumber');
                                                                    
                                                                    Ext.getCmp('recPrimaryNo').setValue(num);
                                                                    Ext.getCmp('memberID-info').setValue(memid);
                                                                    Ext.getCmp('receiverID').setValue(memid);

                                                                    var loan = Ext.getCmp('cmb-add-deposit-loan-product').store.getProxy();
                                                                    loan.extraParams = {
                                                                        id: memid
                                                                    };
                                                                    Ext.getCmp('cmb-add-deposit-loan-product').store.load();

                                                                    var proxy = Ext.getCmp('cmb-add-deposit-saving-product').store.getProxy();
                                                                    proxy.extraParams = {
                                                                        id: memid
                                                                    };
                                                                    Ext.getCmp('cmb-add-deposit-saving-product').store.load();

                                                                    Ext.getCmp('nameTmp').setValue(name);
                                                                    Ext.getCmp('addressTmp').setValue(address);

                                                                    winreceipt.hide();

                                                                    getOutstandingNewMember();

                                                                    setNoTransaction2(rec.get('id'));

                                                                    setValueSaving();
                                                                    
                                                                }
                                                            }]
                                                        }]
                                                    }]
                                                }).show();

                                                storeMember.load({
                                                    params: {
                                                        // key: idSearchMember,
                                                        status: 1
                                                    }
                                                });
                                            }
                                        }]
                                    }, {
                                        xtype: 'fieldcontainer',
                                        fieldLabel: lang('Jenis Produk'),
                                        layout: {
                                            type: 'table',
                                            columns: 2
                                        },
                                        defaults: {
                                            margin: '1px 5px'
                                        },
                                        items: [{
                                            xtype: 'radio',
                                            width: 100,
                                            checked: true,
                                            boxLabel: lang('Simpanan'),
                                            name: 'trans_product_type',
                                            submitValue: false,
                                            listeners: {
                                                change: function(c, v) {
                                                    if (v === true) {
                                                        Ext.getCmp('savingRBtype').show();
                                                        Ext.getCmp('cont-saving-product-deposit').show();
                                                        // Ext.getCmp('cont-loan-product-deposit').hide();
                                                        // Ext.getCmp('grid-add-deposit-loan-trans').hide();
                                                        Ext.getCmp('grid-add-deposit-saving-trans').show();
                                                        // Ext.getCmp('current-balance-add-deposit').show();
                                                        // kadieu
                                                        // Ext.getCmp('uang-pangkal-add-deposit').show();
                                                        // Ext.getCmp('simp-pokok-add-deposit').show();
                                                        // Ext.getCmp('simp-wajib-add-deposit').show();
                                                        // Ext.getCmp('simp-sukarela-add-deposit').show();

                                                        Ext.getCmp('txt-deposit-amount').setReadOnly(true);

                                                        Ext.getCmp('cont-loan-product-deposit').hide();

                                                        Ext.getCmp('loan-term-add-deposit').hide();
                                                        Ext.getCmp('loan-pokok-add-deposit').hide();
                                                        Ext.getCmp('loan-denda-add-deposit').hide();
                                                        Ext.getCmp('loan-interest-add-deposit').hide();
                                                    } else {
                                                        Ext.getCmp('savingRBtype').hide();
                                                        Ext.getCmp('current-balance-add-deposit').hide();

                                                        Ext.getCmp('uang-pangkal-add-deposit').hide();
                                                        Ext.getCmp('simp-pokok-add-deposit').hide();
                                                        Ext.getCmp('simp-wajib-add-deposit').hide();
                                                        Ext.getCmp('simp-sukarela-add-deposit').hide();

                                                        Ext.getCmp('txt-deposit-amount').setReadOnly(false);
                                                    }
                                                }
                                            }
                                        }, {
                                            xtype: 'radio',
                                            boxLabel: lang('Pinjaman'),
                                            name: 'trans_product_type',
                                            // submitValue:false,
                                            listeners: {
                                                change: function(c, v) {
                                                    if (v === true) {
                                                        Ext.getCmp('savingRBtype').hide();
                                                        Ext.getCmp('cont-saving-product-deposit').hide();
                                                        Ext.getCmp('cont-loan-product-deposit').show();
                                                        // Ext.getCmp('grid-add-deposit-loan-trans').show();
                                                        Ext.getCmp('grid-add-deposit-saving-trans').hide();
                                                        Ext.getCmp('current-balance-add-deposit').hide();
                                                        Ext.getCmp('fieldset-saving-acc').setTitle('Loan Account');

                                                        Ext.getCmp('loan-term-add-deposit').show();
                                                        Ext.getCmp('loan-pokok-add-deposit').show();
                                                        Ext.getCmp('loan-interest-add-deposit').show();
                                                        Ext.getCmp('loan-interest-add-deposit').show();
                                                    } else {
                                                        Ext.getCmp('savingRBtype').show();
                                                        // Ext.getCmp('current-balance-add-deposit').show();
                                                        Ext.getCmp('fieldset-saving-acc').setTitle('Saving Account');
                                                    }
                                                }
                                            }
                                        }]
                                    }, {
                                        xtype: 'fieldcontainer',
                                        fieldLabel: '&nbsp;',
                                        id: 'savingRBtype',
                                        layout: {
                                            type: 'table',
                                            columns: 2
                                        },
                                        defaults: {
                                            margin: '1px 5px'
                                        },
                                        items: [{
                                            xtype: 'checkbox',
                                            width: 100,
                                            id:'initSaving',
                                            // checked:true,
                                            boxLabel: lang('Setoran Awal'),
                                            name: 'initial_saving_cb',
                                            // submitValue:false,
                                            listeners: {
                                                change: function(c, v) {
                                                    if (v === true) {
                                                        Ext.getCmp('cmb-add-deposit-saving-product').hide();

                                                        Ext.getCmp('uang-pangkal-add-deposit').show();
                                                        Ext.getCmp('simp-pokok-add-deposit').show();
                                                        Ext.getCmp('simp-wajib-add-deposit').show();
                                                        Ext.getCmp('simp-sukarela-add-deposit').show();

                                                        Ext.getCmp('txt-deposit-amount').setReadOnly(true);

                                                        Ext.getCmp('account_itself').setValue(1);
                                                    } else {
                                                        Ext.getCmp('cmb-add-deposit-saving-product').show();

                                                        Ext.getCmp('uang-pangkal-add-deposit').hide();
                                                        Ext.getCmp('simp-pokok-add-deposit').hide();
                                                        Ext.getCmp('simp-wajib-add-deposit').hide();
                                                        Ext.getCmp('simp-sukarela-add-deposit').hide();

                                                        Ext.getCmp('txt-deposit-amount').setReadOnly(false);
                                                        Ext.getCmp('account_itself').setValue(0);
                                                        // Ext.getCmp('current-balance-add-deposit').hide();
                                                    }
                                                }
                                            }
                                        }]
                                    }, {
                                        xtype: 'container',
                                        id: 'cont-saving-product-deposit',
                                        items: [{
                                                xtype: 'combo',
                                                hidden: true,
                                                editable: false,
                                                id: 'cmb-add-deposit-saving-product',
                                                fieldLabel: 'Pilih Simpanan <b style="color:red">*</b>',
                                                allowBlank: true,
                                                width: 400,
                                                store: Ext.create('Ext.data.Store', {
                                                    fields: ['id', 'label', 'number', 'current'],
                                                    autoLoad: false,
                                                    proxy: {
                                                        type: 'rest',
                                                        url: m_getmembersaving, // url that will load data with respect to start and limit params
                                                        reader: {
                                                            type: 'json',
                                                            root: 'data',
                                                            totalProperty: 'total'
                                                        }
                                                    }
                                                }),
                                                listConfig: {
                                                    itemTpl: '{label} - {number}'
                                                },
                                                listeners: {
                                                    select: function(c, r) {
                                                        var sn = c.next();
                                                        sn.setValue(r[0].data.number);
                                                        Ext.getCmp('current-balance-add-deposit').setValue(r[0].data.current);
                                                        var proxy = Ext.getCmp('grid-add-deposit-saving-trans').store.getProxy();
                                                        proxy.extraParams = {
                                                            id: r[0].data.id
                                                        };
                                                        Ext.getCmp('grid-add-deposit-saving-trans').store.load();

                                                        // console.log(Ext.getCmp('cmb-add-deposit-saving-product').getValue()+' '+r[0].data.id);

                                                        getLastTrans();
                                                        // setNoTransaction2(r[0].data.id);

                                                        // setValueSaving();
                                                    }
                                                },
                                                displayField: 'label',
                                                valueField: 'id',
                                                name: 'depositMemberSavingID'
                                            }, {
                                                xtype: 'textfield',
                                                width: 400,
                                                readOnly: true,
                                                hidden: true,
                                                submitValue: false,
                                                fieldLabel: 'No. Simpanan<b style="color:red">*</b>',
                                                name: 'name'
                                            }, {
                                                xtype: 'textfield',
                                                hidden: true,
                                                fieldLabel: 'No Transaksi',
                                                id: 'memberTransactionNumber',
                                                name: 'memberTransactionNumber',
                                                readOnly: true
                                            },
                                            Ext.create('Ext.grid.Panel', {
                                                hidden: true,
                                                id: 'grid-add-deposit-outstanding-trans',
                                                style: 'margin-top:20px;border-top: 1px solid #999;border-bottom: 0px solid #999;',
                                                store: Ext.create('Ext.data.Store', {
                                                    fields: ['feeName', 'amountPaid', 'amountUnpaid'],
                                                    autoLoad: false,
                                                    proxy: {
                                                        type: 'ajax',
                                                        url: m_api + 'cooperatives/unpaid_member_fee',
                                                        reader: {
                                                            type: 'json',
                                                            root: 'data'
                                                        }
                                                    }
                                                }),
                                                dockedItems: [{
                                                    xtype: 'toolbar',
                                                    plain: true,
                                                    items: [
                                                        '<span><b>Daftar Terhutang</b></span>'
                                                    ]
                                                }],
                                                columns: [{
                                                    text: 'Nama',
                                                    dataIndex: 'feeName',
                                                    width: 100,
                                                    flex: 1
                                                }, {
                                                    text: 'Terbayar',
                                                    align: 'right',
                                                    dataIndex: 'amountPaid',
                                                    width: 150
                                                }, {
                                                    text: 'Terhutang',
                                                    align: 'right',
                                                    dataIndex: 'amountUnpaid',
                                                    width: 150
                                                }],
                                                height: 157
                                            })
                                        ]
                                    },


                                    {
                                        xtype: 'container',
                                        hidden: true,
                                        id: 'cont-loan-product-deposit',
                                        items: [{
                                            xtype: 'combo',
                                            editable: false,
                                            id: 'cmb-add-deposit-loan-product',
                                            fieldLabel: 'Pilih Pinjaman <b style="color:red">*</b>',
                                            allowBlank: true,
                                            width: 350,
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['id', 'label', 'number'],
                                                autoLoad: false,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + 'transaction/getmemberloan', // url that will load data with respect to start and limit params
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            listeners: {
                                                select: function(c, r) {
                                                    var sn = c.next();
                                                    sn.setValue(r[0].data.number);

                                                    Ext.Ajax.request({
                                                        url: m_api + 'loan/getLoanData',
                                                        method: 'GET',
                                                        params: {
                                                            id: r[0].data.id
                                                        },
                                                        success: function(response, opts) {
                                                            var obj = Ext.JSON.decode(response.responseText);
                                                            Ext.getCmp('loan-term-add-deposit').setValue(obj.cicilanKe);
                                                            Ext.getCmp('loan-pokok-add-deposit').setValue(obj.angsuran);
                                                            Ext.getCmp('loan-interest-add-deposit').setValue(obj.denda);
                                                            Ext.getCmp('txt-deposit-amount').setValue(obj.total);
                                                        },
                                                        failure: function(response, opts) {
                                                            var text = response.responseText;
                                                            Ext.Msg.alert('Failure', 'Approval Failed');
                                                        }
                                                    });
                                                    // var store = Ext.getCmp('grid-add-deposit-loan-trans').getStore();
                                                    // store.proxy.extraParams = {id:r[0].data.id};
                                                    // store.load();
                                                }
                                            },
                                            displayField: 'label',
                                            valueField: 'id',
                                            name: 'memberLoanID'
                                        }, {
                                            xtype: 'textfield',
                                            width: 400,
                                            readOnly: true,
                                            submitValue: false,
                                            fieldLabel: 'No. Pinjaman<b style="color:red">*</b>',
                                            name: 'name'
                                        }]
                                    }
                                ]
                            }, {
                                xtype: 'fieldset',
                                title: 'Informasi Setoran',
                                style: 'padding-bottom:2px',
                                items: [{
                                    xtype: 'checkbox',
                                    fieldStyle: 'margin-left:125px',
                                    id: 'account_itself',
                                    boxLabel: lang('Akun Sendiri'),
                                    submitValue: false,
                                    checked: false,
                                    listeners: {
                                        change: function(c, v) {
                                            if (v === true) {
                                                // Ext.getCmp('member-name-add-trans').disable();
                                                Ext.getCmp('member-address-add-trans-info').disable();
                                                // Ext.getCmp('isFarmer').disable();

                                                Ext.getCmp('deposit_memberid_container').hide();

                                                Ext.getCmp('member-name-add-trans-info').setValue(Ext.getCmp('nameTmp').getValue());
                                                Ext.getCmp('member-address-add-trans-info').setValue(Ext.getCmp('addressTmp').getValue());
                                            } else {
                                                Ext.getCmp('member-name-add-trans-info').enable();
                                                Ext.getCmp('member-address-add-trans-info').enable();
                                                // Ext.getCmp('isFarmer').enable();

                                                Ext.getCmp('deposit_memberid_container').show();
                                                Ext.getCmp('member-name-add-trans-info').setValue(null);
                                                Ext.getCmp('member-address-add-trans-info').setValue(null);
                                            }
                                        }
                                    }
                                }, {
                                    xtype: 'checkbox',
                                    fieldStyle: 'margin-left:125px',
                                    boxLabel: 'Anggota',
                                    submitValue: false,
                                    hidden: true,
                                    listeners: {
                                        change: function(c, v) {
                                            if (v === true) {
                                                Ext.getCmp('deposit_memberid_container').enable();
                                            } else {
                                                Ext.getCmp('memberID').reset();
                                                Ext.getCmp('primaryNo').reset();
                                                Ext.getCmp('frm-add-deposit').getForm().reset();
                                                Ext.getCmp('deposit_memberid_container').disable();
                                                Ext.getCmp('check-deposit-own-acc').setValue(false);
                                                Ext.getCmp('receiverID').setValue('');
                                            }
                                        }
                                    }
                                }, {
                                    xtype: 'fieldcontainer',
                                    fieldLabel: lang('No. Anggota'),
                                    id: 'deposit_memberid_container',
                                    hidden: false,
                                    layout: 'hbox',
                                    align: 'stretch',
                                    bodyStyle: 'padding: 10px',
                                    disabled: true,
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'primaryNo-info',
                                        name: 'primaryNo',
                                        submitValue: false,
                                        readOnly: true
                                    }, {
                                        xtype: 'hidden',
                                        id: 'memberID-info',
                                        name: 'memberID',
                                        readOnly: true
                                    }, {
                                        iconCls: 'search',
                                        cls: 's-grey',
                                        xtype: 'button',
                                        id: 'isFarmer-info',
                                        style: 'margin-left:5px',
                                        handler: function() {
                                            var storeMember = Ext.create('Ext.data.Store', {
                                                extend: 'Ext.data.Model',
                                                storeId: 'store-grid-member',
                                                fields: ['id', 'farmerID', 'primaryNo', 'name', 'Village', 'identityNumber', 'address', 'status', 'GroupName'],
                                                // autoLoad: true,
                                                pageSize: 10,
                                                proxy: {
                                                    type: 'ajax',
                                                    url: m_all_member,
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data'
                                                    }
                                                }
                                            });

                                            var winmember = Ext.create('Ext.Window', {
                                                title: 'Daftar Anggota',
                                                width: 750,
                                                height: 400,
                                                modal: true,
                                                layout: 'fit',
                                                items: [{
                                                    xtype: 'grid',
                                                    store: storeMember,
                                                    dockedItems: [{
                                                        xtype: 'toolbar',
                                                        items: [{
                                                            xtype: 'textfield',
                                                            fieldLabel: 'Member Name',
                                                            name: 'memberName',
                                                            id: 'memberNameDepositInfo'
                                                        }, {
                                                            xtype: 'button',
                                                            margin: '0px 0px 0px 6px',
                                                            text: 'Search',
                                                            handler: function() {
                                                                storeMember.load({
                                                                    params: {
                                                                        key: Ext.getCmp('memberNameDepositInfo').getValue(),
                                                                        status: 1
                                                                    }
                                                                });
                                                            }
                                                        }]
                                                    }, {
                                                        xtype: 'pagingtoolbar',
                                                        store: Ext.data.StoreManager.lookup('store-grid-member'),
                                                        dock: 'bottom',
                                                        displayInfo: true
                                                    }],
                                                    columns: [{
                                                        text: 'ID',
                                                        dataIndex: 'id',
                                                        hidden: true
                                                    }, {
                                                        text: 'No',
                                                        xtype: 'rownumberer',
                                                        width: 40
                                                    }, {
                                                        text: 'Member Number',
                                                        width: 150,
                                                        dataIndex: 'primaryNo'
                                                    }, {
                                                        text: 'Name',
                                                        flex: true,
                                                        dataIndex: 'name'
                                                    }, {
                                                        text: 'CPG',
                                                        flex: true,
                                                        dataIndex: 'GroupName'
                                                    }, {
                                                        text: 'Village',
                                                        width: 200,
                                                        dataIndex: 'Village'
                                                    }, {
                                                        text: 'Status',
                                                        width: 75,
                                                        dataIndex: 'status',
                                                        renderer: function(value) {
                                                            if (value == '1') {
                                                                return lang('Active');
                                                            } else if (value == '2') {
                                                                return lang('Inactive');
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'actioncolumn',
                                                        width: 40,
                                                        items: [{
                                                            icon: varjs.config.base_url + 'images/icons/silk/check_error.png', // Use a URL in the icon config
                                                            tooltip: 'Select this member 2',
                                                            handler: function(grid, rowIndex, colIndex) {
                                                                var rec = grid.getStore().getAt(rowIndex);
                                                                // alert('s');
                                                                var num = rec.get('primaryNo');
                                                                var memid = rec.get('id');
                                                                var name = rec.get('name');
                                                                var identity = rec.get('identityNumber');
                                                                var address = rec.get('address');

                                                                Ext.getCmp('primaryNo-info').setValue(num);
                                                                Ext.getCmp('memberID-info').setValue(memid);
                                                                Ext.getCmp('member-name-add-trans-info').setValue(name);
                                                                Ext.getCmp('identityNumber-info').setValue(identity);
                                                                Ext.getCmp('member-address-add-trans-info').setValue(address);

                                                                // Ext.getCmp('check-deposit-own-acc').setValue(true);
                                                                // Ext.getCmp('receiverID').setValue(memid);

                                                                // var proxy = Ext.getCmp('cmb-add-deposit-saving-product').store.getProxy();
                                                                // proxy.extraParams = {id:memid};
                                                                // Ext.getCmp('cmb-add-deposit-saving-product').store.load();

                                                                winmember.hide();
                                                            }
                                                        }]
                                                    }]
                                                }]
                                            }).show();

                                            storeMember.load({
                                                params: {
                                                    key: Ext.getCmp('memberNameDepositInfo').getValue(),
                                                    status: 1
                                                }
                                            });
                                        }
                                    }]
                                }, {
                                    xtype: 'textfield',
                                    anchor: '100%',
                                    id: 'member-name-add-trans-info',
                                    fieldLabel: 'Nama',
                                    name: 'name'
                                }, {
                                    xtype: 'textfield',
                                    width: 400,
                                    hidden: true,
                                    fieldLabel: 'No. Identitas',
                                    id: 'identityNumber-info',
                                    name: 'identityNumber'
                                        // allowBlank: false
                                }, {
                                    xtype: 'textarea',
                                    fieldLabel: 'Address',
                                    width: 550,
                                    height: 80,
                                    id: 'member-address-add-trans-info',
                                    name: 'address'
                                }]
                            }]
                        }, {
                            xtype: 'fieldset',
                            columnWidth: .5,
                            margin: '0 10',
                            height: 400,
                            title: 'Detil Setoran',
                            style: 'padding-bottom:20px',
                            defaults: {
                                labelWidth: 130
                            },
                            items: [{
                                    xtype: 'textfield',
                                    anchor: '100%',
                                    hidden: true,
                                    readOnly: true,
                                    height: 68,
                                    margin: '20px 0px',
                                    fieldStyle: 'text-align:right;font-size:20px;font-weight:bold;font-family:Courier New;',
                                    value: '0',
                                    id: 'current-balance-add-deposit',
                                    submitValue: false,
                                    labelAlign: 'top',
                                    fieldLabel: '<b>BALANCE SAAT INI</b>'
                                }, {
                                    xtype: 'container',
                                    hidden: true,
                                    id: 'ContainerOutstanding',
                                    layout: {
                                        type: 'table',
                                        columns: 2
                                    },
                                    items: [{
                                        xtype: 'numericfield',
                                        id: 'Outstanding-add-source-fund',
                                        fieldStyle: 'text-align:right;',
                                        fieldLabel: 'Terhutang',
                                        // allowBlank: false,
                                        readOnly: true,
                                        width: 250
                                            // name: 'source'
                                    }, {
                                        xtype: 'displayfield',
                                        boxLabel: 'Cash',
                                        name: 'source',
                                        id: 'OutstandingMonth',
                                        // value:'9 Months',
                                        inputValue: 'cash',
                                        fieldStyle: 'margin-left:5px;'
                                    }]
                                },
                                // {
                                //     xtype:'textfield',
                                //     anchor:'100%',
                                //     readOnly:true,
                                //     // height:68,
                                //     margin:'20px 0px',
                                //     fieldStyle:'text-align:right;font-size:20px;font-weight:bold;font-family:Courier New;',
                                //     value:'0',
                                //     id:'outstanding-saving-add-deposit',
                                //     submitValue:false,
                                //     labelAlign:'top',
                                //     fieldLabel:'<b>OUTSTANDING SAVING</b>'
                                // },
                                {
                                    xtype: 'numericfield',
                                    hideTrigger: true,
                                    anchor: '100%',
                                    // margin:'20px 0px',
                                    value: '0',
                                    id: 'uang-pangkal-add-deposit',
                                    name: 'pangkal_form_deposit',
                                    // submitValue:false,
                                    fieldLabel: 'Uang Pangkal',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            calcGrandTotal()
                                        }
                                    }
                                }, {
                                    xtype: 'numericfield',
                                    hideTrigger: true,
                                    anchor: '100%',
                                    // margin:'20px 0px',
                                    value: '0',
                                    id: 'simp-pokok-add-deposit',
                                    name: 'pokok_form_deposit',
                                    // submitValue:false,
                                    fieldLabel: 'Simpanan Pokok',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            calcGrandTotal()
                                        }
                                    }
                                }, {
                                    xtype: 'numericfield',
                                    hideTrigger: true,
                                    anchor: '100%',
                                    // margin:'20px 0px',
                                    value: '0',
                                    id: 'simp-wajib-add-deposit',
                                    name: 'wajib_form_deposit',
                                    // submitValue:false,
                                    fieldLabel: 'Simpanan Wajib',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            calcGrandTotal()
                                        }
                                    }
                                }, {
                                    xtype: 'numericfield',
                                    hideTrigger: true,
                                    anchor: '100%',
                                    // margin:'20px 0px',
                                    value: '0',
                                    id: 'simp-sukarela-add-deposit',
                                    name: 'sukarela_form_deposit',
                                    // submitValue:false,
                                    fieldLabel: 'Simpanan Sukarela',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            calcGrandTotal()
                                        }
                                    }
                                },


                                //loadn
                                {
                                    xtype: 'displayfield',
                                    // hideTrigger:true,
                                    hidden: true,
                                    anchor: '100%',
                                    // margin:'20px 0px',
                                    // value:'0',
                                    id: 'loan-term-add-deposit',
                                    name: 'termloan_form_deposit',
                                    // submitValue:false,
                                    fieldLabel: 'Cicilan Ke #',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            // calcGrandTotal()
                                        }
                                    }
                                }, {
                                    xtype: 'displayfield',
                                    // hideTrigger:true,
                                    hidden: true,
                                    anchor: '100%',
                                    // margin:'20px 0px',
                                    // value:'0',
                                    id: 'loan-pokok-add-deposit',
                                    name: 'loanpokok_form_deposit',
                                    // submitValue:false,
                                    fieldLabel: 'Angsuran Pokok',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            // calcGrandTotal()
                                        }
                                    }
                                }, {
                                    xtype: 'displayfield',
                                    // hideTrigger:true,
                                    hidden: true,
                                    anchor: '100%',
                                    // margin:'20px 0px',
                                    // value:'0',
                                    id: 'loan-interest-add-deposit',
                                    name: 'interest_form_deposit',
                                    // submitValue:false,
                                    fieldLabel: 'Bunga',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            // calcGrandTotal()
                                        }
                                    }
                                }, {
                                    xtype: 'displayfield',
                                    // hideTrigger:true,
                                    hidden: true,
                                    anchor: '100%',
                                    // margin:'20px 0px',
                                    // value:'0',
                                    id: 'loan-denda-add-deposit',
                                    name: 'denda_form_deposit',
                                    // submitValue:false,
                                    fieldLabel: 'Denda',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            // calcGrandTotal()
                                        }
                                    }
                                },
                                //end loan
                                {
                                    xtype: 'numericfield',
                                    anchor: '100%',
                                    readOnly: true,
                                    // width: 500,
                                    // height:68,
                                    height: 80,
                                    id: 'txt-deposit-amount',
                                    hideTrigger: true,
                                    // labelWidth:200,
                                    name: 'memberTransactionAmount',
                                    fieldStyle: 'text-align:right;font-size:20px;font-weight:bold;font-family:Courier New;',
                                    fieldLabel: '<b>Jumlah Setoran</b>',
                                    allowBlank: false,
                                    labelAlign:'top',
                                    listeners: {
                                        change: function(c, v) {
                                            // var adm = Ext.getCmp('txt-deposit-adm-fee').getValue();
                                            // var total = adm + v;
                                            // Ext.getCmp('txt-total-deposit').setValue(total);
                                        }
                                    }
                                }, {
                                    xtype: 'numericfield',
                                    width: 550,
                                    height: 30,
                                    hidden: true,
                                    id: 'txt-deposit-adm-fee',
                                    name: 'memberTransactionFee',
                                    hideTrigger: true,
                                    labelWidth: 200,
                                    fieldStyle: 'text-align:right',
                                    fieldLabel: 'Biaya Admin <b style="color:red">*</b>',
                                    allowBlank: true,
                                    listeners: {
                                        change: function(c, v) {
                                            var amount = Ext.getCmp('txt-deposit-amount').getValue();
                                            var total = amount + v;
                                            Ext.getCmp('txt-total-deposit').setValue(total);
                                        }
                                    }
                                }, {
                                    xtype: 'numericfield',
                                    readOnly: true,
                                    width: 550,
                                    height: 30,
                                    hidden: true,
                                    id: 'txt-total-deposit',
                                    submitValue: false,
                                    hideTrigger: true,
                                    labelWidth: 200,
                                    labelStyle: 'font-weight:bold',
                                    fieldStyle: 'font-weight:bold;text-align:right',
                                    fieldLabel: lang('Total Setoran')
                                },
                                Ext.create('Ext.grid.Panel', {
                                    id: 'grid-add-deposit-saving-trans',
                                    style: 'margin-top:20px;border-top: 1px solid #999;border-bottom: 0px solid #999;',
                                    store: Ext.create('Ext.data.Store', {
                                        fields: ['date', 'type', 'amount'],
                                        autoLoad: false,
                                        proxy: {
                                            type: 'ajax',
                                            url: m_lasttrans,
                                            reader: {
                                                type: 'json',
                                                root: 'data'
                                            }
                                        }
                                    }),
                                    dockedItems: [{
                                        xtype: 'toolbar',
                                        plain: true,
                                        items: [
                                            '<span><b>Transaksi Terakhir</b></span>'
                                        ]
                                    }],
                                    columns: [{
                                        text: 'Tgl',
                                        dataIndex: 'date',
                                        width: 100
                                    }, {
                                        text: 'Transaction',
                                        dataIndex: 'type',
                                        flex: 1,
                                        renderer: function(v) {
                                            if (v === "1") {
                                                return '<span style="color:blue">Deposit</span>';
                                            } else {
                                                return '<span style="color:red">Withdraw</span>';
                                            }
                                        }
                                    }, {
                                        text: 'Jumlah',
                                        align: 'right',
                                        dataIndex: 'amount',
                                        width: 150,
                                        renderer: function(v) {
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v) + '</span>';
                                        }
                                    }],
                                    height: 157
                                })
                                // ,
                                // Ext.create('Ext.grid.Panel', {
                                //     id:'grid-add-deposit-loan-trans',
                                //     style:'margin-top:20px;border-top: 1px solid #999;border-bottom: 0px solid #999;',
                                //     store: Ext.create('Ext.data.Store', {
                                //         autoLoad:false,
                                //         fields:['due', 'paid', 'amount','arrear'],
                                //         proxy: {
                                //             type: 'ajax',
                                //             url: 'api/transaction/paymentplan',
                                //             reader: {
                                //                 type: 'json',
                                //                 root: 'data'
                                //             }
                                //         }
                                //     }),
                                //     dockedItems:[
                                //         {
                                //             xtype:'toolbar',
                                //             plain:true,
                                //             items:[
                                //                 '<span><b>Payment Plan</b></span>'
                                //             ]
                                //         }
                                //     ],
                                //     columns: [
                                //         { text: 'Amount', align:'right', dataIndex: 'amount', width: 200, renderer: function(v){
                                //             return '<span style="font-family:courier new">' + Ext.util.Format.number(v) + '</span>';
                                //         } },
                                //         { text: 'Payment. Date',  dataIndex: 'paid', width:100 },
                                //         { text: 'Due Date',  dataIndex: 'due', width:100 },
                                //         { text: 'Arrear',  dataIndex: 'arrear', flex:true }

                                //     ],
                                //     height: 250
                                // })
                            ]
                        }]
                    })
                ],
                buttons: [{
                    id: 'newDeposit',
                    text: 'Transaksi Baru',
                    ui: 's-button',
                    scale: 'large',
                    cls: 's-blue',
                    margin: '5px',
                    handler: function() {
                        var form = Ext.getCmp('frm-add-deposit');
                        form.getForm().reset();
                        form.enable();
                        this.hide();
                        Ext.getCmp('saveButtonDeposit').show();
                        // setNoTransaction();
                    }
                }, {
                    id: 'saveButtonDeposit',
                    text: 'Simpan Setoran',
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    hidden: true,
                    handler: function() {

                        var id = Ext.id();
                        var useridID = Ext.id();
                        var passwordID = Ext.id();

                        Ext.getCmp('ApprovalIdDeposit').setValue(null);

                        var loginform = Ext.create('Ext.form.Panel', {
                            bodyPadding: 5,
                            width: 350,
                            layout: 'anchor',
                            defaults: {
                                anchor: '100%'
                            },
                            defaultType: 'textfield',
                            items: [{
                                fieldLabel: 'Username',
                                name: 'userid',
                                id: useridID,
                                allowBlank: false
                            }, {
                                fieldLabel: 'Password',
                                name: 'password',
                                id: passwordID,
                                allowBlank: false,
                                inputType: 'password'
                            }],
                            buttons: [{
                                text: 'Login',
                                formBind: true, //only enabled once the form is valid
                                disabled: true,
                                handler: function() {
                                    var msg = Ext.MessageBox.wait('Processing...');
                                    Ext.Ajax.request({
                                        url: m_approval,
                                        method: 'POST',
                                        params: {
                                            username: Ext.getCmp(useridID).getValue(),
                                            password: Ext.getCmp(passwordID).getValue()
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.JSON.decode(response.responseText);
                                            if (obj.approved) {
                                                Ext.getCmp('ApprovalIdDeposit').setValue(obj.UserId);
                                                // return false;
                                                submitDeposit(obj.UserId);
                                                Ext.getCmp(id).hide();
                                            } else {
                                                Ext.Msg.alert('Failure', 'Approval Failed');
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var text = response.responseText;
                                            Ext.Msg.alert('Failure', 'Approval Failed');
                                        }
                                    });
                                }
                            }]
                        });




                        var windowApproval = Ext.create('Ext.Window', {
                            id: id,
                            width: 400,
                            height: 150,
                            title: 'Approval',
                            plain: true,
                            closable: true,
                            // headerPosition: 'left',
                            layout: 'fit',
                            items: [loginform]
                        });

                        var loadingtxt = "Loading...";
                        Ext.Ajax.request({
                            url: m_cooplimit,
                            method: 'GET',
                            params: {
                                amount: Ext.getCmp('txt-deposit-amount').getValue()
                            },
                            success: function(form, action) {
                                var d = Ext.decode(form.responseText);
                                if (!d.status) {
                                    windowApproval.show();
                                } else {
                                    submitDeposit();
                                }
                            },
                            failure: function(form, action) {}
                        });


                    }
                }, {
                    text: 'Reset',
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-grey',
                    disabled: false,
                    handler: function() {
                        var form = Ext.getCmp('frm-add-deposit').getForm();
                        form.reset();
                    }
                }],
                listeners: {
                  beforerender:function(){
                    Ext.getCmp('cmb-add-deposit-saving-product').show();
                    Ext.getCmp('txt-deposit-amount').setReadOnly(false);
                    Ext.getCmp('uang-pangkal-add-deposit').hide();
                    Ext.getCmp('simp-pokok-add-deposit').hide();
                    Ext.getCmp('simp-wajib-add-deposit').hide();
                    Ext.getCmp('simp-sukarela-add-deposit').hide();
                  }
                }
            }, {
                xtype: 'panel',
                frame: true,
                hidden: true,
                id: 'pnl-add-withdrawal',
                style: 'border:5px solid #A90118',
                bodyStyle: 'background:#A90118;',
                header: {
                    style: 'background:#A90118;border-color:#A90118;text-align:center; font-size:25px'
                },
                title: 'P E N A R I K A N',
                items: Ext.create('Ext.form.Panel', {
                    bodyPadding: 5,
                    disabled: true,
                    id: 'frm-add-withdrawal',
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 120
                    },
                    layout: {
                        type: 'column'
                    },
                    items: [{
                        xtype: 'container',
                        columnWidth: .5,
                        layout: {
                            type: 'fit'
                        },
                        items: [{
                            xtype: 'hiddenfield',
                            name: 'UserId',
                            id: 'ApprovalIdWith'
                        }, {
                            xtype: 'fieldset',
                            title: 'Informasi Simpanan',
                            style: 'padding-bottom:20px',
                            items: [{
                                xtype: 'datefield',
                                fieldLabel: lang('Tanggal'),
                                name: 'MemberTransactionDate'
                            }, {
                                xtype: 'fieldcontainer',
                                fieldLabel: lang('No. Anggota<b style="color:red">*</b>'),
                                id: 'withdraw_receiverid_container',
                                hidden: false,
                                layout: 'hbox',
                                align: 'stretch',
                                bodyStyle: 'padding: 10px',
                                items: [{
                                    xtype: 'textfield',
                                    id: 'with_recPrimaryNo',
                                    name: 'recPrimaryNo',
                                    submitValue: false,
                                    allowBlank:false,
                                    readOnly: true
                                }, {
                                    xtype: 'hidden',
                                    id: 'with_receiverID',
                                    name: 'receiverID',
                                    readOnly: true
                                }, {
                                    iconCls: 'search',
                                    cls: 's-grey',
                                    xtype: 'button',
                                    id: 'with_isReceiver',
                                    style: 'margin-left:5px',
                                    handler: function() {
                                        var storeMember = Ext.create('Ext.data.Store', {
                                            extend: 'Ext.data.Model',
                                            storeId: 'store-grid-member',
                                            fields: ['id', 'farmerID', 'primaryNo', 'name', 'Village', 'identityNumber', 'address', 'status', 'memberPhoto', 'memberSignature', 'GroupName'],
                                            // autoLoad: true,
                                            pageSize: 10,
                                            proxy: {
                                                type: 'ajax',
                                                url: m_all_member,
                                                reader: {
                                                    type: 'json',
                                                    root: 'data'
                                                }
                                            }
                                        });

                                        var winmember = Ext.create('Ext.Window', {
                                            title: 'Daftar Anggota',
                                            width: 750,
                                            height: 400,
                                            modal: true,
                                            layout: 'fit',
                                            items: [{
                                                xtype: 'grid',
                                                store: storeMember,
                                                dockedItems: [{
                                                    xtype: 'toolbar',
                                                    items: [{
                                                        xtype: 'textfield',
                                                        fieldLabel: 'Member Name',
                                                        name: 'memberName',
                                                        id: 'memberNameWithdrawal'
                                                    }, {
                                                        xtype: 'button',
                                                        margin: '0px 0px 0px 6px',
                                                        text: 'Search',
                                                        handler: function() {
                                                            storeMember.load({
                                                                params: {
                                                                    key: Ext.getCmp('memberNameWithdrawal').getValue(),
                                                                    status: 1
                                                                }
                                                            });
                                                        }
                                                    }]
                                                }, {
                                                    xtype: 'pagingtoolbar',
                                                    store: Ext.data.StoreManager.lookup('store-grid-member'),
                                                    dock: 'bottom',
                                                    displayInfo: true
                                                }],
                                                columns: [{
                                                    text: 'ID',
                                                    dataIndex: 'id',
                                                    hidden: true
                                                }, {
                                                    text: 'memberPhoto',
                                                    dataIndex: 'memberPhoto',
                                                    hidden: true
                                                }, {
                                                    text: 'memberSignature',
                                                    dataIndex: 'memberSignature',
                                                    hidden: true
                                                }, {
                                                    text: 'No',
                                                    xtype: 'rownumberer',
                                                    width: 40
                                                }, {
                                                    text: 'Member Number',
                                                    width: 150,
                                                    dataIndex: 'primaryNo'
                                                }, {
                                                    text: 'Name',
                                                    flex: true,
                                                    dataIndex: 'name'
                                                }, {
                                                    text: 'CPG',
                                                    flex: true,
                                                    dataIndex: 'GroupName'
                                                }, {
                                                    text: 'Village',
                                                    width: 200,
                                                    dataIndex: 'Village'
                                                }, {
                                                    text: 'Status',
                                                    width: 75,
                                                    dataIndex: 'status',
                                                    renderer: function(value) {
                                                        if (value == '1') {
                                                            return lang('Active');
                                                        } else if (value == '2') {
                                                            return lang('Inactive');
                                                        }
                                                    }
                                                }, {
                                                    xtype: 'actioncolumn',
                                                    width: 40,
                                                    items: [{
                                                        icon: varjs.config.base_url + 'images/icons/silk/check_error.png', // Use a URL in the icon config
                                                        tooltip: 'Select this member 3',
                                                        handler: function(grid, rowIndex, colIndex) {
                                                            var rec = grid.getStore().getAt(rowIndex);

                                                            var num = rec.get('primaryNo');
                                                            var memid = rec.get('id');

                                                            Ext.getCmp('with_recPrimaryNo').setValue(num);
                                                            Ext.getCmp('with_receiverID').setValue(memid);

                                                            var proxy = Ext.getCmp('cmb-add-withdrawal-saving-product').store.getProxy();
                                                            proxy.extraParams = {
                                                                id: memid
                                                            };

                                                            Ext.getCmp('cmb-add-withdrawal-saving-product').store.load();
                                                            Ext.getCmp('img-photo-add-withdraw').setSrc(m_baseurl_api + rec.get('memberPhoto'));
                                                            Ext.getCmp('img-sigi-add-withdraw').setSrc(m_baseurl_api + rec.get('memberSignature'));

                                                            winmember.hide();
                                                        }
                                                    }]
                                                }]
                                            }]
                                        }).show();

                                        storeMember.load({
                                            params: {
                                                key: Ext.getCmp('memberNameWithdrawal').getValue(),
                                                status: 1
                                            }
                                        });
                                    }
                                }]
                            }, {
                                xtype: 'combo',
                                id: 'cmb-add-withdrawal-saving-product',
                                fieldLabel: 'Pilih Simpanan <b style="color:red">*</b>',
                                allowBlank: false,
                                width: 350,
                                store: Ext.create('Ext.data.Store', {
                                    fields: ['id', 'label', 'number', 'current'],
                                    autoLoad: false,
                                    proxy: {
                                        type: 'rest',
                                        url: m_api + 'transaction/getmembersaving', // url that will load data with respect to start and limit params
                                        reader: {
                                            type: 'json',
                                            root: 'data',
                                            totalProperty: 'total'
                                        }
                                    }
                                }),
                                listeners: {
                                    select: function(c, r) {
                                        var sn = c.next();
                                        var curr = Ext.getCmp('txt-current-balance-withdraw').setValue(r[0].data.current);
                                        sn.setValue(r[0].data.number);

                                        var proxy = Ext.getCmp('grid-add-withdrawal-saving-trans').store.getProxy();
                                        proxy.extraParams = {
                                            id: r[0].data.id,
                                            MemberTransactionType: 2
                                        };
                                        Ext.getCmp('grid-add-withdrawal-saving-trans').store.load();
                                    }
                                },
                                displayField: 'label',
                                valueField: 'id',
                                name: 'memberSavingID'
                            }, {
                                xtype: 'textfield',
                                anchor: '80%',
                                readOnly: true,
                                submitValue: false,
                                fieldLabel: 'No. Simpanan<b style="color:red">*</b>',
                                name: 'name'
                            }]
                        }, {
                            xtype: 'panel',
                            layout: {
                                type: 'hbox',
                                pack: 'center'
                            },
                            defaults: {
                                margin: 5
                            },
                            items: [{
                                xtype: 'container',
                                width: 125,
                                layout: {
                                    type: 'anchor'
                                },
                                items: [{
                                    xtype: 'displayfield',
                                    anchor: '100%',
                                    fieldStyle: 'text-align:center;font-weight:bold',
                                    value: 'PHOTO'
                                }, {
                                    xtype: 'image',
                                    anchor: '100%',
                                    height: 150,
                                    id: 'img-photo-add-withdraw',
                                    style: 'margin-bottom:5px;border:1px solid #999'
                                }]
                            }, {
                                xtype: 'container',
                                width: 125,
                                layout: {
                                    type: 'anchor'
                                },
                                items: [{
                                    xtype: 'displayfield',
                                    anchor: '100%',
                                    fieldStyle: 'text-align:center;font-weight:bold;',
                                    value: 'SIGNATURE'
                                }, {
                                    xtype: 'image',
                                    anchor: '100%',
                                    height: 150,
                                    id: 'img-sigi-add-withdraw',
                                    style: 'margin-bottom:5px;border:1px solid #999'
                                }]
                            }]
                        }]
                    }, {
                        xtype: 'container',
                        columnWidth: .5,
                        layout: {
                            type: 'fit'
                        },
                        items: [{
                            xtype: 'fieldset',
                            title: 'Detil Penarikan',
                            style: 'padding-bottom:20px',
                            defaults: {
                                labelWidth: 130
                            },
                            items: [

                                {
                                    xtype: 'textfield',
                                    anchor: '100%',
                                    readOnly: true,
                                    height: 68,
                                    id: 'txt-current-balance-withdraw',
                                    margin: '5px 0px',
                                    fieldStyle: 'text-align:right;font-size:20px;font-weight:bold;font-family:Courier New;',
                                    value: '0',
                                    submitValue: false,
                                    labelAlign: 'top',
                                    fieldLabel: '<b>BALANCE SAAT INI</b>'
                                }, {
                                    xtype: 'numericfield',
                                    anchor: '100%',
                                    labelWidth: 250,
                                    hideTrigger: true,
                                    name: 'amount',
                                    height: 80,
                                    id: 'txt-withdrawal-amount',
                                    fieldStyle: 'text-align:right;font-size:20px;font-weight:bold;font-family:Courier New;',
                                    fieldLabel: '<b>JUMLAH PENARIKAN</b>',
                                    allowBlank: false,
                                    labelAlign: 'top',
                                    listeners: {
                                        change: function(c, v) {
                                            var adm = Ext.getCmp('txt-withdrawal-adm-fee').getValue();
                                            var total = adm + v;
                                            Ext.getCmp('txt-total-withdrawal').setValue(total);
                                        }
                                    },
                                    validator: function (val) {
                                        // remove non-numeric characters
                                        var currn = Ext.getCmp('txt-current-balance-withdraw').getValue(),
                                            errMsg = "Jumlah melebihi saldo, harap periksa kembali";
                                        // if the numeric value is not 10 digits return an error message
                                        var res = parseFloat(currn.replace(/,/g, ""));
                                        return (val <= res) ? true : errMsg;
                                    }
                                }, {
                                    xtype: 'numericfield',
                                    width: 450,
                                    hideTrigger: true,
                                    fieldStyle: 'text-align:right',
                                    labelWidth: 250,
                                    name: 'adm-fee',
                                    hidden: true,
                                    id: 'txt-withdrawal-adm-fee',
                                    fieldLabel: 'Biaya Admin <b style="color:red">*</b>',
                                    allowBlank: true,
                                    listeners: {
                                        change: function(c, v) {
                                            var adm = Ext.getCmp('txt-withdrawal-amount').getValue();
                                            var total = adm + v;
                                            Ext.getCmp('txt-total-withdrawal').setValue(total);
                                        }
                                    }
                                }, {
                                    xtype: 'numericfield',
                                    readOnly: true,
                                    width: 450,
                                    hideTrigger: true,
                                    labelWidth: 250,
                                    submitValue: false,
                                    hidden: true,
                                    id: 'txt-total-withdrawal',
                                    fieldStyle: 'font-weight:bold;text-align:right',
                                    fieldLabel: lang('Total Penarikan')
                                }, {
                                    xtype: 'textarea',
                                    width: 450,
                                    name: 'remark',
                                    height: 65,
                                    hidden: true,
                                    margin: '20px 0px 5px 0px',
                                    fieldLabel: lang('Catatan')
                                }
                            ]
                        }, {
                            xtype: 'container',
                            margin: 5,
                            title: 'Detil Penarikan',
                            defaults: {
                                labelWidth: 90
                            },
                            layout: {
                                type: 'fit'
                            },
                            items: [
                                Ext.create('Ext.grid.Panel', {
                                    id: 'grid-add-withdrawal-saving-trans',
                                    style: 'border-top: 1px solid #999;border-bottom: 1px solid #999;',
                                    store: storeWithdrawalTrans,
                                    columns: [{
                                            text: 'Date',
                                            dataIndex: 'date',
                                            width: 150
                                        },
                                        // { text: 'Transaction', dataIndex: 'email', flex: 1, renderer: function(v) {
                                        //     if(v === "Deposit") {
                                        //         return '<span style="color:blue">'+ v +'</span>';
                                        //     } else {
                                        //         return '<span style="color:red">'+ v +'</span>';
                                        //     }
                                        // } },
                                        {
                                            text: 'Amount',
                                            align: 'right',
                                            dataIndex: 'amount',
                                            width: 200,
                                            flex: 1,
                                            renderer: function(v) {
                                                return '<span style="font-family:courier new">' + Ext.util.Format.number(v) + '</span>';
                                            }
                                        }
                                    ],
                                    height: 172
                                })
                            ]
                        }]
                    }]
                }),
                buttons: [{
                    id: 'newwd',
                    text: 'Transaksi Baru',
                    ui: 's-button',
                    scale: 'large',
                    cls: 's-blue',
                    margin: '5px',
                    handler: function() {
                        let form = Ext.getCmp('frm-add-withdrawal');
                        form.getForm().reset();
                        form.enable();
                        this.hide();
                        Ext.getCmp('saveButtonWithdrawal').show();
                        // setNoTransaction();
                    }
                },{
                    id: 'saveButtonWithdrawal',
                    text: 'Simpan Penarikan',
                    margin: '5px',
                    scale: 'large',
                    hidden:true,
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function() {
                        
                        let form = Ext.getCmp('frm-add-withdrawal');
                        var isvalid = form.getForm().isValid();
                        if(!isvalid) {
                            return false;
                        }
                        var id = Ext.id();
                        var useridwd = Ext.id();
                        var passwordwd = Ext.id();

                        var loginform = Ext.create('Ext.form.Panel', {
                            bodyPadding: 5,
                            width: 350,
                            layout: 'anchor',
                            defaults: {
                                anchor: '100%'
                            },
                            defaultType: 'textfield',
                            items: [{
                                fieldLabel: 'Username',
                                name: 'userid',
                                id: useridwd,
                                allowBlank: false
                            }, {
                                fieldLabel: 'Password',
                                name: 'password',
                                id: passwordwd,
                                allowBlank: false,
                                inputType: 'password'
                            }],
                            buttons: [{
                                text: 'Login',
                                formBind: true, //only enabled once the form is valid
                                disabled: true,
                                handler: function() {
                                    var msg = Ext.MessageBox.wait('Processing...');
                                    Ext.Ajax.request({
                                        url: m_approval,
                                        method: 'POST',
                                        params: {
                                            username: Ext.getCmp(useridwd).getValue(),
                                            password: Ext.getCmp(passwordwd).getValue()
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.JSON.decode(response.responseText);
                                            if (obj.approved) {
                                                Ext.getCmp('ApprovalIdWith').setValue(obj.UserId);

                                                submitWithdraw(obj.UserId);
                                                Ext.getCmp(id).hide();
                                            } else {
                                                Ext.Msg.alert('Failure', 'Approval Failed');
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var text = response.responseText;
                                            Ext.Msg.alert('Failure', 'Approval Failed');
                                        }
                                    });
                                }
                            }]
                        });




                        var windowApproval = Ext.create('Ext.Window', {
                            id: id,
                            width: 400,
                            height: 150,
                            title: 'Approval',
                            plain: true,
                            closable: true,
                            // headerPosition: 'left',
                            layout: 'fit',
                            items: [loginform]
                        });

                        var loadingtxt = "Loading...";
                        let dataValues = form.getForm().getValues();
                        Ext.Ajax.request({
                            url: m_cooplimit,
                            method: 'GET',
                            params: {
                                amount: Ext.getCmp('txt-withdrawal-amount').getValue()
                            },
                            success: function(form, action) {
                                var d = Ext.decode(form.responseText);
                                if (!d.status) {
                                    windowApproval.show();
                                } else {
                                    submitWithdraw(dataValues);
                                }
                            },
                            failure: function(form, action) {
                              console.log('gagal');
                            }
                        });
                        form.disable();
                        this.hide()
                        Ext.getCmp('newwd').show()

                        Ext.getCmp('ApprovalIdWith').setValue(null);
                    }
                }, {
                    text: 'Reset',
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-grey',
                    disabled: false,
                    handler: function() {
                        var form = Ext.getCmp('frm-add-withdrawal').getForm();
                        form.reset();
                    }
                }]
            }]
        }]
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        height: '100%',
        id: 'grid-saving',
        minHeight: 300,
        style: 'border:1px solid #CCC;',
        loadMask: true,
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        }, {
            xtype: 'toolbar',
            items: [{
                iconCls: 'add',
                text: 'Deposit',
                scope: this,
                handler: function() {

                    var rowEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                        clicksToMoveEditor: 1,
                        listeners: {
                            edit: function(editor, context) {

                                var gridstore = context.grid.getStore();

                                if (context.colIdx === 1) {
                                    var comb = context.column.field;
                                    if (comb.getValue() > 0) {
                                        var combrecord = comb.findRecord('typeID', comb.getValue());

                                        var record = context.record;
                                        record.set({
                                            desc: combrecord.data.typeName,
                                            type: comb.getValue()
                                        });
                                    }
                                }

                                var total = parseFloat(0);
                                gridstore.each(function(one, idx, all) {
                                    if (!isNaN(parseFloat(one.get('amount')))) {
                                        total += parseFloat(one.get('amount'));
                                    }
                                });
                                Ext.getCmp('total-trans-add-deposit').setValue(Ext.util.Format.number(total, '0,000.00'));
                                Ext.getCmp('hidden-total-trans-add-deposit').setValue(total);
                            }
                        }
                    });

                    var windeposit = Ext.create('widget.window', {
                        id: 'win-deposit',
                        modal: true,
                        width: '80%',
                        height: '72%',
                        autoScroll: true,
                        title: 'New Deposit',
                        style: 'border:5px solid #444',
                        bodyStyle: 'background:#444;',
                        header: {
                            style: 'background:#444;border-color:#444'
                        },
                        layout: {
                            type: 'fit'
                        },
                        closable: false,
                        items: Ext.create('Ext.form.Panel', {
                            bodyPadding: 5,
                            id: 'frm-add-deposit',
                            fieldDefaults: {
                                labelAlign: 'left',
                                labelWidth: 120
                            },
                            layout: {
                                type: 'column'
                            },
                            items: [{
                                xtype: 'container',
                                columnWidth: .4,
                                layout: {
                                    type: 'fit'
                                },
                                items: [{
                                    xtype: 'fieldset',
                                    title: 'Depositor Info',
                                    style: 'padding-bottom:2px',
                                    items: [{
                                        xtype: 'checkbox',
                                        fieldStyle: 'margin-left:125px',
                                        boxLabel: 'Member',
                                        submitValue: false,
                                        listeners: {
                                            change: function(c, v) {
                                                if (v === true) {
                                                    Ext.getCmp('memberid_container').enable();

                                                } else {
                                                    Ext.getCmp('memberID').reset();
                                                    Ext.getCmp('primaryNo').reset();
                                                    Ext.getCmp('frm-add-deposit').getForm().reset();
                                                    Ext.getCmp('memberid_container').disable();

                                                }
                                            }
                                        }
                                    }, {
                                        xtype: 'fieldcontainer',
                                        fieldLabel: lang('Member No.'),
                                        id: 'memberid_container',
                                        hidden: false,
                                        layout: 'hbox',
                                        align: 'stretch',
                                        bodyStyle: 'padding: 10px',
                                        disabled: true,
                                        items: [{
                                            xtype: 'textfield',
                                            id: 'primaryNo',
                                            name: 'primaryNo',
                                            submitValue: false,
                                            readOnly: true
                                        }, {
                                            xtype: 'hidden',
                                            id: 'memberID',
                                            name: 'memberID',
                                            readOnly: true
                                        }, {
                                            iconCls: 'search',
                                            cls: 's-grey',
                                            xtype: 'button',
                                            id: 'isFarmer',
                                            style: 'margin-left:5px',
                                            handler: function() {

                                                var winmember = Ext.create('Ext.Window', {
                                                    title: 'Member List',
                                                    width: 750,
                                                    height: 400,
                                                    modal: true,
                                                    layout: 'fit',
                                                    items: [{
                                                        xtype: 'grid',
                                                        store: Ext.create('Ext.data.Store', {
                                                            extend: 'Ext.data.Model',
                                                            storeId: 'store-grid-member',
                                                            fields: ['id', 'farmerID', 'primaryNo', 'name', 'Village', 'identityNumber', 'address', 'status', 'GroupName'],
                                                            autoLoad: true,
                                                            pageSize: 10,
                                                            proxy: {
                                                                type: 'ajax',
                                                                // url: 'api/common/all_members',
                                                                url: m_all_member,
                                                                reader: {
                                                                    type: 'json',
                                                                    root: 'data'
                                                                }
                                                            }
                                                        }),
                                                        dockedItems: [{
                                                            xtype: 'toolbar',
                                                            items: [{
                                                                xtype: 'textfield',
                                                                fieldLabel: 'Member Name',
                                                                hidden: true,
                                                                name: 'memberName',
                                                                id: 'memberName'
                                                            }, {
                                                                xtype: 'button',
                                                                margin: '0px 0px 0px 6px',
                                                                text: 'Search',
                                                                handler: function() {
                                                                    store.load({
                                                                        params: {
                                                                            key: Ext.getCmp('memberName').getValue()
                                                                        }
                                                                    });
                                                                }
                                                            }]
                                                        }, {
                                                            xtype: 'pagingtoolbar',
                                                            store: Ext.data.StoreManager.lookup('store-grid-member'),
                                                            dock: 'bottom',
                                                            displayInfo: true
                                                        }],
                                                        columns: [{
                                                            text: 'ID',
                                                            dataIndex: 'id',
                                                            hidden: true
                                                        }, {
                                                            text: 'No',
                                                            xtype: 'rownumberer',
                                                            width: 40
                                                        }, {
                                                            text: 'Member Number',
                                                            width: 150,
                                                            dataIndex: 'primaryNo'
                                                        }, {
                                                            text: 'Name',
                                                            flex: true,
                                                            dataIndex: 'name'
                                                        }, {
                                                            text: 'CPG',
                                                            flex: true,
                                                            dataIndex: 'GroupName'
                                                        }, {
                                                            text: 'Village',
                                                            width: 200,
                                                            dataIndex: 'Village'
                                                        }, {
                                                            text: 'Status',
                                                            width: 75,
                                                            dataIndex: 'status',
                                                            renderer: function(value) {
                                                                if (value == '1') {
                                                                    return lang('Active');
                                                                } else if (value == '2') {
                                                                    return lang('Inactive');
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'actioncolumn',
                                                            width: 40,
                                                            items: [{
                                                                icon: varjs.config.base_url + 'images/icons/silk/check_error.png', // Use a URL in the icon config
                                                                tooltip: 'Select this member 4',
                                                                handler: function(grid, rowIndex, colIndex) {
                                                                    var rec = grid.getStore().getAt(rowIndex);

                                                                    var num = rec.get('primaryNo');
                                                                    var memid = rec.get('id');
                                                                    var name = rec.get('name');
                                                                    var identity = rec.get('identityNumber');
                                                                    var address = rec.get('address');

                                                                    Ext.getCmp('primaryNo').setValue(num);
                                                                    Ext.getCmp('memberID').setValue(memid);
                                                                    Ext.getCmp('member-name-add-trans').setValue(name);
                                                                    Ext.getCmp('identityNumber').setValue(identity);
                                                                    Ext.getCmp('address').setValue(address);
                                                                }
                                                            }]
                                                        }]
                                                    }]
                                                }).show();
                                            }
                                        }]
                                    }, {
                                        xtype: 'textfield',
                                        anchor: '100%',
                                        id: 'member-name-add-trans',
                                        fieldLabel: 'name<b style="color:red">*</b>',
                                        allowBlank: false,
                                        name: 'name'
                                    }, {
                                        xtype: 'textfield',
                                        width: 400,
                                        fieldLabel: 'Identity No. <b style="color:red">*</b>',
                                        id: 'identityNumber',
                                        name: 'identityNumber',
                                        // allowBlank: false
                                    }, {
                                        xtype: 'textarea',
                                        fieldLabel: 'Address <b style="color:red">*</b>',
                                        width: 550,
                                        height: 50,
                                        id: 'address',
                                        name: 'address',
                                        allowBlank: false
                                    }]
                                }, {
                                    xtype: 'fieldset',
                                    title: 'Receiver Info',
                                    style: 'padding-bottom:2px',
                                    items: [{
                                        xtype: 'fieldcontainer',
                                        fieldLabel: lang('Member No.'),
                                        id: 'receiverid_container',
                                        hidden: false,
                                        layout: 'hbox',
                                        align: 'stretch',
                                        bodyStyle: 'padding: 10px',
                                        disabled: true,
                                        items: [{
                                            xtype: 'textfield',
                                            id: 'recPrimaryNo',
                                            name: 'recPrimaryNo',
                                            submitValue: false,
                                            readOnly: true
                                        }, {
                                            xtype: 'hidden',
                                            id: 'receiverID',
                                            name: 'receiverID'
                                        }, {
                                            iconCls: 'search',
                                            cls: 's-grey',
                                            xtype: 'button',
                                            id: 'isReceiver',
                                            style: 'margin-left:5px',
                                            handler: function() {

                                                var winreceipt = Ext.create('Ext.Window', {
                                                    title: 'Member List',
                                                    width: 750,
                                                    height: 400,
                                                    modal: true,
                                                    layout: 'fit',
                                                    items: [{
                                                        xtype: 'grid',
                                                        store: Ext.create('Ext.data.Store', {
                                                            extend: 'Ext.data.Model',
                                                            storeId: 'store-grid-member',
                                                            fields: ['id', 'farmerID', 'primaryNo', 'name', 'Village', 'identityNumber', 'address', 'status', 'GroupName'],
                                                            autoLoad: true,
                                                            pageSize: 10,
                                                            proxy: {
                                                                type: 'ajax',
                                                                // url: 'api/common/all_members',
                                                                url: m_all_member,
                                                                reader: {
                                                                    type: 'json',
                                                                    root: 'data'
                                                                }
                                                            }
                                                        }),
                                                        dockedItems: [{
                                                            xtype: 'pagingtoolbar',
                                                            store: Ext.data.StoreManager.lookup('store-grid-member'),
                                                            dock: 'bottom',
                                                            displayInfo: true
                                                        }],
                                                        columns: [{
                                                            text: 'ID',
                                                            dataIndex: 'id',
                                                            hidden: true
                                                        }, {
                                                            text: 'No',
                                                            xtype: 'rownumberer',
                                                            width: 40
                                                        }, {
                                                            text: 'Member Number',
                                                            width: 150,
                                                            dataIndex: 'primaryNo'
                                                        }, {
                                                            text: 'Name',
                                                            flex: true,
                                                            dataIndex: 'name'
                                                        }, {
                                                            text: 'CPG',
                                                            flex: true,
                                                            dataIndex: 'GroupName'
                                                        }, {
                                                            text: 'Village',
                                                            width: 200,
                                                            dataIndex: 'Village'
                                                        }, {
                                                            text: 'Status',
                                                            width: 75,
                                                            dataIndex: 'status',
                                                            renderer: function(value) {
                                                                if (value == '1') {
                                                                    return lang('Active');
                                                                } else if (value == '2') {
                                                                    return lang('Inactive');
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'actioncolumn',
                                                            width: 40,
                                                            items: [{
                                                                icon: varjs.config.base_url + 'images/icons/silk/check_error.png', // Use a URL in the icon config
                                                                tooltip: 'Select this member 5',
                                                                handler: function(grid, rowIndex, colIndex) {
                                                                    var rec = grid.getStore().getAt(rowIndex);
                                                                    var num = rec.get('primaryNo');
                                                                    var memid = rec.get('id');
                                                                    var name = rec.get('name');
                                                                    var identity = rec.get('address');
                                                                    var address = rec.get('identityNumber');

                                                                    Ext.getCmp('recPrimaryNo').setValue(num);
                                                                    Ext.getCmp('receiverID').setValue(memid);

                                                                }
                                                            }]
                                                        }]
                                                    }]
                                                }).show();
                                            }
                                        }]
                                    }, {
                                        xtype: 'textarea',
                                        width: 450,
                                        height: 40,
                                        name: 'remark',
                                        margin: '20px 0px 5px 0px',
                                        fieldLabel: lang('Remark')
                                    }]
                                }]
                            }, {
                                xtype: 'container',
                                columnWidth: .6,
                                layout: {
                                    type: 'fit'
                                },
                                items: [{
                                    xtype: 'grid',
                                    plugins: [rowEditing],
                                    store: Ext.create('Ext.data.Store', {
                                        extend: 'Ext.data.Model',
                                        storeId: 'store-temp-grid-trans',
                                        fields: ['id', 'type', 'desc', 'amount'],
                                        autoLoad: true,
                                        pageSize: 10,
                                        proxy: {
                                            type: 'ajax',
                                            url: m_api + '/transaction/get_trans_detail',
                                            reader: {
                                                type: 'json',
                                                root: 'data'
                                            }
                                        }
                                    }),
                                    height: 355,
                                    margin: '10 0 0 5',
                                    dockedItems: [{
                                        xtype: 'toolbar',
                                        dock: 'bottom',
                                        items: [
                                            '<span style="font-weight:bold">Total </span>',
                                            '->', {
                                                xtype: 'textfield',
                                                readOnly: true,
                                                width: 200,
                                                submitValue: false,
                                                id: 'total-trans-add-deposit',
                                                value: 0,
                                                fieldStyle: 'text-align:right',
                                                style: 'margin-right:40px;text-align:right'
                                            }, {
                                                xtype: 'hidden',
                                                name: 'totalTransaction',
                                                id: 'hidden-total-trans-add-deposit'
                                            }
                                        ]
                                    }, {
                                        xtype: 'toolbar',
                                        dock: 'top',
                                        items: [{
                                            xtype: 'button',
                                            text: 'Add Transaction',
                                            iconCls: 'add',
                                            handler: function() {
                                                var grid = this.up('grid');
                                                grid.getStore().add({});
                                            }
                                        }]
                                    }],
                                    columns: [{
                                        text: 'No',
                                        xtype: 'rownumberer',
                                        width: 40
                                    }, {
                                        text: 'Description',
                                        flex: true,
                                        dataIndex: 'desc',
                                        editor: new Ext.form.field.ComboBox({
                                            triggerAction: 'all',
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['typeID', 'typeName'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/transaction/combo_transtype', // url that will load data with respect to start and limit params
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            displayField: 'typeName',
                                            valueField: 'typeID'
                                        })
                                    }, {
                                        text: 'Amount',
                                        width: 150,
                                        align: 'right',
                                        dataIndex: 'amount',
                                        editor: {
                                            allowBlank: false
                                        }
                                    }, {
                                        xtype: 'actioncolumn',
                                        width: 40,
                                        items: [{
                                            icon: varjs.config.base_url + 'images/icons/new/delete.png', // Use a URL in the icon config
                                            tooltip: 'remove this transaction',
                                            handler: function(grid, rowIndex, colIndex) {
                                                var rec = grid.getStore().getAt(rowIndex);
                                                grid.getStore().remove(rec);
                                            }
                                        }]
                                    }]
                                }, {
                                    xtype: 'textfield',
                                    readOnly: true,
                                    height: 70,
                                    hidden: true,
                                    margin: '40px 0px 20px 20px',
                                    labelStyle: 'font-size:16px',
                                    fieldStyle: 'text-align:right;font-size:25px;font-weight:bold;font-family:Courier New; padding: 10px;',
                                    value: '100.000.000',
                                    submitValue: false,
                                    labelAlign: 'top',
                                    fieldLabel: '<b>BALANCE SAAT INI</b>'
                                }]
                            }]
                        }),
                        buttons: [{
                            id: 'saveButton',
                            text: 'Save',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-blue',
                            handler: function() {
                                var form = Ext.getCmp('frm-add-deposit').getForm();
                                var details = [];
                                var detailstore = Ext.data.StoreManager.lookup('store-temp-grid-trans');
                                detailstore.each(function(one, idx, all) {

                                    if (parseInt(one.get('type')) > 0 && parseFloat(one.get('amount')) > 0) {
                                        details.push({
                                            id: null,
                                            type: one.get('type'),
                                            amount: one.get('amount')
                                        });
                                    } else {
                                        Ext.MessageBox.alert('Failed', 'Cannot Submit, one of transaction is empty');
                                    }
                                });

                                form.submit({
                                    url: m_api + 'transaction/add_deposit',
                                    // url : m_save_withdrawal,
                                    method: 'POST',
                                    params: {
                                        details: Ext.JSON.encode(details)
                                    },
                                    waitMsg: 'Sending data...',
                                    success: function(fp, o) {
                                        Ext.MessageBox.alert('Success', 'Data saved.');
                                        windeposit.close(this, function() {
                                            store.load();
                                        });
                                    }
                                });

                            }
                        }, {
                            text: 'Close',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-grey',
                            disabled: false,
                            handler: function() {
                                windeposit.close();
                            }
                        }]
                    }).show();
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: 'Withdrawal',
                scope: this,
                handler: function() {

                    var winwithdraw = Ext.create('widget.window', {
                        id: 'win-deposit',
                        modal: true,
                        width: '70%',
                        height: '75%',
                        autoScroll: true,
                        style: 'border:5px solid #A30F0D',
                        bodyStyle: 'background:#A30F0D;',
                        title: 'New Withdrawal',
                        header: {
                            style: 'background:#A30F0D;border-color:#A30F0D'
                        },
                        layout: {
                            type: 'fit'
                        },
                        items: Ext.create('Ext.form.Panel', {
                            bodyPadding: 5,
                            id: 'frm-add-withdrawal',
                            fieldDefaults: {
                                labelAlign: 'left',
                                labelWidth: 120
                            },
                            layout: {
                                type: 'column'
                            },
                            items: [{
                                xtype: 'container',
                                columnWidth: .5,
                                layout: {
                                    type: 'fit'
                                },
                                items: [{
                                    xtype: 'fieldset',
                                    title: 'Account Info',
                                    style: 'padding-bottom:20px',
                                    items: [{
                                        xtype: 'fieldcontainer',
                                        fieldLabel: lang('Member No. <b style="color:red">*</b>'),
                                        id: 'receiverid_container',
                                        hidden: false,
                                        layout: 'hbox',
                                        align: 'stretch',
                                        bodyStyle: 'padding: 10px',
                                        items: [{
                                            xtype: 'textfield',
                                            id: 'recPrimaryNo',
                                            name: 'recPrimaryNo',
                                            submitValue: false,
                                            readOnly: true,
                                        }, {
                                            xtype: 'hidden',
                                            id: 'receiverID',
                                            name: 'receiverID',
                                            readOnly: true
                                        }, {
                                            iconCls: 'search',
                                            cls: 's-grey',
                                            xtype: 'button',
                                            id: 'isReceiver',
                                            style: 'margin-left:5px',
                                            handler: function() {
                                                var winmember = Ext.create('Ext.Window', {
                                                    title: 'Member List',
                                                    width: 750,
                                                    height: 400,
                                                    modal: true,
                                                    layout: 'fit',
                                                    items: [{
                                                        xtype: 'grid',
                                                        store: Ext.create('Ext.data.Store', {
                                                            extend: 'Ext.data.Model',
                                                            storeId: 'store-grid-member',
                                                            fields: ['id', 'farmerID', 'primaryNo', 'name', 'Village', 'identityNumber', 'address', 'status', 'GroupName'],
                                                            autoLoad: true,
                                                            pageSize: 10,
                                                            proxy: {
                                                                type: 'ajax',
                                                                // url: 'api/common/all_members',
                                                                url: m_all_member,
                                                                reader: {
                                                                    type: 'json',
                                                                    root: 'data'
                                                                }
                                                            }
                                                        }),
                                                        dockedItems: [{
                                                            xtype: 'pagingtoolbar',
                                                            store: Ext.data.StoreManager.lookup('store-grid-member'),
                                                            dock: 'bottom',
                                                            displayInfo: true
                                                        }],
                                                        columns: [{
                                                            text: 'ID',
                                                            dataIndex: 'id',
                                                            hidden: true
                                                        }, {
                                                            text: 'No',
                                                            xtype: 'rownumberer',
                                                            width: 40
                                                        }, {
                                                            text: 'Member Number',
                                                            width: 150,
                                                            dataIndex: 'primaryNo'
                                                        }, {
                                                            text: 'Name',
                                                            flex: true,
                                                            dataIndex: 'name'
                                                        }, {
                                                            text: 'CPG',
                                                            flex: true,
                                                            dataIndex: 'GroupName'
                                                        }, {
                                                            text: 'Village',
                                                            width: 200,
                                                            dataIndex: 'Village'
                                                        }, {
                                                            text: 'Status',
                                                            width: 75,
                                                            dataIndex: 'status',
                                                            renderer: function(value) {
                                                                if (value == '1') {
                                                                    return lang('Active');
                                                                } else if (value == '2') {
                                                                    return lang('Inactive');
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'actioncolumn',
                                                            width: 40,
                                                            items: [{
                                                                icon: varjs.config.base_url + 'images/icons/silk/check_error.png', // Use a URL in the icon config
                                                                tooltip: 'Select this member 6',
                                                                handler: function(grid, rowIndex, colIndex) {
                                                                    var rec = grid.getStore().getAt(rowIndex);

                                                                    var num = rec.get('primaryNo');
                                                                    var memid = rec.get('id');

                                                                    Ext.getCmp('recPrimaryNo').setValue(num);
                                                                    Ext.getCmp('receiverID').setValue(memid);

                                                                }
                                                            }]
                                                        }]
                                                    }]
                                                }).show();
                                            }
                                        }]
                                    }, {
                                        xtype: 'combo',
                                        id: 'cmb-add-deposit-saving-product',
                                        fieldLabel: 'Saving Product <b style="color:red">*</b>',
                                        allowBlank: false,
                                        width: 350,
                                        store: Ext.create('Ext.data.Store', {
                                            fields: ['id', 'label'],
                                            autoLoad: false,
                                            proxy: {
                                                type: 'rest',
                                                url: m_api + '/transaction/getmembersaving', // url that will load data with respect to start and limit params
                                                reader: {
                                                    type: 'json',
                                                    root: 'data',
                                                    totalProperty: 'total'
                                                }
                                            }
                                        }),
                                        displayField: 'label',
                                        valueField: 'id',
                                        name: 'id'
                                    }, {
                                        xtype: 'textfield',
                                        anchor: '80%',
                                        readOnly: true,
                                        submitValue: false,
                                        fieldLabel: 'Saving No. <b style="color:red">*</b>',
                                        name: 'name'
                                    }]
                                }, {
                                    xtype: 'fieldset',
                                    title: 'Withdraw Detail',
                                    style: 'padding-bottom:20px',
                                    defaults: {
                                        labelWidth: 130
                                    },
                                    items: [{
                                        xtype: 'textfield',
                                        width: 450,
                                        labelWidth: 250,
                                        fieldLabel: 'Deposit Amount <b style="color:red">*</b>',
                                        allowBlank: false
                                    }, {
                                        xtype: 'textfield',
                                        width: 450,
                                        labelWidth: 250,
                                        fieldLabel: 'Admin Fee <b style="color:red">*</b>',
                                        allowBlank: false
                                    }, {
                                        xtype: 'textfield',
                                        readOnly: true,
                                        width: 450,
                                        labelWidth: 250,
                                        fieldStyle: 'font-weight:bold',
                                        fieldLabel: lang('Total Deposit')
                                    }, {
                                        xtype: 'textarea',
                                        width: 450,
                                        height: 65,
                                        margin: '20px 0px 5px 0px',
                                        fieldLabel: lang('Remark')
                                    }]
                                }]
                            }, {
                                xtype: 'container',
                                columnWidth: .5,
                                layout: {
                                    type: 'fit'
                                },
                                items: [{
                                    xtype: 'panel',
                                    layout: {
                                        type: 'hbox',
                                        pack: 'center'
                                    },
                                    defaults: {
                                        margin: 5
                                    },
                                    items: [{
                                        xtype: 'container',
                                        width: 125,
                                        layout: {
                                            type: 'anchor'
                                        },
                                        items: [{
                                            xtype: 'displayfield',
                                            anchor: '100%',
                                            fieldStyle: 'text-align:center;font-weight:bold',
                                            value: 'PHOTO'
                                        }, {
                                            xtype: 'panel',
                                            anchor: '100%',
                                            height: 150,
                                            style: 'margin-bottom:5px;border:1px solid #999'
                                        }]
                                    }, {
                                        xtype: 'container',
                                        width: 125,
                                        layout: {
                                            type: 'anchor'
                                        },
                                        items: [{
                                            xtype: 'displayfield',
                                            anchor: '100%',
                                            fieldStyle: 'text-align:center;font-weight:bold;',
                                            value: 'SIGNATURE'
                                        }, {
                                            xtype: 'panel',
                                            anchor: '100%',
                                            height: 150,
                                            style: 'margin-bottom:5px;border:1px solid #999'
                                        }]
                                    }]
                                }, {
                                    xtype: 'container',
                                    margin: 5,
                                    title: 'Withdraw Detail',
                                    defaults: {
                                        labelWidth: 90
                                    },
                                    layout: {
                                        type: 'fit'
                                    },
                                    items: [
                                        Ext.create('Ext.grid.Panel', {
                                            style: 'border-top: 1px solid #999;border-bottom: 1px solid #999;',
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['name', 'email', 'phone'],
                                                data: {
                                                    'items': [{
                                                        'name': '21/02/2015',
                                                        "email": "Deposit",
                                                        "phone": "10.000.000"
                                                    }, {
                                                        'name': '25/02/2015',
                                                        "email": "Withdrawal",
                                                        "phone": "1.000.000"
                                                    }]
                                                },
                                                proxy: {
                                                    type: 'memory',
                                                    reader: {
                                                        type: 'json',
                                                        root: 'items'
                                                    }
                                                }
                                            }),
                                            columns: [{
                                                text: 'Tgl',
                                                dataIndex: 'name',
                                                width: 100
                                            }, {
                                                text: 'Transaksi',
                                                dataIndex: 'email',
                                                flex: 1,
                                                renderer: function(v) {
                                                    if (v === "Deposit") {
                                                        return '<span style="color:blue">' + v + '</span>';
                                                    } else {
                                                        return '<span style="color:red">' + v + '</span>';
                                                    }
                                                }
                                            }, {
                                                text: 'Jumlah',
                                                align: 'right',
                                                dataIndex: 'phone',
                                                width: 200,
                                                renderer: function(v) {
                                                    return '<span style="font-family:courier new">' + Ext.util.Format.number(v) + '</span>';
                                                }
                                            }],
                                            height: 105
                                        }), {
                                            xtype: 'textfield',
                                            anchor: '100%',
                                            readOnly: true,
                                            height: 68,
                                            margin: '5px 0px',
                                            fieldStyle: 'text-align:right;font-size:20px;font-weight:bold;font-family:Courier New;',
                                            value: '100.000.000',
                                            submitValue: false,
                                            labelAlign: 'top',
                                            fieldLabel: '<b>BALANCE SAAT INI</b>'
                                        }
                                    ]
                                }]
                            }]
                        }),
                        closable: false,
                        buttons: [{
                            id: 'saveButton',
                            text: 'Save',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-blue',
                            handler: function() {
                                var form = Ext.getCmp('dataForm').getForm();
                                form.submit({
                                    url: m_crud,
                                    method: 'POST',
                                    waitMsg: 'Sending data...',
                                    success: function(fp, o) {
                                        Ext.MessageBox.alert('Success', 'Data saved.');
                                        win.close(this, function() {
                                            store.load();
                                        });
                                    }
                                });

                            }
                        }, {
                            text: 'Close',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-grey',
                            disabled: false,
                            handler: function() {
                                winwithdraw.close();
                            }
                        }]
                    }).show();
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                scope: this,
                hidden: true,
                handler: function() {
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    if (!sm) {
                        Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                        return false;
                    } else {

                    }
                }
            }, {
                itemId: 'remove',
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                cls: m_act_delete,
                text: 'Cancel',
                scope: this,
                hidden: true,
                handler: function() {
                    var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    if (!smb) {
                        Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                        return false;
                    } else {
                        Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_crud,
                                    method: 'DELETE',
                                    params: {
                                        id: smb.raw.id
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store.load();
                                                break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                        }
                                    },
                                    failure: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        });
                    }
                }
            }, {
                xtype: 'textfield',
                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                id: 'key',
                listeners: {
                    specialkey: submitOnEnter
                }
            }, {
                id: 'filterStatus',
                name: 'filterStatus',
                xtype: 'combo',
                emptyText: '-- Find By Status --',
                multiSelect: false,
                store: mc_status,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    specialkey: submitOnEnter
                }
            }, {
                xtype: 'button',
                margin: '0px 0px 0px 6px',
                text: 'Search',
                handler: function() {
                    store.load({
                        params: {
                            key: Ext.getCmp('key').getValue(),
                            status: Ext.getCmp('filterStatus').getValue()
                        }
                    });
                }
            }]
        }],
        columns: [{
            text: 'ID',
            dataIndex: 'id',
            hidden: true
        }, {
            text: 'No',
            xtype: 'rownumberer',
            width: '5%'
        }, {
            text: lang('Transaction Number'),
            width: '15%',
            dataIndex: 'transactionNumber'
        }, {
            text: lang('Name'),
            width: '20%',
            dataIndex: 'transactionName'
        }, {
            text: lang('Trans Type'),
            width: 150,
            dataIndex: 'transactionType'
        }, {
            text: lang('Date [d/m/Y]'),
            width: 100,
            xtype: 'datecolumn',
            format: 'd/m/Y',
            dataIndex: 'transactionDate'
        }, {
            text: lang('Debet'),
            width: 200,
            align: 'right',
            xtype: 'numbercolumn',
            format: '0,000',
            dataIndex: 'debet'
        }, {
            text: lang('Kredit'),
            width: 200,
            style: 'font-family:courier new',
            align: 'right',
            xtype: 'numbercolumn',
            fieldStyle: 'text-align:right;',
            // format:'0,000.00',
            dataIndex: 'credit'
        }]
    });
});


function setNoTransaction() {
    Ext.Ajax.request({
        waitMsg: 'Please Wait',
        url: m_api + 'transaction/get_trans_number',
        method: 'GET',
        // params: {
        //     userid: Ext.getCmp('useridCashCount').getValue(),
        //     date: Ext.getCmp('dateCashCount').getSubmitValue()
        // },
        success: function(response, opts) {
            var r = Ext.decode(response.responseText);
            // console.log(r);
            Ext.getCmp('memberTransactionNumber').setValue(r.number);

        },
        failure: function(response, opts) {

        }
    });
}

function setNoTransaction2(id) {
    Ext.Ajax.request({
        waitMsg: 'Please Wait',
        url: m_api + 'transaction/get_trans_number',
        method: 'GET',
        params: {
            // userid: Ext.getCmp('useridCashCount').getValue(),
            id: id
        },
        success: function(response, opts) {
            var r = Ext.decode(response.responseText);
            // console.log(r);
            Ext.getCmp('memberTransactionNumber').setValue(r.number);

        },
        failure: function(response, opts) {

        }
    });
}

function setValueSaving() {
    Ext.Ajax.request({
        waitMsg: 'Please Wait',
        url: m_api + 'transaction/get_saving_member',
        method: 'GET',
        params: {
            // userid: Ext.getCmp('useridCashCount').getValue(),
            id: Ext.getCmp('receiverID').getValue()
        },
        success: function(response, opts) {
            var r = Ext.decode(response.responseText);
            // console.log(r);
            Ext.getCmp('uang-pangkal-add-deposit').setValue(r.pangkal);
            Ext.getCmp('simp-pokok-add-deposit').setValue(r.pokok);
            Ext.getCmp('simp-wajib-add-deposit').setValue(r.wajib);
            Ext.getCmp('simp-sukarela-add-deposit').setValue(r.sukarela);

            //cek unpaid
            Ext.Ajax.request({
                waitMsg: 'Please Wait',
                url: m_api + 'transaction/get_cek_paid',
                method: 'GET',
                params: {
                    // userid: Ext.getCmp('useridCashCount').getValue(),
                    id: Ext.getCmp('receiverID').getValue()
                },
                success: function(response, opts) {
                    var r = Ext.decode(response.responseText);
                    // console.log(r);
                    Ext.getCmp('uang-pangkal-add-deposit').setValue(r.pangkal);
                    Ext.getCmp('simp-pokok-add-deposit').setValue(r.pokok);
                    Ext.getCmp('simp-wajib-add-deposit').setValue(r.wajib);
                    Ext.getCmp('simp-sukarela-add-deposit').setValue(r.sukarela);

                },
                failure: function(response, opts) {

                }
            });

        },
        failure: function(response, opts) {

        }
    });
}

function calcGrandTotal() {
    var pangkal = Ext.getCmp('uang-pangkal-add-deposit').getValue() * 1;
    var pokok = Ext.getCmp('simp-pokok-add-deposit').getValue() * 1;
    var wajib = Ext.getCmp('simp-wajib-add-deposit').getValue() * 1;
    var sukarela = Ext.getCmp('simp-sukarela-add-deposit').getValue() * 1;
    if(Ext.getCmp('initSaving').getValue() !== false){
      Ext.getCmp('txt-deposit-amount').setValue((pangkal + pokok + wajib + sukarela));
    } else {
      Ext.getCmp('txt-deposit-amount').setValue('');
    }
}

function getLastTrans() {
    Ext.Ajax.request({
        waitMsg: 'Please wait...',
        url: m_lasttrans,
        method: 'GET',
        params: {
            // id:r[0].data.id
            id: Ext.getCmp('cmb-add-deposit-saving-product').getValue()
        },
        success: function(response, opts) {
            var obj = Ext.decode(response.responseText);
            if (obj.dataOutstanding != false) {
                if (obj.dataOutstanding.dueMonth * 1 !== 0) {
                    Ext.getCmp('ContainerOutstanding').show();
                    Ext.getCmp('Outstanding-add-source-fund').setValue(obj.dataOutstanding.dueAmount);
                    if (Ext.getCmp('cmb-add-deposit-saving-product').getValue() * 1 != 99) {
                        Ext.getCmp('OutstandingMonth').setValue(obj.dataOutstanding.dueMonth + ' Months');
                    }
                }
            } else {
                Ext.getCmp('ContainerOutstanding').hide();
                Ext.getCmp('Outstanding-add-source-fund').setValue(null);
                Ext.getCmp('OutstandingMonth').setValue(null);
            }

            Ext.getCmp('current-balance-add-deposit').setValue(obj.currentBalance);
        },
        failure: function(response, opts) {
            var obj = Ext.decode(response.responseText);

        }
    });
}

function submitDeposit(UserId) {

    var form = Ext.getCmp('frm-add-deposit').getForm();
    if (form.isValid()) {
        form.submit({
            url: m_api + 'transaction/add_deposit',
            method: 'POST',
            baseParams: {
                UserId: UserId
            },
            waitMsg: 'Sending data...',
            success: function(fp, o) {
                Ext.MessageBox.alert('Success', 'Data saved.');

                Ext.getCmp('newDeposit').show();
                Ext.getCmp('saveButtonDeposit').hide();
                var form = Ext.getCmp('frm-add-deposit');
                form.getForm().reset();
                form.disable();

                // this.hide();
                // Ext.getCmp('newDeposit').show();
                Ext.getCmp('txt-deposit-amount').setValue(null);

                // var proxy = Ext.getCmp('grid-add-deposit-saving-trans').store.getProxy();
                // proxy.extraParams = {id:Ext.getCmp('cmb-add-deposit-saving-product').getValue()};
                // Ext.getCmp('grid-add-deposit-saving-trans').store.load();
                Ext.getCmp('grid-add-deposit-saving-trans').store.removeAll();

                // getLastTrans();
            },
            failure : function(err){
              Ext.MessageBox.alert('Error', 'Error on saving data.');
            }
        });
    } else {
        var fieldNames = [];
        var fields = Ext.getCmp('frm-add-deposit').getInvalidFields();
        for (var i = 0; i < fields.length; i++) {
            var field = fields[i];
            fieldNames.push(field.getName());
        }

        Ext.MessageBox.alert('Invalid Fields', 'The following fields are invalid: ' + fieldNames.join(', '));
    }
}

function submitWithdraw(DataForm) {
    var forms = Ext.getCmp('frm-add-withdrawal').getForm();
    // let data = UserId;

    // if(typeof data === 'object'){
    //     data = UserId;
    // }
    if (forms.isValid()) {
        Ext.Ajax.request({
                waitMsg: 'Please Wait',
                url: m_api + 'transaction/add_withdrawal',
                method: 'POST',
                params: DataForm,
                success: function(fp, o) {
                    Ext.MessageBox.alert('Success', 'Data saved.');
                    forms.reset();
                    Ext.getCmp('img-photo-add-withdraw').setSrc(null);
                    Ext.getCmp('img-sigi-add-withdraw').setSrc(null);
                    Ext.getCmp('grid-add-withdrawal-saving-trans').store.removeAll();
                    console.log('here 1');

                },
                failure: function(response, opts) {

                }
            });
        // forms.submit({
        //     url: m_api + 'transaction/add_withdrawal',
        //     // url: m_save_withdrawal,
        //     method: 'POST',
        //     baseParams: DataForm,
        //     waitMsg: 'Sending data...',
        //     success: function(fp, o) {
        //         
        //     }
        // });
    } else {
      console.log('here 2');
    }
}


function submitFormLogin(page) {
    var msg = Ext.MessageBox.wait('Processing...');
    Ext.Ajax.request({
        url: m_approval,
        method: 'POST',
        params: {
            username: Ext.getCmp("userid").getValue(),
            password: Ext.getCmp("password").getValue()
        },
        success: function(response, opts) {
            var obj = Ext.JSON.decode(response.responseText);
            if (obj.approved) {
                if (page == 'deposit') {
                    submitDeposit();
                    Ext.getCmp(id).hide();
                } else {
                    Ext.getCmp(id).hide();
                }
            } else {
                Ext.Msg.alert('Failure', 'Approval Failed');
            }
        },
        failure: function(response, opts) {
            var text = response.responseText;
            Ext.Msg.alert('Failure', 'Approval Failed');
        }
    });

}

function getOutstandingNewMember() {
    Ext.getCmp('grid-add-deposit-outstanding-trans').show();

    var grid = Ext.getCmp('grid-add-deposit-outstanding-trans');

    var proxy = grid.store.getProxy();
    proxy.extraParams = {
        id: Ext.getCmp('recPrimaryNo').getValue()
    };
    grid.store.load();

    // console.log(grid.store.getCount());

    //  grid.on('load', function(ds){
    //         alert(ds.getTotalCount());
    // });

    // Ext.Ajax.request({
    //         waitMsg: 'Please wait...',
    //         url: m_api+'cooperatives/unpaid_member_fee',
    //         method: 'GET',
    //         params: {
    //            // id:r[0].data.id
    //            id:Ext.getCmp('recPrimaryNo').getValue()
    //         },
    //         success: function (response, opts) {
    //             var obj = Ext.decode(response.responseText);
    //             Ext.getCmp('grid-add-deposit-outstanding-trans').show();
    //         },
    //         failure: function (response, opts) {
    //             var obj = Ext.decode(response.responseText);
    //         }
    //     });
}
