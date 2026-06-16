    Ext.Loader.setConfig({
        enabled: true
    });
    Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
    //Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
    Ext.require([
        //'Ext.form.Panel',
        //'Ext.ux.form.MultiSelect',
        'Ext.ux.form.ItemSelector'
    ]);


    Ext.onReady(function() {
        Ext.tip.QuickTipManager.init();

        var store = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['CoopID', 'CoopName', 'Phone', 'Email', 'TahunTerbentuk', 'Status', 'District', 'LimitTransaction'],
            autoLoad: true,
            pageSize: 50,
            proxy: {
                type: 'ajax',
                url: m_crud + 's',
                extraParams: {
                    UserId: m_param
                },
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });



        var mc_Provinsi = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_Provinsi,
                extraParams: {
                    prov: m_param
                },
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        var mc_Kabupaten = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_Kabupaten,
                //            extraParams: {prov: m_param},
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        var mc_Kecamatan = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_Kecamatan,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        var mc_Desa = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['label', 'id'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_Desa,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        //staff
        Ext.define('staff.Model', {
            extend: 'Ext.data.Model',
            fields: ['StaffID', 'CoopID', 'Status', 'FarmerID', 'StaffName', 'Position', 'Phone', 'Email', 'StaffBirthday', 'StaffGender', 'StaffStatus', 'PaymentStatus'],
        });
        var store_staff = Ext.create('Ext.data.Store', {
            model: 'staff.Model',
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_staff + 's',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });

        //board
        Ext.define('board.Model', {
            extend: 'Ext.data.Model',
            fields: ['BoardID', 'CoopID', 'Status', 'BoardName', 'Position', 'Phone', 'Email', 'BoardBirthday', 'BoardGender', 'BoardStatus', 'PaymentStatus'],
        });
        var store_board = Ext.create('Ext.data.Store', {
            model: 'board.Model',
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_board + 's',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });


        var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'RowEditing',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });

        var RowEditingBoard = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'RowEditingBoard',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });
        var cposition = Ext.create('Ext.data.Store', {
            fields: ['label'],
            data: [{
                "label": lang("Ketua Badan Pengawas")
            }, {
                "label": lang("Ketua")
            }, {
                "label": lang("Wakil Ketua")
            }, {
                "label": lang("Sekretaris")
            }, {
                "label": lang("Wakil Sekretaris")
            }, {
                "label": lang("Bendahara")
            }, {
                "label": lang("Wakil Bendahara")
            }]
        });
        var bPosition = Ext.create('Ext.data.Store', {
            fields: ['label'],
            data: [{
                "label": lang("Advisory Board")
            }, {
                "label": lang("Supervisory Board")
            }, {
                "label": lang("Managing Director")
            }]
        });
        var cstaffstatus = Ext.create('Ext.data.Store', {
            fields: ['label'],
            data: [{
                "label": lang("Full-Time")
            }, {
                "label": lang("Part-Time")
            }, ]
        });
        var cpaymentstatus = Ext.create('Ext.data.Store', {
            fields: ['label'],
            data: [{
                "label": lang("Paid")
            }, {
                "label": lang("Unpaid")
            }, ]
        });
        var ckelamin = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "1",
                "label": lang("Laki-laki")
            }, {
                "id": "2",
                "label": lang("Perempuan")
            }]
        });
        var ceducation = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "1",
                "label": lang("Belum pernah sekolah")
            }, {
                "id": "2",
                "label": lang("Tidak tamat SD")
            }, {
                "id": "3",
                "label": lang("Tamat SD, tidak melanjutkan")
            }, {
                "id": "4",
                "label": lang("Tamat SMP")
            }, {
                "id": "5",
                "label": lang("Tamat SMA/SMK")
            }, {
                "id": "6",
                "label": lang("Tamat perguruan tinggi")
            }]
        });
        var cfarmer = Ext.create('Ext.data.Store', {
            fields: ['label'],
            data: [{
                "id": "Farmer",
                "label": lang("Farmer")
            }, {
                "id": "Non Farmer",
                "label": lang("Non Farmer")
            }]
        });


        function gs_edit() {
            if (Ext.getCmp('farmer').getValue() == 'Farmer') {
                Ext.getCmp('lfarmer').setVisible(true)
                Ext.getCmp('lnon').setVisible(false)
                Ext.getCmp('lhp').setReadOnly(true)
                    //Ext.getCmp('lemail').setReadOnly(true)
                Ext.getCmp('StaffGender').setReadOnly(true)
                Ext.getCmp('lbirthday').setReadOnly(true)
            } else {
                Ext.getCmp('lfarmer').setVisible(false)
                Ext.getCmp('lnon').setVisible(true)
                Ext.getCmp('lhp').setReadOnly(false)
                    //Ext.getCmp('lemail').setReadOnly(false)
                Ext.getCmp('StaffGender').setReadOnly(false)
                Ext.getCmp('lbirthday').setReadOnly(false)
            }
        }
        Ext.define("Post", {
            extend: 'Ext.data.Model',
            proxy: {
                type: 'ajax',
                url: m_staff + '_farmers',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            fields: [{
                name: 'id',
                mapping: 'id'
            }, {
                name: 'name',
                mapping: 'name'
            }, {
                name: 'handphone',
                mapping: 'hp'
            }, {
                name: 'email',
                mapping: 'email'
            }, {
                name: 'birthdate',
                mapping: 'birthdate'
            }, {
                name: 'kelamin',
                mapping: 'kelamin'
            }]
        });

        var ds = Ext.create('Ext.data.Store', {
            pageSize: 10,
            model: 'Post'
        });
        //end staff

        function submitOnEnter(field, event) {
            if (event.getKey() == event.ENTER) {
                storeDoc.load({
                    params: {
                        key: Ext.getCmp('key').getValue()
                    }
                });
            }
        }

        //document tab
        Ext.define('CoopDoc.Model', {
            extend: 'Ext.data.Model',
            fields: ['FileID', 'FileLabel', 'FileName', 'FileCategory', 'FileSize', 'FileType', 'DateCreated', 'UserName', 'path'],
        });
        var storeDoc = Ext.create('Ext.data.Store', {
            model: 'CoopDoc.Model',
            autoLoad: true,
            pageSize: 50,
            proxy: {
                type: 'ajax',
                url: m_cruddoc + 's',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });

        function displayFormWindow() {
            if (!win.isVisible()) {
                win.show();
            } else {
                win.hide(this, function() {});
                win.toFront();
            }
        }

        var DataForm = Ext.create('Ext.form.Panel', {
            frame: false,
            // height: 250,
            autoHeight: true,
            autoScroll: true,
            // width: 580,
            autoWidth: true,
            bodyPadding: 5,
            id: 'dataForm',
            fileUpload: true,
            enctype: 'multipart/form-data',
            id: 'upload',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 100,
                anchor: '100%'
            },
            items: [{
                    xtype: 'textfield',
                    labelWidth: 100,
                    id: 'label',
                    width: 350,
                    name: 'label',
                    fieldLabel: 'Label'
                },
                Ext.create('Ext.form.ComboBox', {
                    fieldLabel: 'Category',
                    store: Ext.create('Ext.data.Store', {
                        fields: ['FileCategory'],
                        data: [{
                                "FileCategory": "Official Document"
                            }, {
                                "FileCategory": "Image"
                            }, {
                                "FileCategory": "Other"
                            }
                            //...
                        ]
                    }),
                    queryMode: 'local',
                    name: 'FileCategory',
                    displayField: 'FileCategory',
                    valueField: 'FileCategory'
                }), {
                    xtype: 'fileuploadfield',
                    fieldLabel: 'File',
                    labelWidth: 100,
                    id: 'file',
                    // padding : 5,
                    name: 'file',
                    buttonText: 'Browse'
                }, {
                    xtype: 'displayfield',
                    hideLabel: true,
                    value: 'Ketentuan: Jenis berkas yang diperbolehkan adalah gif,jpg,png,pdf,<br>doc,docx,csv,xls dan xlsx. Ukuran berkas maksimal 10 Megabyte'
                }
            ],
            buttons: [{
                // id:'saveButton',
                text: 'Save',
                // margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = Ext.getCmp('upload').getForm();
                    form.submit({
                        url: m_cruddoc,
                        waitMsg: 'Sending files....',
                        success: function(fp, o) {
                            storeDoc.load();
                            win.hide();
                        },
                        failure: function(fp, o) {
                            var obj = Ext.decode(o.response.responseText);
                            Ext.MessageBox.alert('File Document', obj.message);
                        }
                    });
                }
            }, {
                text: 'Close',
                // margin: '5px',
                scale: 'large',
                ui: 's-button',
                hidden: true,
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    win.hide();
                }
            }]
        });
        var win = Ext.create('widget.window', {
            title: 'Upload File',
            id: 'win',
            closable: true,
            modal: true,
            closeAction: 'hide',
            // width: 600,
            // height: 300,
            layout: {
                // type: 'border',
                padding: 5
            },
            items: [DataForm]
        });


        var gridDoc = Ext.create('Ext.grid.Panel', {
            store: storeDoc,
            width: '100%',
            id: 'gridDoc',
            height: 500,
            // minHeight:40,
            //title: 'CPG Batch List',
            style: 'border:1px solid #CCC;',
            // renderTo: 'ext-content',
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeDoc, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/silk/disk_upload.png',
                    text: 'Upload',
                    handler: function() {
                        displayFormWindow();
                    }
                }, {
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'key',
                    xtype: 'textfield',
                    listeners: {
                        specialkey: submitOnEnter
                    }
                }, {
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    margin: '0px 0px 0px -10px',
                    text: 'Search',
                    handler: function() {
                        storeDoc.load({
                            params: {
                                key: Ext.getCmp('key').getValue()
                            }
                        });
                    }
                }]
            }, {
                xtype: 'toolbar',
                id: 'toolbar_dua',
                hidden: true,
                items: [{
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: m_act_delete,
                    text: 'Hapus',
                    scope: this,
                    handler: function() {
                        var sma = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_crud,
                                    method: 'DELETE',
                                    params: {
                                        id: sma.raw.FileID
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
                }]
            }],
            columns: [{
                text: 'Label',
                width: '25%',
                dataIndex: 'FileLabel'
            }, {
                text: 'Name',
                width: '15%',
                dataIndex: 'FileName'
            }, {
                text: 'Size (KB)',
                width: '10%',
                dataIndex: 'FileSize'
            }, {
                text: 'Type',
                width: '7%',
                dataIndex: 'FileType'
            }, {
                text: 'Category',
                width: '20%',
                dataIndex: 'FileCategory'
            }, {
                text: 'Created',
                width: '15%',
                dataIndex: 'DateCreated'
            }, {
                text: 'User',
                width: '15%',
                hidden: true,
                dataIndex: 'UserName'
            }, {
                text: 'Path',
                hidden: true,
                dataIndex: 'path'
            }, {
                menuDisabled: true,
                sortable: false,
                xtype: 'actioncolumn',
                width: 80,
                align: 'center',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/silk/disk_download.png',
                    tooltip: lang('Download'),
                    handler: function(grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);
                        // console.log(rec);
                        window.open(rec.data.path, "_blank");

                    }
                }]
            }],
            listeners: {
                itemdblclick: function(dataview, index, item, e) {
                    // if (Ext.getCmp('toolbar_dua').isVisible()) Ext.getCmp('toolbar_dua').setVisible(false);
                    // else Ext.getCmp('toolbar_dua').setVisible(true);
                }
            }
        });
        //end document tab

        //grid limit transaction 
        Ext.define('CoopLimitTrans.Model', {
            extend: 'Ext.data.Model',
            fields: ['ApprovalID', 'Position', 'MinTransaction', 'MaxTransaction', 'Deposit', 'Withdrawal'],
        });
        var storeCoopLimitTrans = Ext.create('Ext.data.Store', {
            model: 'CoopLimitTrans.Model',
            autoLoad: true,
            pageSize: 50,
            proxy: {
                type: 'ajax',
                url: m_api + '/cooperatives/limit_trans',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });

        function displayFormWindowCoopLimitTrans() {
            if (!winCoopLimitTrans.isVisible()) {
                winCoopLimitTrans.show();
            } else {
                winCoopLimitTrans.hide(this, function() {});
                winCoopLimitTrans.toFront();
            }
        }

        // var CoopID = Ext.getCmp('CoopID').getValue();

        var DataFormCoopLimitTrans = Ext.create('Ext.form.Panel', {
            frame: false,
            autoHeight: true,
            autoScroll: true,
            autoWidth: true,
            bodyPadding: 5,
            id: 'dataFormCoopLimitTrans',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 100,
                anchor: '100%'
            },
            items: [{
                    xtype: 'hiddenfield',
                    id: 'ApprovalID',
                    name: 'ApprovalID'
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Position',
                    width: 330,
                    store: cposition,
                    id: 'Position',
                    name: 'Position',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'label'
                }, {
                    xtype: 'textfield',
                    width: 330,
                    allowBlank: false,
                    fieldLabel: 'Min Amount',
                    id: 'MinAmount',
                    name: 'MinAmount'
                }, {
                    xtype: 'textfield',
                    width: 330,
                    allowBlank: false,
                    fieldLabel: 'Max Amount',
                    id: 'MaxAmount',
                    name: 'MaxAmount'
                }, {
                    xtype: 'checkboxgroup',
                    name: 'TransType',
                    fieldLabel: 'Transaction Type',
                    columns: 1,
                    items: [{
                        boxLabel: 'Deposit',
                        id: 'cbDeposit',
                        name: 'deposit'
                    }, {
                        boxLabel: 'Withdrawal',
                        id: 'cbWithdrawal',
                        name: 'withdrawal'
                    }]
                }, {
                    xtype: 'itemselector',
                    name: 'StaffApproval',
                    // fieldLabel:'Select roles',
                    id: 'itemselector-StaffApproval',
                    // anchor: '100%',
                    width: 600,
                    height: 200,
                    store: store_staff,
                    displayField: 'StaffName',
                    valueField: 'StaffID',
                    //                value: [],
                    allowBlank: true,
                    msgTarget: 'side',
                    fromTitle: 'Available Staff',
                    toTitle: 'Selected Approval Staff'
                }
                // {
                //     xtype: 'boxselect',
                //     itemId: 'valuesSelect',
                //     width:500,
                //     fieldLabel: 'Approval by',
                //     displayField: 'StaffName',
                //     // anchor:'100%',
                //     height:80,
                //     name:'ApprovalBy[]',
                //     store: store_staff,
                //     valueField: 'StaffID',
                //     listeners: {

                //     }
                // }
            ],
            buttons: [{
                text: 'Save',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = Ext.getCmp('dataFormCoopLimitTrans').getForm();
                    form.submit({
                        url: m_api + '/cooperatives/limit_trans',
                        waitMsg: 'Sending files....',
                        success: function(fp, o) {
                            storeCoopLimitTrans.load();
                            Ext.getCmp('winCoopLimitTrans').hide();
                        },
                        failure: function(fp, o) {
                            var obj = Ext.decode(o.response.responseText);
                            Ext.MessageBox.alert('File Document', obj.message);
                        }
                    });
                }
            }, {
                text: 'Close',
                scale: 'large',
                ui: 's-button',
                hidden: true,
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    Ext.getCmp('winCoopLimitTrans').hide();
                }
            }]
        });
        var winCoopLimitTrans = Ext.create('widget.window', {
            title: 'Limit Transaction Configuration',
            id: 'winCoopLimitTrans',
            closable: true,
            modal: true,
            closeAction: 'hide',
            // width: 600,
            // height: 300,
            layout: {
                // type: 'border',
                padding: 5
            },
            items: [DataFormCoopLimitTrans]
        });


        var gridCoopLimitTrans = Ext.create('Ext.grid.Panel', {
            store: storeCoopLimitTrans,
            width: '100%',
            id: 'gridCoopLimitTrans',
            height: 500,
            style: 'border:1px solid #CCC;',
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeCoopLimitTrans, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: 'Add',
                    handler: function() {
                        displayFormWindowCoopLimitTrans();
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Edit'),
                    scope: this,
                    handler: function() {
                        displayFormWindowCoopLimitTrans();

                        var sm = Ext.getCmp('gridCoopLimitTrans').getSelectionModel().getSelection()[0];
                        if (!sm) {
                            Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                            return false;
                        } else {
                            var id = sm.get('ApprovalID');

                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_api + '/cooperatives/limit_trans',
                                method: 'GET',
                                params: {
                                    id: id
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    // console.log(obj.data[0])
                                    Ext.getCmp('ApprovalID').setValue(obj.data[0].ApprovalID);
                                    Ext.getCmp('Position').setValue(obj.data[0].Position);
                                    Ext.getCmp('MinAmount').setValue(obj.data[0].MinTransaction);
                                    Ext.getCmp('MaxAmount').setValue(obj.data[0].MaxTransaction);

                                    if (obj.data[0].Deposit == '1') {
                                        Ext.getCmp('cbDeposit').setValue(true);
                                    }

                                    if (obj.data[0].Withdrawal == '1') {
                                        Ext.getCmp('cbWithdrawal').setValue(true);
                                    }

                                    Ext.getCmp('itemselector-StaffApproval').setValue(obj.staff);
                                },
                                failure: function(response, opts) {
                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                }
                            });
                        }


                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    hidden: true,
                    text: lang('Hapus'),
                    scope: this,
                    handler: function() {
                        var smb = Ext.getCmp('grid_staff').getSelectionModel().getSelection()[0];
                        RowEditing.cancelEdit();
                        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus staff ini ?'), function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_staff,
                                    method: 'DELETE',
                                    params: {
                                        id: smb.raw.StaffID
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store_staff.load({
                                                    params: {
                                                        id: Ext.getCmp('CoopID').getValue()
                                                    }
                                                });
                                                break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                        }
                                    },
                                    failure: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                    }
                                });
                            }
                        });
                    }
                }]
            }],
            columns: [{
                text: 'ApprovalID',
                hidden: true,
                dataIndex: 'ApprovalID'
            }, {
                text: 'Position',
                flex: 1,
                width: '10%',
                dataIndex: 'Position'
            }, {
                text: 'Min Transaction',
                renderer: Ext.util.Format.numberRenderer('0,000'),
                xtype: 'numbercolumn',
                align: 'right',
                width: '15%',
                dataIndex: 'MinTransaction'
            }, {
                text: 'Max Transaction',
                renderer: Ext.util.Format.numberRenderer('0,000'),
                xtype: 'numbercolumn',
                align: 'right',
                width: '15%',
                dataIndex: 'MaxTransaction'
            }, {
                text: 'Deposit',
                width: '15%',
                dataIndex: 'Deposit',
                renderer: function(value) {
                    if (value == '1') {
                        return lang('yes');
                    } else if (value == '0') {
                        return lang('No');
                    }
                }
            }, {
                text: 'Withdrawal',
                width: '15%',
                dataIndex: 'Withdrawal',
                renderer: function(value) {
                    if (value == '1') {
                        return lang('yes');
                    } else if (value == '0') {
                        return lang('No');
                    }
                }
            }, {
                menuDisabled: true,
                hidden: true,
                sortable: false,
                xtype: 'actioncolumn',
                width: 80,
                align: 'center',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/silk/disk_download.png',
                    tooltip: lang('Download'),
                    handler: function(grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);
                        // console.log(rec);
                        window.open(rec.data.path, "_blank");

                    }
                }]
            }],
            listeners: {
                itemdblclick: function(dataview, index, item, e) {
                    // if (Ext.getCmp('toolbar_dua').isVisible()) Ext.getCmp('toolbar_dua').setVisible(false);
                    // else Ext.getCmp('toolbar_dua').setVisible(true);
                }
            }
        });
        //end grid limit transaction

        //start area member
        let storeAreaMember = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            // a.VillageID,a.Village,b.SubDistrict,c.District,x.CoopAreaMemberID,x.VillageID
            fields: ['CoopAreaMemberID', 'VillageID', 'Village', 'SubDistrict', 'District', 'DateCreated'],
            // autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'rest',
                url: m_api + 'cooperatives/area_members',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });

        var idFormArea = Ext.id();

        var DataFormAreaMember = Ext.create('Ext.form.Panel', {
            frame: false,
            autoHeight: true,
            autoScroll: true,
            // autoWidth:true,
            width: 400,
            bodyPadding: 5,
            id: 'form-penerimaan',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 100,
                anchor: '100%'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'CoopID',
                id: "CoopID_AreaMember"
            }, {
                id: 'Provinsi_AreaMember',
                name: 'Provinsi',
                hidden: true,
                xtype: 'combo',
                fieldLabel: lang('Provinsi'),
                store: mc_Provinsi,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                readOnly: true,
                listeners: {
                    change: function(cb, nv, ov) {
                        mc_Kabupaten.load({
                            params: {
                                key: Ext.getCmp('Provinsi').getValue()
                            }
                        });
                        Ext.getCmp('Kabupaten_AreaMember').enable();
                    }
                }
            }, {
                id: 'Kabupaten_AreaMember',
                name: 'Kabupaten',
                xtype: 'combo',
                fieldLabel: lang('Kabupaten'),
                disabled: 'true',
                store: mc_Kabupaten,
                displayField: 'label',
                valueField: 'label',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        mc_Kecamatan.load({
                            params: {
                                key: Ext.getCmp('Kabupaten_AreaMember').getValue()
                            }
                        });
                        Ext.getCmp('Kecamatan_AreaMember').enable();
                        ds.getProxy().setExtraParam("district", Ext.getCmp('Kabupaten_AreaMember').getValue())
                    }
                }
            }, {
                id: 'Kecamatan_AreaMember',
                name: 'Kecamatan',
                xtype: 'combo',
                fieldLabel: lang('Kecamatan'),
                store: mc_Kecamatan,
                displayField: 'label',
                valueField: 'label',
                queryMode: 'local',
                disabled: 'true',
                listeners: {
                    change: function(cb, nv, ov) {
                        mc_Desa.load({
                            params: {
                                key: Ext.getCmp('Kecamatan_AreaMember').getValue()
                            }
                        });
                        Ext.getCmp('Desa_AreaMember').enable();
                    }
                }
            }, {
                xtype: 'boxselect',
                id: 'Desa_AreaMember',
                displayField: 'label',
                valueField: 'id',
                name: 'Desa[]',
                anchor: '100%',
                fieldLabel: lang('Desa'),
                store: mc_Desa,
                disabled: 'true',
                queryMode: 'local'
            }],
            buttons: [{
                text: 'Save',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = Ext.getCmp('form-penerimaan').getForm();
                    form.submit({
                        method: 'POST',
                        url: m_api + '/cooperatives/area_member',
                        waitMsg: 'Sending data....',
                        success: function(fp, o) {
                            storeAreaMember.load();
                            Ext.getCmp('winAreaMember').hide();
                        },
                        failure: function(fp, o) {
                            var obj = Ext.decode(o.response.responseText);
                            Ext.MessageBox.alert('Area Member', obj.message);
                        }
                    });
                }
            }, {
                text: 'Close',
                scale: 'large',
                ui: 's-button',
                // hidden:true,
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    Ext.getCmp('winAreaMember').hide();
                }
            }]
        });

        var winAreaMember = Ext.create('widget.window', {
            title: 'Tambah Area Penerimaan Anggota',
            id: 'winAreaMember',
            closable: false,
            modal: true,
            closeAction: 'hide',
            // width: 600,
            // height: 300,
            layout: {
                // type: 'border',
                padding: 5
            },
            items: [DataFormAreaMember]
        });


        function displayFormWindowAreaMember() {
            if (!winAreaMember.isVisible()) {
                winAreaMember.show();
            } else {
                winAreaMember.hide(this, function() {});
                winAreaMember.toFront();
            }
        }

        let gridAreaMember = Ext.create('Ext.grid.Panel', {
            store: storeAreaMember,
            width: '100%',
            id: 'gridAreaMember',
            height: 500,
            style: 'border:1px solid #CCC;',
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeAreaMember, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: 'Add',
                    handler: function() {
                        displayFormWindowAreaMember();
                        Ext.getCmp('form-penerimaan').getForm().reset();
                        mc_Provinsi.load();
                        Ext.getCmp('Provinsi_AreaMember').setValue(1 * 1);
                        Ext.getCmp('CoopID_AreaMember').setValue(Ext.getCmp('CoopID').getValue());
                        Ext.getCmp('Kabupaten_AreaMember').enable();
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    // hidden:true,
                    text: lang('Hapus'),
                    scope: this,
                    handler: function() {
                        var smb = Ext.getCmp('grid_staff').getSelectionModel().getSelection()[0];
                        RowEditing.cancelEdit();
                        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus staff ini ?'), function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_staff,
                                    method: 'DELETE',
                                    params: {
                                        id: smb.raw.StaffID
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store_staff.load({
                                                    params: {
                                                        id: Ext.getCmp('CoopID').getValue()
                                                    }
                                                });
                                                break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                        }
                                    },
                                    failure: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                    }
                                });
                            }
                        });
                    }
                }, '-', {
                    emptyText: '-- ' + lang('Provinsi') + ' --',
                    id: 'provinceFilterGridArea',
                    name: 'province',
                    xtype: 'combo',
                    width: 150,
                    hidden: true,
                    value: 74,
                    store: mc_Provinsi,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    listeners: {
                        change: function(cb, nv, ov) {
                            mc_Kabupaten.load({
                                params: {
                                    key: Ext.getCmp('provinceFilterGridArea').getValue()
                                },
                                callback: function() {
                                    if (m_user_district) {
                                        Ext.getCmp('districtFilterGridArea').setValue(m_user_district).setDisabled(true);
                                    }
                                }
                            });
                        }
                    }
                }, {
                    emptyText: '-- ' + lang('Kabupaten') + ' --',
                    id: 'districtFilterGridArea',
                    name: 'district',
                    xtype: 'combo',
                    width: 150,
                    store: mc_Kabupaten,
                    displayField: 'label',
                    valueField: 'label',
                    queryMode: 'local',
                    listeners: {
                        change: function(cb, nv, ov) {
                            mc_Kecamatan.load({
                                params: {
                                    key: Ext.getCmp('districtFilterGridArea').getValue()
                                }
                            });

                        }
                    }
                }, {
                    emptyText: '-- ' + lang('Kecamatan') + ' --',
                    id: 'subdistrictFilterGridArea',
                    name: 'subdistrict',
                    xtype: 'combo',
                    width: 150,
                    store: mc_Kecamatan,
                    displayField: 'label',
                    valueField: 'label',
                    queryMode: 'local',
                    listeners: {
                        change: function(cb, nv, ov) {

                        }
                    }
                }, {
                    xtype: 'button',
                    margin: '0px 0px 0px 6px',
                    text: lang('Search'),
                    handler: function() {
                        // console.log(Ext.getCmp('subdistrictFilterGridArea').getValue());

                        storeAreaMember.load({
                            params: {
                                prov: Ext.getCmp('provinceFilterGridArea').getValue(),
                                kab: Ext.getCmp('districtFilterGridArea').getValue(),
                                kec: Ext.getCmp('subdistrictFilterGridArea').getValue()
                            }
                        });
                    }
                }]
            }],
            columns: [{
                hidden: true,
                dataIndex: 'CoopAreaMemberID'
            }, {
                text: 'Kabupaten',
                width: '15%',
                dataIndex: 'District'
            }, {
                text: 'Kecamatan',
                width: '15%',
                dataIndex: 'SubDistrict'
            }, {
                text: 'Desa',
                width: '15%',
                dataIndex: 'Village'
            }],
            // listeners: {
            // itemdblclick: function(dataview, index, item, e) {
            // if (Ext.getCmp('toolbar_dua').isVisible()) Ext.getCmp('toolbar_dua').setVisible(false);
            // else Ext.getCmp('toolbar_dua').setVisible(true);
            // }
            // }       
        });
        //end area member


        //start exportimport
        var storeExportImport = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            // a.VillageID,a.Village,b.SubDistrict,c.District,x.CoopExportImportID,x.VillageID
            fields: ['CoopExportImportID', 'VillageID', 'Village', 'SubDistrict', 'District', 'DateCreated'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/cooperatives/exportimport_data',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });

        var idFormArea = Ext.id();

        var DataFormExportImportServer = Ext.create('Ext.form.Panel', {
            frame: false,
            autoHeight: true,
            autoScroll: true,
            // autoWidth:true,
            fileUpload: true,
            enctype: 'multipart/form-data',
            width: 400,
            bodyPadding: 5,
            id: idFormArea,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 100,
                anchor: '100%'
            },
            items: [{
                xtype: 'fileuploadfield',
                id: 'filedata',
                name: 'filedatas',
                emptyText: 'Select a document to upload...',
                fieldLabel: 'File',
                buttonText: 'Browse'
            }],
            buttons: [{
                text: 'Import',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    // var form = Ext.getCmp(idFormArea).getForm();
                    DataFormExportImportServer.getForm().submit({
                        url: m_api + '/cooperatives/import_sync_server',
                        method:'POST',
                        waitMsg: 'Sending data....',
                        success: function(fp, o) {
                            // storeExportImport.load();
                            Ext.getCmp('winExportImportServer').hide();
                        },
                        failure: function(fp, o) {
                            var obj = Ext.decode(o.response.responseText);
                            Ext.MessageBox.alert('Import', obj.message);
                        }
                    });
                }
            }, {
                text: 'Cancel',
                scale: 'large',
                ui: 's-button',
                // hidden:true,
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    Ext.getCmp('winExportImportServer').hide();
                }
            }]
        });

        var winExportImportServer = Ext.create('widget.window', {
            title: 'Import Data',
            id: 'winExportImportServer',
            closable: false,
            modal: true,
            closeAction: 'hide',
            // width: 600,
            // height: 300,
            layout: {
                // type: 'border',
                padding: 5
            },
            items: [DataFormExportImportServer]
        });

        //////

        var DataFormExportImportLocal = Ext.create('Ext.form.Panel', {
            frame: false,
            autoHeight: true,
            autoScroll: true,
            // autoWidth:true,
            width: 400,
            bodyPadding: 5,
            id: idFormArea,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 100,
                anchor: '100%'
            },
            items: [{
                xtype: 'fileuploadfield',
                id: 'filedata',
                name: 'filedata',
                emptyText: 'Select a document to upload...',
                fieldLabel: 'File',
                buttonText: 'Browse'
            }],
            buttons: [{
                text: 'Import',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = Ext.getCmp(idFormArea).getForm();
                    form.submit({
                        url: m_api + '/cooperatives/import_sync_local_feedback',
                        // waitMsg: 'Sending data....',
                        success: function(fp, o) {
                            var obj = Ext.decode(o.response.responseText);
                            if (obj.success) {
                                Ext.MessageBox.alert('Import', 'Import Data Berhasil');
                            } else {
                                Ext.MessageBox.alert('Import', 'Import Data Gagal');
                            }
                            // storeExportImport.load();
                            Ext.getCmp('winExportImportLocal').hide();
                        },
                        failure: function(fp, o) {
                            var obj = Ext.decode(o.response.responseText);

                        }
                    });
                }
            }, {
                text: 'Cancel',
                scale: 'large',
                ui: 's-button',
                // hidden:true,
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    Ext.getCmp('winExportImportLocal').hide();
                }
            }]
        });

        var winExportImportLocal = Ext.create('widget.window', {
            title: 'Import Data Feedback',
            id: 'winExportImportLocal',
            closable: false,
            modal: true,
            closeAction: 'hide',
            // width: 600,
            // height: 300,
            layout: {
                // type: 'border',
                padding: 5
            },
            items: [DataFormExportImportLocal]
        });

        /////


        function displayFormWindowExportImportServer() {
            if (!winExportImportServer.isVisible()) {
                winExportImportServer.show();
            } else {
                winExportImportServer.hide(this, function() {});
                winExportImportServer.toFront();
            }
        }

        function displayFormWindowExportImportLocal() {
            if (!winExportImportLocal.isVisible()) {
                winExportImportLocal.show();
            } else {
                winExportImportLocal.hide(this, function() {});
                winExportImportLocal.toFront();
            }
        }

        //end exportimport

        // general panel container
        var DataForm = Ext.create('Ext.form.Panel', {
            frame: false,
            // height: 580,
            autoScroll: true,
            //        width: 1010,
            width: '100%',
            renderTo: 'ext-content',
            bodyPadding: 5,
            fileUpload: true,
            enctype: 'multipart/form-data',
            id: 'dataForm',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 140,
                anchor: '100%'
            },
            items: [{
                xtype: 'tabpanel',
                height: 560,
                flex: 1,
                margin: 2,
                activeTab: 0,
                plain: true,
                items: [{
                    xtype: 'panel',
                    autoScroll: true,
                    title: lang('Data Umum'),
                    padding: 5,
                    style: 'border:2px solid #D6EDA4',
                    listeners: {
                        activate: function(selModel, Cmp) {
                            Ext.getCmp('saveButton').show();
                        }
                    },
                    items: [{
                        xtype: 'textfield',
                        id: 'CoopID',
                        name: 'CoopID',
                        hidden: true
                    }, {
                        layout: 'column',
                        items: [{
                            columnWidth: 0.5,
                            items: [{
                                xtype: 'fieldset',
                                style: 'padding: 15px 11px 17px',
                                title: lang('Data Perusahaan'),
                                items: [{
                                    xtype: 'textfield',
                                    id: 'CoopName',
                                    name: 'CoopName',
                                    labelWidth: 180,
                                    fieldLabel: lang('Nama')
                                }, {
                                    xtype: 'textfield',
                                    id: 'Phone',
                                    name: 'Phone',
                                    labelWidth: 180,
                                    fieldLabel: lang('No Telepon')
                                }, {
                                    xtype: 'textfield',
                                    id: 'Email',
                                    name: 'Email',
                                    labelWidth: 180,
                                    fieldLabel: lang('Email')
                                }, {
                                    xtype: 'radiogroup',
                                    labelWidth: 180,
                                    fieldLabel: lang('Status Hukum Perusahaan'),
                                    columns: 1,
                                    items: [{
                                        xtype: 'radiofield',
                                        boxLabel: lang('Koperasi'),
                                        id: 'Status',
                                        name: 'Status',
                                        inputValue: 'Koperasi'
                                    }, {
                                        xtype: 'radiofield',
                                        boxLabel: lang('Gapoktan'),
                                        id: 'Status2',
                                        name: 'Status',
                                        inputValue: 'Gapoktan'
                                    }, {
                                        xtype: 'radiofield',
                                        boxLabel: lang('KUR'),
                                        id: 'Status3',
                                        name: 'Status',
                                        inputValue: 'KUR'
                                    }, {
                                        xtype: 'radiofield',
                                        boxLabel: lang('Tidak Berbadan Hukum'),
                                        id: 'Status4',
                                        name: 'Status',
                                        inputValue: 'Tidak Berbadan Hukum'
                                    }]
                                }, {
                                    xtype: 'textfield',
                                    id: 'TahunTerbentuk',
                                    labelWidth: 180,
                                    name: 'TahunTerbentuk',
                                    fieldLabel: lang('Tahun Berdiri')
                                }, {
                                    xtype: 'textfield',
                                    hidden: true,
                                    id: 'LimitTransaction',
                                    labelWidth: 180,
                                    name: 'LimitTransaction',
                                    fieldLabel: 'Limit Transaksi'
                                }, {
                                    xtype: 'radiogroup',
                                    labelWidth: 180,
                                    id: 'AutoJournal',
                                    width: 100,
                                    name: 'AutoJournal',
                                    fieldLabel: 'Auto Journal',
                                    items: [{
                                        boxLabel: 'Ya',
                                        name: 'AutoJournal',
                                        inputValue: 1
                                    }, {
                                        boxLabel: 'Tidak',
                                        name: 'AutoJournal',
                                        inputValue: 0
                                    }]
                                }]
                            }]
                        }, {
                            columnWidth: 0.5,
                            margin: 5,
                            items: [
                                //                                            {
                                //                                                layout: 'column',
                                //                                                hidden: true,
                                //                                                border: true,
                                //                                                items: [{
                                //                                                        columnWidth: 0.6,
                                //                                                        padding: 10,
                                //                                                        items: [{
                                //                                                                xtype: 'textfield',
                                //                                                                id: 'Photo_old',
                                //                                                                name: 'Photo_old',
                                //                                                                inputType: 'hidden'
                                //                                                            }, {
                                //                                                                xtype: 'fileuploadfield',
                                //                                                                fieldLabel: lang('Icon'),
                                //                                                                labelWidth: 50,
                                //                                                                id: 'Photo',
                                //                                                                name: 'Photo',
                                //                                                                buttonText: 'Browse',
                                //                                                                listeners: {
                                //                                                                    'change': function (fb, v) {
                                //                                                                        var form = this.up('form').getForm();
                                //                                                                        form.submit({
                                //                                                                            url: m_crud + '_image',
                                //                                                                            waitMsg: lang('Sending Photo...'),
                                //                                                                            success: function (fp, o) {
                                //                                                                                Ext.getCmp('iphoto').setSrc(m_photo + o.result.file);
                                //                                                                                Ext.getCmp('Photo_old').setValue(o.result.file);
                                //                                                                            }
                                //                                                                        });
                                //                                                                    }
                                //                                                                }
                                //                                                            }]
                                //                                                    }, {
                                //                                                        columnWidth: 0.4,
                                //                                                        items: [{
                                //                                                                xtype: 'image',
                                //                                                                id: 'iphoto',
                                //                                                                height: '120px'
                                //                                                            }]
                                //                                                    }]
                                //                                            }, 
                                {
                                    xtype: 'fieldset',
                                    style: 'padding: 15px 11px 17px',
                                    title: lang('Lokasi'),
                                    items: [{
                                        id: 'Provinsi',
                                        name: 'Provinsi',
                                        xtype: 'combo',
                                        fieldLabel: lang('Provinsi'),
                                        store: mc_Provinsi,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        readOnly: true,
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                mc_Kabupaten.load({
                                                    params: {
                                                        key: Ext.getCmp('Provinsi').getValue()
                                                    }
                                                });
                                                Ext.getCmp('Kabupaten').enable();
                                            }
                                        }
                                    }, {
                                        id: 'Kabupaten',
                                        name: 'Kabupaten',
                                        xtype: 'combo',
                                        fieldLabel: lang('Kabupaten'),
                                        disabled: 'true',
                                        store: mc_Kabupaten,
                                        displayField: 'label',
                                        valueField: 'label',
                                        queryMode: 'local',
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                mc_Kecamatan.load({
                                                    params: {
                                                        key: Ext.getCmp('Kabupaten').getValue()
                                                    }
                                                });
                                                Ext.getCmp('Kecamatan').enable();
                                                ds.getProxy().setExtraParam("district", Ext.getCmp('Kabupaten').getValue())
                                            }
                                        }
                                    }, {
                                        id: 'Kecamatan',
                                        name: 'Kecamatan',
                                        xtype: 'combo',
                                        fieldLabel: lang('Kecamatan'),
                                        store: mc_Kecamatan,
                                        displayField: 'label',
                                        valueField: 'label',
                                        queryMode: 'local',
                                        disabled: 'true',
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                mc_Desa.load({
                                                    params: {
                                                        key: Ext.getCmp('Kecamatan').getValue()
                                                    }
                                                });
                                                Ext.getCmp('Desa').enable();
                                            }
                                        }
                                    }, {
                                        id: 'Desa',
                                        name: 'Desa',
                                        xtype: 'combo',
                                        fieldLabel: lang('Desa'),
                                        store: mc_Desa,
                                        displayField: 'label',
                                        disabled: 'true',
                                        valueField: 'id',
                                        queryMode: 'local'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Alamat'),
                                        id: 'Address',
                                        name: 'Address'
                                    }, {
                                        xtype: 'textfield',
                                        id: 'Latitude',
                                        name: 'Latitude',
                                        fieldLabel: lang('Latitude')
                                    }, {
                                        xtype: 'textfield',
                                        id: 'Longitude',
                                        name: 'Longitude',
                                        fieldLabel: lang('Longitude')
                                    }]
                                }
                            ]
                        }]
                    }]
                }, {
                    xtype: 'panel',
                    autoScroll: false,
                    id: 'panel_staff',
                    //                        disabled: true,
                    title: lang('Staff'),
                    padding: 5,
                    style: 'border:2px solid #D6EDA4',
                    listeners: {
                        activate: function(selModel, Cmp) {
                            Ext.getCmp('saveButton').hide();
                        }
                    },
                    items: [{
                        xtype: 'gridpanel',
                        autoScroll: false,
                        id: 'grid_staff',
                        store: store_staff,
                        height: 500,
                        width: '100%',
                        loadMask: true,
                        selType: 'rowmodel',
                        dockedItems: [
                            /*{
                                                        xtype: 'toolbar',
                                                        items: [{
                                                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                                            text: lang('Add'),
                                                            //                                                cls: m_act_save,
                                                            scope: this,
                                                            handler: function() {
                                                                RowEditing.cancelEdit();
                                                                var r = Ext.create('staff.Model', {
                                                                    StaffID: '',
                                                                    CoopID: '',
                                                                    Status: '',
                                                                    FarmerID: '',
                                                                    StaffName: '',
                                                                    Position: '',
                                                                    Phone: '',
                                                                    Email: '',
                                                                    StaffBirthday: '',
                                                                    StaffGender: ''
                                                                });
                                                                store_staff.insert(0, r);
                                                                RowEditing.startEdit(0, 0);
                                                            }
                                                        }, {
                                                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                                                            //                                                cls: m_act_save,
                                                            text: lang('Edit'),
                                                            scope: this,
                                                            handler: function() {
                                                                RowEditing.cancelEdit();
                                                                var sm = Ext.getCmp('grid_staff').getSelectionModel().getSelection();
                                                                RowEditing.startEdit(sm[0].index, 0);
                                                                gs_edit()
                                                            }
                                                        }, {
                                                            itemId: 'remove',
                                                            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                                            text: lang('Hapus'),
                                                            scope: this,
                                                            handler: function() {
                                                                var smb = Ext.getCmp('grid_staff').getSelectionModel().getSelection()[0];
                                                                RowEditing.cancelEdit();
                                                                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus staff ini ?'), function(btn) {
                                                                    if (btn == 'yes') {
                                                                        Ext.Ajax.request({
                                                                            waitMsg: lang('Please Wait'),
                                                                            url: m_staff,
                                                                            method: 'DELETE',
                                                                            params: {
                                                                                id: smb.raw.StaffID
                                                                            },
                                                                            success: function(response, opts) {
                                                                                var obj = Ext.decode(response.responseText);
                                                                                switch (obj.success) {
                                                                                    case true:
                                                                                        store_staff.load({
                                                                                            params: {
                                                                                                id: Ext.getCmp('CoopID').getValue()
                                                                                            }
                                                                                        });
                                                                                        break;
                                                                                    default:
                                                                                        Ext.MessageBox.alert('Warning', obj.message);
                                                                                        break;
                                                                                }
                                                                            },
                                                                            failure: function(response, opts) {
                                                                                var obj = Ext.decode(response.responseText);
                                                                                Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            }
                                                        }]
                                                    }*/
                        ],
                        columns: [{
                            text: lang('Status'),
                            id: 'lstatus',
                            dataIndex: 'Status',
                            width: '10%',
                            editor: {
                                xtype: 'combo',
                                store: cfarmer,
                                id: 'farmer',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                listeners: {
                                    change: function(combo, selection) {
                                        gs_edit();
                                    }
                                }
                            }
                        }, {
                            text: lang('Nama'),
                            id: 'lfarmer',
                            dataIndex: 'StaffName',
                            width: '20%',
                            editor: {
                                xtype: 'combo',
                                store: ds,
                                id: 'lfarmerid',
                                displayField: 'name',
                                typeAhead: false,
                                hideLabel: true,
                                hideTrigger: true,
                                anchor: '100%',
                                listConfig: {
                                    loadingText: 'Searching...',
                                    emptyText: lang('No matching farmer found.'),
                                    getInnerTpl: function() {
                                        return '<div class="search-item">' +
                                            '{id} - {name}' +
                                            '{excerpt}' +
                                            '</div>';
                                    }
                                },
                                pageSize: 10,
                                listeners: {
                                    select: function(combo, selection) {
                                        var post = selection[0];
                                        if (post) {
                                            Ext.getCmp('lfarmerid').setValue('[' + post.get('id') + '] ' + post.get('name'))
                                            Ext.getCmp('namanon').setValue(post.get('id'))
                                            Ext.getCmp('lhp').setValue(post.get('handphone'))
                                            Ext.getCmp('lhp').setReadOnly(true)
                                                //Ext.getCmp('lemail').setValue(post.get('email'))
                                                //Ext.getCmp('lemail').setReadOnly(true)
                                            Ext.getCmp('StaffGender').setValue(post.get('kelamin'))
                                            Ext.getCmp('StaffGender').setReadOnly(true)
                                            Ext.getCmp('lbirthday').setValue(post.get('birthdate'))
                                            Ext.getCmp('lbirthday').setReadOnly(true)
                                        }
                                    }
                                }
                            }
                        }, {
                            text: lang('Nama'),
                            id: 'lnon',
                            dataIndex: 'StaffName',
                            width: '20%',
                            hidden: true,
                            editor: {
                                xtype: 'textfield',
                                id: 'namanon',
                                name: 'namanon',
                            }
                        }, {
                            text: lang('Staff Status'),
                            dataIndex: 'StaffStatus',
                            width: '10%',
                            editor: {
                                xtype: 'combo',
                                store: cstaffstatus,
                                id: 'StaffStatus',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'label'
                            }
                        }, {
                            text: lang('Payment Status'),
                            dataIndex: 'PaymentStatus',
                            width: '10%',
                            editor: {
                                xtype: 'combo',
                                store: cpaymentstatus,
                                id: 'PaymentStatus',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'label'
                            }
                        }, {
                            text: lang('Position'),
                            dataIndex: 'Position',
                            width: '15%',
                            editor: {
                                xtype: 'combo',
                                store: cposition,
                                id: 'Position',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'label'
                            }
                        }, {
                            text: lang('Handphone'),
                            dataIndex: 'Phone',
                            width: '10%',
                            editor: {
                                id: 'lhp',
                                xtype: 'textfield'
                            }
                        }, {
                            text: lang('Email'),
                            dataIndex: 'Email',
                            width: '10%',
                            editor: {
                                xtype: 'textfield',
                                id: 'lemail',
                                allowBlank: false
                            }
                        }, {
                            text: lang('Birthday'),
                            dataIndex: 'StaffBirthday',
                            width: '10%',
                            editor: {
                                xtype: 'datefield',
                                id: 'lbirthday',
                                format: 'Y-m-d'
                            }
                        }, {
                            text: lang('Kelamin'),
                            dataIndex: 'StaffGender',
                            width: '10%',
                            editor: {
                                xtype: 'combo',
                                store: ckelamin,
                                queryMode: 'local',
                                id: 'StaffGender',
                                displayField: 'label',
                                valueField: 'id'
                            }
                        }],
                        plugins: [RowEditing],
                        listeners: {
                            itemdblclick: function(dv, record, item, index, e) {
                                gs_edit()
                            },
                            'canceledit': function(editor, e, eOpts) {
                                store_staff.load({
                                    params: {
                                        id: Ext.getCmp('CoopID').getValue()
                                    }
                                });
                            },
                            'edit': function(editor, e) {
                                if (e.record.data.Status == 'Farmer') {
                                    name = e.record.data.StaffName;
                                    farmer_id = e.record.data.StaffName.split("]")[0].split('[')[1];
                                } else {
                                    name = Ext.getCmp('namanon').getValue();
                                    farmer_id = null;
                                };
                                if (e.record.data.StaffID == '') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please wait...'),
                                        url: m_staff,
                                        method: 'POST',
                                        params: {
                                            CoopID: Ext.getCmp('CoopID').getValue(),
                                            Status: e.record.data.Status,
                                            FarmerID: farmer_id,
                                            Position: e.record.data.Position,
                                            StaffName: name,
                                            Phone: e.record.data.Phone,
                                            Email: e.record.data.Email,
                                            StaffBirthday: e.record.data.StaffBirthday,
                                            StaffGender: Ext.getCmp('StaffGender').getValue(),
                                            StaffStatus: e.record.data.StaffStatus,
                                            PaymentStatus: e.record.data.PaymentStatus,
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_staff.load({
                                                        params: {
                                                            id: Ext.getCmp('CoopID').getValue()
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                } else {
                                    Ext.MessageBox.confirm('Message', lang('Update data staff ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please wait...'),
                                                url: m_staff,
                                                method: 'PUT',
                                                params: {
                                                    StaffID: e.record.data.StaffID,
                                                    CoopID: Ext.getCmp('CoopID').getValue(),
                                                    Status: e.record.data.Status,
                                                    // FarmerID:       Ext.getCmp('namanon').getValue(),
                                                    FarmerID: farmer_id,
                                                    Position: e.record.data.Position,
                                                    StaffName: name,
                                                    Phone: e.record.data.Phone,
                                                    Email: e.record.data.Email,
                                                    StaffBirthday: e.record.data.StaffBirthday,
                                                    StaffGender: Ext.getCmp('StaffGender').getValue(),
                                                    StaffStatus: e.record.data.StaffStatus,
                                                    PaymentStatus: e.record.data.PaymentStatus,
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_staff.load({
                                                                params: {
                                                                    id: Ext.getCmp('CoopID').getValue()
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        }
                    }]
                }, {
                    xtype: 'panel',
                    autoScroll: false,
                    id: 'panel_board',
                    //                        disabled: true,
                    title: lang('Board'),
                    padding: 5,
                    style: 'border:2px solid #D6EDA4',
                    listeners: {
                        activate: function(selModel, Cmp) {
                            Ext.getCmp('saveButton').hide();
                            store_board.load({
                                params: {
                                    id: Ext.getCmp('CoopID').getValue()
                                }
                            });
                        }
                    },
                    items: [{
                        xtype: 'gridpanel',
                        autoScroll: false,
                        id: 'grid_board',
                        height: 500,
                        store: store_board,
                        width: '100%',
                        loadMask: true,
                        selType: 'rowmodel',
                        dockedItems: [{
                            xtype: 'toolbar',
                            items: [{
                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                text: lang('Add'),
                                //                                                cls: m_act_save,
                                scope: this,
                                handler: function() {
                                    RowEditingBoard.cancelEdit();
                                    var r = Ext.create('board.Model', {
                                        BoardID: '',
                                        CoopID: '',
                                        FarmerID: '',
                                        StaffName: '',
                                        Position: '',
                                        Phone: '',
                                        Email: '',
                                        BoardBirthday: '',
                                        BoardGender: ''
                                    });
                                    store_board.insert(0, r);
                                    RowEditingBoard.startEdit(0, 0);
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                //                                                cls: m_act_save,
                                text: lang('Edit'),
                                scope: this,
                                handler: function() {
                                    RowEditingBoard.cancelEdit();
                                    var sm = Ext.getCmp('grid_board').getSelectionModel().getSelection();
                                    RowEditingBoard.startEdit(sm[0].index, 0);
                                    // gs_edit()
                                }
                            }, {
                                itemId: 'remove',
                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                text: lang('Hapus'),
                                scope: this,
                                handler: function() {
                                    var smb = Ext.getCmp('grid_board').getSelectionModel().getSelection()[0];
                                    RowEditingBoard.cancelEdit();
                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please Wait'),
                                                url: m_board,
                                                method: 'DELETE',
                                                params: {
                                                    id: smb.raw.BoardID
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            store_board.load({
                                                                params: {
                                                                    id: Ext.getCmp('CoopID').getValue()
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        }
                                    });
                                }
                            }]
                        }],
                        columns: [{
                            text: lang('Nama'),
                            id: 'lfarmerBoard',
                            dataIndex: 'BoardName',
                            width: '20%',
                            editor: {
                                xtype: 'combo',
                                store: ds,
                                id: 'lfarmeridBoard',
                                displayField: 'name',
                                typeAhead: false,
                                hideLabel: true,
                                hideTrigger: true,
                                anchor: '100%',
                                listConfig: {
                                    loadingText: 'Searching...',
                                    emptyText: lang('No matching farmer found.'),
                                    getInnerTpl: function() {
                                        return '<div class="search-item">' +
                                            '{id} - {name}' +
                                            '{excerpt}' +
                                            '</div>';
                                    }
                                },
                                pageSize: 10,
                                listeners: {
                                    select: function(combo, selection) {
                                        var post = selection[0];
                                        if (post) {
                                            Ext.getCmp('lfarmeridBoard').setValue('[' + post.get('id') + '] ' + post.get('name'))
                                            Ext.getCmp('namanon').setValue(post.get('id'))
                                            Ext.getCmp('lhpBoard').setValue(post.get('handphone'))
                                            Ext.getCmp('lhpBoard').setReadOnly(true)
                                                //Ext.getCmp('lemail').setValue(post.get('email'))
                                                //Ext.getCmp('lemail').setReadOnly(true)
                                            Ext.getCmp('BoardGender').setValue(post.get('kelamin'))
                                            Ext.getCmp('BoardGender').setReadOnly(true)
                                            Ext.getCmp('lbirthdayBoard').setValue(post.get('birthdate'))
                                            Ext.getCmp('lbirthdayBoard').setReadOnly(true)
                                        }
                                    }
                                }
                            }
                        }, {
                            text: lang('Nama'),
                            flex: 1,
                            id: 'lnonBoard',
                            dataIndex: 'BoardName',
                            // width: '20%',
                            hidden: true,
                            editor: {
                                xtype: 'textfield',
                                id: 'namanonBoard',
                                name: 'namanon',
                            }
                        }, {
                            text: lang('Board Status'),
                            dataIndex: 'BoardStatus',
                            width: '10%',
                            editor: {
                                xtype: 'combo',
                                store: cstaffstatus,
                                id: 'BoardStatus',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'label'
                            }
                        }, {
                            text: lang('Position'),
                            dataIndex: 'Position',
                            width: '15%',
                            editor: {
                                xtype: 'combo',
                                store: bPosition,
                                id: 'PositionBoard',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'label'
                            }
                        }, {
                            text: lang('Handphone'),
                            dataIndex: 'Phone',
                            width: '10%',
                            editor: {
                                id: 'lhp',
                                xtype: 'textfield'
                            }
                        }, {
                            text: lang('Email'),
                            dataIndex: 'Email',
                            width: '10%',
                            editor: {
                                xtype: 'textfield',
                                id: 'lemailBoard',
                                allowBlank: false
                            }
                        }, {
                            text: lang('Birthday'),
                            dataIndex: 'BoardBirthday',
                            width: '10%',
                            editor: {
                                xtype: 'datefield',
                                id: 'lbirthdayBoard',
                                format: 'Y-m-d'
                            }
                        }, {
                            text: lang('Kelamin'),
                            dataIndex: 'BoardGender',
                            width: '10%',
                            editor: {
                                xtype: 'combo',
                                store: ckelamin,
                                queryMode: 'local',
                                id: 'BoardGender',
                                displayField: 'label',
                                valueField: 'id'
                            }
                        }],
                        plugins: [RowEditingBoard],
                        listeners: {
                            itemdblclick: function(dv, record, item, index, e) {
                                // gs_edit()
                            },
                            'canceledit': function(editor, e, eOpts) {
                                store_board.load({
                                    params: {
                                        id: Ext.getCmp('CoopID').getValue()
                                    }
                                });
                            },
                            'edit': function(editor, e) {
                                var name = e.record.data.BoardName;
                                if (e.record.data.BoardID == '') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please wait...'),
                                        url: m_board,
                                        method: 'POST',
                                        params: {
                                            CoopID: Ext.getCmp('CoopID').getValue(),
                                            Status: e.record.data.Status,
                                            // FarmerID: farmer_id,
                                            Position: e.record.data.Position,
                                            BoardName: name,
                                            Phone: e.record.data.Phone,
                                            Email: e.record.data.Email,
                                            BoardBirthday: e.record.data.BoardBirthday,
                                            BoardGender: Ext.getCmp('BoardGender').getValue(),
                                            BoardStatus: e.record.data.BoardStatus
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_board.load({
                                                        params: {
                                                            id: Ext.getCmp('CoopID').getValue()
                                                        }
                                                    });
                                                    store_board.getAt(rowIndex).commit();
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                } else {
                                    Ext.MessageBox.confirm('Message', lang('Update data board ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please wait...'),
                                                url: m_board,
                                                method: 'PUT',
                                                params: {
                                                    BoardID: e.record.data.BoardID,
                                                    CoopID: Ext.getCmp('CoopID').getValue(),
                                                    Status: e.record.data.Status,
                                                    // FarmerID:       Ext.getCmp('namanon').getValue(),
                                                    // FarmerID: farmer_id,
                                                    Position: e.record.data.Position,
                                                    BoardName: name,
                                                    Phone: e.record.data.Phone,
                                                    Email: e.record.data.Email,
                                                    BoardBirthday: e.record.data.BoardBirthday,
                                                    BoardGender: Ext.getCmp('BoardGender').getValue(),
                                                    BoardStatus: e.record.data.BoardStatus
                                                        // PaymentStatus: e.record.data.PaymentStatus,
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_board.load({
                                                                params: {
                                                                    id: Ext.getCmp('CoopID').getValue()
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        }
                    }]
                }, {
                    xtype: 'panel',
                    id: 'panel_document',
                    title: lang('Documents'),
                    padding: 5,
                    style: 'border:2px solid #D6EDA4',
                    listeners: {
                        activate: function(selModel, Cmp) {
                            Ext.getCmp('saveButton').hide();
                            storeDoc.load();
                        }
                    },
                    items: [gridDoc]
                }, {
                    xtype: 'panel',
                    id: 'panel_limittrans',
                    title: lang('Transaction Limit'),
                    padding: 5,
                    style: 'border:2px solid #D6EDA4',
                    listeners: {
                        activate: function(selModel, Cmp) {
                            Ext.getCmp('saveButton').hide();
                            storeCoopLimitTrans.load();
                        }
                    },
                    items: [gridCoopLimitTrans]
                }, {
                    xtype: 'panel',
                    id: 'panel_accounting',
                    hidden: true,
                    title: lang('Accounting'),
                    padding: 5,
                    style: 'border:2px solid #D6EDA4',
                    listeners: {
                        activate: function(selModel, Cmp) {
                            Ext.getCmp('saveButton').hide();
                        }
                    },
                    items: []
                }, {
                    xtype: 'panel',
                    id: 'panel_areamember',
                    title: lang('Area Penerimaan Anggota'),
                    padding: 5,
                    style: 'border:2px solid #D6EDA4',
                    listeners: {
                        activate: function(selModel, Cmp) {
                            storeAreaMember.load();
                            mc_Provinsi.load();
                        }
                    },
                    items: [gridAreaMember]
                }, {
                    xtype: 'panel',
                    id: 'panel_exportimport',
                    title: lang('Export/Import'),
                    padding: 11,
                    style: 'border:2px solid #D6EDA4',
                    listeners: {
                        activate: function(selModel, Cmp) {
                            // storeAreaMember.load();
                            // mc_Provinsi.load();
                        }
                    },
                    items: [{
                            xtype: 'button',
                            scale: 'large',
                            ui: 's-button',
                            margin: '7px 0px 0px 6px',
                            text: lang('Export'),
                            handler: function() {
                                // Ext.Ajax.request({
                                //     url: m_api + '/cooperatives/sync_export',
                                //     method: 'GET',
                                //     success: function(data, action) {
                                //         console.log(data)
                                //             // var d = Ext.decode(form.responseText);
                                //             // Ext.MessageBox.alert('Success', lang('Data saved.'));
                                //     },
                                //     failure: function(form, action) {
                                //         Ext.Msg.alert('Failed', action.result ? action.result.message : 'No response');
                                //     }
                                // });
                                var win = window.open(m_api + '/cooperatives/sync_export', '_blank');
                                // win.focus();
                            }
                        }, {
                            xtype: 'button',
                            scale: 'large',
                            ui: 's-button',
                            margin: '7px 0px 0px 6px',
                            text: lang('Import'),
                            handler: function() {
                                displayFormWindowExportImportServer();
                            }
                        },
                        /*{
                                                xtype: 'button',
                                                margin: '0px 0px 0px 6px',
                                                text: lang('Import Feedback'),
                                                handler: function() {
                                                    displayFormWindowExportImportLocal();
                                                }
                                            }*/
                    ]

                }]
            }],
            buttons: [{
                id: 'saveButton',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = this.up('form').getForm();
                    //                    var form = Ext.getCmp('dataForm').getForm();
                    //                    console.log(form)
                    var urle;
                    if (Ext.getCmp('CoopID').getValue() != '')
                        urle = m_crud + 'u';
                    else
                        urle = m_crud;
                    //                     console.log(urle)
                    //                    form.submit({
                    //                        url: urle,
                    //                        waitMsg: lang('Sending data...'),
                    //                        success: function (fp, o) {
                    //                            Ext.MessageBox.alert('Success', 'Data saved.');
                    //                        },
                    //                        failure: function(form, action) {
                    //                                     Ext.Msg.alert("Load failed", 's');
                    //                        }
                    //                    });
                    //                    win.hide(this, function () {
                    //                        store.load();
                    //                    });

                    Ext.Ajax.request({
                        url: urle,
                        method: 'POST',
                        params: form.getValues(),
                        success: function(form, action) {
                            var d = Ext.decode(form.responseText);
                            Ext.MessageBox.alert('Success', lang('Data saved.'));
                        },
                        failure: function(form, action) {
                            Ext.Msg.alert('Failed', action.result ? action.result.message : 'No response');
                        }
                    });
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                hidden: true,
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    win.hide();
                }
            }],
            listeners: {
                render: {
                    scope: this,
                    fn: function(grid) {
                        //                       displayFormWindow();
                        //                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                        Ext.Ajax.request({
                            url: m_crud,
                            method: 'GET',
                            //                                params: {id: sm.get('CoopID')},
                            params: {
                                id: m_param
                            },
                            success: function(fp, o) {
                                var r = Ext.decode(fp.responseText);
                                //                                    Ext.getCmp('CoopID').setValue(r.data.CoopID);
                                fset(r.data[0])
                            }
                        });

                        console.log('rendered')
                    }
                }
            }
        });

        //    var win = Ext.create('widget.window', {
        //        title: lang('Organisasi Petani'),
        //        id: 'win',
        //        closable: true,
        ////        renderTo: 'ext-content',
        //        modal: true,
        //        closeAction: 'show',
        //        autoScroll: true,
        //        width: '90%',
        ////        height: '90%',
        //        layout: {
        //            type: 'fit'
        //        },
        //        items: [DataForm]
        //    });


        //function displayFormWindow(){
        //        if(!win.isVisible()){
        //            DataForm.getForm().reset();
        //            win.show();
        //        } else {
        //            win.hide(this, function() {});
        //            win.toFront();
        //        }
        //        Ext.getCmp('Provinsi').setValue(m_param);
        //    }

        //    var grid = Ext.create('Ext.grid.Panel', {
        //        store: store,
        //        width: '100%',
        //        minHeight: 250,
        //        id: 'grid',
        //        style: 'border:1px solid #CCC;',
        ////        renderTo: 'ext-content',
        //        loadMask: true,
        //        selType: 'rowmodel',
        //        listeners: {
        //            itemdblclick: function (dv, record, item, index, e) {
        //                displayFormWindow();
        //                var sm = record;
        //                Ext.Ajax.request({
        //                    url: m_crud,
        //                    method: 'GET',
        //                    params: {id: sm.get('CoopID')},
        //                    success: function (fp, o) {
        //                        var r = Ext.decode(fp.responseText);
        //                        Ext.getCmp('CoopID').setValue(sm.get('CoopID'));
        //                        fset(r.data[0])
        //                    }
        //                });
        //            }
        //        },
        //        dockedItems: [{
        //                xtype: 'pagingtoolbar',
        //                store: store, // same store GridPanel is using
        //                dock: 'bottom',
        //                displayInfo: true
        //            }, {
        //                xtype: 'toolbar',
        //                items: [{
        //                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
        //                        text: lang('Add'),
        //                        scope: this,
        //                        handler: function () {
        ////                            Ext.getCmp('panel_staff').disable()
        //                            displayFormWindow();
        //                            Ext.getCmp('iphoto').setSrc('');
        //                            Ext.getCmp('Kabupaten').setValue('');
        //                            Ext.getCmp('Kecamatan').disable()
        //                            Ext.getCmp('Desa').disable()
        //                            Ext.getCmp('Provinsi').setReadOnly(false);
        //                        },
        ////                        cls: m_act_add
        //                    }, {
        //                        icon: varjs.config.base_url + 'images/icons/new/update.png',
        //                        text: lang('Update'),
        //                        scope: this,
        //                        handler: function () {
        //                            displayFormWindow();
        //                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
        //                            Ext.Ajax.request({
        //                                url: m_crud,
        //                                method: 'GET',
        //                                params: {id: sm.get('CoopID')},
        //                                success: function (fp, o) {
        //                                    var r = Ext.decode(fp.responseText);
        //                                    Ext.getCmp('CoopID').setValue(sm.get('CoopID'));
        //                                    fset(r.data[0])
        //                                }
        //                            });
        //                        },
        ////                        cls: m_act_update
        //                    }, {
        //                        itemId: 'remove',
        //                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
        ////                        cls: m_act_delete,
        //                        text: lang('Hapus'),
        //                        scope: this,
        //                        handler: function () {
        //                            var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
        //                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
        //                                if (btn == 'yes') {
        //                                    Ext.Ajax.request({
        //                                        waitMsg: lang('Please Wait'),
        //                                        url: m_crud,
        //                                        method: 'DELETE',
        //                                        params: {id: smb.raw.CoopID},
        //                                        success: function (response, opts) {
        //                                            var obj = Ext.decode(response.responseText);
        //                                            switch (obj.success) {
        //                                                case true:
        //                                                    store.load();
        //                                                    break;
        //                                                default:
        //                                                    Ext.MessageBox.alert('Warning', obj.message);
        //                                                    break;
        //                                            }
        //                                        },
        //                                        failure: function (response, opts) {
        //                                            var obj = Ext.decode(response.responseText);
        //                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
        //                                        }
        //                                    });
        //                                }
        //                            });
        //                        }
        //                    }]
        //            }],
        //        columns: [{
        //                text: lang('ID'),
        //                dataIndex: 'id',
        //                hidden: true
        //            }, {
        //                text: lang('No'),
        //                xtype: 'rownumberer',
        //                width: '5%'
        //            }, {
        //                text: lang('Nama'),
        //                width: '20%',
        //                dataIndex: 'CoopName'
        //            }, {
        //                text: lang('Phone'),
        //                width: '10%',
        //                dataIndex: 'Phone'
        //            }, {
        //                text: lang('Email'),
        //                width: '20%',
        //                dataIndex: 'Email'
        //            }, {
        //                text: lang('Tahun Terbentuk'),
        //                width: '20%',
        //                dataIndex: 'TahunTerbentuk'
        //            }, {
        //                text: lang('Status'),
        //                width: '10%',
        //                dataIndex: 'Status'
        //            }, {
        //                text: lang('District'),
        //                width: '15%',
        //                dataIndex: 'District'
        //            }]
        //    });



        function fset(r) {
            //    console.log(r)
            Ext.getCmp('panel_staff').enable();
            store_staff.load({
                params: {
                    id: r.CoopID
                }
            });
            // store_board.load({
            //   params: {
            //           id: r.CoopID
            //     }
            // })

            Ext.getCmp('CoopID').setValue(r.CoopID);
            Ext.getCmp('CoopName').setValue(r.CoopName);
            Ext.getCmp('Address').setValue(r.Address);
            Ext.getCmp('Phone').setValue(r.Phone);
            Ext.getCmp('Email').setValue(r.Email);
            Ext.getCmp('LimitTransaction').setValue(r.LimitTransaction);
            if (r.VillageID != '') {
                Ext.getCmp('Provinsi').setValue(r.ProvinceID);
                Ext.getCmp('Kabupaten').setValue(r.District);
                Ext.getCmp('Kecamatan').setValue(r.SubDistrict);
                Ext.getCmp('Desa').setValue(r.VillageID);
            }
            //        console.log(r)
            if (r.Status !== '') {
                if (r.Status == 'Koperasi') Ext.getCmp('Status').setValue(true);
                if (r.Status == 'Gapoktan') Ext.getCmp('Status2').setValue(true);
                if (r.Status == 'KUR') Ext.getCmp('Status3').setValue(true);
                if (r.Status == 'Tidak Berbadan Hukum') Ext.getCmp('Status4').setValue(true);
            }
            Ext.getCmp('TahunTerbentuk').setValue(r.TahunTerbentuk);
            Ext.getCmp('Latitude').setValue(r.Latitude);
            Ext.getCmp('Longitude').setValue(r.Longitude);

            if (r.AutoJournal == null) {
                Ext.getCmp('AutoJournal').setValue({
                    AutoJournal: 1
                });
            } else {
                Ext.getCmp('AutoJournal').setValue({
                    AutoJournal: 0
                });
            }

            //Ext.getCmp('Photo_old').setValue(r.Photo);
            //Ext.getCmp('iphoto').setSrc(m_photo+'/'+r.Photo);
        }



    });