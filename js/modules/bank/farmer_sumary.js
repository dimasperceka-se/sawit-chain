if(Ext.getCmp('winFormDownload')) Ext.getCmp('winFormDownload').destroy();
if(Ext.getCmp('winFormApproval')) Ext.getCmp('winFormApproval').destroy();
if(Ext.getCmp('winFormFinalization')) Ext.getCmp('winFormFinalization').destroy();
if(Ext.getCmp('winDetail')) Ext.getCmp('winDetail').destroy();

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    var store_farmer_list = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','FarmerID','FarmerName','Address','GroupName','Village','SubDistrict','District','Province','ApprovalStatus'],
        autoLoad: false,
        pageSize: 20,
        proxy: {
            type: 'ajax',
            url: m_list,
            params: {
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.province        = Ext.getCmp('province').getValue();
                store.proxy.extraParams.district        = Ext.getCmp('district').getValue();
                store.proxy.extraParams.subdistrict     = Ext.getCmp('subdistrict').getValue();
                store.proxy.extraParams.cpg             = Ext.getCmp('cpg').getValue();
                store.proxy.extraParams.status          = Ext.getCmp('status').getValue();
                store.proxy.extraParams.NeedLoan                = Ext.getCmp('NeedLoan').getValue();
                store.proxy.extraParams.Production              = Ext.getCmp('Production').getValue();
                // store.proxy.extraParams.LoanYesNo               = Ext.getCmp('LoanYesNo').getValue();
                // store.proxy.extraParams.Professionalism         = (Ext.getCmp('Professionalism').getValue()).join('|');
                store.proxy.extraParams.SignedLearningContract  = Ext.getCmp('SignedLearningContract').getValue();
            }
        }
    });


    var store_provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_provinsi,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_kabupaten,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_kecamatan,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_cpg = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_cpg,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_bank = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_bank,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_bank_branch = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_branch,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var approval_filter = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [
            {
                "id": "0",
                "name": lang("Unprocessed")
            }, 
            {
                "id": "1",
                "name": lang("Approved")
            }, 
            {
                "id": "3",
                "name": lang("Rejected")
            }, 
            // {
            //     "id": "2",
            //     "name": lang("Final")
            // },
        ]
    });

    var yes_no = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [{
            "id": "",
            "name": lang("Select")
        }, {
            "id": "yes",
            "name": lang("Yes")
        }, {
            "id": "no",
            "name": lang("No")
        },]
    });

    var professionalism = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [{
            "id": "",
            "name": lang("Select")
        }, {
            "id": "unprofessional",
            "name": lang("Unprofessional")
        }, {
            "id": "progressing",
            "name": lang("Progressing")
        }, {
            "id": "professional",
            "name": lang("Professional")
        },]
    });

    var approval_status = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [{
            "id": "1",
            "name": lang("Approved")
        }, {
            "id": "3",
            "name": lang("Rejected")
        },]
    });

    store_provinsi.load({
        callback: function() {            
            if (m_user_province) {
                Ext.getCmp('province').setValue(m_user_province).setDisabled(true);
            }
        }
    })

    var grid_farmer = Ext.create('Ext.grid.Panel', {
        store: store_farmer_list,
        width: '100%',
        minHeight: 250,
        id: 'grid_farmer',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        // selType: 'rowmodel',
        selType: 'checkboxmodel',
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store_farmer_list, 
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [
                    {
                        emptyText: '-- '+lang('Provinsi')+' --',
                        id: 'province',
                        name: 'province',
                        xtype: 'combo',
                        width: 150,
                        store: store_provinsi,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                store_kabupaten.load({
                                    params: {key: Ext.getCmp('province').getValue()},
                                    callback: function() {            
                                        if (m_user_district) {
                                            Ext.getCmp('district').setValue(m_user_district).setDisabled(true);
                                        }
                                    }
                                });
                            }
                        }
                    }, {
                        emptyText: '-- '+lang('Kabupaten')+' --',
                        id: 'district',
                        name: 'district',
                        xtype: 'combo',
                        width: 150,
                        store: store_kabupaten,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                store_kecamatan.load({params: {key: Ext.getCmp('district').getValue()}});
                                store_cpg.load({
                                    params: {dist: Ext.getCmp('district').getValue()},
                                    callback: function() {            
                                        if (m_user_subdistrict) {
                                            Ext.getCmp('subdistrict').setValue(m_user_subdistrict).setDisabled(true);
                                        }
                                    }
                                });
                                store_bank.load({params: {DistrictID: Ext.getCmp('district').getValue()}});
                            }
                        }
                    }, {
                        emptyText: '-- '+lang('Kecamatan')+' --',
                        id: 'subdistrict',
                        name: 'subdistrict',
                        xtype: 'combo',
                        width: 150,
                        store: store_kecamatan,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('cpg').setValue('');
                                store_cpg.load({params: {subd: Ext.getCmp('subdistrict').getValue()}});
                            }
                        }
                    }, {
                        emptyText: '-- '+lang('CPG')+' --',
                        id: 'cpg',
                        name: 'cpg',
                        xtype: 'combo',
                        width: 180,
                        store: store_cpg,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                    }, {
                        emptyText: '-- '+lang('Status')+' --',
                        id: 'status',
                        name: 'status',
                        xtype: 'combo',
                        width: 100,
                        store: approval_filter,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('buttonApproval').hide();
                                // Ext.getCmp('buttonFinalization').hide();
                                Ext.getCmp('buttonDetail').hide();

                                if (nv === '0') {
                                    Ext.getCmp('buttonApproval').show();
                                // } else if (nv === '1') {
                                //     Ext.getCmp('buttonFinalization').show();
                                } else if (nv === '1' || nv === '2' || nv === '3') {
                                    Ext.getCmp('buttonDetail').show();
                                }
                            }
                        }
                    }, {
                        xtype: 'button',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {                            
                            var prov    = Ext.getCmp('province').getValue();
                            var dist    = Ext.getCmp('district').getValue();
                            var subd    = Ext.getCmp('subdistrict').getValue();
                            var cpg     = Ext.getCmp('cpg').getValue();
                            if (!prov) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih Provinsi'));
                                return false;
                            }
                            if (!dist) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih Kabupaten'));
                                return false;
                            }
                            // if (!subd) {
                            //     Ext.MessageBox.alert('Warning', lang('Silahkan pilih Kecamatan'));
                            //     return false;
                            // }
                            // if (!cpg) {
                            //     Ext.MessageBox.alert('Warning', lang('Silahkan pilih CPG'));
                            //     return false;
                            // }
                            store_farmer_list.load();
                        }
                    }, {
                        xtype: 'button',
                        margin: '0px 0px 0px 6px',
                        text: lang('Preview'),
                        handler: function() {    
                            var selection = grid_farmer.getSelectionModel().getSelection();
                            
                            if (selection.length === 0) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih Petani'));
                                return false;
                            } 
                            var farmerids = [];
                            $.each(selection, function(index, val) {
                                farmerids.push(val.data.FarmerID);
                            });
                            data = selection[0].data;
                            preview_link(m_preview_url + '/FarmerID/' + farmerids.join().replace(/,/g, '::'));
                        }
                    }, {
                        xtype: 'button',
                        margin: '0px 0px 0px 6px',
                        text: lang('Download Form'),
                        handler: function() {    
                            var selection = grid_farmer.getSelectionModel().getSelection();
                            
                            if (selection.length === 0) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih Petani'));
                                return false;
                            } 
                            var farmerids = [];
                            $.each(selection, function(index, val) {
                                farmerids.push(val.data.FarmerID);
                            });
                            Ext.getCmp('FarmerIDs').setValue(farmerids.join(','));
                            displayFormDownload();
                        }
                    }, {
                        xtype: 'button',
                        id: 'buttonApproval',
                        margin: '0px 0px 0px 6px',
                        text: lang('Approval'),
                        handler: function() {    
                            var selection = grid_farmer.getSelectionModel().getSelection();
                            
                            if (selection.length === 0) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih Petani'));
                                return false;
                            } 
                            var farmerids = [];
                            $.each(selection, function(index, val) {
                                farmerids.push(val.data.FarmerID);
                            });
                            Ext.getCmp('FormApproval').getForm().reset();
                            Ext.getCmp('FarmerIDApproval').setValue(farmerids.join(','));
                            displayFormApproval();
                        }
                    }, {
                        xtype: 'button',
                        id: 'buttonFinalization',
                        hidden: true,
                        margin: '0px 0px 0px 6px',
                        text: lang('Finalization'),
                        handler: function() {    
                            var selection = grid_farmer.getSelectionModel().getSelection();

                            if (selection.length === 0 || selection.length > 1) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih satu Petani'));
                                return false;
                            } 
                            data = selection[0].data;
                            if (data.ApprovalStatus !== 'Approved') {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih Petani dengan status approved namun belum di finalisasi'));
                                return false;
                            }
                            Ext.getCmp('FarmerIDFinalization').setValue(data.FarmerID);
                            Ext.getCmp('FarmerNameFinalization').setValue(data.FarmerName);
                            // displayFormFinalization();
                        }
                    }, {
                        xtype: 'button',
                        id: 'buttonDetail',
                        hidden: true,
                        margin: '0px 0px 0px 6px',
                        text: lang('Detail'),
                        handler: function() {    
                            var selection = grid_farmer.getSelectionModel().getSelection();

                            if (selection.length === 0 || selection.length > 1) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih Petani'));
                                return false;
                            } 
                            data = selection[0].data;
                            displayWinDetail(data.FarmerID);
                        }
                    },
                ]
            }, {
                xtype: 'toolbar',
                items: [
                    {
                        fieldLabel: lang('Want a loan'),
                        labelWidth: 75,
                        emptyText: lang('Select'),
                        id: 'NeedLoan',
                        name: 'NeedLoan',
                        xtype: 'combo',
                        width: 150,
                        store: yes_no,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                    }, 
                    {
                        xtype: 'textfield',
                        labelWidth: 180,
                        width: 230,
                        fieldLabel: lang('Minimum Production (Kg/year)'),
                        id: 'Production',
                        name: 'Production',
                    },
                    // {
                    //     fieldLabel: lang('Production above 500 Kg/year'),
                    //     labelWidth: 160,
                    //     emptyText: lang('Select'),
                    //     id: 'Production',
                    //     name: 'Production',
                    //     xtype: 'combo',
                    //     width: 235,
                    //     store: yes_no,
                    //     displayField: 'name',
                    //     valueField: 'id',
                    //     queryMode: 'local',
                    // },  
                    // {
                    //     fieldLabel: lang('Have Loan Request History'),
                    //     labelWidth: 140,
                    //     emptyText: lang('Select'),
                    //     id: 'LoanYesNo',
                    //     name: 'LoanYesNo',
                    //     xtype: 'combo',
                    //     width: 220,
                    //     store: yes_no,
                    //     displayField: 'name',
                    //     valueField: 'id',
                    //     queryMode: 'local',
                    // }, 
                    // {
                    //     fieldLabel: lang('Professionalism'),
                    //     labelWidth: 85,
                    //     emptyText: lang('Select'),
                    //     id: 'Professionalism',
                    //     name: 'Professionalism[]',
                    //     xtype: 'combo',
                    //     multiSelect: true,
                    //     width: 200,
                    //     store: professionalism,
                    //     displayField: 'name',
                    //     valueField: 'id',
                    //     queryMode: 'local',
                    // },
                    {
                        fieldLabel: lang('Signed the Learning Contract'),
                        labelWidth: 170,
                        emptyText: lang('Select'),
                        id: 'SignedLearningContract',
                        name: 'SignedLearningContract',
                        xtype: 'combo',
                        width: 260,
                        store: yes_no,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                    }, 
                ]
            }],
        columns: [
            {
                text: lang('No'),
                xtype: 'rownumberer',
                width: 50,
                align: 'center'
            },
            {
                text: lang('FarmerID'),
                flex: 1,
                dataIndex: 'FarmerID'
            },
            {
                text: lang('Farmer Name'),
                flex: 2,
                dataIndex: 'FarmerName'
            },
            {
                text: lang('GroupName'),
                flex: 1,
                dataIndex: 'GroupName'
            },
            {
                text: lang('Address'),
                flex: 2,
                dataIndex: 'Address'
            },
            {
                text: lang('Village'),
                flex: 1,
                dataIndex: 'Village'
            },
            {
                text: lang('District'),
                flex: 1,
                dataIndex: 'District'
            },
            {
                text: lang('Province'),
                flex: 1,
                dataIndex: 'Province'
            },
            {
                text: lang('Status'),
                flex: 1,
                dataIndex: 'ApprovalStatus'
            },
        ]
    });
    
    function displayFormDownload() {
        if (!winFormDownload.isVisible()) {
            winFormDownload.show();
        } else {
            winFormDownload.hide();
            winFormDownload.toFront();
        }
    }

    var dataFormDownload = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 680,
        width: 400,
        bodyPadding: 5,
        id: 'dataFormDownload',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'hiddenfield',
                id: 'FarmerIDs',
                name: 'FarmerIDs',
            },
            {
                fieldLabel: lang('Kecamatan'),
                id: 'subdistrictbank',
                name: 'subdistrictbank',
                xtype: 'combo',
                width: 150,
                store: store_kecamatan,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function (cb, nv, ov) {
                        store_bank.load({params: {SubDistrictID: Ext.getCmp('subdistrictbank').getValue()}});
                    }
                }
            },
            {
                id: 'BankID',
                name: 'BankID',
                xtype: 'combobox',
                fieldLabel: lang('Bank'),
                store: store_bank,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        store_bank_branch.load({
                            params: {
                                bank: Ext.getCmp('BankID').getValue(),
                                SubDistrictID: Ext.getCmp('subdistrictbank').getValue()
                            }
                        });
                    }
                }
            },
            {
                id: 'BankBrancID',
                name: 'BankBrancID',
                xtype: 'combobox',
                fieldLabel: lang('Branch'),
                store: store_bank_branch,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',

            },
            {
                xtype: 'textareafield',
                fieldLabel: lang('Notes'),
                id: 'Desc',
                name: 'Desc'
            },
        ],
        buttons: [{
            // id: 'saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = this.up('form').getForm();
                if (form.isValid()) {
                    form.submit({
                        url: m_download,
                        method: 'POST',
                        timeout: 600,
                        waitMsg: 'Generating files...',
                        success: function (fp, o) {
                            var url = $.parseJSON(o.response.responseText).url;
                            window.open(url, 'Download', "height=200,width=200");
                            // Ext.MessageBox.alert('Success', 'Data saved.');
                            fp.reset();
                        }
                    });
                    winFormDownload.hide();
                } else {
                    Ext.MessageBox.alert('Warning', lang('Silahkan isi form dengan data yang benar'));
                }
            }
        }, {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function () {
                winFormDownload.hide();
            }
        }]
    });

    var winFormDownload = Ext.create('widget.window', {
        title: 'Download Form',
        frame: false,
        closable: true,
        id: 'winFormDownload',
        modal: true,
        closeAction: 'show',
        width: 400,
        height: 300,
        layout: 'fit',
        items: [dataFormDownload]
    });
    
    function displayFormApproval() {
        if (!winFormApproval.isVisible()) {
            winFormApproval.show();
        } else {
            winFormApproval.hide();
            winFormApproval.toFront();
        }
    }

    var dataFormApproval = Ext.create('Ext.form.Panel', {
        id: 'FormApproval',
        frame: false,
        height: 650,
        width: 400,
        bodyPadding: 5,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'hiddenfield',
                id: 'FarmerIDApproval',
                name: 'FarmerID',
            },
            {
                id: 'ApprovalStatus',
                name: 'ApprovalStatus',
                xtype: 'combobox',
                fieldLabel: lang('Status'),
                store: approval_status,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                allowBlank: false,
                listeners: {
                    change: function(cb, nv, ov) {
                        if (nv == '1') {
                            Ext.getCmp('LoanAmount').show();
                            Ext.getCmp('LoanAmount').allowBlank = false;
                        } else {
                            Ext.getCmp('LoanAmount').hide().setValue('');
                            Ext.getCmp('LoanAmount').allowBlank = true;
                        }
                    }
                }
            },
            {
                xtype: 'textfield',
                fieldLabel: lang('Amount'),
                id: 'LoanAmount',
                name: 'LoanAmount',
                hidden: true,
                // allowBlank: false,
                regex: /^[0-9]+(.|,)[0-9]{2}$/,
                validator: function(value){
                    if (Ext.getCmp('ApprovalStatus').getValue() === '1' && parseFloat(value) === 0) {
                        return lang('Please input loan amount');
                    }
                    return true;
                }
            },
            {
                xtype: 'textareafield',
                fieldLabel: lang('Notes'),
                id: 'StatusNotes',
                name: 'StatusNotes',
                validator: function(value){
                    if (Ext.getCmp('ApprovalStatus').getValue() === '3' && value === '') {
                        return lang('Please input notes');
                    }
                    return true;
                }
            },
        ],
        buttons: [{
            // id: 'saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = this.up('form').getForm();
                if (form.isValid()) {
                    form.submit({
                        url: m_approval,
                        method: 'POST',
                        // timeout: 600,
                        waitMsg: lang('Please Wait'),
                        success: function (fp, o) {
                            fp.reset();
                            store_farmer_list.load();
                        }
                    });                    
                    winFormApproval.hide();
                } else {
                    Ext.MessageBox.alert('Warning', lang('Silahkan isi form dengan data yang benar'));
                }
            }
        }, {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function () {
                winFormApproval.hide();
            }
        }]
    });

    var winFormApproval = Ext.create('widget.window', {
        title: lang('Approval Form'),
        frame: false,
        closable: true,
        id: 'winFormApproval',
        modal: true,
        closeAction: 'show',
        width: 400,
        height: 260,
        layout: 'fit',
        items: [dataFormApproval]
    });
    
    // function displayFormFinalization() {
    //     if (!winFormFinalization.isVisible()) {
    //         winFormFinalization.show();
    //     } else {
    //         winFormFinalization.hide();
    //         winFormFinalization.toFront();
    //     }
    // }

    // var dataFormFinalization = Ext.create('Ext.form.Panel', {
    //     frame: false,
    //     height: 650,
    //     width: 400,
    //     bodyPadding: 5,
    //     fieldDefaults: {
    //         labelAlign: 'left',
    //         labelWidth: 100,
    //         anchor: '100%'
    //     },
    //     items: [
    //         {
    //             xtype: 'textfield',
    //             fieldLabel: lang('Farmer ID'),
    //             id: 'FarmerIDFinalization',
    //             name: 'FarmerID',
    //             readOnly: true
    //         },
    //         {
    //             xtype: 'textfield',
    //             fieldLabel: lang('Farmer Name'),
    //             id: 'FarmerNameFinalization',
    //             name: 'FarmerNameFinalization',
    //             readOnly: true
    //         },
    //         {
    //             xtype: 'textfield',
    //             fieldLabel: lang('Amount'),
    //             id: 'LoanAmount',
    //             name: 'LoanAmount',
    //             allowBlank: false,
    //             regex: /^[0-9]+(.|,)[0-9]{2}$/,
    //         },
    //     ],
    //     buttons: [{
    //         // id: 'saveButton',
    //         text: lang('Save'),
    //         margin: '5px',
    //         scale: 'large',
    //         ui: 's-button',
    //         cls: 's-blue',
    //         handler: function () {
    //             var form = this.up('form').getForm();
    //             if (form.isValid()) {
    //                 form.submit({
    //                     url: m_finalization,
    //                     method: 'POST',
    //                     // timeout: 600,
    //                     waitMsg: lang('Please Wait'),
    //                     success: function (fp, o) {
    //                         fp.reset();
    //                         store_farmer_list.load();
    //                     }
    //                 });
    //                 winFormFinalization.hide();
    //             } else {
    //                 Ext.MessageBox.alert('Warning', lang('Silahkan isi form dengan data yang benar'));
    //             }
    //         }
    //     }, {
    //         text: lang('Close'),
    //         margin: '5px',
    //         scale: 'large',
    //         ui: 's-button',
    //         cls: 's-grey',
    //         disabled: false,
    //         handler: function () {
    //             winFormFinalization.hide();
    //         }
    //     }]
    // });

    // var winFormFinalization = Ext.create('widget.window', {
    //     title: lang('Finalization Form'),
    //     frame: false,
    //     closable: true,
    //     id: 'winFormFinalization',
    //     modal: true,
    //     closeAction: 'show',
    //     width: 400,
    //     height: 260,
    //     layout: 'fit',
    //     items: [dataFormFinalization]
    // });
    
    function displayWinDetail(FarmerID) {
        $.ajax({
            url: m_detail,
            // type: 'default GET (Other values: POST)',
            // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
            data: {FarmerID: FarmerID},
        })
        .done(function(data) {
            // console.log("success");
            var status = '';
            switch (data.ApprovalStatus) {
                case "0":
                    status = lang("Unprocessed");
                    break;
                case "1":
                    status = lang("Approved");
                    break;
                case "3":
                    status = lang("Rejected");
                    break;
                case "2":
                    status = lang("Final");
                    break;
                default:
                    // statements_def
                    break;
            }
            Ext.getCmp('detailStatus').setValue(status);
            Ext.getCmp('detailAmount').setValue(data.LoanAmount);
            Ext.getCmp('detailNotes').setValue(data.StatusNotes);
            Ext.getCmp('detailModify').setValue(data.UpdatedBy+', '+data.DateUpdated);
        })
        .fail(function() {
            // console.log("error");
        })
        .always(function() {
            // console.log("complete");
        });
        
        if (!winDetail.isVisible()) {
            winDetail.show();
        } else {
            winDetail.hide();
            winDetail.toFront();
        }
    }

    var dataDetail = Ext.create('Ext.form.Panel', {
        id: 'formDetail',
        frame: false,
        height: 650,
        width: 400,
        bodyPadding: 5,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'textfield',
                fieldLabel: lang('Status'),
                id: 'detailStatus',
                name: 'Status',
                readOnly: true
            },
            {
                xtype: 'textfield',
                fieldLabel: lang('Amount'),
                id: 'detailAmount',
                name: 'LoanAmount',
                readOnly: true
            },            
            {
                xtype: 'textareafield',
                fieldLabel: lang('Notes'),
                id: 'detailNotes',
                name: 'StatusNotes',
                readOnly: true
            },
            {
                xtype: 'textfield',
                fieldLabel: lang('Modified By'),
                id: 'detailModify',
                name: 'UpdatedBy',
                readOnly: true
            },  
        ],
        buttons: [{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function () {
                winDetail.hide();
            }
        }]
    });

    var winDetail = Ext.create('widget.window', {
        title: lang('Detail'),
        frame: false,
        closable: true,
        id: 'winDetail',
        modal: true,
        closeAction: 'show',
        width: 400,
        height: 300,
        layout: 'fit',
        items: [dataDetail]
    });
    Ext.getCmp('NeedLoan').setValue('yes');
    Ext.getCmp('Production').setValue('500');
    Ext.getCmp('SignedLearningContract').setValue('yes');
    if (m_is_bank == '1') {
        Ext.getCmp('NeedLoan').hide();
        Ext.getCmp('Production').hide();
        Ext.getCmp('SignedLearningContract').hide();
    }
});

