Ext.require(['Ext.ux.RowExpander']);

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();


    var takePhotoWindow = Ext.create('widget.window', {
        id: 'takePhotoWindow',
        title: 'Take a Signature',
        closable: true,
        closeAction: 'hide',
        autoWidth: true,
        autoHeight: true,
        layout: 'fit',
        border: false,
        items: [
            Ext.create('Ext.form.Panel', {
                width: 700,
                height: 490,
                // url: m_api+'member/import_member',
                bodyStyle: 'padding:5px',
                labelAlign: 'top',
                autoScroll: true,
                fieldDefaults: {
                    msgTarget: 'side',
                    blankText: 'Tidak Boleh Kosong',
                    labelWidth: 150
                        // width: 400
                },
                items: [
                    Ext.panel.Panel({
                        html: "<video id='video' width='640' height='480' autoplay></video><canvas id='canvas' width='640' height='480'></canvas>"
                    })
                ],
                buttons: [
                    '->', {
                        text: 'Capture',
                        handler: function() {
                            var canvas = document.getElementById("canvas"),
                                context = canvas.getContext("2d"),
                                video = document.getElementById("video"),
                                videoObj = {
                                    "video": true
                                },
                                errBack = function(error) {
                                    console.log("Video capture error: ", error.code);
                                };

                            document.getElementById('video').style.display = 'none';
                            document.getElementById('canvas').style.display = 'block';
                            context.drawImage(video, 0, 0, 640, 480);

                            var image = Ext.getCmp('img-sigi-add-member');

                            var dataURL = canvas.toDataURL();

                            $.ajax({
                                type: "POST",
                                url: m_api + "member/tmp_cam",
                                data: {
                                    imgBase64: dataURL
                                }
                            }).done(function(o) {
                                console.log(0);
                                image.setSrc(m_api + 'images/coop/members/' + o.photo);
                                Ext.getCmp('hidden-add-member-sigi-name').setValue(o.imgname);
                            });

                            Ext.getCmp('takePhotoWindow').hide();

                        }
                    }, {
                        text: 'Cancel',
                        handler: function() {
                            Ext.getCmp('takePhotoWindow').hide();
                        }
                    }
                ]
            })
        ]
    });

    var takeSignWindow = Ext.create('widget.window', {
        id: 'takeSignWindow',
        title: 'Take a Photo',
        closable: true,
        closeAction: 'hide',
        autoWidth: true,
        autoHeight: true,
        layout: 'fit',
        border: false,
        items: [
            Ext.create('Ext.form.Panel', {
                width: 650,
                height: 490,
                // url: m_api+'member/import_member',
                bodyStyle: 'padding:5px',
                labelAlign: 'top',
                autoScroll: true,
                fieldDefaults: {
                    msgTarget: 'side',
                    blankText: 'Tidak Boleh Kosong',
                    labelWidth: 150
                        // width: 400
                },
                items: [
                    Ext.panel.Panel({
                        html: "<video id='videophoto' width='640' height='480' autoplay></video><canvas id='canvasphoto' width='640' height='480'></canvas>"
                    })
                ],
                buttons: [
                    '->', {
                        text: 'Capture',
                        handler: function() {
                            var canvas = document.getElementById("canvasphoto"),
                                context = canvas.getContext("2d"),
                                video = document.getElementById("videophoto"),
                                videoObj = {
                                    "video": true
                                },
                                errBack = function(error) {
                                    console.log("Video capture error: ", error.code);
                                };

                            document.getElementById('videophoto').style.display = 'none';
                            document.getElementById('canvasphoto').style.display = 'block';
                            context.drawImage(video, 0, 0, 640, 480);

                            // var canvas = document.getElementById('canvas');
                            var dataURL = canvas.toDataURL();
                            // console.log(dataURL);

                            var image = Ext.getCmp('img-photo-add-member');

                            $.ajax({
                                type: "POST",
                                url: m_api + "member/tmp_cam",
                                data: {
                                    imgBase64: dataURL
                                }
                            }).done(function(o) {
                                // console.log(0);
                                image.setSrc(m_api + 'images/coop/members/' + o.photo);
                                Ext.getCmp('hidden-add-member-photo-name').setValue(o.imgname);
                            });

                            Ext.getCmp('takeSignWindow').hide();

                            video.src = "";

                        }
                    }, {
                        text: 'Cancel',
                        handler: function() {
                            Ext.getCmp('takeSignWindow').hide();
                        }
                    }
                ]
            })
        ]
    });

    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'farmerID', 'remark', 'primaryNo', 'name', 'Village', 'status', 'registeredDate', 'saldoSimpok', 'saldoWajib', 'saldoSuka', 'uangPangkal', 'GroupName', 'remark'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + "s",
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        // listeners: {
        // beforeload: function(store, operation) {
        //store.proxy.extraParams.key = Ext.getCmp('farmerKey').getValue();
        // }
        // }
    });

    var store_farmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID', 'FarmerName', 'VillageID', 'Village', 'SubDistrict', 'District', 'GroupName', 'isCertified'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_farmer + "s",
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var store_saving = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['memberTransactionNumber', 'memberTransactionType', 'memberTransactionDate', 'memberTransactionAmount', 'savingTypeName', 'memberSavingNo'],
        autoLoad: false,
        pageSize: 50,
        groupField: 'memberSavingNo',
        sorters: [{
            property: 'memberSavingNo',
            direction: 'ASC'
        }],
        proxy: {
            type: 'ajax',
            url: m_saving + "s",
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var store_savingmember = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['memberSavingID', 'memberID', 'memberSavingNo', 'savingTypeMinAmount', 'savingTypeName'],
        autoLoad: false,
        pageSize: 50,
        sorters: [{
            property: 'memberSavingID',
            direction: 'ASC'
        }],
        proxy: {
            type: 'ajax',
            url: m_savingmember + "s",
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var store_loan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['LoanInstallmentID', 'LoanInstallmentValue', 'LoanInstallmentPinalty', 'LoanInstallmentTotal', 'LoanInstallmentPaidDate', 'LoanTypeName', 'LoanInstallmentPinalty', 'LoanInstallmentInterestPercent', 'LoanInstallmentInterest'],
        autoLoad: false,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_loan + "s",
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });


    function displayFormWindow() {
        if (!win.isVisible()) {
            // Ext.getCmp('iphoto').setSrc();
            // Ext.getCmp('isignature').setSrc();
            DataForm.getForm().reset();
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }

    function displayDetailWindow() {
        if (!winDetail.isVisible()) {
            winDetail.show();
        } else {
            winDetail.hide(this, function() {});
            winDetail.toFront();
        }

    }

    function displaySavingFormWindow() {
        if (!winSaving.isVisible()) {
            DataFormSaving.getForm().reset();
            winSaving.show();
        } else {
            winSaving.hide(this, function() {});
            winSaving.toFront();
        }
    }


    function displayFarmerWindow() {
        store_farmer.load({
            params: {
                key: Ext.getCmp('farmerKey').getValue()
            }
        });
        if (!winFarmer.isVisible()) {
            winFarmer.show();
        } else {
            winFarmer.hide(this, function() {});
            winFarmer.toFront();
        }
    }

    function resetFarmerData() {

    }

    var mc_membertype = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_membertype,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_district,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_subdistrict = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_subdistrict,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_village = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_village,
            reader: {
                type: 'json',
                root: 'data'
            }
        },

    });
    var mc_identity = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_identity,
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

    var mc_Provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Provinsi,
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
            extraParams: {
                prov: m_param
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
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
        autoLoad: false,
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

    var mc_GroupID = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        autoLoad: false,
        fields: ['id', 'label'],
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_GroupID,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'totalCount'
            }
        }
    });

    var store_garden_status = Ext.create('Ext.data.Store', {
        model: 'gardenStatus.Model',
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_garden_status',
            reader: {
                type: 'json',
                // root: 'data',
                // totalProperty: 'total'
            }
        }
    });

    var gardenstatus = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
            // {
            //     "id": "1",
            //     "label": lang("Died")
            // },
            {
                "id": "2",
                "label": lang("Moved/left the area")
            }, {
                "id": "3",
                "label": lang("Switched to other crop")
            }, {
                "id": "4",
                "label": lang("Sold the land")
            }, {
                "id": "5",
                "label": lang("Gave the land to family member")
            }, {
                "id": "6",
                "label": lang("Force Major")
            },
        ]
    });

    var gardenRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'gardenRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    Ext.define('other_land.Model', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID', 'Commodity', 'GardenHa'],
    });

    var store_other_land = Ext.create('Ext.data.Store', {
        model: 'other_land.Model',
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_other_lands,
            reader: {
                type: 'json',
                // root: 'data',
                // totalProperty: 'total'
            }
        }
    });

    var commodity = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": lang("Jagung")
        }, {
            "id": "2",
            "label": lang("Sawit")
        }, {
            "id": "3",
            "label": lang("Karet")
        }, {
            "id": "4",
            "label": lang("Cengkeh")
        }, {
            "id": "5",
            "label": lang("Padi")
        }, {
            "id": "6",
            "label": lang("Kosong")
        }, {
            "id": "7",
            "label": lang("Dll")
        }]
    });

    var StatusMemberStore = Ext.create('Ext.data.Store', {
        fields: ['StatusMemberID', 'StatusMemberName'],
        data: [{
                "StatusMemberID": "1",
                "StatusMemberName": "Active"
            },
            //  {
            //     "StatusMemberID": "2",
            //     "StatusMemberName": "Inactive"
            // }
            {
                "StatusMemberID": "3",
                "StatusMemberName": "Suspended"
            }
            // , {
            //     "StatusMemberID": "4",
            //     "StatusMemberName": "Candidate"
            // }
        ]
    });

    var StatusFilterMemberStore = Ext.create('Ext.data.Store', {
        fields: ['StatusMemberID', 'StatusMemberName'],
        data: [{
            "StatusMemberID": "1",
            "StatusMemberName": "Active"
        }, {
            "StatusMemberID": "2",
            "StatusMemberName": "Inactive"
        }, {
            "StatusMemberID": "3",
            "StatusMemberName": "Suspended"
        }, {
            "StatusMemberID": "4",
            "StatusMemberName": "Candidate"
        }]
    });

    var storeSavingTypeList = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['savingTypeName', 'savingTypeMinAmount', 'savingTypeMinTrans', 'savingTypeInterestRate', 'savingRemark', 'status'],
        //    autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_savingtype,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var winUpdateStatus = Ext.create('widget.window', {
        title: 'Update Status',
        id: 'win-member-update-status',
        modal: true,
        // width: 430,
        // closable:true,
        closeAction: 'hide',
        autoWidth: true,
        layout: 'fit',
        items: Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            autoScroll: true,
            id: 'frm-edit-member-update-status',
            fieldDefaults: {
                labelAlign: 'left',
                // labelWidth: 190
            },
            listeners: {
                'add': function(form) {

                }
            },
            items: [{
                id: 'updateStatus',
                fieldLabel: 'Pilih Status',
                name: 'updateStatus',
                xtype: 'combo',
                emptyText: '-- Set Status --',
                multiSelect: false,
                store: StatusMemberStore,
                displayField: 'StatusMemberName',
                valueField: 'StatusMemberID',
                queryMode: 'local'
            }],
            buttons: [{
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {

                    var status = Ext.getCmp('updateStatus').getValue();

                    if (status == null) {
                        Ext.MessageBox.alert('Warning', 'Pilih jenis status terlebih dahulu');
                        return false;
                    }

                    var smb = Ext.getCmp('grid-member').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm('Message', 'Apakah anda mau mengubah data ini ?', function(btn) {
                        if (btn == 'yes' && status != '') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + 'member/update_status',
                                method: 'POST',
                                params: {
                                    id: smb.raw.id,
                                    status: status
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
                                    winUpdateStatus.hide();
                                },
                                failure: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error', 'Failed to execute. Please select member and status');
                                    winUpdateStatus.hide();
                                }
                            });
                        }
                    });

                }
            }, {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                // hidden:true,
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winUpdateStatus.hide();
                }
            }]
        })
    });

    function displayUpdateStatusWindow() {
        if (!winUpdateStatus.isVisible()) {
            winUpdateStatus.show();
        } else {
            winUpdateStatus.hide(this, function() {});
            winUpdateStatus.toFront();
        }
    }

    Ext.define('GridSavingTypeList', {
        itemId: 'GridSavingTypeList',
        id: 'GridSavingTypeList',
        extend: 'Ext.grid.Panel',
        alias: 'widget.GridSavingTypeList',
        store: storeSavingTypeList,
        loadMask: true,
        columns: [{
                text: 'Select',
                width: 65,
                xtype: 'actioncolumn',
                tooltip: 'Select',
                align: 'center',
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                handler: function(grid, rowIndex, colIndex, actionItem, event, selectedRecord, row) {
                    var FinStatusFS = Ext.getCmp('FinStatusFS');
                    var idk = Ext.id();
                    //                    alert(idk);
                    var addField = {
                        id: idk,
                        layout: {
                            type: 'table',
                            columns: 3,
                            tableAttrs: {
                                style: {
                                    width: '70%',
                                    height: '100%',
                                    padding: 5,
                                    align: 'center'
                                }
                            }
                        },
                        defaults: {
                            bodyStyle: 'padding:5 5 5 5',
                            align: 'center',
                        },
                        //                        fieldDefaults:{
                        //                             labelWidth: 130,
                        //                             width:460
                        //                        },
                        items: [{
                            html: selectedRecord.get('savingTypeName'),
                            width: 130,
                            align: 'center'
                        }, {
                            xtype: 'textfield',
                            fieldStyle: 'text-align: right;',
                            readOnly: true
                        }, {
                            xtype: 'button',
                            width: 75,
                            text: lang('Deactivate'),
                            handler: function() {

                            }
                        }]
                    }

                    var a = new Ext.form.TextField({
                        padding: 5,
                        id: Ext.id(),
                        width: 100,
                        fieldLabel: "A"
                    });


                    Ext.Ajax.request({
                        url: m_save_member_saving,
                        method: 'POST',
                        params: {
                            memberID: Ext.getCmp('MemberID').getValue(),
                            savingTypeID: selectedRecord.get('id'),
                        },
                        success: function(form, action) {
                            var d = Ext.decode(form.responseText);
                            if (d.success) {
                                //                                FinStatusFS.add(addField);
                                insertFinStatus();
                            }
                            //                            FinStatusFS.add(addField);
                        },
                        failure: function(form, action) {
                            Ext.Msg.alert("Failed", action.result.errorMessage);
                        }
                    });

                    //                    fDataAct.doLayout();

                    //                    Ext.getCmp('idpelamar_fPergerakanP_from').setValue(selectedRecord.get('idpelamar'));

                    Ext.getCmp('wSavingTypePopup').hide();
                }
            }, {
                text: 'ID',
                dataIndex: 'id',
                hidden: true
            }, {
                text: lang('Saving Type Name'),
                flex: 1,
                width: '25%',
                dataIndex: 'savingTypeName'
            }, {
                text: lang('Min Payment'),
                xtype: 'numbercolumn',
                align: 'right',
                width: '15%',
                dataIndex: 'savingTypeMinAmount'
            }, {
                text: lang('Min Transaction'),
                xtype: 'numbercolumn',
                align: 'right',
                width: '15%',
                dataIndex: 'savingTypeMinTrans'
            }, {
                text: lang('Interest Rate'),
                align: 'right',
                width: '15%',
                dataIndex: 'savingTypeInterestRate'
            }, {
                text: lang('Status'),
                width: 100,
                dataIndex: 'status'
            }
            //            {
            //                text: lang('Remark'),
            //                width: '25%',
            //                dataIndex: 'savingRemark'
            //            }
        ],
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: []
        }, {
            xtype: 'pagingtoolbar',
            store: storeSavingTypeList, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
                // pageSize:20
        }]
    });

    var wSavingTypePopup = Ext.create('widget.window', {
        id: 'wSavingTypePopup',
        title: 'Choose Saving Type',
        header: {
            titlePosition: 2,
            titleAlign: 'center'
        },
        closable: true,
        closeAction: 'hide',
        //    autoWidth: true,
        width: 770,
        height: 330,
        layout: 'fit',
        border: false,
        items: [{
            xtype: 'GridSavingTypeList'
        }]
    });


    //////////////////WINDOW IMPORT
    var formImportMember = Ext.create('Ext.form.Panel', {
        id: 'formImportMember',
        width: 460,
        height: 190,
        url: m_api + 'member/import_member',
        bodyStyle: 'padding:5px',
        labelAlign: 'top',
        autoScroll: true,
        fieldDefaults: {
            msgTarget: 'side',
            blankText: 'Tidak Boleh Kosong',
            labelWidth: 150
                // width: 400
        },
        items: [{
                xtype: 'filefield',
                fieldLabel: 'File xlsx',
                name: 'filexlsx',
                // id: 'filexlsxImportPerencanaanXlsx',
                anchor: '100%'
            }, {
                xtype: 'button',
                text: 'Download file template',
                handler: function() {
                    window.location = m_api + "files/template-import-member.xlsx";
                }
            },
            Ext.panel.Panel({
                // title:'Informasi',
                html: '<br>Petunjuk Import Data Member:<br><li>Isi sesuai urutan kolom yang telah disediakan</li><li>Format tangal dd.mm.yyy (tanggal.bulan.tahun). Contoh 01.05.2015</li>'
            })
        ],
        buttons: [
            '->', {
                text: 'Cancel',
                handler: function() {
                    var win = Ext.getCmp('winImportMember');
                    Ext.getCmp('formImportMember').getForm().reset();
                    win.hide();
                }
            }, {
                text: 'Import',
                handler: function() {
                    var msg = Ext.MessageBox.wait('Sedang memproses...');
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        form.submit({
                            // params: {idunit:Ext.getCmp('idunitKehadiran').getValue()},
                            success: function(form, action) {
                                // msg.hide();
                                Ext.getCmp('winImportMember').hide();
                                Ext.getCmp('formImportMember').getForm().reset();
                                Ext.Msg.alert('Import Data Member', action.result.message);
                                store.load();
                            },
                            failure: function(form, action) {
                                Ext.Msg.alert('Import Data Gagal', action.result ? action.result.message : 'No response');
                                // msg.hide();
                                //                            storeGridSetupTax.load();
                            }

                        });
                    } else {
                        Ext.Msg.alert("Error!", "Your form is invalid!");
                    }
                }
            }
        ]
    });

    var winImportMember = Ext.create('widget.window', {
            id: 'winImportMember',
            title: 'Import Member',
            closable: true,
            closeAction: 'hide',
            autoWidth: true,
            autoHeight: true,
            layout: 'fit',
            border: false,
            items: [formImportMember]
        })
        /////////////////END WINDOW IMPORT

    //////////////////WINDOW SAVING PRODUCT
    var formAmountSavingMember = Ext.create('Ext.form.Panel', {
        id: 'formAmountSavingMember',
        // width: 460,
        // height: 140,
        autoWidth: true,
        autoHeight: true,
        url: m_api + 'member/setup_saving_amount',
        bodyStyle: 'padding:5px',
        labelAlign: 'top',
        autoScroll: true,
        fieldDefaults: {
            msgTarget: 'side',
            blankText: 'Tidak Boleh Kosong',
            labelWidth: 150
                // width: 400
        },
        items: [{
            xtype: 'hiddenfield',
            name: 'memberSavingID',
            id: 'memberSavingID_savingSetup'
        }, {
            xtype: 'displayfield',
            fieldLabel: 'Saving Name',
            id: 'SavingNameLabel'
        }, {
            xtype: 'textfield',
            fieldLabel: 'Saving Amount',
            fieldStyle: 'text-align: right;',
            name: 'amountSaving',
            id: 'amountSaving',
        }],
        buttons: [
            '->', {
                text: 'Cancel',
                hidden: true,
                handler: function() {
                    var win = Ext.getCmp('winImportMember');
                    Ext.getCmp('formImportMember').getForm().reset();
                    win.hide();
                }
            }, {
                text: 'Saving',
                handler: function() {
                    var msg = Ext.MessageBox.wait('Processing...');
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        form.submit({
                            // params: {idunit:Ext.getCmp('idunitKehadiran').getValue()},
                            success: function(form, action) {
                                msg.hide();
                                Ext.getCmp('WinAmountSavingMember').hide();
                                // Ext.Msg.alert('Info', action.result.message);
                                store_savingmember.load();
                            },
                            failure: function(form, action) {
                                Ext.Msg.alert('Info', action.result ? action.result.message : 'No response');
                                msg.hide();
                                //                            storeGridSetupTax.load();
                            }

                        });
                    } else {
                        Ext.Msg.alert("Error!", "Your form is invalid!");
                    }
                }
            }
        ]
    });

    var WinAmountSavingMember = Ext.create('widget.window', {
            id: 'WinAmountSavingMember',
            title: 'Saving Amount',
            closable: true,
            closeAction: 'hide',
            autoWidth: true,
            autoHeight: true,
            layout: 'fit',
            border: false,
            items: [formAmountSavingMember]
        })
        /////////////////END WINDOW SAVING PRODUCT

    var otherRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'otherRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var currentTime = new Date();
    var now = currentTime.getFullYear();
    var years = [];
    var yearsb = [];
    var y = now;
    while (y > 1979) {
        years.push([y]);
        y--;
    }
    var y = now;
    while (y > 1989) {
        yearsb.push([y]);
        y--;
    }
    var storeThn = new Ext.data.SimpleStore({
        fields: ['tahun'],
        data: years
    });
    var storeThnb = new Ext.data.SimpleStore({
        fields: ['tahun'],
        data: yearsb
    });

    Ext.define('keluarga.Model', {
        extend: 'Ext.data.Model',
        fields: ['FamilyID', 'FarmerID', 'AnggotaName', 'HubunganKeluarga', 'AnggotaAge', 'AnggotaGender', 'StatusSekolah', 'hubungan', 'kelamin', 'sekolah'],
    });

    var store_keluarga = Ext.create('Ext.data.Store', {
        model: 'keluarga.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_farmerkeluargas,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var hub = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": "Suami/Istri"
        }, {
            "id": "2",
            "label": "Anak"
        }, {
            "id": "3",
            "label": "Lain-lain"
        }]
    });

    var kelamin = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": "Laki-laki"
        }, {
            "id": "2",
            "label": "Perempuan"
        }, ]
    });

    var ya_tidak = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": "Ya"
        }, {
            "id": "2",
            "label": "Tidak"
        }]
    });

    var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'RowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        bodyPadding: 5,
        id: 'dataForm',
        fileUpload: true,
        enctype: 'multipart/form-data',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120
        },
        items: [{
            layout: 'column',
            border: false,
            items: [{
                xtype: 'fieldset',
                title: lang('Data Umum'),
                columnWidth: .55,
                //                        layout: 'form',
                items: [{
                    xtype: 'textfield',
                    hidden: true,
                    id: 'id',
                    name: 'id',
                    inputType: 'hidden'
                }, {
                    id: 'typeID',
                    name: 'typeID',
                    xtype: 'combo',
                    width: 300,
                    emptyText: '-- Select --',
                    fieldLabel: lang('Member Type') + " *",
                    multiSelect: false,
                    store: mc_membertype,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    allowBlank: false,
                    listeners: {
                        'change': function() {
                            if (Ext.getCmp('typeID').getRawValue() != 'Anggota SCPP') {
                                Ext.getCmp('farmer_container').hide();
                            } else {
                                Ext.getCmp('farmer_container').show();
                            }
                        }
                    }
                }, {
                    xtype: 'fieldcontainer',
                    fieldLabel: lang('FarmerID'),
                    id: 'farmer_container',
                    hidden: true,
                    layout: 'hbox',
                    align: 'stretch',
                    bodyStyle: 'padding: 10px',
                    items: [{
                        xtype: 'textfield',
                        id: 'farmerID',
                        name: 'farmerID',
                        readOnly: false
                    }, {
                        icon: varjs.config.base_url + 'images/icons/silk/magnifier.png',
                        text: lang('Search'),
                        xtype: 'button',
                        id: 'isFarmer',
                        margin: 5,
                        listeners: {
                            click: function(cb, nv, ov) {
                                //                                                if (Ext.getCmp('farmerID').getValue() == '') {
                                //                                                    alert('asd');
                                displayFarmerWindow();
                                //                                                } else {
                                //                                                    resetFarmerData();
                                //                                                }
                            }
                        }
                    }]
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('No. Member') + '*',
                    id: 'primaryNo',
                    name: 'primaryNo',
                    allowBlank: false
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Member Name') + " *",
                    width: 400,
                    id: 'name',
                    name: 'name',
                    allowBlank: false
                }, {
                    xtype: 'fieldcontainer',
                    fieldLabel: lang('Identity') + " *",
                    layout: 'hbox',
                    items: [{
                        xtype: 'combo',
                        emptyText: '-- Select --',
                        id: 'identityType',
                        name: 'identityType',
                        multiSelect: false,
                        store: mc_identity,
                        displayField: 'label',
                        valueField: 'id',
                        width: 150,
                        margin: '0 5 0 0',
                        queryMode: 'local',
                        allowBlank: false
                    }, {
                        xtype: 'textfield',
                        id: 'identityNumber',
                        name: 'identityNumber',
                        allowBlank: false
                    }]
                }, {
                    xtype: 'fieldcontainer',
                    fieldLabel: lang('Gender') + " *",
                    defaultType: 'radiofield',
                    defaults: {
                        flex: 1
                    },
                    layout: 'hbox',
                    items: [{
                        boxLabel: lang('Male'),
                        name: 'gender',
                        inputValue: '1',
                        id: 'gender1',
                        allowBlank: false,
                        checked: true,

                    }, {
                        boxLabel: lang('Female'),
                        name: 'gender',
                        inputValue: '2',
                        id: 'gender2',
                        allowBlank: false
                    }]
                }, {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Tempat, Tgl. Lahir' + " *",
                    layout: 'hbox',
                    items: [{
                        xtype: 'textfield',
                        id: 'placeOfBirth',
                        margin: '0 5 0 0',
                        name: 'placeOfBirth'
                    }, {
                        xtype: 'datefield',
                        id: 'dateOfBirth',
                        name: 'dateOfBirth',
                        format: 'Y-m-d',
                        altFormats: 'Y-m-d',
                        submitFormat: 'Y-m-d'
                    }]
                }, {
                    xtype: 'textarea',
                    fieldLabel: 'Address' + " *",
                    width: 550,
                    id: 'address',
                    name: 'address',
                    allowBlank: false
                }, {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Village' + " *",
                    layout: 'hbox',
                    items: [{
                        id: 'districtID',
                        name: 'districtID',
                        xtype: 'combo',
                        emptyText: '-- District --',
                        multiSelect: false,
                        store: mc_district,
                        displayField: 'label',
                        valueField: 'id',
                        margin: '0 5 0 0',
                        queryMode: 'local',
                        hidden: true,
                        listeners: {
                            change: function(cb, nv, ov) {
                                mc_subdistrict.load({
                                    params: {
                                        district: Ext.getCmp('districtID').getValue()
                                    }
                                });
                            }
                        }
                    }, {
                        id: 'subdistrictID',
                        name: 'subdistrictID',
                        xtype: 'combo',
                        emptyText: '-- Subdistrict --',
                        multiSelect: false,
                        store: mc_subdistrict,
                        displayField: 'label',
                        valueField: 'id',
                        margin: '0 5 0 0',
                        queryMode: 'local',
                        listeners: {
                            change: function(cb, nv, ov) {
                                mc_village.load({
                                    params: {
                                        sub_district: Ext.getCmp('subdistrictID').getValue()
                                    }
                                });
                            }
                        }
                    }, {
                        id: 'villageID',
                        name: 'villageID',
                        xtype: 'combo',
                        emptyText: '-- Village --',
                        multiSelect: false,
                        store: mc_village,
                        width: 250,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local'
                    }]
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Phone') + " *",
                    width: 250,
                    id: 'phone',
                    name: 'phone',
                    allowBlank: false
                }, {
                    xtype: 'fieldcontainer',
                    fieldLabel: lang('Marital Status'),
                    defaultType: 'radiofield',
                    defaults: {
                        flex: 1
                    },
                    layout: 'hbox',
                    items: [{
                        boxLabel: 'Lajang',
                        name: 'maritalStatus',
                        inputValue: '1',
                        checked: true,
                        id: 'maritalStatus1'
                    }, {
                        boxLabel: 'Menikah',
                        name: 'maritalStatus',
                        inputValue: '2',
                        id: 'maritalStatus2'
                    }, {
                        boxLabel: 'Widow/Widower',
                        xtype: 'radio',
                        name: 'maritalStatus',
                        inputValue: '3',
                        id: 'maritalStatus3'
                    }]
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Profession') + " *",
                    id: 'job',
                    name: 'job',
                    allowBlank: false
                }, {
                    layout: 'column',
                    hidden: true,
                    border: false,
                    items: [{
                        columnWidth: .5,
                        //                                        layout: 'auto',
                        items: [{
                            xtype: 'radiofield',
                            name: 'education',
                            id: 'education1',
                            inputValue: '1',
                            fieldLabel: lang('Education'),
                            labelSeparator: ':',
                            hideEmptyLabel: false,
                            boxLabel: 'Belum Pernah Sekolah'
                        }, {
                            xtype: 'radiofield',
                            name: 'education',
                            id: 'education2',
                            inputValue: '2',
                            fieldLabel: '',
                            labelSeparator: '',
                            hideEmptyLabel: false,
                            boxLabel: 'Tidak Tamat SD'
                        }, {
                            xtype: 'radiofield',
                            name: 'education',
                            id: 'education3',
                            inputValue: '3',
                            fieldLabel: '',
                            labelSeparator: '',
                            hideEmptyLabel: false,
                            boxLabel: 'Tamat SD Tidak Melanjutkan'
                        }]
                    }, {
                        columnWidth: .5,
                        layout: 'auto',
                        items: [{
                            xtype: 'radiofield',
                            name: 'education',
                            id: 'education4',
                            inputValue: '4',
                            labelSeparator: '',
                            hideEmptyLabel: false,
                            boxLabel: 'Tamat SMP'
                        }, {
                            xtype: 'radiofield',
                            name: 'education',
                            id: 'education5',
                            inputValue: '5',
                            labelSeparator: '',
                            hideEmptyLabel: false,
                            boxLabel: 'Tamat SMK/SMA'
                        }, {
                            xtype: 'radiofield',
                            name: 'education',
                            id: 'education6',
                            inputValue: '6',
                            labelSeparator: '',
                            hideEmptyLabel: false,
                            boxLabel: 'Tamat Perguruan Tinggi'
                        }]
                    }]
                }, {
                    xtype: 'fieldset',
                    border: true,
                    title: lang('Status'),
                    items: [{
                        id: 'status',
                        name: 'status',
                        xtype: 'combo',
                        emptyText: '-- Select --',
                        fieldLabel: lang('Member Status'),
                        multiSelect: false,
                        store: mc_status,
                        displayField: 'label',
                        valueField: 'id',
                        margin: '5',
                        queryMode: 'local',
                        allowBlank: false
                    }]
                }]
            }, {
                xtype: 'fieldcontainer',
                columnWidth: .45,
                layout: 'fit',
                margin: "0 0 0 5px",
                items: [{
                    xtype: 'fieldset',
                    title: lang('Photo'),
                    items: [{
                        layout: 'column',
                        height: 250,
                        items: [{
                            margin: '5',
                            columnWidth: 0.5,
                            padding: 2,
                            layout: {
                                type: 'vbox',
                                align: 'center'
                            },
                            items: [{
                                xtype: 'displayfield',
                                value: lang('PHOTOGRAPH')
                            }, {
                                xtype: 'image',
                                id: 'iphoto',
                                height: '147px',
                                weight: '122px'
                            }, {
                                xtype: 'fileuploadfield',
                                buttonOnly: true,
                                //                                                        fieldLabel: lang('Photo'),
                                labelWidth: 60,
                                id: 'memberPhoto',
                                padding: 5,
                                name: 'memberPhoto',
                                buttonText: 'Upload Photo',
                                listeners: {
                                    'change': function(fb, v) {
                                        var form = Ext.getCmp('dataForm').getForm();
                                        form.submit({
                                            url: m_crud + '_image',
                                            waitMsg: 'Sending Photo...',
                                            success: function(fp, o) {
                                                Ext.getCmp('iphoto').setSrc(m_photo + o.result.file);
                                                Ext.getCmp('photo').setValue(o.result.file);
                                            }
                                        });
                                    }
                                }
                            }, {
                                xtype: 'textfield',
                                id: 'photo',
                                name: 'photo',
                                inputType: 'hidden'
                            }]
                        }, {
                            margin: '5',
                            columnWidth: 0.5,
                            padding: 2,
                            layout: {
                                type: 'vbox',
                                align: 'center'
                            },
                            items: [{
                                xtype: 'displayfield',
                                value: lang('SIGNATURE')
                            }, {
                                xtype: 'image',
                                id: 'isignature',
                                height: '147px',
                                weight: '122px'
                            }, {
                                xtype: 'fileuploadfield',
                                buttonOnly: true,
                                //                                                        fieldLabel: lang('Signature'),
                                labelWidth: 60,
                                id: 'memberSignature',
                                padding: 5,
                                name: 'memberSignature',
                                buttonText: 'Upload Signature',
                                listeners: {
                                    'change': function(fb, v) {
                                        var form = Ext.getCmp('dataForm').getForm();
                                        form.submit({
                                            url: m_crud + '_signature',
                                            waitMsg: 'Sending File...',
                                            success: function(fp, o) {
                                                Ext.getCmp('isignature').setSrc(m_signature + o.result.file);
                                                Ext.getCmp('signature').setValue(o.result.file);
                                            }
                                        });
                                    }
                                }
                            }, {
                                xtype: 'textfield',
                                id: 'signature',
                                name: 'signature',
                                inputType: 'hidden'
                            }]
                        }]
                    }]
                }, {
                    xtype: 'fieldset',
                    title: lang('Family'),
                    hidden: true,
                    //                                layout: 'form',
                    padding: 5,
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: 'Name' + " *",
                        id: 'familyName',
                        name: 'familyName',
                        width: 300,
                        // allowBlank: false
                    }, {
                        xtype: 'fieldcontainer',
                        fieldLabel: lang('Identity') + " *",
                        layout: 'hbox',
                        items: [{
                            xtype: 'combo',
                            emptyText: '-- Select --',
                            id: 'familyIdentityType',
                            name: 'familyIdentityType',
                            multiSelect: false,
                            store: mc_identity,
                            displayField: 'label',
                            valueField: 'id',
                            margin: '0 5 0 0',
                            queryMode: 'local',
                            width: 200,
                            // allowBlank: false
                        }, {
                            xtype: 'textfield',
                            id: 'familyIdentityNumber',
                            name: 'familyIdentityNumber',
                            // allowBlank: false
                        }]
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Relationship') + " *",
                        id: 'familyRelation',
                        name: 'familyRelation',
                        // allowBlank: false
                    }, {
                        xtype: 'textarea',
                        fieldLabel: lang('Address') + " *",
                        id: 'familyAddress',
                        name: 'familyAddress',
                        width: 350,
                        // allowBlank: false
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Phone') + " *",
                        id: 'familyPhone',
                        name: 'familyPhone',
                        width: 250,
                        // allowBlank: false
                    }]
                }]
            }]
        }]
    });

    var mc_membersaving = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_membersaving,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_transactiontype = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_transactiontype,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_cashsource = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_cashsource,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var DataFormSaving = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        height: 550,
        width: 600,
        bodyPadding: 5,
        id: 'dataFormSaving',
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
                    items: [
                        //                            {
                        //                              xtype:'hiddenfield',
                        //                              id:'savingTypeID',
                        //                              name:'savingTypeID'
                        //                            },
                        {
                            id: 'memberSavingID',
                            name: 'memberSavingID',
                            xtype: 'combo',
                            emptyText: '-- Select --',
                            fieldLabel: lang('Member Saving'),
                            multiSelect: false,
                            //                                readOnly: true,
                            store: mc_membersaving,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            //                                allowBlank: false,
                            listeners: {
                                change: function() {

                                    if (this.getValue() != '') {
                                        Ext.Ajax.request({
                                            url: m_transaction + '_member',
                                            method: 'GET',
                                            params: {
                                                id: this.getValue()
                                                    //                                                  MemberID: Ext.getCmp('MemberID').getValue(),
                                                    //                                                  savingTypeID: Ext.getCmp('savingTypeID').getValue()
                                            },
                                            success: function(fp, o) {
                                                var r = Ext.decode(fp.responseText);
                                                Ext.getCmp('primaryNoSaving').setValue(r.primaryNo);
                                                Ext.getCmp('nameSaving').setValue(r.name);
                                                Ext.getCmp('addressSaving').setValue(r.address);
                                                Ext.getCmp('memberSavingNo').setValue(r.memberSavingNo);
                                                Ext.getCmp('savingTypeID').setValue(r.savingTypeID);
                                                Ext.getCmp('savingTypeName').setValue(r.savingTypeName);
                                            }
                                        });
                                    }
                                }
                            }
                        }, {
                            xtype: 'fieldset',
                            title: lang('Saving'),
                            items: [{
                                xtype: 'textfield',
                                fieldLabel: lang('Saving Number'),
                                name: 'memberSavingNo',
                                id: 'memberSavingNo',
                                readOnly: true
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Saving Type'),
                                name: 'savingTypeName',
                                id: 'savingTypeName',
                                readOnly: true
                            }, {
                                xtype: 'textfield',
                                name: 'savingTypeID',
                                id: 'savingTypeID',
                                hidden: true
                            }]
                        }, {
                            xtype: 'fieldset',
                            title: lang('Member'),
                            items: [{
                                xtype: 'textfield',
                                fieldLabel: lang('Number'),
                                name: 'primaryNoSaving',
                                id: 'primaryNoSaving',
                                readOnly: true
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Name'),
                                name: 'nameSaving',
                                id: 'nameSaving',
                                readOnly: true
                            }, {
                                xtype: 'textarea',
                                fieldLabel: lang('Address'),
                                name: 'addressSaving',
                                id: 'addressSaving',
                                readOnly: true
                            }]
                        }
                    ]
                }, {
                    xtype: 'fieldset',
                    layout: 'anchor',
                    title: lang('Transaction'),
                    margin: '0 5 0 0',
                    columnWidth: .5,
                    items: [{
                        xtype: 'textfield',
                        id: 'idSaving',
                        name: 'ididSaving',
                        inputType: 'hidden'
                    }, {
                        xtype: 'datefield',
                        readOnly: false,
                        fieldLabel: lang('Transaction Date'),
                        value: new Date(),
                        allowBlank: false,
                        id: 'memberTransactionDate',
                        name: 'memberTransactionDate',
                        format: 'd M Y',
                        submitFormat: 'Y-m-d'
                    }, {
                        allowBlank: false,
                        id: 'memberTransactionType',
                        name: 'memberTransactionType',
                        xtype: 'combo',
                        emptyText: '-- Select --',
                        fieldLabel: lang('Transaction Type'),
                        multiSelect: false,
                        readOnly: true,
                        hidden: true,
                        store: mc_transactiontype,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local'
                    }, {
                        allowBlank: false,
                        id: 'cashSourceID',
                        name: 'cashSourceID',
                        xtype: 'combo',
                        emptyText: '-- Select --',
                        fieldLabel: lang('Cash Source'),
                        multiSelect: false,
                        store: mc_cashsource,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Amount'),
                        allowBlank: false,
                        id: 'memberTransactionAmount',
                        name: 'memberTransactionAmount'
                    }, {
                        xtype: 'textarea',
                        fieldLabel: lang('Remark'),
                        id: 'memberTransactionRemark',
                        name: 'memberTransactionRemark'
                    }, {
                        xtype: 'image',
                        id: 'transaction_type',
                        height: 100,
                        weight: 100
                    }]
                }

            ]
        }],
        buttons: [{
            id: 'saveButtonSaving',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('idSaving').getValue() == '')
                    methode = 'POST';
                else
                    methode = 'PUT';
                form.submit({
                    url: m_transaction,
                    method: methode,
                    waitMsg: 'Sending data 1...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                winSaving.hide(this, function() {
                    refreshCurrentFinancialStatus();
                    store_saving.load({
                        params: {
                            id: Ext.getCmp('MemberID').getValue()
                        }
                    });
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
                winSaving.hide();
            }
        }]
    });

    function refreshCurrentFinancialStatus() {
        Ext.Ajax.request({
            url: m_crud,
            method: 'GET',
            params: {
                id: Ext.getCmp('MemberID').getValue()
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('idSimpananPokok').setValue(r.idSimpananPokok);
                Ext.getCmp('idSimpananWajib').setValue(r.idSimpananWajib);
                Ext.getCmp('idSimpananSukarela').setValue(r.idSimpananSukarela);
            }
        });
    }

    var winSaving = Ext.create('widget.window', {
        title: lang('Transaction'),
        frame: false,
        closable: true,
        id: 'winSaving',
        modal: true,
        closeAction: 'show',
        height: 500,
        width: 850,
        layout: 'fit',
        items: [DataFormSaving]
    });

    Ext.define('DataActivity', {
        extend: 'Ext.form.Panel',
        id: 'DataActivity',
        alias: 'widget.DataActivity',
        initComponent: function() {
            var frm = this;
            frm.bodyStyle = 'padding:5px';
            frm.width = 950;
            frm.autoScroll = true;
            frm.height = 500;
            frm.fieldDefaults = {
                msgTarget: 'side',
                blankText: 'Tidak Boleh Kosong',
                labelWidth: 130,
                width: 460
            };
            frm.items = [{
                xtype: 'fieldset',
                id: 'FinStatusFS',
                title: lang('Status keuangan saat ini'),
                layout: 'form',
                padding: 5,
                items: [{
                    xtype: 'textfield',
                    id: 'MemberID',
                    name: 'MemberID',
                    hidden: true
                }, {
                    xtype: 'textfield',
                    id: 'idSimpananPokok',
                    name: 'idSimpananPokok',
                    hidden: true
                }, {
                    xtype: 'textfield',
                    id: 'idSimpananWajib',
                    name: 'idSimpananWajib',
                    hidden: true
                }, {
                    xtype: 'textfield',
                    id: 'idSimpananSukarela',
                    name: 'idSimpananSukarela',
                    hidden: true
                }, {
                    layout: {
                        type: 'table',
                        border: 1,
                        id: 'tabelFinanceStat',
                        columns: 3,
                        tableAttrs: {
                            style: {
                                width: '70%',
                                height: '100%',
                                padding: 5,
                                align: 'center'
                            }
                        }
                    },
                    defaults: {
                        bodyStyle: 'padding:5 5 5 5',
                        align: 'center'
                    },
                    items: [{
                            xtype: 'button',
                            colspan: 2,
                            width: 150,
                            style: 'margin-bottom:15px',
                            text: lang('Simpanan Baru'),
                            handler: function() {
                                wSavingTypePopup.show();
                                storeSavingTypeList.load();
                            }
                        }, {

                            xtype: 'button',
                            colspan: 1,
                            hidden: true,
                            width: 150,
                            style: 'margin-bottom:15px',
                            text: lang('New Loan'),
                            handler: function() {

                            }
                        }
                        //                            , {
                        //                                html: lang('Simpanan Pokok'),
                        //                                align: 'center'
                        //                            }, {
                        //                                xtype: 'textfield',
                        //                                fieldStyle: 'text-align: right;',
                        //                                id: 'simpananPokok',
                        //                                readOnly: true
                        //                            }, {
                        //                                xtype: 'button',
                        //                                width:75,
                        //                                text: lang('Deactivate'),
                        //                                handler: function() {
                        //
                        //                                }
                        //                            }, {
                        //                                xtype: 'button',
                        //                                text: lang('Deposit'),
                        //                                width: '95%',
                        //                                hidden:true,
                        //                                handler: function() {
                        //                                    displaySavingFormWindow();
                        //                                    var idMemberSaving = Ext.getCmp('idSimpananPokok').getValue();
                        //                                    Ext.getCmp('memberSavingID').setValue(idMemberSaving);
                        //                                    Ext.getCmp('memberTransactionType').setValue('1');
                        //                                    Ext.getCmp('transaction_type').setSrc(m_withdrawal + 'deposit.png');
                        //
                        //                                    mc_membersaving.on('beforeload',function(store, operation,eOpts){
                        //                                        operation.params={
                        //                                                    'MemberID': Ext.getCmp('MemberID').getValue(),
                        //                                                    'savingTypeID':1
                        //                                                  };
                        //                                              });
                        //                                    mc_membersaving.load();
                        //                                }
                        //                            }, {
                        //                                xtype: 'button',
                        //                                text: lang('Withdraw'),
                        //                                width: '95%',
                        //                                hidden:true,
                        //                                handler: function() {
                        //                                    displaySavingFormWindow();
                        //                                    var idMemberSaving = Ext.getCmp('idSimpananPokok').getValue();
                        //                                    Ext.getCmp('memberSavingID').setValue(idMemberSaving);
                        //                                    Ext.getCmp('memberTransactionType').setValue('2');
                        //                                    Ext.getCmp('transaction_type').setSrc(m_withdrawal + 'withdrawal.png');
                        //
                        //                                    mc_membersaving.on('beforeload',function(store, operation,eOpts){
                        //                                        operation.params={
                        //                                                    'MemberID': Ext.getCmp('MemberID').getValue(),
                        //                                                    'savingTypeID':1
                        //                                                  };
                        //                                              });
                        //                                    mc_membersaving.load();
                        //                                }
                        //                            }, {
                        //                                html: lang('Simpanan Wajib')
                        //                            }, {
                        //                                xtype: 'textfield',
                        //                                id: 'simpananWajib',
                        //                                fieldStyle: 'text-align: right;',
                        //                                readOnly: true
                        //                            }, {
                        //                                xtype: 'button',
                        //                                width:75,
                        //                                text: lang('Deactivate'),
                        //                                handler: function() {
                        //
                        //                                }
                        //                            }, {
                        //                                xtype: 'button',
                        //                                text: lang('Deposit'),
                        //                                width: '95%',
                        //                                hidden:true,
                        //                                handler: function() {
                        //                                    displaySavingFormWindow();
                        //                                    var idMemberSaving = Ext.getCmp('idSimpananWajib').getValue();
                        //                                    Ext.getCmp('memberSavingID').setValue(idMemberSaving);
                        //                                    Ext.getCmp('memberTransactionType').setValue('1');
                        //                                    Ext.getCmp('transaction_type').setSrc(m_withdrawal + 'deposit.png');
                        ////                                    Ext.getCmp('savingTypeID').setValue(2);
                        //                                    mc_membersaving.on('beforeload',function(store, operation,eOpts){
                        //                                        operation.params={
                        //                                                    'MemberID': Ext.getCmp('MemberID').getValue(),
                        //                                                    'savingTypeID':2
                        //                                                  };
                        //                                              });
                        //                                    mc_membersaving.load();
                        //                                }
                        //                            }, {
                        //                                xtype: 'button',
                        //                                text: lang('Withdraw'),
                        //                                width: '95%',
                        //                                hidden:true,
                        //                                handler: function() {
                        //                                    displaySavingFormWindow();
                        //                                    var idMemberSaving = Ext.getCmp('idSimpananWajib').getValue();
                        //                                    Ext.getCmp('memberSavingID').setValue(idMemberSaving);
                        //                                    Ext.getCmp('memberTransactionType').setValue('2');
                        //                                    Ext.getCmp('transaction_type').setSrc(m_withdrawal + 'withdrawal.png');
                        //                                    Ext.getCmp('savingTypeID').setValue(2);
                        //
                        //                                    mc_membersaving.on('beforeload',function(store, operation,eOpts){
                        //                                        operation.params={
                        //                                                    'MemberID': Ext.getCmp('MemberID').getValue(),
                        //                                                    'savingTypeID':2
                        //                                                  };
                        //                                              });
                        //                                    mc_membersaving.load();
                        //                                }
                        //                            }, {
                        //                                html: lang('Simpanan Sukarela')
                        //                            }, {
                        //                                xtype: 'textfield',
                        //                                id: 'simpananSukarela',
                        //                                fieldStyle: 'text-align: right;',
                        //                                readOnly: true
                        //                            }, {
                        //                                xtype: 'button',
                        //                                width:75,
                        //                                text: lang('Deactivate'),
                        //                                handler: function() {
                        //
                        //                                }
                        //                            }, {
                        //                                xtype: 'button',
                        //                                text: lang('Deposit'),
                        //                                width: '95%',
                        //                                hidden:true,
                        //                                handler: function() {
                        //                                    displaySavingFormWindow();
                        //                                    var idMemberSaving = Ext.getCmp('idSimpananSukarela').getValue();
                        //                                    Ext.getCmp('memberSavingID').setValue(idMemberSaving);
                        //                                    Ext.getCmp('memberTransactionType').setValue('1');
                        //                                    Ext.getCmp('transaction_type').setSrc(m_withdrawal + 'deposit.png');
                        //                                    Ext.getCmp('savingTypeID').setValue(4);
                        //                                }
                        //                            }, {
                        //                                xtype: 'button',
                        //                                text: lang('Withdraw'),
                        //                                width: '95%',
                        //                                hidden:true,
                        //                                handler: function() {
                        //                                    displaySavingFormWindow();
                        //                                    var idMemberSaving = Ext.getCmp('idSimpananSukarela').getValue();
                        //                                    Ext.getCmp('memberSavingID').setValue(idMemberSaving);
                        //                                    Ext.getCmp('memberTransactionType').setValue('1');
                        //                                    Ext.getCmp('transaction_type').setSrc(m_withdrawal + 'withdrawal.png');
                        //                                    Ext.getCmp('savingTypeID').setValue(4);
                        //                                }
                        //                            }

                        //                            , {
                        //                                html: lang('Active Loans')
                        //                            }, {
                        //                                xtype: 'textfield',
                        //                                id: 'loanActive',
                        //                                readOnly: true
                        //                            }, {
                        //                                xtype: 'button',
                        //                                hidden:true,
                        //                                text: lang('Proposal'),
                        //                                colspan: 2,
                        //                                width: '97%',
                        //                            }
                    ]
                }]
            }, {
                xtype: 'tabpanel',
                id: 'tab_finansial',
                plain: true,
                margin: 5,
                items: [{
                    title: lang('Transaksi Simpanan'),
                    autoScroll: true,
                    items: [{
                        xtype: 'gridpanel',
                        store: store_saving,
                        style: 'border:1px solid #CCC;',
                        width: '99%',
                        minHeight: 255,
                        loadMask: true,
                        features: [{
                            ftype: 'grouping',
                            groupHeaderTpl: 'No Simpanan: {name} ({[values.rows.length]} transactions)',
                            hideGroupedHeader: true,
                            startCollapsed: true,
                            id: 'transSavingGroup'
                        }],
                        selType: 'rowmodel',
                        listeners: {},
                        dockedItems: [{
                            xtype: 'pagingtoolbar',
                            store: store_saving, // same store GridPanel is using
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
                            text: 'memberSavingNo',
                            dataIndex: 'memberSavingNo'
                        }, {
                            text: lang('No. Transaksi'),
                            flex: 1,
                            width: '20%',
                            dataIndex: 'memberTransactionNumber'
                        }, {
                            text: lang('Jenis Transaksi'),
                            width: '15%',
                            dataIndex: 'memberTransactionType',
                            renderer: function(value) {
                                if (value == '1') {
                                    return lang('Deposit');
                                } else if (value == '2') {
                                    return lang('Withdrawal');
                                }
                            }
                        }, {
                            text: lang('Tgl'),
                            width: '15%',
                            dataIndex: 'memberTransactionDate'
                        }, {
                            text: lang('Jumlah'),
                            format: '0',
                            align: 'right',
                            xtype: 'numbercolumn',
                            width: '25%',
                            dataIndex: 'memberTransactionAmount'
                        }]
                    }]
                }, {
                    title: lang('Transaksi Pinjaman'),
                    autoScroll: true,
                    items: [{
                        xtype: 'gridpanel',
                        store: store_loan,
                        style: 'border:1px solid #CCC;',
                        width: '100%',
                        minHeight: 300,
                        loadMask: true,
                        selType: 'rowmodel',
                        listeners: {},
                        dockedItems: [{
                            xtype: 'toolbar',
                            dock: 'top',
                            items: [{
                                xtype: 'textfield',
                                width: 300,
                                id: 'TotalLoanMemberDetail',
                                fieldLabel: 'Total Pinjaman'
                            }, {
                                xtype: 'textfield',
                                width: 300,
                                id: 'PaidLoanMemberDetail',
                                fieldLabel: 'Terbayar'
                            }, {
                                xtype: 'textfield',
                                width: 300,
                                id: 'OutstandingLoanMemberDetail',
                                fieldLabel: 'Terhutang'
                            }]
                        }, {
                            xtype: 'pagingtoolbar',
                            store: store_loan, // same store GridPanel is using
                            dock: 'bottom',
                            displayInfo: true
                        }],
                        columns: [{
                                text: 'ID',
                                dataIndex: 'LoanInstallmentID',
                                hidden: true
                            }, {
                                text: 'No',
                                xtype: 'rownumberer',
                                width: '5%'
                            },
                            // {
                            //     text: lang('Transaction Number'),
                            //     width: '25%',
                            //     dataIndex: ''
                            // },
                            {
                                text: lang('Tgl Transaksi'),
                                width: '13%',
                                dataIndex: 'LoanInstallmentPaidDate'
                            }, {
                                text: lang('Produk'),
                                flex: 1,
                                width: '15%',
                                dataIndex: 'LoanTypeName'
                            }, {
                                text: lang('Cicilan'),
                                xtype: 'numbercolumn',
                                align: 'right',
                                width: '15%',
                                dataIndex: 'LoanInstallmentValue'
                            }, {
                                text: lang('Interest'),
                                xtype: 'numbercolumn',
                                align: 'right',
                                width: '15%',
                                dataIndex: 'LoanInstallmentInterest'
                            }, {
                                text: lang('Penalti'),
                                xtype: 'numbercolumn',
                                align: 'right',
                                width: '15%',
                                dataIndex: 'LoanInstallmentPinalty'
                            }, {
                                text: lang('Jumlah'),
                                xtype: 'numbercolumn',
                                align: 'right',
                                width: '15%',
                                dataIndex: 'LoanInstallmentTotal'
                            }
                        ]
                    }]
                }, {
                    title: lang('Saving Setup'),
                    hidden: true,
                    autoScroll: true,
                    items: [{
                        xtype: 'gridpanel',
                        store: store_savingmember,
                        style: 'border:1px solid #CCC;',
                        width: '100%',
                        minHeight: 300,
                        loadMask: true,
                        selType: 'rowmodel',
                        listeners: {},
                        dockedItems: [{
                            xtype: 'pagingtoolbar',
                            store: store_savingmember, // same store GridPanel is using
                            dock: 'bottom',
                            displayInfo: true
                        }],
                        columns: [{
                            text: 'ID',
                            dataIndex: 'memberSavingID',
                            hidden: true
                        }, {
                            text: 'No',
                            xtype: 'rownumberer',
                            width: '5%'
                        }, {
                            text: lang('Simpanan'),
                            flex: 1,
                            width: '150',
                            dataIndex: 'savingTypeName'
                        }, {
                            text: lang('Jumlah'),
                            xtype: 'numbercolumn',
                            align: 'right',
                            width: 200,
                            dataIndex: 'savingTypeMinAmount'
                        }, {
                            menuDisabled: true,
                            text: lang('Options'),
                            sortable: false,
                            xtype: 'actioncolumn',
                            width: 120,
                            align: 'center',
                            items: [{
                                icon: varjs.config.base_url + 'images/icons/silk/page_white_edit.png',
                                tooltip: lang('Edit'),
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = grid.getStore().getAt(rowIndex);
                                    WinAmountSavingMember.show();
                                    Ext.getCmp('SavingNameLabel').setValue(rec.data.savingTypeName);
                                    Ext.getCmp('amountSaving').setValue(rec.data.savingTypeMinAmount);
                                    Ext.getCmp('memberSavingID_savingSetup').setValue(rec.data.memberSavingID);
                                }
                            }]
                        }]
                    }]
                }]
            }];
            frm.callParent();
        },
        afterRender: function() {
            this.superclass.afterRender.apply(this);
            this.doLayout();
        }
    });

    Ext.define('DetailFarmer', {
        extend: 'Ext.tab.Panel',
        id: 'DetailFarmer',
        alias: 'widget.DetailFarmer',
        initComponent: function() {
            var frm = this;
            frm.bodyStyle = 'padding:5px';
            frm.enctype = 'multipart/form-data';
            frm.width = 950;
            frm.title = 'Farmer Detail';
            frm.autoScroll = true;
            frm.height = 500;
            frm.fieldDefaults = {
                msgTarget: 'side',
                blankText: 'Tidak Boleh Kosong',
                labelWidth: 130,
                width: 460
            };
            frm.items = [{
                    xtype: 'form',
                    autoScroll: true,
                    title: lang('Data Umum'),
                    padding: 5,
                    //                style: 'border:2px solid #D6EDA4',
                    items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: .5,
                            //                        layout: 'form',
                            xtype: 'form',
                            id: 'formDataUmumMember',
                            padding: 3,
                            border: false,
                            items: [{
                                xtype: 'fieldset',
                                id: 'data_umum',
                                fieldDefaults: {
                                    labelWidth: 160
                                },
                                //disabled: true,
                                items: [{
                                        xtype: 'datefield',
                                        readOnly: true,
                                        fieldLabel: lang('Tanggal Wawancara'),
                                        id: 'DateCollectionf',
                                        name: 'DateCollection',
                                        format: 'Y-m-d H:i:s'
                                    }, {
                                        xtype: 'datefield',
                                        fieldLabel: lang('Tanggal Update'),
                                        id: 'DateUpdatedf',
                                        name: 'DateUpdated',
                                        format: 'Y-m-d H:i:s',
                                        readOnly: true
                                    }, {
                                        xtype: 'textfield',
                                        readOnly: true,
                                        fieldLabel: lang('ID Petani'),
                                        id: 'FarmerID',
                                        name: 'FarmerID'
                                    }, {
                                        xtype: 'textfield',
                                        readOnly: true,
                                        fieldLabel: lang('Nama Petani'),
                                        anchor: '100%',
                                        id: 'PersonNm',
                                        name: 'FarmerName'
                                    }, {
                                        id: 'Provinsi',
                                        readOnly: true,
                                        name: 'Provinsi',
                                        xtype: 'textfield',
                                        fieldLabel: lang('Provinsi'),
                                        store: mc_Provinsi,
                                        displayField: 'label',
                                        valueField: 'label',
                                        queryMode: 'local',
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                mc_Kabupaten.load({
                                                    params: {
                                                        key: Ext.getCmp('Provinsi').getValue()
                                                    }
                                                });
                                            }
                                        }
                                    }, {
                                        id: 'Kabupaten',
                                        readOnly: true,
                                        name: 'Kabupaten',
                                        xtype: 'textfield',
                                        fieldLabel: lang('Kabupaten'),
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
                                            }
                                        }
                                    }, {
                                        id: 'Kecamatan',
                                        readOnly: true,
                                        name: 'Kecamatan',
                                        xtype: 'textfield',
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
                                        readOnly: true,
                                        name: 'Desa',
                                        xtype: 'textfield',
                                        fieldLabel: lang('Desa'),
                                        store: mc_Desa,
                                        displayField: 'label',
                                        disabled: 'true',
                                        valueField: 'id',
                                        queryMode: 'local'
                                    }, {
                                        xtype: 'textarea',
                                        readOnly: true,
                                        anchor: '100%',
                                        fieldLabel: lang('Alamat'),
                                        id: 'Address',
                                        name: 'Address'
                                    }, {
                                        id: 'FarmerGroupID',
                                        readOnly: true,
                                        name: 'FarmerGroupID',
                                        xtype: 'combo',
                                        fieldLabel: lang('CPG'),
                                        minChars: 1,
                                        store: mc_GroupID,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        autoload: true
                                    }, {
                                        fieldLabel: lang('Jenis Kelamin'),
                                        xtype: 'radiogroup',
                                        width: '100%',
                                        items: [{
                                            boxLabel: lang('Laki-laki'),
                                            name: 'Gender',
                                            readOnly: true,
                                            inputValue: '1',
                                            checked: true,
                                            id: 'Gender'
                                        }, {
                                            boxLabel: lang('Perempuan'),
                                            name: 'Gender',
                                            readOnly: true,
                                            inputValue: '2',
                                            id: 'Gender2'
                                        }]
                                    }, {
                                        fieldLabel: lang('Status Perkawinan'),
                                        xtype: 'radiogroup',
                                        width: '100%',
                                        columns: 2,
                                        items: [{
                                            boxLabel: lang('Menikah'),
                                            name: 'MaritalSt',
                                            readOnly: true,
                                            inputValue: '1',
                                            id: 'MaritalSt'
                                        }, {
                                            boxLabel: lang('Single'),
                                            name: 'MaritalSt',
                                            readOnly: true,
                                            inputValue: '2',
                                            id: 'MaritalSt2'
                                        }, {
                                            boxLabel: 'Janda/Duda',
                                            name: 'MaritalSt',
                                            readOnly: true,
                                            inputValue: '3',
                                            id: 'MaritalSt3'
                                        }, {
                                            boxLabel: 'Duda',
                                            name: 'MaritalSt',
                                            inputValue: '4',
                                            id: 'MaritalSt4',
                                            hidden: true
                                        }]
                                    }, {
                                        xtype: 'datefield',
                                        readOnly: true,
                                        fieldLabel: lang('Tanggal Lahir'),
                                        id: 'BirthDttm',
                                        name: 'BirthDttm',
                                        format: 'Y-m-d'
                                    }, {
                                        xtype: 'textfield',
                                        readOnly: true,
                                        fieldLabel: lang('Handphone'),
                                        id: 'Handphone',
                                        name: 'Handphone'
                                    }, {
                                        xtype: 'radiogroup',
                                        fieldLabel: lang('Pendidikan Terakhir'),
                                        columns: 2,
                                        items: [{
                                            xtype: 'radiofield',
                                            readOnly: true,
                                            boxLabel: lang('Belum pernah sekolah'),
                                            id: 'Education',
                                            name: 'Education',
                                            inputValue: '1'
                                        }, {
                                            xtype: 'radiofield',
                                            readOnly: true,
                                            boxLabel: lang('Tidak tamat SD'),
                                            id: 'Education2',
                                            name: 'Education',
                                            inputValue: '2'
                                        }, {
                                            xtype: 'radiofield',
                                            readOnly: true,
                                            boxLabel: lang('Tamat SD, tidak melanjutkan'),
                                            id: 'Education3',
                                            name: 'Education',
                                            inputValue: '3'
                                        }, {
                                            xtype: 'radiofield',
                                            readOnly: true,
                                            boxLabel: lang('Tamat SMP'),
                                            id: 'Education4',
                                            name: 'Education',
                                            inputValue: '4'
                                        }, {
                                            xtype: 'radiofield',
                                            readOnly: true,
                                            boxLabel: lang('Tamat SMA/SMK'),
                                            id: 'Education5',
                                            name: 'Education',
                                            inputValue: '5'
                                        }, {
                                            xtype: 'radiofield',
                                            readOnly: true,
                                            boxLabel: lang('Tamat perguruan tinggi'),
                                            id: 'Education6',
                                            name: 'Education',
                                            inputValue: '6'
                                        }]
                                    }
                                    //  ,{
                                    //     xtype: 'radiogroup',
                                    //     fieldLabel: lang('Pedagang Level Desa'),
                                    //       //labelWidth: 250,
                                    //       columns: 2,
                                    //       items: [{
                                    //         name: 'Muge',
                                    //         boxLabel: lang('Ya'),
                                    //         inputValue: '1',
                                    //         id: 'Muge'
                                    //     }, {
                                    //         name: 'Muge',
                                    //         boxLabel: lang('Tidak'),
                                    //         inputValue: '2',
                                    //         id: 'Muge2'
                                    //     }]
                                    // }
                                    // , {
                                    //     xtype: 'radiogroup',
                                    //     fieldLabel: lang('Apakah Anda anggota aktif dalam Koperasi'),
                                    //     labelWidth: 250,
                                    //     columns: 2,
                                    //     items: [{
                                    //         name: 'ActiveMemberCooperation',
                                    //         boxLabel: lang('Ya'),
                                    //         inputValue: '1',
                                    //         id: 'ActiveMemberCooperation'
                                    //     }, {
                                    //         name: 'ActiveMemberCooperation',
                                    //         boxLabel: lang('Tidak'),
                                    //         inputValue: '2',
                                    //         id: 'ActiveMemberCooperation2'
                                    //     }]
                                    // }
                                ]
                            }]
                        }, {
                            columnWidth: .5,
                            layout: 'form',
                            id: 'data_umum_lanjutan',
                            xtype: 'fieldset',
                            margin: 5,
                            padding: 10,
                            //disabled: true,
                            items: [{
                                    xtype: 'fieldset',
                                    hidden: true,
                                    border: false,
                                    items: [{
                                        xtype: 'radiogroup',
                                        fieldLabel: lang('Demo Plot'),
                                        items: [{
                                            name: 'DemoPlot',
                                            id: 'DemoPlot',
                                            boxLabel: lang('ya'),
                                            inputValue: '1'
                                        }, {
                                            name: 'DemoPlot',
                                            id: 'DemoPlot2',
                                            boxLabel: lang('Tidak'),
                                            inputValue: '2'
                                        }]
                                    }]
                                }, {
                                    xtype: 'radiogroup',
                                    fieldLabel: lang('Demo Plot Rehab'),
                                    labelWidth: 150,
                                    name: 'DemoPlotRehab',
                                    border: false,
                                    hidden: true,
                                    items: [{
                                        boxLabel: lang('Ya'),
                                        inputValue: '1',
                                        name: 'DemoPlotRehab',
                                        id: 'DemoPlotRehab'
                                    }, {
                                        name: 'DemoPlotRehab',
                                        boxLabel: lang('Tidak'),
                                        inputValue: '2',
                                        id: 'DemoPlotRehab2'
                                    }]
                                }, {
                                    xtype: 'fieldset',
                                    title: lang('Farmer Status'),
                                    items: [{
                                        xtype: 'radiogroup',
                                        fieldLabel: lang('Status'),
                                        items: [{
                                            xtype: 'radiofield',
                                            boxLabel: lang('Active'),
                                            readOnly: true,
                                            id: 'StatusFarmer1',
                                            name: 'StatusFarmer',
                                            inputValue: '1'
                                        }, {
                                            xtype: 'radiofield',
                                            boxLabel: lang('Not Active'),
                                            readOnly: true,
                                            id: 'StatusFarmer2',
                                            name: 'StatusFarmer',
                                            inputValue: '2'
                                        }, ],
                                        listeners: {
                                            change: function(field, newValue, oldValue) {
                                                setGardenStatus();
                                            }
                                        }
                                    }, {
                                        xtype: 'gridpanel',
                                        id: 'grid_garden_status',
                                        store: store_garden_status,
                                        width: '100%',
                                        hidden: true,
                                        columns: [{
                                            text: lang('GardenNr'),
                                            dataIndex: 'GardenNr',
                                            width: '20%',
                                        }, {
                                            text: lang('Garden Status'),
                                            dataIndex: 'GardenStatus',
                                            id: 'gardenstatus_status',
                                            width: '40%',
                                            editor: {
                                                xtype: 'combo',
                                                store: gardenstatus,
                                                id: 'GardenStatus',
                                                queryMode: 'local',
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false,
                                                listeners: {
                                                    change: function(field, newValue, oldValue) {
                                                        var col3 = Ext.getCmp('gardenstatus_remarks');
                                                        if (newValue == 3) {
                                                            col3.setEditor({
                                                                xtype: 'combo',
                                                                store: commodity,
                                                                queryMode: 'local',
                                                                displayField: 'label',
                                                                valueField: 'id',
                                                                allowBlank: false,
                                                            });
                                                            col3.renderer =
                                                                function(value) {
                                                                    var label = '';
                                                                    switch (value) {
                                                                        case "1":
                                                                            label = lang('Jagung');
                                                                            break;
                                                                        case "2":
                                                                            label = lang('Sawit');
                                                                            break;
                                                                        case "3":
                                                                            label = lang('Karet');
                                                                            break;
                                                                        case "4":
                                                                            label = lang('Cengkeh');
                                                                            break;
                                                                        case "5":
                                                                            label = lang('Padi');
                                                                            break;
                                                                        case "6":
                                                                            label = lang('Kosong');
                                                                            break;
                                                                        case "7":
                                                                            label = lang('Dll');
                                                                            break;
                                                                        default:
                                                                            label = value;
                                                                    }
                                                                    return label;
                                                                };
                                                        } else {
                                                            col3.setEditor({
                                                                xtype: 'textfield',
                                                            });
                                                        }
                                                    }
                                                }
                                            },
                                            renderer: function(value) {
                                                var label = '';
                                                switch (value) {
                                                    case "1":
                                                        label = lang('Died');
                                                        break;
                                                    case "2":
                                                        label = lang('Moved/Left the area');
                                                        break;
                                                    case "3":
                                                        label = lang('Switched to other crop');
                                                        break;
                                                    case "4":
                                                        label = lang('Sold the land');
                                                        break;
                                                    case "5":
                                                        label = lang('Gave the land to family member');
                                                        break;
                                                    case "6":
                                                        label = lang('Force Major');
                                                        break;
                                                }
                                                return label;
                                            }
                                        }, {
                                            text: lang('Remarks'),
                                            dataIndex: 'Remarks',
                                            id: 'gardenstatus_remarks',
                                            width: '40%',
                                            editor: {
                                                xtype: 'textfield',
                                            },
                                        }],
                                        plugins: [gardenRowEditing],
                                        listeners: {
                                            'canceledit': function(editor, e, eOpts) {
                                                store_garden_status.load({
                                                    params: {
                                                        id: Ext.getCmp('id').getValue()
                                                    }
                                                });
                                            },
                                            'edit': function(editor, e) {
                                                var data = e.record.data;
                                                // console.log(data); return;
                                                var params = {};
                                                params.FarmerID = Ext.getCmp('id').getValue();
                                                params.GardenNr = data.GardenNr;
                                                params.GardenStatus = data.GardenStatus;

                                                if (data.GardenStatus == 3) {
                                                    params.Commodity = data.Remarks;
                                                    switch (params.Commodity) {
                                                        case "1":
                                                            label = lang('Jagung');
                                                            break;
                                                        case "2":
                                                            label = lang('Sawit');
                                                            break;
                                                        case "3":
                                                            label = lang('Karet');
                                                            break;
                                                        case "4":
                                                            label = lang('Cengkeh');
                                                            break;
                                                        case "5":
                                                            label = lang('Padi');
                                                            break;
                                                        case "6":
                                                            label = lang('Kosong');
                                                            break;
                                                        case "7":
                                                            label = lang('Dll');
                                                            break;
                                                    }
                                                    params.Remarks = label;
                                                } else {
                                                    params.Commodity = null;
                                                    params.Remarks = data.Remarks;
                                                }
                                                if (!data.GardenStatusID) {
                                                    Ext.Ajax.request({
                                                        waitMsg: 'Please wait...',
                                                        url: m_crud_garden_status,
                                                        method: 'POST',
                                                        params: params,
                                                        success: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            if (obj.success) {
                                                                Ext.MessageBox.alert('Success', lang('Garden status updated'));
                                                                store_garden_status.load({
                                                                    params: {
                                                                        id: Ext.getCmp('id').getValue()
                                                                    }
                                                                });
                                                            } else {
                                                                Ext.MessageBox.alert('Warning', lang('Failed to update garden status'));
                                                            }
                                                        },
                                                        failure: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                                        }
                                                    });
                                                } else {
                                                    Ext.MessageBox.confirm('Message', lang('Update data garden status ini ?'), function(btn) {
                                                        params.GardenStatusID = data.GardenStatusID;
                                                        if (btn == 'yes') {
                                                            Ext.Ajax.request({
                                                                waitMsg: 'Please wait...',
                                                                url: m_crud_garden_status,
                                                                method: 'PUT',
                                                                params: params,
                                                                success: function(response, opts) {
                                                                    var obj = Ext.decode(response.responseText);
                                                                    if (obj.success) {
                                                                        Ext.MessageBox.alert('Success', lang('Garden status updated'));
                                                                        store_garden_status.load({
                                                                            params: {
                                                                                id: Ext.getCmp('id').getValue()
                                                                            }
                                                                        });
                                                                        // Ext.getCmp('grid_garden_status').reconfigure(store_garden_status);
                                                                    } else {
                                                                        Ext.MessageBox.alert('Warning', lang('Failed to update garden status'));
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
                                        }
                                    }, ]
                                }, {
                                    xtype: 'fieldset',
                                    title: lang('Other Commodity'),
                                    height: '200px',
                                    items: [{
                                        xtype: 'gridpanel',
                                        id: 'grid_other_land',
                                        store: store_other_land,
                                        width: '100%',
                                        height: '200px',
                                        features: [{
                                            ftype: 'summary'
                                        }],
                                        columns: [{
                                            text: lang('Commodity'),
                                            dataIndex: 'Commodity',
                                            width: '50%',
                                            editor: {
                                                xtype: 'combo',
                                                store: commodity,
                                                id: 'Commodity',
                                                queryMode: 'local',
                                                displayField: 'label',
                                                valueField: 'id',
                                                allowBlank: false
                                            },
                                            renderer: function(value) {
                                                var label = '';
                                                switch (value) {
                                                    case "1":
                                                        label = lang('Jagung');
                                                        break;
                                                    case "2":
                                                        label = lang('Sawit');
                                                        break;
                                                    case "3":
                                                        label = lang('Karet');
                                                        break;
                                                    case "4":
                                                        label = lang('Cengkeh');
                                                        break;
                                                    case "5":
                                                        label = lang('Padi');
                                                        break;
                                                    case "6":
                                                        label = lang('Kosong');
                                                        break;
                                                    case "7":
                                                        label = lang('Dll');
                                                        break;
                                                }
                                                return label;
                                            },
                                            summaryRenderer: function() {
                                                return lang('Total');
                                            }
                                        }, {
                                            text: lang('Size (Ha)'),
                                            dataIndex: 'GardenHa',
                                            width: '48%',
                                            editor: {
                                                xtype: 'textfield',
                                                allowBlank: false
                                            },
                                            summaryType: 'sum'
                                        }],
                                        plugins: [otherRowEditing],
                                        listeners: {
                                            'canceledit': function(editor, e, eOpts) {
                                                store_other_land.load({
                                                    params: {
                                                        id: Ext.getCmp('id').getValue()
                                                    }
                                                });
                                            },
                                            'edit': function(editor, e) {
                                                // console.log(e.record.data);
                                                // return;
                                                if (e.record.data.OtherID == '') {
                                                    Ext.Ajax.request({
                                                        waitMsg: 'Please wait...',
                                                        url: m_crud_other_land,
                                                        method: 'POST',
                                                        params: {
                                                            FarmerID: Ext.getCmp('id').getValue(),
                                                            Commodity: e.record.data.Commodity,
                                                            GardenHa: e.record.data.GardenHa,
                                                        },
                                                        success: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            if (obj.success) {
                                                                Ext.MessageBox.alert('Success', lang('Other land added'));
                                                                store_other_land.load({
                                                                    params: {
                                                                        id: Ext.getCmp('id').getValue()
                                                                    }
                                                                });
                                                            } else {
                                                                Ext.MessageBox.alert('Warning', lang('Failed to add other land'));
                                                            }
                                                        },
                                                        failure: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                                        }
                                                    });
                                                } else {
                                                    Ext.MessageBox.confirm('Message', lang('Update data lahan lain ini ?'), function(btn) {
                                                        if (btn == 'yes') {
                                                            Ext.Ajax.request({
                                                                waitMsg: 'Please wait...',
                                                                url: m_crud_other_land,
                                                                method: 'PUT',
                                                                params: {
                                                                    OtherID: e.record.data.OtherID,
                                                                    // FarmerID: e.record.data.FarmerID,
                                                                    Commodity: e.record.data.Commodity,
                                                                    GardenHa: e.record.data.GardenHa,
                                                                },
                                                                success: function(response, opts) {
                                                                    var obj = Ext.decode(response.responseText);
                                                                    if (obj.success) {
                                                                        Ext.MessageBox.alert('Success', lang('Other land updated'));
                                                                        store_other_land.load({
                                                                            params: {
                                                                                id: Ext.getCmp('id').getValue()
                                                                            }
                                                                        });
                                                                    } else {
                                                                        Ext.MessageBox.alert('Warning', lang('Failed to update other land'));
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
                                        }
                                    }]
                                },
                                // { // fieldset Lahan Petani
                                //     xtype: 'fieldset',
                                //     title: lang('Lahan Petani'),
                                //     items: [{
                                //         xtype: 'textfield',
                                //         fieldLabel: lang('Luas Kebun kakao yang dimiliki'),
                                //         labelWidth: 300,
                                //         id: 'LahanKakao',
                                //         name: 'LahanKakao',
                                //         listeners: {
                                //             change: function (cb, nv, ov) {
                                //                 TotalLahanK();
                                //             }
                                //         }
                                //     }, {
                                //         xtype: 'textfield',
                                //         fieldLabel: lang('Luas Tanaman lain selain kakao'),
                                //         labelWidth: 300,
                                //         id: 'LahanProduksiLain',
                                //         name: 'LahanProduksiLain',
                                //         listeners: {
                                //             change: function (cb, nv, ov) {
                                //                 TotalLahanK();
                                //             }
                                //         }
                                //     }, {
                                //         xtype: 'textfield',
                                //         fieldLabel: lang('Kepemilikan Lahan Kosong'),
                                //         labelWidth: 300,
                                //         id: 'LahanKosong',
                                //         name: 'LahanKosong',
                                //         listeners: {
                                //             change: function (cb, nv, ov) {
                                //                 TotalLahanK();
                                //             }
                                //         }
                                //     }, {
                                //         xtype: 'menuseparator',
                                //         width: '100%'
                                //     }, {
                                //         xtype: 'textfield',
                                //         fieldLabel: lang('Total Lahan'),
                                //         labelWidth: 300,
                                //         id: 'TotalLahan',
                                //         name: 'TotalLahan',
                                //         readOnly: true
                                //     }, {
                                //         xtype: 'textfield',
                                //         fieldLabel: lang('Berapa Titik Kebun Kakao yang dimiliki'),
                                //         labelWidth: 300,
                                //         id: 'KebunKakao',
                                //         name: 'KebunKakao'
                                //     }]
                                // },
                                // fieldset Farmer Status
                                // {
                                //     xtype: 'fieldset',
                                //     title: lang('Farmer Status'),
                                //     items: [{ // Radio option status
                                //         xtype: 'radiogroup',
                                //         fieldLabel: lang('Status'),
                                //         columns: 2,
                                //         items: [{
                                //             xtype: 'radiofield',
                                //             boxLabel: lang('Died'),
                                //             id: 'StatusFarmer1',
                                //             name: 'StatusFarmer',
                                //             inputValue: '1'
                                //         }, {
                                //             xtype: 'radiofield',
                                //             boxLabel: lang('Moved/Left the area'),
                                //             id: 'StatusFarmer2',
                                //             name: 'StatusFarmer',
                                //             inputValue: '2'
                                //         }, {
                                //             xtype: 'radiofield',
                                //             boxLabel: lang('Switched to other crop'),
                                //             id: 'StatusFarmer3',
                                //             name: 'StatusFarmer',
                                //             inputValue: '3'
                                //         }, {
                                //             xtype: 'radiofield',
                                //             boxLabel: lang('Sold the land'),
                                //             id: 'StatusFarmer4',
                                //             name: 'StatusFarmer',
                                //             inputValue: '4'
                                //         }, {
                                //             xtype: 'radiofield',
                                //             boxLabel: lang('Gave the land to family member'),
                                //             id: 'StatusFarmer5',
                                //             name: 'StatusFarmer',
                                //             inputValue: '5'
                                //         }],
                                //         listeners: {
                                //             change: function (field, newValue, oldValue) {
                                //                 clearFarmerStatus();
                                //                 switch (parseInt(newValue['StatusFarmer'])) {
                                //                     case 1: // died
                                //                         Ext.getCmp('rgDeceased').show();
                                //                         Ext.getCmp('rgDeceased').allowBlank = false;
                                //                         Ext.getCmp('clFamilyMemberContinue').hide();
                                //                         Ext.getCmp('MovedLeftArea').hide();
                                //                         Ext.getCmp('MovedLeftArea').allowBlank = true;
                                //                         Ext.getCmp('MovedLeftArea').setValue();
                                //                         Ext.getCmp('SwitchOtherCrop').hide();
                                //                         Ext.getCmp('SwitchOtherCrop').allowBlank = true;
                                //                         Ext.getCmp('SwitchOtherCrop').setValue();
                                //                         break;
                                //                     case 2: // Moved/Left the area
                                //                         Ext.getCmp('rgDeceased').hide();
                                //                         Ext.getCmp('rgDeceased').allowBlank = true;
                                //                         Ext.getCmp('clFamilyMemberContinue').hide();
                                //                         Ext.getCmp('MovedLeftArea').allowBlank = false;
                                //                         Ext.getCmp('MovedLeftArea').show();
                                //                         Ext.getCmp('SwitchOtherCrop').hide();
                                //                         Ext.getCmp('SwitchOtherCrop').allowBlank = true;
                                //                         Ext.getCmp('SwitchOtherCrop').setValue();
                                //                         Ext.getCmp('FamilyMemberID').reset();
                                //                         Ext.getCmp('FamilyMemberID').allowBlank = true;
                                //                         Ext.getCmp('FamilyMemberStatus').setValue();
                                //                         break;
                                //                     case 3: // Switched to other crop
                                //                         Ext.getCmp('rgDeceased').hide();
                                //                         Ext.getCmp('rgDeceased').allowBlank = true;
                                //                         Ext.getCmp('FamilyMemberID').allowBlank = true;
                                //                         Ext.getCmp('clFamilyMemberContinue').hide();
                                //                         Ext.getCmp('MovedLeftArea').hide();
                                //                         Ext.getCmp('MovedLeftArea').allowBlank = true;
                                //                         Ext.getCmp('MovedLeftArea').setValue();
                                //                         Ext.getCmp('SwitchOtherCrop').show();
                                //                         Ext.getCmp('SwitchOtherCrop').allowBlank = false;
                                //                         Ext.getCmp('FamilyMemberID').reset();
                                //                         Ext.getCmp('FamilyMemberStatus').setValue();
                                //                         break;
                                //                     case 4: // Sold the land
                                //                         Ext.getCmp('rgDeceased').hide();
                                //                         Ext.getCmp('rgDeceased').allowBlank = true;
                                //                         Ext.getCmp('FamilyMemberID').allowBlank = true;
                                //                         Ext.getCmp('clFamilyMemberContinue').hide();
                                //                         Ext.getCmp('MovedLeftArea').hide();
                                //                         Ext.getCmp('MovedLeftArea').allowBlank = true;
                                //                         Ext.getCmp('MovedLeftArea').setValue();
                                //                         Ext.getCmp('SwitchOtherCrop').hide();
                                //                         Ext.getCmp('SwitchOtherCrop').allowBlank = true;
                                //                         Ext.getCmp('SwitchOtherCrop').setValue();
                                //                         Ext.getCmp('FamilyMemberID').reset();
                                //                         Ext.getCmp('FamilyMemberStatus').setValue();
                                //                         break;
                                //                     case 5: // Gave the land to family member
                                //                         mc_family.load({
                                //                             params: {
                                //                                 key: Ext.getCmp('id').getValue()
                                //                             }
                                //                         });
                                //                         Ext.getCmp('rgDeceased').hide();
                                //                         Ext.getCmp('rgDeceased').allowBlank = true;
                                //                         Ext.getCmp('FamilyMemberID').allowBlank = false;
                                //                         Ext.getCmp('clFamilyMemberContinue').show();
                                //                         Ext.getCmp('MovedLeftArea').hide();
                                //                         Ext.getCmp('MovedLeftArea').allowBlank = true;
                                //                         Ext.getCmp('MovedLeftArea').setValue();
                                //                         Ext.getCmp('SwitchOtherCrop').hide();
                                //                         Ext.getCmp('SwitchOtherCrop').allowBlank = true;
                                //                         Ext.getCmp('SwitchOtherCrop').setValue();
                                //                         break;
                                //                 }
                                //             }
                                //         }
                                //     }, { // Radio deceased status
                                //         xtype: 'radiogroup',
                                //         fieldLabel: lang('Deceased Status'),
                                //         columns: 2,
                                //         id: 'rgDeceased',
                                //         hidden: true,
                                //         items: [{
                                //             xtype: 'radiofield',
                                //             boxLabel: lang('Family members continue'),
                                //             name: 'rgDeceased',
                                //             inputValue: '1'
                                //         }, {
                                //             xtype: 'radiofield',
                                //             boxLabel: lang('No one continue with cocoa'),
                                //             name: 'rgDeceased',
                                //             inputValue: '2'
                                //         }],
                                //         listeners: {
                                //             change: function (field, newValue, oldValue) {
                                //                 switch (parseInt(newValue['rgDeceased'])) {
                                //                     case 1: // Family members continue (option)
                                //                         Ext.getCmp('clFamilyMemberContinue').show();
                                //                         Ext.getCmp('FamilyMemberID').allowBlank = false;
                                //                         mc_family.load({
                                //                             params: {
                                //                                 key: Ext.getCmp('id').getValue()
                                //                             }
                                //                         });
                                //                         break;
                                //                     case 2: // No one continue with cocoa (option)
                                //                         Ext.getCmp('clFamilyMemberContinue').hide();
                                //                         Ext.getCmp('FamilyMemberID').allowBlank = true;
                                //                         Ext.getCmp('FamilyMemberID').setValue();
                                //                         Ext.getCmp('FamilyMemberStatus').setValue();
                                //                         break;
                                //                 }
                                //             }
                                //         }
                                //     }, { // Family member continue
                                //         layout: 'column',
                                //         border: false,
                                //         id: 'clFamilyMemberContinue',
                                //         hidden: true,
                                //         items: [{
                                //             columnWidth: 0.3,
                                //             layout: 'form',
                                //             border: false,
                                //             items: [{
                                //                 xtype: 'label',
                                //                 cls: 'x-form-item-label',
                                //                 text: lang('Family Member Continue')
                                //             }]
                                //         }, {
                                //             columnWidth: 0.3,
                                //             layout: 'form',
                                //             border: false,
                                //             padding: 1,
                                //             items: [{
                                //                 xtype: 'combo',
                                //                 id: 'FamilyMemberID',
                                //                 name: 'FamilyMemberID',
                                //                 displayField: 'label',
                                //                 valueField: 'id',
                                //                 queryMode: 'local',
                                //                 store: mc_family,
                                //                 listeners: {
                                //                     change: function (combo, selection) {
                                //                         var idFamily = this.getValue();
                                //                         Ext.Ajax.request({
                                //                             url: m_fam_relation,
                                //                             method: 'GET',
                                //                             params: {
                                //                                 id: idFamily
                                //                             },
                                //                             success: function (response) {
                                //                                 var famRelation = [];
                                //                                 famRelation[1] = "Suami/Istri";
                                //                                 famRelation[2] = "Anak";
                                //                                 famRelation[3] = "Lainnya";
                                //                                 var r = Ext.decode(response.responseText);
                                //                                 Ext.getCmp('FamilyMemberStatus').setValue(famRelation[r]);
                                //                             }
                                //                         });
                                //                     }
                                //                 }
                                //             }]
                                //         }, {
                                //             columnWidth: 0.2,
                                //             layout: 'form',
                                //             border: false,
                                //             padding: 1,
                                //             items: [{
                                //                 xtype: 'textfield',
                                //                 id: 'FamilyMemberStatus',
                                //                 name: 'FamilyMemberStatus',
                                //                 readOnly: true
                                //             }]
                                //         }]
                                //     }, { // Moved/Left the area
                                //         xtype: 'textfield',
                                //         fieldLabel: lang('Moved/Left the area'),
                                //         labelWidth: 150,
                                //         id: 'MovedLeftArea',
                                //         name: 'MovedLeftArea',
                                //         width: '60%',
                                //         hidden: true
                                //     }, { // Switched to other crop
                                //         xtype: 'textfield',
                                //         fieldLabel: lang('Switched to other crop'),
                                //         labelWidth: 150,
                                //         id: 'SwitchOtherCrop',
                                //         name: 'SwitchOtherCrop',
                                //         width: '60%',
                                //         hidden: true
                                //     }]
                                // },
                                {
                                    xtype: 'fieldset',
                                    hidden: true,
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'Photo_old',
                                        name: 'Photo_old',
                                        inputType: 'hidden'
                                    }, {
                                        xtype: 'radiogroup',
                                        fieldLabel: lang('Key Farmer'),
                                        hidden: true,
                                        labelWidth: 150,
                                        columns: 2,
                                        items: [{
                                            name: 'KeyFarmer',
                                            boxLabel: lang('Ya'),
                                            inputValue: '1',
                                            id: 'KeyFarmer'
                                        }, {
                                            name: 'KeyFarmer',
                                            boxLabel: lang('Tidak'),
                                            inputValue: '2',
                                            id: 'KeyFarmer2'
                                        }]
                                    }, {
                                        xtype: 'radiogroup',
                                        fieldLabel: lang('Posisi di Kelompok Tani'),
                                        hidden: true,
                                        labelWidth: 150,
                                        columns: 2,
                                        items: [{
                                            name: 'FarmerGroupFunctionsID',
                                            boxLabel: lang('Anggota'),
                                            inputValue: '1',
                                            id: 'FarmerGroupFunctionsID'
                                        }, {
                                            name: 'FarmerGroupFunctionsID',
                                            boxLabel: lang('Bendahara'),
                                            inputValue: '2',
                                            id: 'FarmerGroupFunctionsID2'
                                        }, {
                                            boxLabel: lang('Sekretaris'),
                                            inputValue: '3',
                                            name: 'FarmerGroupFunctionsID',
                                            id: 'FarmerGroupFunctionsID3'
                                        }, {
                                            boxLabel: lang('Ketua'),
                                            inputValue: '4',
                                            name: 'FarmerGroupFunctionsID',
                                            id: 'FarmerGroupFunctionsID4'
                                        }]
                                    }]
                                }
                            ]
                        }]
                    }, {
                        xtype: 'fieldset',
                        id: 'data_umum_pelatihan',
                        hidden: true,
                        title: lang('Pelatihan yang telah diterima'),
                        items: [{
                            xtype: 'fieldset',
                            items: [{
                                xtype: 'radiogroup',
                                labelWidth: '75%',
                                fieldLabel: lang('Pernah Mendapatkan pelatihan kakao sebelumnya'),
                                items: [{
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    name: 'OtherTraining',
                                    id: 'OtherTraining'
                                }, {
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    name: 'OtherTraining',
                                    id: 'OtherTraining2'
                                }]
                            }]
                        }, {
                            xtype: 'fieldset',
                            title: lang('Jika Ya'),
                            items: [{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Pelatihan dari'),
                                name: 'OtherTrainingSiapa',
                                items: [{
                                    boxLabel: lang('Petani Andalan'),
                                    name: 'OtherTrainingSiapa',
                                    inputValue: '1',
                                    id: 'OtherTrainingSiapa'
                                }, {
                                    name: 'OtherTrainingSiapa',
                                    boxLabel: lang('Petani Biasa'),
                                    inputValue: '2',
                                    id: 'OtherTrainingSiapa2'
                                }, {
                                    boxLabel: lang('Penyuluh'),
                                    name: 'OtherTrainingSiapa',
                                    inputValue: '3',
                                    id: 'OtherTrainingSiapa3'
                                }, {
                                    boxLabel: lang('Lembaga Lain'),
                                    inputValue: '4',
                                    name: 'OtherTrainingSiapa',
                                    id: 'OtherTrainingSiapa4'
                                }]
                            }, {
                                xtype: 'combo',
                                store: storeThn,
                                displayField: 'tahun',
                                valueField: 'tahun',
                                fieldLabel: lang('Tahun'),
                                id: 'OtherTrainingTahun',
                                name: 'OtherTrainingTahun',
                                anchor: '30%'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Lama'),
                                id: 'OtherTrainingLama',
                                name: 'OtherTrainingLama',
                                anchor: '30%'
                            }]
                        }]
                    }]
                }, {
                    xtype: 'panel',
                    hidden: true,
                    title: lang('Keluarga'),
                    id: 'keluarga',
                    //                style: 'border:2px solid #D6EDA4',
                    //disabled: true,
                    items: [{
                        xtype: 'gridpanel',
                        id: 'grid_keluarga',
                        store: store_keluarga,
                        width: '100%',
                        loadMask: true,
                        selType: 'rowmodel',
                        columns: [{
                            text: lang('No'),
                            xtype: 'rownumberer',
                            width: '5%'
                        }, {
                            text: lang('Nama Anggota Keluarga'),
                            dataIndex: 'AnggotaName',
                            width: '25%',
                            editor: {
                                xtype: 'textfield',
                                allowBlank: false
                            }
                        }, {
                            text: lang('Hubungan'),
                            dataIndex: 'hubungan',
                            width: '15%',
                            editor: {
                                xtype: 'combo',
                                store: hub,
                                id: 'HubunganKeluarga',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            }
                        }, {
                            text: lang('Tahun Lahir'),
                            dataIndex: 'AnggotaAge',
                            width: '10%',
                            editor: {
                                xtype: 'textfield',
                                allowBlank: false
                            }
                        }, {
                            text: lang('Jenis Kelamin'),
                            dataIndex: 'kelamin',
                            width: '20%',
                            editor: {
                                xtype: 'combo',
                                store: kelamin,
                                queryMode: 'local',
                                id: 'AnggotaGender',
                                displayField: 'label',
                                valueField: 'id'
                            }
                        }, {
                            text: lang('Sedang Sekolah'),
                            dataIndex: 'sekolah',
                            width: '25%',
                            editor: {
                                xtype: 'combo',
                                store: ya_tidak,
                                queryMode: 'local',
                                id: 'StatusSekolah',
                                displayField: 'label',
                                valueField: 'id'
                            }
                        }],
                        plugins: [RowEditing],
                        listeners: {
                            'canceledit': function(editor, e, eOpts) {
                                store_keluarga.load({
                                    params: {
                                        id: Ext.getCmp('id').getValue()
                                    }
                                });
                            },
                            'edit': function(editor, e) {
                                if (e.record.data.FamilyID == '') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please wait...',
                                        url: m_crud_family,
                                        method: 'POST',
                                        params: {
                                            FarmerID: Ext.getCmp('id').getValue(),
                                            AnggotaName: e.record.data.AnggotaName,
                                            HubunganKeluarga: e.record.data.hubungan,
                                            AnggotaAge: e.record.data.AnggotaAge,
                                            AnggotaGender: e.record.data.kelamin,
                                            StatusSekolah: e.record.data.sekolah
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_keluarga.load({
                                                        params: {
                                                            id: Ext.getCmp('id').getValue()
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
                                            //console.log(obj);
                                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                        }
                                    });
                                } else {
                                    Ext.MessageBox.confirm('Message', 'Update data family ini ?', function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: 'Please wait...',
                                                url: m_crud_family,
                                                method: 'PUT',
                                                params: {
                                                    FamilyID: e.record.data.FamilyID,
                                                    FarmerID: e.record.data.FarmerID,
                                                    AnggotaName: e.record.data.AnggotaName,
                                                    HubunganKeluarga: e.record.data.hubungan,
                                                    AnggotaAge: e.record.data.AnggotaAge,
                                                    AnggotaGender: e.record.data.kelamin,
                                                    StatusSekolah: e.record.data.sekolah,
                                                    hubungan: e.record.data.HubunganKeluarga,
                                                    kelamin: e.record.data.AnggotaGender,
                                                    sekolah: e.record.data.StatusSekolah
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_keluarga.load({
                                                                params: {
                                                                    id: Ext.getCmp('id').getValue()
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
                                                    Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
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
                    title: lang('Bank Account'),
                    id: 'panel_bank_account',
                    defaults: {
                        padding: '5 5 5 5'
                    },
                    //                style: 'border:2px solid #D6EDA4',
                    items: [{
                        xtype: 'fieldset',
                        items: [{
                            xtype: 'textfield',
                            fieldLabel: lang('Account Name'),
                            id: 'AccountBeneficiary',
                            name: 'AccountBeneficiary'
                        }, {
                            xtype: 'textfield',
                            fieldLabel: lang('Bank'),
                            id: 'BankName',
                            name: 'BankName'
                        }, {
                            xtype: 'textfield',
                            fieldLabel: lang('Branch'),
                            id: 'BankBranch',
                            name: 'BankBranch'
                        }, {
                            xtype: 'textfield',
                            fieldLabel: lang('Account Number'),
                            id: 'fAccountNumber',
                            name: 'AccountNumber'
                        }, ]
                    }]
                }

            ];
            frm.callParent();
        },
        afterRender: function() {
            this.superclass.afterRender.apply(this);
            this.doLayout();
        }
    });

    var winFarmer = Ext.create('widget.window', {
        title: 'Farmer Data',
        closable: true,
        id: 'winFarmer',
        modal: true,
        closeAction: 'show',
        width: 800,
        minWidth: 350,
        height: 500,
        layout: {
            type: 'fit'
        },
        items: [{
            xtype: 'gridpanel',
            id: 'grid_farmer',
            store: store_farmer,
            style: 'border:1px solid #CCC;',
            width: '100%',
            minHeight: 350,
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store_farmer, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                    xtype: 'textfield',
                    fieldLabel: lang('Key'),
                    name: 'farmerKey',
                    id: 'farmerKey'
                }, {
                    xtype: 'button',
                    margin: '0px 0px 0px 6px',
                    text: 'Search',
                    handler: function() {
                        console.log(Ext.getCmp('farmerKey').getValue());
                        store_farmer.load({
                            params: {
                                key: Ext.getCmp('farmerKey').getValue()
                            }
                        });

                        console.log(Ext.getCmp('farmerKey').getValue());
                    }
                }]
            }],
            columns: [{
                text: 'No',
                xtype: 'rownumberer',
                align: 'center',
                width: '5%'
            }, {
                text: 'Farmer ID',
                width: '15%',
                dataIndex: 'FarmerID'
            }, {
                text: 'Name',
                width: '25%',
                dataIndex: 'FarmerName'
            }, {
                text: 'District',
                width: '15%',
                dataIndex: 'District'
            }, {
                text: 'SubDistrict',
                width: '15%',
                dataIndex: 'SubDistrict'
            }, {
                text: 'Village',
                width: '15%',
                dataIndex: 'Village'
            }, {
                menuDisabled: true,
                sortable: false,
                xtype: 'actioncolumn',
                width: '7%',
                align: 'center',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/silk/page_white_edit.png',
                    tooltip: lang('Copy'),
                    handler: function(grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);
                        Ext.Ajax.request({
                            url: m_farmer,
                            method: 'GET',
                            params: {
                                id: rec.data.FarmerID
                            },
                            success: function(fp, o) {
                                var r = Ext.decode(fp.responseText);
                                Ext.getCmp('farmerID').setValue(r.FarmerID);
                                Ext.getCmp('name').setValue(r.FarmerName);
                                if (r.Gender == '1') {
                                    Ext.getCmp('gender1').setValue(true);
                                } else if (r.Gender == '2') {
                                    Ext.getCmp('gender2').setValue(true);
                                }
                                Ext.getCmp('address').setValue(r.Address);
                                Ext.getCmp('dateOfBirth').setValue(r.Birthdate);
                                Ext.getCmp('phone').setValue(r.HandPhone);
                                Ext.getCmp('job').setValue('Petani');
                                winFarmer.hide();
                            }
                        })
                    }
                }]
            }]
        }],
        buttons: [{
            text: 'Close',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winFarmer.hide();
                //                    Ext.getCmp('isFarmer').setValue(false);
            }
        }]
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.getProxy().extraParams = {
                key: Ext.getCmp('key').getValue(),
                start: 0,
                status: Ext.getCmp('filterStatus').getValue()
            };
            store.load();
        }
    }

    var menuCetak = Ext.create('Ext.menu.Menu', {
        // id: 'mainMenu',
        style: {
            overflow: 'visible' // For the Combo popup
        },
        items: [{
            text: 'Blank',
            handler: function() {
                // displayUpdateStatusWindow(); Form Printout Pendaftaran.pdf
                // preview_cetak_surat(m_api+'member/cetak_blank_member');
                window.open(m_api + "files/Form_Printout_Pendaftaran.pdf");
            }
        }, {
            text: 'Filled',
            handler: function() {
                var sm = Ext.getCmp('grid-member').getSelectionModel().getSelection()[0];
                if (!sm) {
                    Ext.MessageBox.alert(lang('Error'), lang('Harap pilih data terlebih dahulu'));
                    return false;
                }

                var id = sm.get('id');
                preview_cetak_surat(m_api + 'member/cetak_member/?MemberID=' + id);
            }
        }, ]
    });

    var menu = Ext.create('Ext.menu.Menu', {
        // id: 'mainMenu',
        style: {
            overflow: 'visible' // For the Combo popup
        },
        items: [{
            text: 'Update Status',
            handler: function() {
                var sm = Ext.getCmp('grid-member').getSelectionModel().getSelection()[0];
                if (!sm) {
                    Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                    return false;
                }

                var id = sm.get('id');

                displayUpdateStatusWindow();
            }
        }, {
            text: 'Close Membership',
            handler: function() {

                var sm = Ext.getCmp('grid-member').getSelectionModel().getSelection()[0];
                if (!sm) {
                    Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                    return false;
                }

                var id = sm.get('id');
                var status = sm.get('status');

                if (status * 1 !== 1) {
                    Ext.MessageBox.alert(lang('Info'), lang('Close Membership hanya untuk member dengan status <b>Active</b>'));
                    return false;
                }

                var win = Ext.create('widget.window', {
                    title: 'Close Membership',
                    id: 'win-member-clossing',
                    modal: true,
                    width: 460,
                    layout: 'fit',
                    items: Ext.create('Ext.form.Panel', {
                        bodyPadding: 5,
                        autoScroll: true,
                        id: 'frm-edit-member-clossing',
                        fieldDefaults: {
                            labelAlign: 'left',
                            labelWidth: 190
                        },
                        listeners: {
                            'add': function(form) {
                                Ext.Ajax.request({
                                    url: m_crud + '_close',
                                    method: 'GET',
                                    params: {
                                        id: id
                                    },
                                    success: function(response) {
                                        var text = Ext.decode(response.responseText);
                                        Ext.getCmp('memberIDCloseMember').setValue(text.memberID * 1);
                                        Ext.getCmp('ResignationDate').setValue(text.ResignationDate);
                                        Ext.getCmp('ResignationReason').setValue(text.ResignationReason);
                                        // console.log(text.ResignationDate)
                                    }
                                });
                            }
                        },
                        items: [{
                            xtype: 'hiddenfield',
                            name: 'memberID',
                            id: 'memberIDCloseMember',
                            value: id
                        }, {
                            xtype: 'datefield',
                            fieldLabel: 'Applied Date',
                            name: 'ResignationDate',
                            id: 'ResignationDate',
                            format: 'Y-m-d',
                            altFormats: 'Y-m-d',
                            submitFormat: 'Y-m-d'
                        }, {
                            xtype: 'textarea',
                            allowBlank: false,
                            anchor: '100%',
                            fieldLabel: 'Notes',
                            name: 'ResignationReason',
                            id: 'ResignationReason'
                        }],
                        buttons: [{
                            id: 'saveButtonCloseMember',
                            text: 'Save',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-blue',
                            handler: function() {
                                var form = this.up('form').getForm();
                                form.submit({
                                    url: m_crud + '_close',
                                    method: 'PUT',
                                    waitMsg: 'Sending data 2...',
                                    success: function(fp, o) {
                                        Ext.MessageBox.alert('Success', 'Data saved.');
                                        win.close(this, function() {

                                        });
                                        store.load();
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
                                win.close();
                            }
                        }]
                    })
                }).show();
            }
        }, {
            // xtype: 'button',
            // margin: '0px 6px 0px 6px',
            text: 'Re-apply',
            handler: function() {

                var sm = Ext.getCmp('grid-member').getSelectionModel().getSelection()[0];
                if (!sm) {
                    Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                    return false;
                }
                var id = sm.get('id');
                var status = sm.get('status');

                if (status * 1 === 1) {
                    Ext.MessageBox.alert(lang('Error'), lang('Re-apply hanya untuk member dengan status <b>Inactive</b>'));
                    return false;
                }

                var mc_membertype = Ext.create('Ext.data.Store', {
                    fields: ['id', 'label'],
                    autoLoad: true,
                    pageSize: 10,
                    proxy: {
                        type: 'ajax',
                        url: m_membertype,
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });
                var mc_district = Ext.create('Ext.data.Store', {
                    fields: ['id', 'label'],
                    autoLoad: true,
                    pageSize: 10,
                    proxy: {
                        type: 'ajax',
                        url: m_district,
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });
                var mc_subdistrict = Ext.create('Ext.data.Store', {
                    fields: ['id', 'label'],
                    autoLoad: true,
                    pageSize: 10,
                    proxy: {
                        type: 'ajax',
                        url: m_subdistrict,
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });
                var mc_village = Ext.create('Ext.data.Store', {
                    fields: ['id', 'label'],
                    autoLoad: false,
                    pageSize: 10,
                    proxy: {
                        type: 'ajax',
                        url: m_village,
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });
                var mc_identity = Ext.create('Ext.data.Store', {
                    fields: ['id', 'label'],
                    autoLoad: true,
                    pageSize: 10,
                    proxy: {
                        type: 'ajax',
                        url: m_identity,
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });
                var mc_status = Ext.create('Ext.data.Store', {
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

                function disableChildren() {

                    var form = Ext.getCmp('dataForm');
                    var children = form.query();
                    var xtypes = ['textfield', 'textarea', 'combo', 'checkbox', 'radio', 'datefield'];

                    Ext.each(children, function(one, index, all) {

                        if ($.inArray(one.xtype, xtypes) != -1) {
                            one.setReadOnly(true);
                        } else if (one.xtype === 'button' || one.xtype === 'fileuploadfield') {
                            one.hide();
                        }
                    });
                }


                //start window reapply
                var win = Ext.create('widget.window', {
                    id: 'win-reapply-member',
                    modal: true,
                    width: '80%',
                    height: 620,
                    autoScroll: true,
                    title: 'Re-apply Member',
                    layout: {
                        type: 'fit'
                    },
                    listeners: {
                        'add': function() {
                            var form = Ext.getCmp('dataForm');
                            disableChildren();
                            form.getForm().load({
                                url: m_crud,
                                method: 'GET',
                                params: {
                                    id: id
                                },
                                success: function(c, r) {
                                    var data = r.result.data;
                                    var farmer = data.farmerID;
                                    //disini cuy
                                    console.log(data)
                                    mc_subdistrict.load({
                                        params: {
                                            district: data.DistrictID
                                        }
                                    });
                                    // if(farmer === null || farmer.length === 0){
                                    //     Ext.getCmp('tab-member-detail').remove(Ext.getCmp('member-farmer-detail'));
                                    // }
                                    Ext.getCmp('subdistrictID').setValue(data.SubDistrictID)
                                    Ext.getCmp('appUangPangkal').setValue(data.uangPangkal);
                                    Ext.getCmp('appSimpananPokok').setValue(data.savingPokok);
                                    Ext.getCmp('appSimpananWajib').setValue(data.savingWajib);

                                    Ext.getCmp('img-photo-add-member').setSrc(m_api + data.memberPhoto);
                                    Ext.getCmp('img-sigi-add-member').setSrc(m_api + data.memberSignature);
                                    // win.show();
                                    // Ext.getCmp('dataForm').getForm
                                    enableChildren('dataForm');
                                }
                            });
                        }
                    },
                    items: Ext.create('Ext.form.Panel', {
                        bodyPadding: 5,
                        id: 'dataForm',
                        fileUpload: true,
                        enctype: 'multipart/form-data',
                        fieldDefaults: {
                            labelAlign: 'left',
                            labelWidth: 120
                        },
                        layout: {
                            type: 'column'
                        },
                        items: [{
                                xtype: 'hiddenfield',
                                name: 'reapply',
                                value: 'true'
                            }, {
                                xtype: 'hiddenfield',
                                name: 'id'
                            }, {
                                xtype: 'fieldset',
                                title: 'Personal Information',
                                columnWidth: .7,
                                style: 'padding-bottom:2px',
                                items: [{
                                    xtype: 'checkbox',
                                    fieldStyle: 'margin-left:125px',
                                    boxLabel: 'SCPP Farmer',
                                    submitValue: false,
                                    listeners: {
                                        change: function(c, v) {
                                            if (v === true) {
                                                Ext.getCmp('farmerid_container').enable();
                                            } else {
                                                Ext.getCmp('farmerID').reset();
                                                Ext.getCmp('dataForm').getForm().reset();
                                                Ext.getCmp('farmerid_container').disable();
                                            }
                                        }
                                    }
                                }, {
                                    xtype: 'fieldcontainer',
                                    fieldLabel: lang('Farmer ID'),
                                    id: 'farmerid_container',
                                    hidden: false,
                                    layout: 'hbox',
                                    align: 'stretch',
                                    bodyStyle: 'padding: 10px',
                                    disabled: true,
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'farmerID',
                                        name: 'farmerID',
                                        emptyText: 'search farmer',
                                        readOnly: true,
                                        listeners: {
                                            focus: function(c) {
                                                var winFarmer = Ext.create('widget.window', {
                                                    title: 'Farmer Data',
                                                    closable: true,
                                                    id: 'winFarmer',
                                                    modal: true,
                                                    width: 800,
                                                    minWidth: 350,
                                                    height: 500,
                                                    layout: {
                                                        type: 'fit'
                                                    },
                                                    listeners: {
                                                        render: function() {
                                                            store_farmer.load({
                                                                params: {
                                                                    key: Ext.getCmp('farmerKey').getValue()
                                                                }
                                                            });
                                                        }
                                                    },
                                                    items: [{
                                                        xtype: 'gridpanel',
                                                        id: 'grid_farmer',
                                                        store: store_farmer,
                                                        style: 'border:1px solid #CCC;',
                                                        width: '100%',
                                                        minHeight: 350,
                                                        loadMask: true,
                                                        selType: 'rowmodel',
                                                        dockedItems: [{
                                                            xtype: 'pagingtoolbar',
                                                            store: store_farmer, // same store GridPanel is using
                                                            dock: 'bottom',
                                                            displayInfo: true
                                                        }, {
                                                            xtype: 'toolbar',
                                                            items: [{
                                                                xtype: 'textfield',
                                                                fieldLabel: lang('Key'),
                                                                name: 'farmerKey',
                                                                id: 'farmerKey'
                                                            }, {
                                                                xtype: 'button',
                                                                margin: '0px 0px 0px 6px',
                                                                text: 'Search',
                                                                handler: function() {
                                                                    store_farmer.load({
                                                                        params: {
                                                                            key: Ext.getCmp('farmerKey').getValue()
                                                                        }
                                                                    });
                                                                }
                                                            }]
                                                        }],
                                                        columns: [{
                                                            text: 'No',
                                                            xtype: 'rownumberer',
                                                            align: 'center',
                                                            width: '5%'
                                                        }, {
                                                            text: 'Farmer ID',
                                                            width: '15%',
                                                            dataIndex: 'FarmerID'
                                                        }, {
                                                            text: 'Name',
                                                            width: '25%',
                                                            dataIndex: 'FarmerName'
                                                        }, {
                                                            text: 'District',
                                                            width: '15%',
                                                            dataIndex: 'District'
                                                        }, {
                                                            text: 'SubDistrict',
                                                            width: '15%',
                                                            dataIndex: 'SubDistrict'
                                                        }, {
                                                            text: 'Village',
                                                            width: '15%',
                                                            dataIndex: 'Village'
                                                        }, {
                                                            menuDisabled: true,
                                                            sortable: false,
                                                            xtype: 'actioncolumn',
                                                            width: '7%',
                                                            align: 'center',
                                                            items: [{
                                                                icon: varjs.config.base_url + 'images/icons/silk/page_white_edit.png',
                                                                tooltip: lang('Pilih'),
                                                                handler: function(grid, rowIndex, colIndex) {
                                                                    var rec = grid.getStore().getAt(rowIndex);
                                                                    var form = Ext.getCmp('dataForm').getForm().load({
                                                                        url: m_farmer,
                                                                        method: 'GET',
                                                                        params: {
                                                                            id: rec.data.FarmerID
                                                                        },
                                                                        success: function(c, r) {
                                                                            var data = r.result.data;
                                                                            Ext.getCmp('farmerID').setValue(data.FarmerID);

                                                                            if (data.gender == '1') {
                                                                                Ext.getCmp('gender1').setValue(true);
                                                                            } else if (data.gender == '2') {
                                                                                Ext.getCmp('gender2').setValue(true);
                                                                            }
                                                                            Ext.getCmp('subdistrictID').store.load();
                                                                            Ext.getCmp('subdistrictID').setValue(data.SubDistrictID);
                                                                            switch (data.MaritalStatus) {
                                                                                case '1':
                                                                                    Ext.getCmp('maritalStatus1').setValue(true);
                                                                                    break;
                                                                                case '2':
                                                                                    Ext.getCmp('maritalStatus2').setValue(true);
                                                                                    break;
                                                                                case '3':
                                                                                    Ext.getCmp('maritalStatus3').setValue(true);
                                                                                    break;
                                                                            }
                                                                            winFarmer.close();
                                                                        }
                                                                    });
                                                                }
                                                            }]
                                                        }]
                                                    }],
                                                    buttons: [{
                                                        text: 'Close',
                                                        margin: '5px',
                                                        scale: 'large',
                                                        ui: 's-button',
                                                        cls: 's-grey',
                                                        disabled: false,
                                                        handler: function() {
                                                            winFarmer.close();
                                                        }
                                                    }]
                                                }).show();
                                            }
                                        }
                                    }, {
                                        iconCls: 'search',
                                        cls: 's-grey',
                                        xtype: 'button',
                                        id: 'isFarmer',
                                        style: 'margin-left:5px',
                                        handler: function() {

                                            var winFarmer = Ext.create('widget.window', {
                                                title: 'Farmer Data',
                                                closable: true,
                                                id: 'winFarmer',
                                                modal: true,
                                                width: 800,
                                                minWidth: 350,
                                                height: 500,
                                                layout: {
                                                    type: 'fit'
                                                },
                                                items: [{
                                                    xtype: 'gridpanel',
                                                    id: 'grid_farmer',
                                                    store: store_farmer,
                                                    style: 'border:1px solid #CCC;',
                                                    width: '100%',
                                                    minHeight: 350,
                                                    loadMask: true,
                                                    selType: 'rowmodel',
                                                    dockedItems: [{
                                                        xtype: 'pagingtoolbar',
                                                        store: store_farmer, // same store GridPanel is using
                                                        dock: 'bottom',
                                                        displayInfo: true
                                                    }, {
                                                        xtype: 'toolbar',
                                                        items: [{
                                                            xtype: 'textfield',
                                                            fieldLabel: lang('Key'),
                                                            name: 'farmerKey',
                                                            id: 'farmerKey'
                                                        }, {
                                                            xtype: 'button',
                                                            margin: '0px 0px 0px 6px',
                                                            text: 'Search',
                                                            handler: function() {
                                                                store_farmer.load({
                                                                    params: {
                                                                        key: Ext.getCmp('farmerKey').getValue()
                                                                    }
                                                                });
                                                            }
                                                        }]
                                                    }],
                                                    columns: [{
                                                        text: 'No',
                                                        xtype: 'rownumberer',
                                                        align: 'center',
                                                        width: '5%'
                                                    }, {
                                                        text: 'Farmer ID',
                                                        width: '15%',
                                                        dataIndex: 'FarmerID'
                                                    }, {
                                                        text: 'Name',
                                                        width: '25%',
                                                        dataIndex: 'FarmerName'
                                                    }, {
                                                        text: 'District',
                                                        width: '15%',
                                                        dataIndex: 'District'
                                                    }, {
                                                        text: 'SubDistrict',
                                                        width: '15%',
                                                        dataIndex: 'SubDistrict'
                                                    }, {
                                                        text: 'Village',
                                                        width: '15%',
                                                        dataIndex: 'Village'
                                                    }, {
                                                        menuDisabled: true,
                                                        sortable: false,
                                                        xtype: 'actioncolumn',
                                                        width: '7%',
                                                        align: 'center',
                                                        items: [{
                                                            icon: varjs.config.base_url + 'images/icons/silk/page_white_edit.png',
                                                            tooltip: lang('Pilih'),
                                                            handler: function(grid, rowIndex, colIndex) {
                                                                var rec = grid.getStore().getAt(rowIndex);
                                                                var form = Ext.getCmp('dataForm').getForm().load({
                                                                    url: m_farmer,
                                                                    method: 'GET',
                                                                    params: {
                                                                        id: rec.data.FarmerID
                                                                    },
                                                                    success: function(c, r) {
                                                                        var data = r.result.data;
                                                                        Ext.getCmp('farmerID').setValue(data.FarmerID);

                                                                        if (data.gender == '1') {
                                                                            Ext.getCmp('gender1').setValue(true);
                                                                        } else if (data.gender == '2') {
                                                                            Ext.getCmp('gender2').setValue(true);
                                                                        }
                                                                        Ext.getCmp('subdistrictID').store.load();
                                                                        Ext.getCmp('subdistrictID').setValue(data.SubDistrictID);
                                                                        switch (data.MaritalStatus) {
                                                                            case '1':
                                                                                Ext.getCmp('maritalStatus1').setValue(true);
                                                                                break;
                                                                            case '2':
                                                                                Ext.getCmp('maritalStatus2').setValue(true);
                                                                                break;
                                                                            case '3':
                                                                                Ext.getCmp('maritalStatus3').setValue(true);
                                                                                break;
                                                                        }
                                                                        winFarmer.close();
                                                                    }
                                                                });
                                                            }
                                                        }]
                                                    }]
                                                }],
                                                buttons: [{
                                                    text: 'Close',
                                                    margin: '5px',
                                                    scale: 'large',
                                                    ui: 's-button',
                                                    cls: 's-grey',
                                                    disabled: false,
                                                    handler: function() {
                                                        winFarmer.close();
                                                    }
                                                }]
                                            }).show();
                                        }
                                    }]
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Member Type <b style="color:red">*</b>',
                                    allowBlank: false,
                                    width: 350,
                                    store: Ext.create('Ext.data.Store', {
                                        fields: ['typeID', 'typeName'],
                                        autoLoad: true,
                                        proxy: {
                                            type: 'rest',
                                            url: m_api + 'member/combomembertype',
                                            reader: {
                                                type: 'json',
                                                root: 'data',
                                                totalProperty: 'total'
                                            }
                                        }
                                    }),
                                    displayField: 'typeName',
                                    valueField: 'typeID',
                                    name: 'typeID'
                                }, {
                                    xtype: 'textfield',
                                    anchor: '100%',
                                    fieldLabel: 'Name <b style="color:red">*</b>',
                                    allowBlank: false,
                                    name: 'name'
                                }, {
                                    xtype: 'textfield',
                                    width: 400,
                                    fieldLabel: 'KTP No. <b style="color:red">*</b>',
                                    id: 'identityNumber',
                                    name: 'identityNumber',
                                    allowBlank: false
                                }, {
                                    xtype: 'fieldcontainer',
                                    fieldLabel: lang('Gender <b style="color:red">*</b>'),
                                    layout: 'hbox',
                                    items: [{
                                        xtype: 'radio',
                                        id: 'gender1',
                                        inputValue: '1',
                                        boxLabel: 'Male',
                                        name: 'gender',
                                        checked: true
                                    }, {
                                        xtype: 'radio',
                                        id: 'gender2',
                                        inputValue: '2',
                                        fieldStyle: 'margin-left:75px',
                                        boxLabel: 'Female',
                                        name: 'gender'
                                    }]
                                }, {
                                    xtype: 'fieldcontainer',
                                    fieldLabel: 'Tempat, Tgl. Lahir <b style="color:red">*</b>',
                                    layout: 'hbox',
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'placeOfBirth',
                                        margin: '0 5 0 0',
                                        name: 'placeOfBirth'
                                    }, {
                                        xtype: 'datefield',
                                        id: 'dateOfBirth',
                                        name: 'dateOfBirth',
                                        format: 'Y-m-d',
                                        width: 100,
                                        altFormats: 'Y-m-d',
                                        submitFormat: 'Y-m-d'
                                    }]
                                }, {
                                    xtype: 'textarea',
                                    fieldLabel: 'Address <b style="color:red">*</b>',
                                    width: 550,
                                    id: 'address',
                                    name: 'address',
                                    allowBlank: false
                                }, {
                                    xtype: 'fieldcontainer',
                                    fieldLabel: 'Village <b style="color:red">*</b>',
                                    layout: 'hbox',
                                    items: [{
                                        id: 'districtID',
                                        name: 'districtID',
                                        xtype: 'combo',
                                        submitValue: false,
                                        emptyText: '-- District --',
                                        multiSelect: false,
                                        store: mc_district,
                                        displayField: 'label',
                                        valueField: 'id',
                                        margin: '0 5 0 0',
                                        queryMode: 'local',
                                        hidden: true,
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                mc_subdistrict.load({
                                                    params: {
                                                        district: Ext.getCmp('districtID').getValue()
                                                    }
                                                });
                                            }
                                        }
                                    }, {
                                        id: 'subdistrictID',
                                        name: 'subdistrictID',
                                        xtype: 'combo',
                                        emptyText: '-- Subdistrict --',
                                        multiSelect: false,
                                        store: mc_subdistrict,
                                        displayField: 'label',
                                        valueField: 'id',
                                        margin: '0 5 0 0',
                                        queryMode: 'local',
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                mc_village.load({
                                                    params: {
                                                        sub_district: Ext.getCmp('subdistrictID').getValue()
                                                    }
                                                });
                                            }
                                        }
                                    }, {
                                        id: 'villageID',
                                        name: 'villageID',
                                        xtype: 'combo',
                                        emptyText: '-- Village --',
                                        multiSelect: false,
                                        store: mc_village,
                                        width: 270,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local'
                                    }]
                                }, {
                                    xtype: 'textfield',
                                    fieldLabel: lang('Phone <b style="color:red">*</b>'),
                                    width: 250,
                                    id: 'phone',
                                    name: 'phone',
                                    allowBlank: false
                                }, {
                                    xtype: 'textfield',
                                    fieldLabel: lang('Job <b style="color:red">*</b>'),
                                    id: 'job',
                                    width: 350,
                                    name: 'job',
                                    allowBlank: false
                                }, {
                                    xtype: 'fieldcontainer',
                                    fieldLabel: lang('Marital Status'),
                                    defaultType: 'radiofield',
                                    defaults: {
                                        flex: 1
                                    },
                                    layout: 'hbox',
                                    items: [{
                                        boxLabel: 'Lajang',
                                        name: 'maritalStatus',
                                        checked: true,
                                        inputValue: '1',
                                        id: 'maritalStatus1'
                                    }, {
                                        boxLabel: 'Menikah',
                                        name: 'maritalStatus',
                                        inputValue: '2',
                                        id: 'maritalStatus2'
                                    }, {
                                        boxLabel: 'Janda/Duda',
                                        xtype: 'radio',
                                        name: 'maritalStatus',
                                        inputValue: '3',
                                        id: 'maritalStatus3'
                                    }]
                                }, {
                                    name: 'status',
                                    fieldLabel: 'Member Status',
                                    xtype: 'combo',
                                    hidden: true,
                                    multiSelect: false,
                                    store: StatusMemberStore,
                                    displayField: 'StatusMemberName',
                                    valueField: 'StatusMemberID',
                                    id: 'StatusMemberID',
                                    margin: '0 5 0 0',
                                    queryMode: 'local',
                                    listeners: {
                                        change: function(cb, nv, ov) {

                                        }
                                    }
                                }]
                            },

                            {
                                xtype: 'container',
                                columnWidth: .3,
                                layout: {
                                    type: 'fit'
                                },
                                items: []
                            }, {
                                xtype: 'container',
                                columnWidth: .3,
                                padding: 10,
                                layout: {
                                    type: 'fit'
                                },
                                items: [{
                                    xtype: 'numericfield',
                                    hideTriger: true,
                                    fieldLabel: 'Uang Pangkal <b style="color:red">*</b>',
                                    id: 'appUangPangkal',
                                    name: 'RegUangPangkal',
                                    width: 300,
                                    allowBlank: false
                                }, {
                                    xtype: 'numericfield',
                                    hideTriger: true,
                                    width: 300,
                                    fieldLabel: 'Simpanan Pokok <b style="color:red">*</b>',
                                    id: 'appSimpananPokok',
                                    name: 'RegSimpananPokok',
                                    allowBlank: false
                                }, {
                                    xtype: 'numericfield',
                                    hideTriger: true,
                                    width: 300,
                                    fieldLabel: lang('Simpanan Wajib <b style="color:red">*</b>'),
                                    id: 'appSimpananWajib',
                                    name: 'RegSimpananWajib',
                                    allowBlank: false
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
                                        xtype: 'form',
                                        id: 'uploaderImage1',
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
                                            fieldStyle: 'border:1px solid #ccc;',
                                            anchor: '100%',
                                            height: 150,
                                            submitValue: false,
                                            id: 'img-photo-add-member',
                                            style: 'margin-bottom:5px'
                                        }, {
                                            xtype: 'fileuploadfield',
                                            buttonOnly: true,
                                            buttonConfig: {
                                                width: 125
                                            },
                                            submitValue: false,
                                            name: 'memberPhoto',
                                            buttonText: 'Upload',
                                            listeners: {
                                                'change': function(fb, v) {
                                                    base64Converter(fb)
                                                        // var form = fb.up('form');
                                                        // form.getForm().submit({
                                                        //     url: m_api + 'member/coop_member_image',
                                                        //     success: function(c, v) {

                                                    //         var data = v.result.data;

                                                    //         // Use createObjectURL to make a URL for the blob
                                                    //         var image = Ext.getCmp('img-photo-add-member');

                                                    //         image.setSrc(m_api + data);

                                                    //         Ext.getCmp('hidden-add-member-photo-path').setValue(v.result.path);
                                                    //         Ext.getCmp('hidden-add-member-photo-name').setValue(v.result.name);
                                                    //     }
                                                    // });
                                                }
                                            }
                                        }, {
                                            xtype: 'hidden',
                                            name: 'memberPhotoPath',
                                            id: 'hidden-add-member-photo-path'
                                        }, {
                                            xtype: 'hidden',
                                            name: 'memberPhotoName',
                                            id: 'hidden-add-member-photo-name'
                                        }]
                                    }, {
                                        xtype: 'form',
                                        id: 'uploadSign3',
                                        width: 125,
                                        layout: {
                                            type: 'anchor'
                                        },
                                        items: [{
                                            xtype: 'displayfield',
                                            anchor: '100%',
                                            fieldStyle: 'text-align:center;font-weight:bold',
                                            value: 'SIGNATURE'
                                        }, {
                                            xtype: 'image',
                                            fieldStyle: 'border:1px solid #ccc;',
                                            anchor: '100%',
                                            height: 150,
                                            submitValue: false,
                                            id: 'img-sigi-add-member',
                                            style: 'margin-bottom:5px'
                                        }, {
                                            xtype: 'fileuploadfield',
                                            buttonOnly: true,
                                            buttonConfig: {
                                                width: 125
                                            },
                                            submitValue: false,
                                            name: 'memberSignature',
                                            buttonText: 'Upload',
                                            listeners: {
                                                'change': function(fb, v) {
                                                    base64Converter(fb, 'sigi');
                                                    // var form = fb.up('form');
                                                    // form.getForm().submit({
                                                    //     url: m_api + 'member/coop_member_image',
                                                    //     success: function(c, v) {

                                                    //         var data = v.result.data;

                                                    //         // Use createObjectURL to make a URL for the blob
                                                    //         var image = Ext.getCmp('img-sigi-add-member');

                                                    //         image.setSrc(m_api + data);

                                                    //         Ext.getCmp('hidden-add-member-sigi-path').setValue(v.result.path);
                                                    //         Ext.getCmp('hidden-add-member-sigi-name').setValue(v.result.name);
                                                    //     }
                                                    // });
                                                }
                                            }
                                        }, {
                                            xtype: 'hidden',
                                            name: 'memberSigiPath',
                                            id: 'hidden-add-member-sigi-path'
                                        }, {
                                            xtype: 'hidden',
                                            name: 'memberSigiName',
                                            id: 'hidden-add-member-sigi-name'
                                        }]
                                    }]
                                }, {
                                    xtype: 'fieldset',
                                    margin: 5,
                                    hidden: true,
                                    title: 'Family',
                                    defaults: {
                                        labelWidth: 90
                                    },
                                    items: [{
                                        xtype: 'textfield',
                                        fieldLabel: 'Name <b style="color:red">*</b>',
                                        id: 'familyName',
                                        name: 'familyName',
                                        width: 300,
                                        // allowBlank: false
                                    }, {
                                        xtype: 'textfield',
                                        width: 300,
                                        fieldLabel: 'Identity <b style="color:red">*</b>',
                                        id: 'familyIdentityNumber',
                                        name: 'familyIdentityNumber',
                                        // allowBlank: false
                                    }, {
                                        xtype: 'textfield',
                                        width: 300,
                                        fieldLabel: lang('Relationship <b style="color:red">*</b>'),
                                        id: 'familyRelation',
                                        name: 'familyRelation',
                                        // allowBlank: false
                                    }, {
                                        xtype: 'textarea',
                                        fieldLabel: lang('Address'),
                                        id: 'familyAddress',
                                        name: 'familyAddress',
                                        width: 300
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Phone'),
                                        id: 'familyPhone',
                                        name: 'familyPhone',
                                        width: 250
                                    }]
                                }]
                            }
                        ]
                    }),
                    buttons: [{
                        id: 'saveButton',
                        text: 'Saved',
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        handler: function() {
                            var StatusMemberID = Ext.getCmp('StatusMemberID').getValue();
                            let formImg = [Ext.getCmp('uploaderImage1'), Ext.getCmp('uploadSign3')];
                            var form = Ext.getCmp('dataForm').getForm();
                            console.log(form)
                            doUpload(formImg).then(s => {
                                // console.log(s)
                                form.submit({
                                    url: m_crud,
                                    method: 'POST',
                                    waitMsg: 'Sending data...',
                                    success: function(fp, o) {
                                        Ext.MessageBox.alert('Success', 'Data saved.');
                                        win.close(this, function() {
                                            store.load();
                                        });
                                        store.load({
                                            params: {
                                                'newadd': true
                                            }
                                        });
                                        // store.load();
                                    }
                                });
                            })

                        }
                    }, {
                        text: 'Close',
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-grey',
                        disabled: false,
                        handler: function() {

                            var photo = Ext.getCmp('hidden-add-member-sigi-path').getValue();
                            var sigi = Ext.getCmp('hidden-add-member-photo-path').getValue();

                            Ext.Ajax.request({
                                method: 'POST',
                                url: m_api + '/member/cancel_image',
                                params: {
                                    photo: Ext.JSON.encode(photo),
                                    sigi: Ext.JSON.encode(sigi)
                                }
                            });

                            win.close();
                        }
                    }]
                }).show();


                //reapply member set to candidate
                Ext.getCmp('StatusMemberID').setValue('4');
                Ext.getCmp('StatusMemberID').setReadOnly(true);

                //end window reapply
            }
        }, {
            itemId: 'remove',
            hidden: false,
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            cls: m_act_delete,
            text: 'Hapus',
            scope: this,
            hidden: true,
            handler: function() {
                var smb = Ext.getCmp('grid-member').getSelectionModel().getSelection()[0];
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
        }]
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid-member',
        minHeight: 350,
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            },
            //  {
            //     xtype: 'toolbar',
            //     dock: 'top',
            //     items: [
            //         {
            //             id: 'updateStatusxx',
            //             fieldLabel:'Status Member',
            //             name: 'updateStatus',
            //             xtype: 'combo',
            //             multiSelect: false,
            //             store: StatusFilterMemberStore,
            //             displayField: 'StatusMemberName',
            //             valueField: 'StatusMemberID',
            //             queryMode: 'local'
            //         }
            //     ]
            // },
            {
                xtype: 'toolbar',
                items: [{
                        iconCls: 'add',
                        text: lang('Add'),
                        scope: this,
                        handler: function() {

                            var win = Ext.create('widget.window', {
                                id: 'win-add-member',
                                modal: true,
                                width: '80%',
                                height: 620,
                                autoScroll: true,
                                title: 'Tambah Anggota',
                                layout: {
                                    type: 'fit'
                                },
                                items: Ext.create('Ext.form.Panel', {
                                    bodyPadding: 5,
                                    id: 'dataForm',
                                    fileUpload: true,
                                    enctype: 'multipart/form-data',
                                    fieldDefaults: {
                                        labelAlign: 'left',
                                        labelWidth: 120
                                    },
                                    layout: {
                                        type: 'column'
                                    },
                                    items: [{
                                        xtype: 'fieldset',
                                        title: 'Informasi Personal',
                                        columnWidth: .7,
                                        style: 'padding-bottom:2px',
                                        items: [{
                                            xtype: 'checkbox',
                                            fieldStyle: 'margin-left:125px',
                                            boxLabel: 'Petani SCPP',
                                            submitValue: false,
                                            listeners: {
                                                change: function(c, v) {
                                                    if (v === true) {
                                                        Ext.getCmp('farmerid_container').enable();
                                                    } else {
                                                        Ext.getCmp('farmerID').reset();
                                                        Ext.getCmp('dataForm').getForm().reset();
                                                        Ext.getCmp('farmerid_container').disable();
                                                    }
                                                }
                                            }
                                        }, {
                                            xtype: 'fieldcontainer',
                                            fieldLabel: lang('Farmer ID'),
                                            id: 'farmerid_container',
                                            hidden: false,
                                            layout: 'hbox',
                                            align: 'stretch',
                                            bodyStyle: 'padding: 10px',
                                            disabled: true,
                                            items: [{
                                                xtype: 'textfield',
                                                id: 'farmerID',
                                                name: 'farmerID',
                                                emptyText: 'search farmer',
                                                readOnly: true,
                                                listeners: {
                                                    focus: function(c) {
                                                        var winFarmer = Ext.create('widget.window', {
                                                            title: 'Farmer Data',
                                                            closable: true,
                                                            id: 'winFarmer',
                                                            modal: true,
                                                            width: 800,
                                                            minWidth: 350,
                                                            height: 500,
                                                            layout: {
                                                                type: 'fit'
                                                            },
                                                            listeners: {
                                                                render: function() {
                                                                    store_farmer.load({
                                                                        params: {
                                                                            key: Ext.getCmp('farmerKey').getValue()
                                                                        }
                                                                    });
                                                                }
                                                            },
                                                            items: [{
                                                                xtype: 'gridpanel',
                                                                id: 'grid_farmer',
                                                                store: store_farmer,
                                                                style: 'border:1px solid #CCC;',
                                                                width: '100%',
                                                                minHeight: 350,
                                                                loadMask: true,
                                                                selType: 'rowmodel',
                                                                dockedItems: [{
                                                                    xtype: 'pagingtoolbar',
                                                                    store: store_farmer, // same store GridPanel is using
                                                                    dock: 'bottom',
                                                                    displayInfo: true
                                                                }, {
                                                                    xtype: 'toolbar',
                                                                    items: [{
                                                                        xtype: 'textfield',
                                                                        fieldLabel: lang('Key'),
                                                                        name: 'farmerKey',
                                                                        id: 'farmerKey'
                                                                    }, {
                                                                        xtype: 'combo',
                                                                        id: 'cmb-group-search-add-member',
                                                                        fieldLabel: lang('Kelompok'),
                                                                        allowBlank: false,
                                                                        width: 350,
                                                                        store: Ext.create('Ext.data.Store', {
                                                                            fields: ['CPGid', 'GroupName'],
                                                                            autoLoad: true,
                                                                            proxy: {
                                                                                type: 'rest',
                                                                                url: m_api + 'member/combogroup',
                                                                                reader: {
                                                                                    type: 'json',
                                                                                    root: 'data',
                                                                                    totalProperty: 'total'
                                                                                }
                                                                            }
                                                                        }),
                                                                        displayField: 'GroupName',
                                                                        valueField: 'CPGid',
                                                                        name: 'CPGid'
                                                                    }, {
                                                                        xtype: 'button',
                                                                        margin: '0px 0px 0px 6px',
                                                                        text: 'Search',
                                                                        handler: function() {
                                                                            store_farmer.getProxy().extraParams = {
                                                                                key: Ext.getCmp('farmerKey').getValue(),
                                                                                cpg: Ext.getCmp('cmb-group-search-add-member').getValue(),
                                                                            };
                                                                            store_farmer.load({
                                                                                params: {
                                                                                    start: 0
                                                                                }
                                                                            });
                                                                        }
                                                                    }]
                                                                }],
                                                                columns: [{
                                                                    text: 'No',
                                                                    xtype: 'rownumberer',
                                                                    align: 'center',
                                                                    width: '5%'
                                                                }, {
                                                                    text: 'Farmer ID',
                                                                    width: '15%',
                                                                    dataIndex: 'FarmerID'
                                                                }, {
                                                                    text: 'Name',
                                                                    width: '25%',
                                                                    dataIndex: 'FarmerName'
                                                                }, {
                                                                    text: 'District',
                                                                    width: '15%',
                                                                    dataIndex: 'District'
                                                                }, {
                                                                    text: lang('Kelompok'),
                                                                    width: '15%',
                                                                    dataIndex: 'GroupName'
                                                                }, {
                                                                    text: 'Village',
                                                                    width: '15%',
                                                                    dataIndex: 'Village'
                                                                }, {
                                                                    menuDisabled: true,
                                                                    sortable: false,
                                                                    xtype: 'actioncolumn',
                                                                    width: '7%',
                                                                    align: 'center',
                                                                    items: [{
                                                                        icon: varjs.config.base_url + 'images/icons/silk/page_white_edit.png',
                                                                        tooltip: lang('Pilih'),
                                                                        handler: function(grid, rowIndex, colIndex) {
                                                                            var rec = grid.getStore().getAt(rowIndex);
                                                                            var form = Ext.getCmp('dataForm').getForm().load({
                                                                                url: m_farmer,
                                                                                method: 'GET',
                                                                                params: {
                                                                                    id: rec.data.FarmerID
                                                                                },
                                                                                success: function(c, r) {
                                                                                    var data = r.result.data;
                                                                                    Ext.getCmp('farmerID').setValue(data.FarmerID);

                                                                                    if (data.gender == '1') {
                                                                                        Ext.getCmp('gender1').setValue(true);
                                                                                    } else if (data.gender == '2') {
                                                                                        Ext.getCmp('gender2').setValue(true);
                                                                                    }
                                                                                    Ext.getCmp('subdistrictID').store.load();
                                                                                    Ext.getCmp('subdistrictID').setValue(data.SubDistrictID);
                                                                                    switch (data.MaritalStatus) {
                                                                                        case '1':
                                                                                            Ext.getCmp('maritalStatus1').setValue(true);
                                                                                            break;
                                                                                        case '2':
                                                                                            Ext.getCmp('maritalStatus2').setValue(true);
                                                                                            break;
                                                                                        case '3':
                                                                                            Ext.getCmp('maritalStatus3').setValue(true);
                                                                                            break;
                                                                                    }
                                                                                    winFarmer.close();
                                                                                }
                                                                            });
                                                                        }
                                                                    }]
                                                                }]
                                                            }],
                                                            buttons: [{
                                                                text: 'Close',
                                                                margin: '5px',
                                                                scale: 'large',
                                                                ui: 's-button',
                                                                cls: 's-grey',
                                                                disabled: false,
                                                                handler: function() {
                                                                    winFarmer.close();
                                                                }
                                                            }]
                                                        }).show();
                                                    }
                                                }
                                            }, {
                                                iconCls: 'search',
                                                cls: 's-grey',
                                                xtype: 'button',
                                                id: 'isFarmer',
                                                style: 'margin-left:5px',
                                                handler: function() {

                                                    var winFarmer = Ext.create('widget.window', {
                                                        title: 'Farmer Data',
                                                        closable: true,
                                                        id: 'winFarmer',
                                                        modal: true,
                                                        width: 800,
                                                        minWidth: 350,
                                                        height: 500,
                                                        layout: {
                                                            type: 'fit'
                                                        },
                                                        items: [{
                                                            xtype: 'gridpanel',
                                                            id: 'grid_farmer',
                                                            store: store_farmer,
                                                            style: 'border:1px solid #CCC;',
                                                            width: '100%',
                                                            minHeight: 350,
                                                            loadMask: true,
                                                            selType: 'rowmodel',
                                                            dockedItems: [{
                                                                xtype: 'pagingtoolbar',
                                                                store: store_farmer, // same store GridPanel is using
                                                                dock: 'bottom',
                                                                displayInfo: true
                                                            }, {
                                                                xtype: 'toolbar',
                                                                items: [{
                                                                    xtype: 'textfield',
                                                                    fieldLabel: lang('Key'),
                                                                    name: 'farmerKey',
                                                                    id: 'farmerKey'
                                                                }, {
                                                                    xtype: 'combo',
                                                                    id: 'cmb-group-search-add-member',
                                                                    fieldLabel: lang('Kelompok'),
                                                                    allowBlank: false,
                                                                    width: 350,
                                                                    store: Ext.create('Ext.data.Store', {
                                                                        fields: ['CPGid', 'GroupName'],
                                                                        autoLoad: true,
                                                                        proxy: {
                                                                            type: 'rest',
                                                                            url: m_api + 'member/combogroup',
                                                                            reader: {
                                                                                type: 'json',
                                                                                root: 'data',
                                                                                totalProperty: 'total'
                                                                            }
                                                                        }
                                                                    }),
                                                                    displayField: 'GroupName',
                                                                    valueField: 'CPGid',
                                                                    name: 'CPGid'
                                                                }, {
                                                                    xtype: 'button',
                                                                    margin: '0px 0px 0px 6px',
                                                                    text: 'Search',
                                                                    handler: function() {
                                                                        store_farmer.getProxy().extraParams = {
                                                                            key: Ext.getCmp('farmerKey').getValue(),
                                                                            cpg: Ext.getCmp('cmb-group-search-add-member').getValue(),
                                                                        };
                                                                        store_farmer.load({
                                                                            params: {
                                                                                start: 0
                                                                            }
                                                                        });
                                                                    }
                                                                }]
                                                            }],
                                                            columns: [{
                                                                text: 'No',
                                                                xtype: 'rownumberer',
                                                                align: 'center',
                                                                width: '5%'
                                                            }, {
                                                                text: 'Farmer ID',
                                                                width: '15%',
                                                                dataIndex: 'FarmerID'
                                                            }, {
                                                                text: 'Name',
                                                                width: '25%',
                                                                dataIndex: 'FarmerName'
                                                            }, {
                                                                text: 'Tersertifikasi',
                                                                width: '15%',
                                                                dataIndex: 'isCertified',
                                                                renderer: function(value, metaData, record, row, col, store, gridView) {
                                                                    if (value * 1 == 1) {
                                                                        return 'YA';
                                                                    } else {
                                                                        return 'TIDAK';
                                                                    }
                                                                }
                                                            }, {
                                                                text: 'District',
                                                                width: '15%',
                                                                dataIndex: 'District'
                                                            }, {
                                                                text: lang('Kelompok'),
                                                                width: '15%',
                                                                dataIndex: 'GroupName'
                                                            }, {
                                                                text: 'Village',
                                                                width: '15%',
                                                                dataIndex: 'Village'
                                                            }, {
                                                                menuDisabled: true,
                                                                sortable: false,
                                                                xtype: 'actioncolumn',
                                                                width: '7%',
                                                                align: 'center',
                                                                items: [{
                                                                    icon: varjs.config.base_url + 'images/icons/silk/page_white_edit.png',
                                                                    tooltip: lang('Copy'),
                                                                    handler: function(grid, rowIndex, colIndex) {
                                                                        var rec = grid.getStore().getAt(rowIndex);
                                                                        var form = Ext.getCmp('dataForm').getForm().load({
                                                                            url: m_farmer,
                                                                            method: 'GET',
                                                                            params: {
                                                                                id: rec.data.FarmerID
                                                                            },
                                                                            success: function(c, r) {
                                                                                var data = r.result.data;
                                                                                Ext.getCmp('farmerID').setValue(data.FarmerID);

                                                                                if (data.gender == '1') {
                                                                                    Ext.getCmp('gender1').setValue(true);
                                                                                } else if (data.gender == '2') {
                                                                                    Ext.getCmp('gender2').setValue(true);
                                                                                }
                                                                                Ext.getCmp('subdistrictID').store.load();
                                                                                Ext.getCmp('subdistrictID').setValue(data.SubDistrictID);
                                                                                switch (data.MaritalStatus) {
                                                                                    case '1':
                                                                                        Ext.getCmp('maritalStatus1').setValue(true);
                                                                                        break;
                                                                                    case '2':
                                                                                        Ext.getCmp('maritalStatus2').setValue(true);
                                                                                        break;
                                                                                    case '3':
                                                                                        Ext.getCmp('maritalStatus3').setValue(true);
                                                                                        break;
                                                                                }
                                                                                winFarmer.close();
                                                                            }
                                                                        });
                                                                    }
                                                                }]
                                                            }]
                                                        }],
                                                        buttons: [{
                                                            text: 'Close',
                                                            margin: '5px',
                                                            scale: 'large',
                                                            ui: 's-button',
                                                            cls: 's-grey',
                                                            disabled: false,
                                                            handler: function() {
                                                                winFarmer.close();
                                                            }
                                                        }]
                                                    }).show();

                                                    store_farmer.load({
                                                        params: {
                                                            key: Ext.getCmp('farmerKey').getValue()
                                                        }
                                                    });
                                                }
                                            }]
                                        }, {
                                            xtype: 'combo',
                                            fieldLabel: lang('Jenis Anggota<b style="color:red">*</b>'),
                                            allowBlank: false,
                                            width: 350,
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['typeID', 'typeName'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + 'member/combomembertype',
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            displayField: 'typeName',
                                            valueField: 'typeID',
                                            name: 'typeID'
                                        }, {
                                            xtype: 'textfield',
                                            anchor: '100%',
                                            fieldLabel: lang('Nama <b style="color:red">*</b>'),
                                            allowBlank: false,
                                            name: 'name'
                                        }, {
                                            xtype: 'textfield',
                                            width: 400,
                                            fieldLabel: lang('No. KTP<b style="color:red">*</b>'),
                                            id: 'identityNumber',
                                            name: 'identityNumber',
                                            allowBlank: false
                                        }, {
                                            xtype: 'fieldcontainer',
                                            fieldLabel: lang('Jenis Kelamin <b style="color:red">*</b>'),
                                            layout: 'hbox',
                                            allowBlank: false,
                                            items: [{
                                                xtype: 'radio',
                                                id: 'gender1',
                                                inputValue: '1',
                                                boxLabel: 'Male',
                                                name: 'gender',
                                                checked: true,
                                            }, {
                                                xtype: 'radio',
                                                id: 'gender2',
                                                inputValue: '2',
                                                fieldStyle: 'margin-left:75px',
                                                boxLabel: 'Female',
                                                name: 'gender'
                                            }]
                                        }, {
                                            xtype: 'fieldcontainer',
                                            fieldLabel: lang('Tempat, Tgl. Lahir <b style="color:red">*</b>'),
                                            layout: 'hbox',
                                            items: [{
                                                xtype: 'textfield',
                                                id: 'placeOfBirth',
                                                margin: '0 5 0 0',
                                                name: 'placeOfBirth',
                                                allowBlank: false
                                            }, {
                                                xtype: 'datefield',
                                                id: 'dateOfBirth',
                                                name: 'dateOfBirth',
                                                allowBlank: false,
                                                format: 'Y-m-d',
                                                width: 100,
                                                altFormats: 'Y-m-d',
                                                submitFormat: 'Y-m-d'
                                            }]
                                        }, {
                                            xtype: 'textarea',
                                            fieldLabel: 'Alamat <b style="color:red">*</b>',
                                            width: 550,
                                            id: 'address',
                                            name: 'address',
                                            allowBlank: false
                                        }, {
                                            xtype: 'fieldcontainer',
                                            fieldLabel: lang('Desa <b style="color:red">*</b>'),
                                            layout: 'hbox',
                                            items: [{
                                                id: 'districtID',
                                                name: 'districtID',
                                                // allowBlank:false,
                                                xtype: 'combo',
                                                submitValue: false,
                                                emptyText: '-- District --',
                                                multiSelect: false,
                                                store: mc_district,
                                                displayField: 'label',
                                                valueField: 'id',
                                                margin: '0 5 0 0',
                                                queryMode: 'local',
                                                hidden: true,
                                                listeners: {
                                                    change: function(cb, nv, ov) {
                                                        mc_subdistrict.load({
                                                            params: {
                                                                district: Ext.getCmp('districtID').getValue()
                                                            }
                                                        });
                                                    }
                                                }
                                            }, {
                                                id: 'subdistrictID',
                                                name: 'subdistrictID',
                                                xtype: 'combo',
                                                allowBlank: false,
                                                emptyText: lang('-- Subdistrict --'),
                                                multiSelect: false,
                                                store: mc_subdistrict,
                                                displayField: 'label',
                                                valueField: 'id',
                                                margin: '0 5 0 0',
                                                queryMode: 'local',
                                                listeners: {
                                                    change: function(cb, nv, ov) {
                                                        mc_village.load({
                                                            params: {
                                                                sub_district: Ext.getCmp('subdistrictID').getValue()
                                                            }
                                                        });
                                                    }
                                                }
                                            }, {
                                                id: 'villageID',
                                                name: 'villageID',
                                                allowBlank: false,
                                                xtype: 'combo',
                                                emptyText: lang('-- Village --'),
                                                multiSelect: false,
                                                store: mc_village,
                                                width: 270,
                                                displayField: 'label',
                                                valueField: 'id',
                                                queryMode: 'local'
                                            }]
                                        }, {
                                            xtype: 'textfield',
                                            fieldLabel: lang('Telp. <b style="color:red">*</b>'),
                                            width: 250,
                                            id: 'phone',
                                            name: 'phone',
                                            allowBlank: false
                                        }, {
                                            xtype: 'textfield',
                                            fieldLabel: lang('Pekerjaan <b style="color:red">*</b>'),
                                            id: 'job',
                                            width: 350,
                                            name: 'job',
                                            allowBlank: false
                                        }, {
                                            xtype: 'fieldcontainer',
                                            fieldLabel: lang('Status Pernikahan'),
                                            defaultType: 'radiofield',
                                            defaults: {
                                                flex: 1
                                            },

                                            allowBlank: false,
                                            layout: 'hbox',
                                            items: [{
                                                boxLabel: lang('Lajang'),
                                                name: 'maritalStatus',
                                                checked: true,
                                                inputValue: '1',
                                                id: 'maritalStatus1'
                                            }, {
                                                boxLabel: lang('Menikah'),
                                                name: 'maritalStatus',
                                                inputValue: '2',
                                                id: 'maritalStatus2'
                                            }, {
                                                boxLabel: lang('Janda/Duda'),
                                                xtype: 'radio',
                                                name: 'maritalStatus',
                                                inputValue: '3',
                                                id: 'maritalStatus3'
                                            }]
                                        }, {
                                            name: 'status',
                                            fieldLabel: 'Status Anggota',
                                            xtype: 'combo',
                                            hidden: true,
                                            multiSelect: false,
                                            store: StatusMemberStore,
                                            displayField: 'StatusMemberName',
                                            valueField: 'StatusMemberID',
                                            id: 'StatusMemberID',
                                            margin: '0 5 0 0',
                                            queryMode: 'local',
                                            listeners: {
                                                change: function(cb, nv, ov) {

                                                }
                                            }
                                        }]
                                    }, {
                                        xtype: 'container',
                                        columnWidth: .3,
                                        layout: {
                                            type: 'fit'
                                        },
                                        items: [{
                                                xtype: 'fieldset',
                                                margin: 5,
                                                // bodyStyle:'margin-left:15px;',
                                                title: 'Keanggotaan',
                                                defaults: {
                                                    labelWidth: 120
                                                },
                                                items: [{
                                                    xtype: 'numericfield',
                                                    hideTriger: true,
                                                    fieldLabel: 'Uang Pangkal <b style="color:red">*</b>',
                                                    id: 'RegUangPangkal',
                                                    name: 'RegUangPangkal',
                                                    width: 300,
                                                    allowBlank: false
                                                }, {
                                                    xtype: 'numericfield',
                                                    hideTriger: true,
                                                    width: 300,
                                                    fieldLabel: 'Simpanan Pokok <b style="color:red">*</b>',
                                                    id: 'RegSimpananPokok',
                                                    name: 'RegSimpananPokok',
                                                    allowBlank: false
                                                }, {
                                                    xtype: 'numericfield',
                                                    hideTriger: true,
                                                    width: 300,
                                                    fieldLabel: lang('Simpanan Wajib <b style="color:red">*</b>'),
                                                    id: 'RegSimpananWajib',
                                                    name: 'RegSimpananWajib',
                                                    allowBlank: false
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
                                                items: [

                                                    {
                                                        xtype: 'form',
                                                        id: 'uploaderImage',
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
                                                            fieldStyle: 'border:1px solid #ccc;',
                                                            anchor: '100%',
                                                            height: 150,
                                                            submitValue: false,
                                                            id: 'img-photo-add-member',
                                                            style: 'margin-bottom:5px'
                                                        }, {
                                                            xtype: 'fileuploadfield',
                                                            buttonOnly: true,
                                                            buttonConfig: {
                                                                width: 125
                                                            },
                                                            submitValue: false,
                                                            name: 'memberPhoto',
                                                            id: 'up3',
                                                            buttonText: 'Upload',
                                                            listeners: {
                                                                'change': function(fb, v, c) {
                                                                    base64Converter(fb)
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'button',
                                                            hidden: true,
                                                            width: 125,
                                                            submitValue: false,
                                                            name: 'memberPhotoCam',
                                                            text: 'Take Picture',
                                                            handler: function() {
                                                                takeSignWindow.show();

                                                                var canvas = document.getElementById("canvasphoto"),
                                                                    context = canvas.getContext("2d"),
                                                                    video = document.getElementById("videophoto"),
                                                                    videoObj = {
                                                                        "video": true
                                                                    },
                                                                    errBack = function(error) {
                                                                        console.log("Video capture error: ", error.code);
                                                                    };

                                                                if (navigator.getUserMedia) { // Standard
                                                                    navigator.getUserMedia(videoObj, function(stream) {
                                                                        video.src = stream;
                                                                        video.play();
                                                                    }, errBack);
                                                                } else if (navigator.webkitGetUserMedia) { // WebKit-prefixed
                                                                    navigator.webkitGetUserMedia(videoObj, function(stream) {
                                                                        video.src = window.webkitURL.createObjectURL(stream);
                                                                        video.play();
                                                                    }, errBack);
                                                                } else if (navigator.mozGetUserMedia) { // Firefox-prefixed
                                                                    navigator.mozGetUserMedia(videoObj, function(stream) {
                                                                        video.src = window.URL.createObjectURL(stream);
                                                                        video.play();
                                                                    }, errBack);
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'hidden',
                                                            name: 'memberPhotoPath',
                                                            id: 'hidden-add-member-photo-path'
                                                        }, {
                                                            xtype: 'hidden',
                                                            name: 'memberPhotoName',
                                                            id: 'hidden-add-member-photo-name'
                                                        }]
                                                    }, {
                                                        xtype: 'form',
                                                        id: 'uploadSign1',
                                                        width: 125,
                                                        layout: {
                                                            type: 'anchor'
                                                        },
                                                        items: [{
                                                            xtype: 'displayfield',
                                                            anchor: '100%',
                                                            fieldStyle: 'text-align:center;font-weight:bold',
                                                            value: lang('Tanda Tangan')
                                                        }, {
                                                            xtype: 'image',
                                                            fieldStyle: 'border:1px solid #ccc;',
                                                            anchor: '100%',
                                                            height: 150,
                                                            submitValue: false,
                                                            id: 'img-sigi-add-member',
                                                            style: 'margin-bottom:5px'
                                                        }, {
                                                            xtype: 'fileuploadfield',
                                                            buttonOnly: true,
                                                            buttonConfig: {
                                                                width: 125
                                                            },
                                                            submitValue: false,
                                                            name: 'memberSignature',
                                                            buttonText: 'Upload',
                                                            listeners: {
                                                                'change': function(fb, v) {
                                                                    base64Converter(fb, 'sigi');
                                                                    // var form = fb.up('form');
                                                                    // form.getForm().submit({
                                                                    //     url: m_api + 'member/coop_member_image',
                                                                    //     success: function(c, v) {

                                                                    //         var data = v.result.data;

                                                                    //         // Use createObjectURL to make a URL for the blob
                                                                    //         var image = Ext.getCmp('img-sigi-add-member');

                                                                    //         image.setSrc(m_api + data);

                                                                    //         Ext.getCmp('hidden-add-member-sigi-path').setValue(v.result.path);
                                                                    //         Ext.getCmp('hidden-add-member-sigi-name').setValue(v.result.name);
                                                                    //     },
                                                                    //     failure: function(c, v) {
                                                                    //         Ext.MessageBox.alert('Info', v.result.error);
                                                                    //     }
                                                                    // });
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'button',
                                                            hidden: true,
                                                            width: 125,
                                                            name: 'memberSigiCam',
                                                            text: 'Take Picture',
                                                            handler: function() {
                                                                takePhotoWindow.show();

                                                                var canvas = document.getElementById("canvas"),
                                                                    context = canvas.getContext("2d"),
                                                                    video = document.getElementById("video"),
                                                                    videoObj = {
                                                                        "video": true
                                                                    },
                                                                    errBack = function(error) {
                                                                        console.log("Video capture error: ", error.code);
                                                                    };

                                                                if (navigator.getUserMedia) { // Standard
                                                                    navigator.getUserMedia(videoObj, function(stream) {
                                                                        video.src = stream;
                                                                        video.play();
                                                                    }, errBack);
                                                                } else if (navigator.webkitGetUserMedia) { // WebKit-prefixed
                                                                    navigator.webkitGetUserMedia(videoObj, function(stream) {
                                                                        video.src = window.webkitURL.createObjectURL(stream);
                                                                        video.play();
                                                                    }, errBack);
                                                                } else if (navigator.mozGetUserMedia) { // Firefox-prefixed
                                                                    navigator.mozGetUserMedia(videoObj, function(stream) {
                                                                        video.src = window.URL.createObjectURL(stream);
                                                                        video.play();
                                                                    }, errBack);
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'hidden',
                                                            name: 'memberSigiPath',
                                                            id: 'hidden-add-member-sigi-path'
                                                        }, {
                                                            xtype: 'hidden',
                                                            name: 'memberSigiName',
                                                            id: 'hidden-add-member-sigi-name'
                                                        }]
                                                    }
                                                ]
                                            }, {
                                                xtype: 'fieldset',
                                                margin: 5,
                                                // title:'Family',
                                                defaults: {
                                                    labelWidth: 1
                                                },
                                                items: [{
                                                    xtype: 'component',
                                                    html: '<center>Tipe file yang diperbolehkan adalah gif/jpg/png/bmp dengan ukuran file maksimal 900KB.</center>'
                                                }]
                                            },

                                            {
                                                xtype: 'fieldset',
                                                margin: 5,
                                                hidden: true,
                                                title: 'Family',
                                                defaults: {
                                                    labelWidth: 90
                                                },
                                                items: [{
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Name <b style="color:red">*</b>',
                                                    id: 'familyName',
                                                    name: 'familyName',
                                                    width: 300,
                                                    // allowBlank: false
                                                }, {
                                                    xtype: 'textfield',
                                                    width: 300,
                                                    fieldLabel: 'Identity <b style="color:red">*</b>',
                                                    id: 'familyIdentityNumber',
                                                    name: 'familyIdentityNumber',
                                                    // allowBlank: false
                                                }, {
                                                    xtype: 'textfield',
                                                    width: 300,
                                                    fieldLabel: lang('Relationship <b style="color:red">*</b>'),
                                                    id: 'familyRelation',
                                                    name: 'familyRelation',
                                                    // allowBlank: false
                                                }, {
                                                    xtype: 'textarea',
                                                    fieldLabel: lang('Address'),
                                                    id: 'familyAddress',
                                                    name: 'familyAddress',
                                                    width: 300
                                                }, {
                                                    xtype: 'textfield',
                                                    fieldLabel: lang('Phone'),
                                                    id: 'familyPhone',
                                                    name: 'familyPhone',
                                                    width: 250
                                                }]
                                            }
                                        ]
                                    }]
                                }),
                                buttons: [{
                                    id: 'saveButton',
                                    text: 'Save',
                                    margin: '5px',
                                    scale: 'large',
                                    ui: 's-button',
                                    cls: 's-blue',
                                    handler: function(c) {
                                        var StatusMemberID = Ext.getCmp('StatusMemberID').getValue();
                                        let formImg = [Ext.getCmp('uploaderImage'), Ext.getCmp('uploadSign1')];
                                        var form = Ext.getCmp('dataForm').getForm();
                                        doUpload(formImg).then(s => {
                                            form.submit({
                                                url: m_crud,
                                                method: 'POST',
                                                waitMsg: 'Sending data ...',
                                                success: function(fp, o) {
                                                    Ext.MessageBox.alert('Success', 'Data saved.');
                                                    win.close(this, function() {});
                                                    store.load({
                                                        params: {
                                                            'newadd': true
                                                        }
                                                    });
                                                    // store.load();
                                                }
                                            });
                                        })

                                    }
                                }, {
                                    text: 'Close',
                                    margin: '5px',
                                    scale: 'large',
                                    ui: 's-button',
                                    cls: 's-grey',
                                    disabled: false,
                                    handler: function() {

                                        var photo = Ext.getCmp('hidden-add-member-sigi-path').getValue();
                                        var sigi = Ext.getCmp('hidden-add-member-photo-path').getValue();

                                        Ext.Ajax.request({
                                            method: 'POST',
                                            url: m_api + '/member/cancel_image',
                                            params: {
                                                photo: Ext.JSON.encode(photo),
                                                sigi: Ext.JSON.encode(sigi)
                                            }
                                        });

                                        win.close();

                                        // console.log(document.getElementById("video"));
                                        // $("video").each(function () { this.pause() });
                                        // document.getElementById('video').style.display = 'none';
                                        // var video = document.getElementById('video');
                                        // video.pause();
                                        // video.src="";
                                        var MediaStream = window.MediaStream;

                                        if (typeof MediaStream === 'undefined' && typeof webkitMediaStream !== 'undefined') {
                                            MediaStream = webkitMediaStream;
                                        }

                                        /*global MediaStream:true */
                                        if (typeof MediaStream !== 'undefined' && !('stop' in MediaStream.prototype)) {
                                            MediaStream.prototype.stop = function() {
                                                this.getAudioTracks().forEach(function(track) {
                                                    track.stop();
                                                });

                                                this.getVideoTracks().forEach(function(track) {
                                                    track.stop();
                                                });
                                            };
                                        }

                                    }
                                }]
                            }).show();


                            //new member set to candidate
                            Ext.getCmp('StatusMemberID').setValue('4');
                            Ext.getCmp('StatusMemberID').setReadOnly(true);

                            //predifined image
                            // alert(m_api+'images/Photo/default-user.png')
                            Ext.getCmp('img-photo-add-member').setSrc(m_api + 'images/Photo/default-user.png');
                            Ext.getCmp('img-sigi-add-member').setSrc(m_api + 'images/signature.png');
                        }
                    }, {
                        iconCls: 'edit',
                        text: lang('Detil Anggota'),
                        scope: this,
                        handler: function() {
                            // Ext.getCmp('saveButton').hide();

                            var sm = Ext.getCmp('grid-member').getSelectionModel().getSelection()[0];
                            if (!sm) {
                                Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                                return false;
                            } else {
                                var id = sm.get('id');



                                store_savingmember.on('beforeload', function(store, operation, eOpts) {
                                    operation.params = {
                                        'id': id
                                    };
                                });
                                store_savingmember.load();

                                var mc_membertype = Ext.create('Ext.data.Store', {
                                    fields: ['id', 'label'],
                                    autoLoad: true,
                                    pageSize: 10,
                                    proxy: {
                                        type: 'ajax',
                                        url: m_membertype,
                                        reader: {
                                            type: 'json',
                                            root: 'data'
                                        }
                                    }
                                });
                                var mc_district = Ext.create('Ext.data.Store', {
                                    fields: ['id', 'label'],
                                    autoLoad: true,
                                    pageSize: 10,
                                    proxy: {
                                        type: 'ajax',
                                        url: m_district,
                                        reader: {
                                            type: 'json',
                                            root: 'data'
                                        }
                                    }
                                });
                                var mc_subdistrict = Ext.create('Ext.data.Store', {
                                    fields: ['id', 'label'],
                                    autoLoad: true,
                                    pageSize: 10,
                                    proxy: {
                                        type: 'ajax',
                                        url: m_subdistrict,
                                        reader: {
                                            type: 'json',
                                            root: 'data'
                                        }
                                    }
                                });
                                var mc_village = Ext.create('Ext.data.Store', {
                                    fields: ['id', 'label'],
                                    autoLoad: false,
                                    pageSize: 10,
                                    proxy: {
                                        type: 'ajax',
                                        url: m_village,
                                        reader: {
                                            type: 'json',
                                            root: 'data'
                                        }
                                    }
                                });
                                var mc_identity = Ext.create('Ext.data.Store', {
                                    fields: ['id', 'label'],
                                    autoLoad: true,
                                    pageSize: 10,
                                    proxy: {
                                        type: 'ajax',
                                        url: m_identity,
                                        reader: {
                                            type: 'json',
                                            root: 'data'
                                        }
                                    }
                                });
                                var mc_status = Ext.create('Ext.data.Store', {
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



                                var win = Ext.create('widget.window', {
                                    id: 'win-edit-member',
                                    modal: true,
                                    width: '80%',
                                    height: 650,
                                    autoScroll: true,
                                    title: 'Detil Anggota',
                                    style: 'border:5px solid #799143',
                                    bodyStyle: 'background:#fff;',
                                    listeners: {
                                        'add': function() {
                                            var form = Ext.getCmp('frm-edit-member');
                                            disableChildren();
                                            form.getForm().load({
                                                url: m_crud,
                                                method: 'GET',
                                                params: {
                                                    id: id
                                                },
                                                success: function(c, r) {
                                                    var data = r.result.data;
                                                    var farmer = data.farmerID;
                                                    if (farmer === null || farmer.length === 0) {
                                                        Ext.getCmp('tab-member-detail').remove(Ext.getCmp('member-farmer-detail'));
                                                    }
                                                    console.log(data);
                                                    if (data.memberPhoto == null) {
                                                        Ext.getCmp('img-photo-add-member').setSrc(m_api + 'images/Photo/default-user.png');
                                                    } else {
                                                        Ext.getCmp('img-photo-add-member').setSrc(m_api + data.memberPhoto);
                                                    }

                                                    if (data.memberSignature == null) {
                                                        Ext.getCmp('img-sigi-add-member').setSrc(m_api + 'images/signature.png');
                                                    } else {
                                                        Ext.getCmp('img-sigi-add-member').setSrc(m_api + data.memberSignature);
                                                    }


                                                    if (data.memberPhoto === null) {
                                                        Ext.getCmp('fieldsetKeterangan').show();
                                                    } else {
                                                        Ext.getCmp('fieldsetKeterangan').hide();
                                                    }

                                                    win.show();

                                                    Ext.getCmp('EditRegUangPangkal').setValue(data.uangPangkal);
                                                    Ext.getCmp('EditRegSimpananPokok').setValue(data.savingPokok);
                                                    Ext.getCmp('EditRegSimpananWajib').setValue(data.savingWajib);

                                                    if (data.farmerID !== null) {
                                                        Ext.getCmp('isCertifiedDisplay').show();
                                                        if (data.isCertified * 1 == 1) {
                                                            Ext.getCmp('isCertifiedDisplay').setValue('YA');
                                                        } else {
                                                            Ext.getCmp('isCertifiedDisplay').setValue('TIDAK');
                                                        }
                                                    } else {
                                                        Ext.getCmp('isCertifiedDisplay').hide();
                                                    }

                                                    //here
                                                    mc_subdistrict.load({
                                                        params: {
                                                            district: data.DistrictID
                                                        }
                                                    });
                                                    Ext.getCmp('subdistrictID').setValue(data.SubDistrictID);

                                                }
                                            });
                                        }
                                    },
                                    items: [{
                                        xtype: 'tabpanel',
                                        style: 'margin-top:5px;',
                                        plain: true,
                                        tabBar: {
                                            style: 'padding-left:5px;background:#fff;'
                                        },
                                        height: 550,
                                        id: 'tab-member-detail',
                                        items: [{
                                                xtype: 'DataActivity',
                                                id: 'FinancialDetails',
                                                title: 'Detil Keuangan',
                                                listeners: {
                                                    activate: function() {
                                                        Ext.getCmp('saveButton').hide();

                                                        var tabs = Ext.getCmp('tab-member-detail');

                                                        store_saving.on('beforeload', function(store, operation, eOpts) {
                                                            operation.params = {
                                                                'id': id
                                                            };
                                                        });
                                                        store_saving.load();

                                                        Ext.Ajax.request({
                                                            url: m_saving + '_check',
                                                            method: 'GET',
                                                            params: {
                                                                memberID: id
                                                            },
                                                            success: function(fp, o) {
                                                                var r = Ext.decode(fp.responseText);
                                                                if (r.success) {
                                                                    tabs.child('#FinancialDetails').tab.show();
                                                                    insertFinStatus();
                                                                } else {
                                                                    Ext.getCmp('FinancialDetails').hide();
                                                                    tabs.child('#FinancialDetails').tab.hide();
                                                                    var frmedtmm = tabs.child('#frm-edit-member');
                                                                    tabs.setActiveTab(frmedtmm);
                                                                }
                                                            }
                                                        });

                                                        insertFinStatus();
                                                    }
                                                }
                                            }, Ext.create('Ext.form.Panel', {
                                                bodyPadding: 5,
                                                id: 'frm-edit-member',
                                                title: 'Detil Keanggotaan',
                                                fieldDefaults: {
                                                    labelAlign: 'left',
                                                    labelWidth: 120
                                                },
                                                trackResetOnLoad: true,
                                                layout: {
                                                    type: 'column'
                                                },
                                                listeners: {
                                                    activate: function() {
                                                        Ext.getCmp('saveButton').show();
                                                    }
                                                },
                                                items: [{
                                                    xtype: 'fieldset',
                                                    title: lang('Informasi Anggota'),
                                                    columnWidth: .7,
                                                    style: 'padding-bottom:2px',
                                                    items: [{
                                                        xtype: 'checkbox',
                                                        fieldStyle: 'margin-left:125px',
                                                        boxLabel: 'Petani SCPP',
                                                        name: 'scpp',
                                                        submitValue: false,
                                                        id: 'checkbox-scpp-frm-edit-member',
                                                        listeners: {
                                                            change: function(c, v) {
                                                                if (v === true) {
                                                                    Ext.getCmp('farmerid_container').enable();
                                                                } else {
                                                                    Ext.getCmp('farmerID').reset();
                                                                    Ext.getCmp('frm-edit-member').getForm().reset();
                                                                    Ext.getCmp('farmerid_container').disable();
                                                                }
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: lang('Farmer ID'),
                                                        id: 'farmerid_container',
                                                        hidden: false,
                                                        layout: 'hbox',
                                                        align: 'stretch',
                                                        bodyStyle: 'padding: 10px',
                                                        disabled: true,
                                                        items: [{
                                                            xtype: 'textfield',
                                                            id: 'farmerID',
                                                            name: 'farmerID',
                                                            readOnly: true,
                                                            listeners: {
                                                                focus: function() {
                                                                    var winFarmer = Ext.create('widget.window', {
                                                                        title: 'Farmer Data',
                                                                        closable: true,
                                                                        id: 'winFarmer',
                                                                        modal: true,
                                                                        width: 800,
                                                                        minWidth: 350,
                                                                        height: 500,
                                                                        layout: {
                                                                            type: 'fit'
                                                                        },
                                                                        items: [{
                                                                            xtype: 'gridpanel',
                                                                            id: 'grid_farmer',
                                                                            store: store_farmer,
                                                                            style: 'border:1px solid #CCC;',
                                                                            width: '100%',
                                                                            minHeight: 350,
                                                                            loadMask: true,
                                                                            selType: 'rowmodel',
                                                                            dockedItems: [{
                                                                                xtype: 'pagingtoolbar',
                                                                                store: store_farmer, // same store GridPanel is using
                                                                                dock: 'bottom',
                                                                                displayInfo: true
                                                                            }, {
                                                                                xtype: 'toolbar',
                                                                                items: [{
                                                                                    xtype: 'textfield',
                                                                                    fieldLabel: lang('Key'),
                                                                                    name: 'farmerKey',
                                                                                    id: 'farmerKey'
                                                                                }, {
                                                                                    xtype: 'combo',
                                                                                    id: 'cmb-group-search-edit-member',
                                                                                    fieldLabel: lang('Kelompok'),
                                                                                    allowBlank: false,
                                                                                    width: 350,
                                                                                    store: Ext.create('Ext.data.Store', {
                                                                                        fields: ['CPGid', 'GroupName'],
                                                                                        autoLoad: true,
                                                                                        proxy: {
                                                                                            type: 'rest',
                                                                                            url: m_api + 'member/combogroup',
                                                                                            reader: {
                                                                                                type: 'json',
                                                                                                root: 'data',
                                                                                                totalProperty: 'total'
                                                                                            }
                                                                                        }
                                                                                    }),
                                                                                    displayField: 'GroupName',
                                                                                    valueField: 'CPGid',
                                                                                    name: 'CPGid'
                                                                                }, {
                                                                                    xtype: 'button',
                                                                                    margin: '0px 0px 0px 6px',
                                                                                    text: 'Search',
                                                                                    handler: function() {
                                                                                        store_farmer.getProxy().extraParams = {
                                                                                            key: Ext.getCmp('farmerKey').getValue(),
                                                                                            start: 0,
                                                                                            cpg: Ext.getCmp('cmb-group-search-edit-member').getValue(),
                                                                                        };
                                                                                        store_farmer.load();
                                                                                    }
                                                                                }]
                                                                            }],
                                                                            columns: [{
                                                                                text: 'No',
                                                                                xtype: 'rownumberer',
                                                                                align: 'center',
                                                                                width: '5%'
                                                                            }, {
                                                                                text: 'Farmer ID',
                                                                                width: '15%',
                                                                                dataIndex: 'FarmerID'
                                                                            }, {
                                                                                text: 'Name',
                                                                                width: '25%',
                                                                                dataIndex: 'FarmerName'
                                                                            }, {
                                                                                text: 'District',
                                                                                width: '15%',
                                                                                dataIndex: 'District'
                                                                            }, {
                                                                                text: lang('Kelompok'),
                                                                                width: '15%',
                                                                                dataIndex: 'GroupName'
                                                                            }, {
                                                                                text: 'Village',
                                                                                width: '15%',
                                                                                dataIndex: 'Village'
                                                                            }, {
                                                                                menuDisabled: true,
                                                                                sortable: false,
                                                                                xtype: 'actioncolumn',
                                                                                width: '7%',
                                                                                align: 'center',
                                                                                items: [{
                                                                                    icon: varjs.config.base_url + 'images/icons/silk/page_white_edit.png',
                                                                                    tooltip: lang('Copy'),
                                                                                    handler: function(grid, rowIndex, colIndex) {
                                                                                        var rec = grid.getStore().getAt(rowIndex);
                                                                                        var form = Ext.getCmp('dataForm').getForm().load({
                                                                                            url: m_farmer,
                                                                                            method: 'GET',
                                                                                            params: {
                                                                                                id: rec.data.FarmerID
                                                                                            },
                                                                                            success: function(c, r) {
                                                                                                var data = r.result.data;
                                                                                                Ext.getCmp('farmerID').setValue(data.FarmerID);

                                                                                                if (data.gender == '1') {
                                                                                                    Ext.getCmp('gender1').setValue(true);
                                                                                                } else if (data.gender == '2') {
                                                                                                    Ext.getCmp('gender2').setValue(true);
                                                                                                }
                                                                                                Ext.getCmp('subdistrictID').store.load();
                                                                                                Ext.getCmp('subdistrictID').setValue(data.SubDistrictID);
                                                                                                switch (data.MaritalStatus) {
                                                                                                    case '1':
                                                                                                        Ext.getCmp('maritalStatus1').setValue(true);
                                                                                                        break;
                                                                                                    case '2':
                                                                                                        Ext.getCmp('maritalStatus2').setValue(true);
                                                                                                        break;
                                                                                                    case '3':
                                                                                                        Ext.getCmp('maritalStatus3').setValue(true);
                                                                                                        break;
                                                                                                }
                                                                                                winFarmer.close();
                                                                                            }
                                                                                        });
                                                                                    }
                                                                                }]
                                                                            }]
                                                                        }],
                                                                        buttons: [{
                                                                            text: 'Close',
                                                                            margin: '5px',
                                                                            scale: 'large',
                                                                            ui: 's-button',
                                                                            cls: 's-grey',
                                                                            disabled: false,
                                                                            handler: function() {
                                                                                winFarmer.close();
                                                                            }
                                                                        }]
                                                                    }).show();
                                                                }
                                                            }
                                                        }, {
                                                            iconCls: 'search',
                                                            cls: 's-grey',
                                                            xtype: 'button',
                                                            id: 'isFarmer',
                                                            style: 'margin-left:5px',
                                                            handler: function() {
                                                                var winFarmer = Ext.create('widget.window', {
                                                                    title: 'Daftar Petani',
                                                                    closable: true,
                                                                    id: 'winFarmer',
                                                                    modal: true,
                                                                    width: 800,
                                                                    minWidth: 350,
                                                                    height: 500,
                                                                    layout: {
                                                                        type: 'fit'
                                                                    },
                                                                    listeners: {
                                                                        render: function() {
                                                                            store_farmer.load({
                                                                                params: {
                                                                                    key: Ext.getCmp('farmerKey').getValue()
                                                                                }
                                                                            });
                                                                        }
                                                                    },
                                                                    items: [{
                                                                        xtype: 'gridpanel',
                                                                        id: 'grid_farmer',
                                                                        store: store_farmer,
                                                                        style: 'border:1px solid #CCC;',
                                                                        width: '100%',
                                                                        minHeight: 350,
                                                                        loadMask: true,
                                                                        selType: 'rowmodel',
                                                                        dockedItems: [{
                                                                            xtype: 'pagingtoolbar',
                                                                            store: store_farmer, // same store GridPanel is using
                                                                            dock: 'bottom',
                                                                            displayInfo: true
                                                                        }, {
                                                                            xtype: 'toolbar',
                                                                            items: [{
                                                                                xtype: 'textfield',
                                                                                fieldLabel: lang('Key'),
                                                                                name: 'farmerKey',
                                                                                id: 'farmerKey'
                                                                            }, {
                                                                                xtype: 'combo',
                                                                                id: 'cmb-group-search-edit-member',
                                                                                fieldLabel: lang('Kelompok'),
                                                                                allowBlank: false,
                                                                                width: 350,
                                                                                store: Ext.create('Ext.data.Store', {
                                                                                    fields: ['CPGid', 'GroupName'],
                                                                                    autoLoad: true,
                                                                                    proxy: {
                                                                                        type: 'rest',
                                                                                        url: m_api + 'member/combogroup',
                                                                                        reader: {
                                                                                            type: 'json',
                                                                                            root: 'data',
                                                                                            totalProperty: 'total'
                                                                                        }
                                                                                    }
                                                                                }),
                                                                                displayField: 'GroupName',
                                                                                valueField: 'CPGid',
                                                                                name: 'CPGid'
                                                                            }, {
                                                                                xtype: 'button',
                                                                                margin: '0px 0px 0px 6px',
                                                                                text: 'Search',
                                                                                handler: function() {
                                                                                    store_farmer.getProxy().extraParams = {
                                                                                        key: Ext.getCmp('farmerKey').getValue(),
                                                                                        start: 0,
                                                                                        cpg: Ext.getCmp('cmb-group-search-edit-member').getValue(),
                                                                                    };
                                                                                    store_farmer.load();
                                                                                }
                                                                            }]
                                                                        }],
                                                                        columns: [{
                                                                            text: 'No',
                                                                            xtype: 'rownumberer',
                                                                            align: 'center',
                                                                            width: '5%'
                                                                        }, {
                                                                            text: 'Farmer ID',
                                                                            width: '15%',
                                                                            dataIndex: 'FarmerID'
                                                                        }, {
                                                                            text: lang('Nama'),
                                                                            width: '25%',
                                                                            dataIndex: 'FarmerName'
                                                                        }, {
                                                                            text: lang('District'),
                                                                            width: '15%',
                                                                            dataIndex: 'District'
                                                                        }, {
                                                                            text: lang('Kelompok'),
                                                                            width: '15%',
                                                                            dataIndex: 'GroupName'
                                                                        }, {
                                                                            text: lang('Desa'),
                                                                            width: '15%',
                                                                            dataIndex: 'Village'
                                                                        }, {
                                                                            menuDisabled: true,
                                                                            sortable: false,
                                                                            xtype: 'actioncolumn',
                                                                            width: '7%',
                                                                            align: 'center',
                                                                            items: [{
                                                                                icon: varjs.config.base_url + 'images/icons/silk/page_white_edit.png',
                                                                                tooltip: lang('Copy'),
                                                                                handler: function(grid, rowIndex, colIndex) {
                                                                                    var rec = grid.getStore().getAt(rowIndex);
                                                                                    var form = Ext.getCmp('frm-edit-member').getForm().load({
                                                                                        url: m_farmer,
                                                                                        method: 'GET',
                                                                                        params: {
                                                                                            id: rec.data.FarmerID
                                                                                        },
                                                                                        success: function(c, r) {
                                                                                            var data = r.result.data;
                                                                                            Ext.getCmp('farmerID').setValue(data.FarmerID);

                                                                                            if (data.gender == '1') {
                                                                                                Ext.getCmp('gender1').setValue(true);
                                                                                            } else if (data.gender == '2') {
                                                                                                Ext.getCmp('gender2').setValue(true);
                                                                                            }
                                                                                            Ext.getCmp('subdistrictID').store.load();
                                                                                            Ext.getCmp('subdistrictID').setValue(data.SubDistrictID);
                                                                                            switch (data.MaritalStatus) {
                                                                                                case '1':
                                                                                                    Ext.getCmp('maritalStatus1').setValue(true);
                                                                                                    break;
                                                                                                case '2':
                                                                                                    Ext.getCmp('maritalStatus2').setValue(true);
                                                                                                    break;
                                                                                                case '3':
                                                                                                    Ext.getCmp('maritalStatus3').setValue(true);
                                                                                                    break;
                                                                                            }
                                                                                            winFarmer.close();
                                                                                        }
                                                                                    });
                                                                                }
                                                                            }]
                                                                        }]
                                                                    }],
                                                                    buttons: [{
                                                                        text: 'Close',
                                                                        margin: '5px',
                                                                        scale: 'large',
                                                                        ui: 's-button',
                                                                        cls: 's-grey',
                                                                        disabled: false,
                                                                        handler: function() {
                                                                            winFarmer.close();
                                                                            //                    Ext.getCmp('isFarmer').setValue(false);
                                                                        }
                                                                    }]
                                                                }).show();
                                                            }
                                                        }]
                                                    }, {
                                                        xtype: 'combo',
                                                        fieldLabel: 'Jenis Anggota <b style="color:red">*</b>',
                                                        allowBlank: false,
                                                        width: 350,
                                                        id: 'cmb-member-type-frm-edit-member',
                                                        store: Ext.create('Ext.data.Store', {
                                                            fields: ['typeID', 'typeName'],
                                                            autoLoad: true,
                                                            proxy: {
                                                                type: 'rest',
                                                                url: m_api + 'member/combomembertype', // url that will load data with respect to start and limit params
                                                                reader: {
                                                                    type: 'json',
                                                                    root: 'data',
                                                                    totalProperty: 'total'
                                                                }
                                                            }
                                                        }),
                                                        displayField: 'typeName',
                                                        valueField: 'typeID',
                                                        name: 'typeID'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'memno',
                                                        readOnly: true,
                                                        submitValue: false,
                                                        width: 350,
                                                        fieldLabel: 'No. Anggota<b style="color:red">*</b>',
                                                        name: 'primaryNo'
                                                    }, {
                                                        xtype: 'textfield',
                                                        anchor: '100%',
                                                        fieldLabel: 'Nama <b style="color:red">*</b>',
                                                        allowBlank: false,
                                                        name: 'name'
                                                    }, {
                                                        xtype: 'textfield',
                                                        readOnly: true,
                                                        fieldLabel: 'Tersertifikasi',
                                                        id: 'isCertifiedDisplay',
                                                        hidden: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        width: 400,
                                                        fieldLabel: 'No. KTP<b style="color:red">*</b>',
                                                        id: 'identityNumber',
                                                        name: 'identityNumber',
                                                        allowBlank: false
                                                    }, {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: lang('Jenis Kelamin') + '<b style="color:red">*</b>',
                                                        layout: 'hbox',
                                                        items: [{
                                                            xtype: 'radio',
                                                            inputValue: 1,
                                                            boxLabel: 'Male',
                                                            name: 'gender',
                                                            checked: true
                                                        }, {
                                                            xtype: 'radio',
                                                            inputValue: 2,
                                                            fieldStyle: 'margin-left:75px',
                                                            boxLabel: 'Female',
                                                            name: 'gender'
                                                        }]
                                                    }, {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: 'Tempat, Tgl. Lahir <b style="color:red">*</b>',
                                                        layout: 'hbox',
                                                        items: [{
                                                            xtype: 'textfield',
                                                            id: 'placeOfBirth',
                                                            margin: '0 5 0 0',
                                                            name: 'placeOfBirth'
                                                        }, {
                                                            xtype: 'datefield',
                                                            id: 'dateOfBirth',
                                                            name: 'dateOfBirth',
                                                            format: 'Y-m-d',
                                                            width: 100,
                                                            altFormats: 'Y-m-d',
                                                            submitFormat: 'Y-m-d'
                                                        }]
                                                    }, {
                                                        xtype: 'textarea',
                                                        fieldLabel: 'Alamat <b style="color:red">*</b>',
                                                        width: 550,
                                                        height: 50,
                                                        id: 'address',
                                                        name: 'address',
                                                        allowBlank: false
                                                    }, {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: 'Desa <b style="color:red">*</b>',
                                                        layout: 'hbox',
                                                        items: [{
                                                            id: 'districtID',
                                                            name: 'districtID',
                                                            xtype: 'combo',
                                                            emptyText: '-- District --',
                                                            multiSelect: false,
                                                            store: mc_district,
                                                            displayField: 'label',
                                                            valueField: 'id',
                                                            margin: '0 5 0 0',
                                                            queryMode: 'local',
                                                            hidden: true,
                                                            listeners: {
                                                                change: function(cb, nv, ov) {
                                                                    mc_subdistrict.load({
                                                                        params: {
                                                                            district: Ext.getCmp('districtID').getValue()
                                                                        }
                                                                    });
                                                                }
                                                            }
                                                        }, {
                                                            id: 'subdistrictID',
                                                            name: 'subdistrictID',
                                                            xtype: 'combo',
                                                            emptyText: '-- Subdistrictx --',
                                                            multiSelect: false,
                                                            store: mc_subdistrict,
                                                            displayField: 'label',
                                                            valueField: 'id',
                                                            margin: '0 5 0 0',
                                                            queryMode: 'local',
                                                            listeners: {
                                                                change: function(cb, nv, ov) {
                                                                    mc_village.load({
                                                                        params: {
                                                                            sub_district: Ext.getCmp('subdistrictID').getValue()
                                                                        }
                                                                    });
                                                                }
                                                            }
                                                        }, {
                                                            id: 'villageID',
                                                            name: 'villageID',
                                                            xtype: 'combo',
                                                            emptyText: '-- Village --',
                                                            // multiSelect: false,
                                                            store: mc_village,
                                                            width: 270,
                                                            displayField: 'label',
                                                            valueField: 'id',
                                                            queryMode: 'local',
                                                            listeners: {

                                                                // beforerender:(k,v) => {
                                                                //     console.log('k',k)
                                                                //     console.log('v',v)
                                                                // }
                                                            }
                                                        }]
                                                    }, {
                                                        xtype: 'textfield',
                                                        fieldLabel: lang('Telp. <b style="color:red">*</b>'),
                                                        width: 250,
                                                        id: 'phone',
                                                        name: 'phone',
                                                        allowBlank: false
                                                    }, {
                                                        xtype: 'textfield',
                                                        fieldLabel: lang('Pekerjaan <b style="color:red">*</b>'),
                                                        id: 'job',
                                                        width: 350,
                                                        name: 'job',
                                                        allowBlank: false
                                                    }, {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: lang('Status Pernikahan'),
                                                        defaults: {
                                                            flex: 1
                                                        },
                                                        layout: 'hbox',
                                                        items: [{
                                                            boxLabel: 'Lajang',
                                                            xtype: 'radio',
                                                            name: 'maritalStatus',
                                                            inputValue: '1',
                                                            checked: true,
                                                            id: 'maritalStatus1'
                                                        }, {
                                                            boxLabel: 'Menikah',
                                                            xtype: 'radio',
                                                            name: 'maritalStatus',
                                                            inputValue: '2',
                                                            id: 'maritalStatus2'
                                                        }, {
                                                            boxLabel: 'Widow/Widower',
                                                            xtype: 'radio',
                                                            name: 'maritalStatus',
                                                            inputValue: '3',
                                                            id: 'maritalStatus3'
                                                        }]
                                                    }]
                                                }, {
                                                    xtype: 'container',
                                                    columnWidth: .3,
                                                    layout: {
                                                        type: 'fit'
                                                    },
                                                    items: [{
                                                            xtype: 'panel',
                                                            layout: {
                                                                // type:'hbox',
                                                                // pack:'center'
                                                            },
                                                            defaults: {
                                                                margin: 5
                                                            },
                                                            items: [{
                                                                xtype: 'numericfield',
                                                                readOnly: true,
                                                                hideTriger: true,
                                                                fieldLabel: 'Uang Pangkal',
                                                                id: 'EditRegUangPangkal',
                                                                name: 'RegUangPangkal',
                                                                width: '100%',
                                                                // allowBlank: false
                                                            }, {
                                                                xtype: 'numericfield',
                                                                readOnly: true,
                                                                hideTriger: true,
                                                                width: '100%',
                                                                fieldLabel: 'Simpanan Pokok',
                                                                id: 'EditRegSimpananPokok',
                                                                name: 'RegSimpananPokok',
                                                                // allowBlank: false
                                                            }, {
                                                                xtype: 'numericfield',
                                                                readOnly: true,
                                                                hideTriger: true,
                                                                width: '100%',
                                                                fieldLabel: lang('Simpanan Wajib'),
                                                                id: 'EditRegSimpananWajib',
                                                                name: 'RegSimpananWajib',
                                                                // allowBlank: false
                                                            }]
                                                        }

                                                    ]
                                                }, {
                                                    xtype: 'container',
                                                    columnWidth: .3,
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
                                                            xtype: 'form',
                                                            id: 'uploaderImage5',
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
                                                                fieldStyle: 'border:1px solid #ccc;',
                                                                anchor: '100%',
                                                                height: 150,
                                                                submitValue: false,
                                                                id: 'img-photo-add-member',
                                                                style: 'margin-bottom:5px'
                                                            }, {
                                                                xtype: 'fileuploadfield',
                                                                buttonOnly: true,
                                                                buttonConfig: {
                                                                    width: 125
                                                                },
                                                                submitValue: false,
                                                                name: 'memberPhoto',
                                                                buttonText: 'Upload',
                                                                listeners: {
                                                                    'change': function(fb, v) {
                                                                        base64Converter(fb)
                                                                            // var form = fb.up('form');
                                                                            // form.getForm().submit({
                                                                            //     url: m_api + 'member/coop_member_image',
                                                                            //     success: function(c, v) {

                                                                        //         var data = v.result.data;

                                                                        //         // Use createObjectURL to make a URL for the blob
                                                                        //         var image = Ext.getCmp('img-photo-add-member');

                                                                        //         image.setSrc(m_api + data);

                                                                        //         Ext.getCmp('hidden-add-member-photo-path').setValue(v.result.path);
                                                                        //         Ext.getCmp('hidden-add-member-photo-name').setValue(v.result.name);
                                                                        //     }
                                                                        // });
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'hidden',
                                                                name: 'memberPhotoPath',
                                                                id: 'hidden-add-member-photo-path'
                                                            }, {
                                                                xtype: 'hidden',
                                                                name: 'memberPhotoName',
                                                                id: 'hidden-add-member-photo-name'
                                                            }]
                                                        }, {
                                                            xtype: 'form',
                                                            id: 'uploadSign2',
                                                            width: 125,
                                                            layout: {
                                                                type: 'anchor'
                                                            },
                                                            items: [{
                                                                xtype: 'displayfield',
                                                                anchor: '100%',
                                                                fieldStyle: 'text-align:center;font-weight:bold',
                                                                value: 'Tanda Tangan'
                                                            }, {
                                                                xtype: 'image',
                                                                fieldStyle: 'border:1px solid #ccc;',
                                                                anchor: '100%',
                                                                height: 150,
                                                                submitValue: false,
                                                                id: 'img-sigi-add-member',
                                                                style: 'margin-bottom:5px'
                                                            }, {
                                                                xtype: 'fileuploadfield',
                                                                buttonOnly: true,
                                                                buttonConfig: {
                                                                    width: 125
                                                                },
                                                                submitValue: false,
                                                                name: 'memberSignature',
                                                                buttonText: 'Upload',
                                                                listeners: {
                                                                    'change': function(fb, v) {
                                                                        base64Converter(fb, 'sigi');

                                                                        // var form = fb.up('form');
                                                                        // form.getForm().submit({
                                                                        //     url: m_api + 'member/coop_member_image',
                                                                        //     success: function(c, v) {

                                                                        //         var data = v.result.data;

                                                                        //         // Use createObjectURL to make a URL for the blob
                                                                        //         var image = Ext.getCmp('img-sigi-add-member');

                                                                        //         image.setSrc(m_api + data);

                                                                        //         Ext.getCmp('hidden-add-member-sigi-path').setValue(v.result.path);
                                                                        //         Ext.getCmp('hidden-add-member-sigi-name').setValue(v.result.name);
                                                                        //     }
                                                                        // });
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'hidden',
                                                                name: 'memberSigiPath',
                                                                id: 'hidden-add-member-sigi-path'
                                                            }, {
                                                                xtype: 'hidden',
                                                                name: 'memberSigiName',
                                                                id: 'hidden-add-member-sigi-name'
                                                            }]
                                                        }]
                                                    }, {
                                                        xtype: 'fieldset',
                                                        margin: 5,
                                                        id: 'fieldsetKeterangan',
                                                        defaults: {
                                                            labelWidth: 1
                                                        },
                                                        items: [{
                                                            xtype: 'component',
                                                            html: '<center>Tipe file yang diperbolehkan adalah gif/jpg/png/bmp dengan ukuran file maksimal 900KB.</center>'
                                                        }]
                                                    }, {
                                                        xtype: 'fieldset',
                                                        hidden: true,
                                                        margin: 5,
                                                        title: 'Family',
                                                        defaults: {
                                                            labelWidth: 90
                                                        },
                                                        items: [{
                                                            xtype: 'textfield',
                                                            fieldLabel: 'Name <b style="color:red">*</b>',
                                                            id: 'familyName',
                                                            name: 'familyName',
                                                            width: 300,
                                                            // allowBlank: false
                                                        }, {
                                                            xtype: 'textfield',
                                                            width: 300,
                                                            fieldLabel: 'Identity <b style="color:red">*</b>',
                                                            id: 'familyIdentityNumber',
                                                            name: 'familyIdentityNumber',
                                                            // allowBlank: false
                                                        }, {
                                                            xtype: 'textfield',
                                                            width: 300,
                                                            fieldLabel: lang('Relationship <b style="color:red">*</b>'),
                                                            id: 'familyRelation',
                                                            name: 'familyRelation',
                                                            // allowBlank: false
                                                        }, {
                                                            xtype: 'textarea',
                                                            fieldLabel: lang('Address'),
                                                            id: 'familyAddress',
                                                            name: 'familyAddress',
                                                            width: 300
                                                        }, {
                                                            xtype: 'textfield',
                                                            fieldLabel: lang('Phone'),
                                                            id: 'familyPhone',
                                                            name: 'familyPhone',
                                                            width: 250
                                                        }]
                                                    }]
                                                }]
                                            }), {
                                                xtype: 'DetailFarmer',
                                                hidden: true,
                                                listeners: {
                                                    activate: function() {
                                                        //                                                            alert(Ext.getCmp('FarmerIDFormMember').getValue());
                                                        var id = Ext.getCmp('farmerID').getValue();
                                                        var formDataUmumMember = Ext.getCmp('formDataUmumMember');
                                                        formDataUmumMember.getForm().load({
                                                            url: m_datafarmer,
                                                            method: 'GET',
                                                            params: {
                                                                id: id
                                                            },
                                                            success: function(form, action) {
                                                                var d = Ext.decode(action.response.responseText);
                                                                console.log(d)
                                                            },
                                                            failure: function(form, action) {
                                                                var d = Ext.decode(action.response.responseText);
                                                                Ext.getCmp('DateCollectionf').setValue(d.DateCollection);
                                                                Ext.getCmp('DateUpdatedf').setValue(d.DateUpdated);
                                                                Ext.getCmp('FarmerID').setValue(d.FarmerID);
                                                                Ext.getCmp('Provinsi').setValue(d.Provinsi);
                                                                Ext.getCmp('Kabupaten').setValue(d.Kabupaten);
                                                                Ext.getCmp('Kecamatan').setValue(d.Kecamatan);
                                                                Ext.getCmp('Desa').setValue(d.Desa);
                                                                Ext.getCmp('Address').setValue(d.Address);
                                                                Ext.getCmp('FarmerGroupID').setValue(d.FarmerGroupID);
                                                                Ext.getCmp('BirthDttm').setValue(d.Birthdate);
                                                                Ext.getCmp('PersonNm').setValue(d.FarmerName);
                                                                Ext.getCmp('Handphone').setValue(d.HandPhone);

                                                                if (d.Gender == 1 * 1) {
                                                                    Ext.getCmp('Gender').setValue(true);
                                                                    Ext.getCmp('Gender2').setValue(false);
                                                                } else {
                                                                    Ext.getCmp('Gender').setValue(false);
                                                                    Ext.getCmp('Gender2').setValue(true);
                                                                }

                                                                if (d.MaritalStatus == 1 * 1) {
                                                                    Ext.getCmp('MaritalSt').setValue(true);
                                                                    Ext.getCmp('MaritalSt2').setValue(false);
                                                                    Ext.getCmp('MaritalSt3').setValue(false);
                                                                    Ext.getCmp('MaritalSt4').setValue(false);
                                                                } else if (d.MaritalStatus == 2 * 1) {
                                                                    Ext.getCmp('MaritalSt').setValue(false);
                                                                    Ext.getCmp('MaritalSt2').setValue(true);
                                                                    Ext.getCmp('MaritalSt3').setValue(false);
                                                                    Ext.getCmp('MaritalSt4').setValue(false);
                                                                } else if (d.MaritalStatus == 3 * 1) {
                                                                    Ext.getCmp('MaritalSt').setValue(false);
                                                                    Ext.getCmp('MaritalSt2').setValue(false);
                                                                    Ext.getCmp('MaritalSt3').setValue(true);
                                                                    Ext.getCmp('MaritalSt4').setValue(false);
                                                                } else if (d.MaritalStatus == 4 * 1) {
                                                                    Ext.getCmp('MaritalSt').setValue(false);
                                                                    Ext.getCmp('MaritalSt2').setValue(false);
                                                                    Ext.getCmp('MaritalSt3').setValue(false);
                                                                    Ext.getCmp('MaritalSt4').setValue(true);
                                                                }

                                                                if (d.Education == 1 * 1) {
                                                                    Ext.getCmp('Education').setValue(true);
                                                                    Ext.getCmp('Education2').setValue(false);
                                                                    Ext.getCmp('Education3').setValue(false);
                                                                    Ext.getCmp('Education4').setValue(false);
                                                                    Ext.getCmp('Education5').setValue(false);
                                                                    Ext.getCmp('Education6').setValue(false);
                                                                } else if (d.Education == 2 * 1) {
                                                                    Ext.getCmp('Education').setValue(false);
                                                                    Ext.getCmp('Education2').setValue(true);
                                                                    Ext.getCmp('Education3').setValue(false);
                                                                    Ext.getCmp('Education4').setValue(false);
                                                                    Ext.getCmp('Education5').setValue(false);
                                                                    Ext.getCmp('Education6').setValue(false);
                                                                } else if (d.Education == 3 * 1) {
                                                                    Ext.getCmp('Education').setValue(false);
                                                                    Ext.getCmp('Education2').setValue(false);
                                                                    Ext.getCmp('Education3').setValue(true);
                                                                    Ext.getCmp('Education4').setValue(false);
                                                                    Ext.getCmp('Education5').setValue(false);
                                                                    Ext.getCmp('Education6').setValue(false);
                                                                } else if (d.Education == 4 * 1) {
                                                                    Ext.getCmp('Education').setValue(false);
                                                                    Ext.getCmp('Education2').setValue(false);
                                                                    Ext.getCmp('Education3').setValue(false);
                                                                    Ext.getCmp('Education4').setValue(true);
                                                                    Ext.getCmp('Education5').setValue(false);
                                                                    Ext.getCmp('Education6').setValue(false);
                                                                } else if (d.Education == 5 * 1) {
                                                                    Ext.getCmp('Education').setValue(false);
                                                                    Ext.getCmp('Education2').setValue(false);
                                                                    Ext.getCmp('Education3').setValue(false);
                                                                    Ext.getCmp('Education4').setValue(false);
                                                                    Ext.getCmp('Education5').setValue(true);
                                                                    Ext.getCmp('Education6').setValue(false);
                                                                } else if (d.Education == 6 * 1) {
                                                                    Ext.getCmp('Education').setValue(false);
                                                                    Ext.getCmp('Education2').setValue(false);
                                                                    Ext.getCmp('Education3').setValue(false);
                                                                    Ext.getCmp('Education4').setValue(false);
                                                                    Ext.getCmp('Education5').setValue(false);
                                                                    Ext.getCmp('Education6').setValue(true);
                                                                } else {
                                                                    Ext.getCmp('Education').setValue(false);
                                                                    Ext.getCmp('Education2').setValue(false);
                                                                    Ext.getCmp('Education3').setValue(false);
                                                                    Ext.getCmp('Education4').setValue(false);
                                                                    Ext.getCmp('Education5').setValue(false);
                                                                    Ext.getCmp('Education6').setValue(false);
                                                                }


                                                                if (d.StatusFarmer == 1 * 1) {
                                                                    Ext.getCmp('StatusFarmer1').setValue(true);
                                                                    Ext.getCmp('StatusFarmer2').setValue(false);
                                                                } else if (d.StatusFarmer == 2 * 1) {
                                                                    Ext.getCmp('StatusFarmer1').setValue(false);
                                                                    Ext.getCmp('StatusFarmer2').setValue(true);
                                                                } else {
                                                                    Ext.getCmp('StatusFarmer1').setValue(false);
                                                                    Ext.getCmp('StatusFarmer2').setValue(false);
                                                                }
                                                                //                                                               Ext.getCmp('PersonNm').setValue(d.FarmerName);
                                                                //                                                               Ext.getCmp('PersonNm').setValue(d.FarmerName);
                                                            }
                                                        });

                                                        store_keluarga.load({
                                                            params: {
                                                                id: id
                                                            }
                                                        });

                                                        store_other_land.load({
                                                            params: {
                                                                id: id
                                                            }
                                                        });

                                                        Ext.Ajax.request({
                                                            url: m_databank,
                                                            method: 'GET',
                                                            params: {
                                                                id: id
                                                            },
                                                            success: function(fp, o) {
                                                                var r = Ext.decode(fp.responseText);
                                                                Ext.getCmp('AccountBeneficiary').setValue(r.AccountHolderFarmer);
                                                                Ext.getCmp('BankName').setValue(r.AccountBankName);
                                                                Ext.getCmp('BankBranch').setValue(r.AccountBankBranch);
                                                                Ext.getCmp('fAccountNumber').setValue(r.AccountNumber);
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                            //                                                ,
                                            //                                                {
                                            //                                                    xtype:'panel',
                                            //                                                    id:'member-farmer-detail',
                                            //                                                    title:'Farmer Detail'
                                            //                                                }
                                        ]
                                    }],
                                    buttons: [{
                                        text: 'Print Balance Statement',
                                        margin: '5px',
                                        scale: 'large',
                                        hidden: true,
                                        ui: 's-button',
                                        cls: 's-green',
                                        disabled: false,
                                        handler: function() {
                                            Ext.create('Ext.window.Window', {
                                                width: 800,
                                                height: 600,
                                                modal: true,
                                                closeAction: 'hide',
                                                items: [{
                                                    xtype: 'component',
                                                    html: '<iframe src="' + m_siteurl + '/member/coop_print_statement/printout/' + Ext.getCmp('MemberID').getValue() + '"  style="position: absolute; border: 0; top:0; left:0; right:0; bottom:0; width:100%; height:100%;"></iframe>',
                                                }]
                                            }).show();
                                        }
                                    }, '->', {
                                        id: 'saveButton',
                                        text: 'Edit',
                                        edit: false,
                                        margin: '5px',
                                        scale: 'large',
                                        ui: 's-button',
                                        cls: 's-blue',
                                        handler: function() {
                                            var form = Ext.getCmp('frm-edit-member').getForm();
                                            let formImg = [Ext.getCmp('uploaderImage5'), Ext.getCmp('uploadSign2')];
                                            if (this.edit === true) {
                                                disableChildren();
                                                if (form.isValid()) {
                                                    doUpload(formImg).then(s => {
                                                        if (s) {
                                                            form.submit({
                                                                url: m_api + 'member/update_coop_member',
                                                                method: 'POST',
                                                                params: {
                                                                    id: id
                                                                },
                                                                waitMsg: 'Sending data...',
                                                                success: function(fp, o) {
                                                                    Ext.MessageBox.alert('Success', 'Data saved.');
                                                                    win.close(this, function() {});
                                                                    store.load();
                                                                }
                                                            });
                                                        }
                                                    })
                                                }

                                            } else {

                                                this.edit = true;
                                                this.setText('Save');
                                                this.removeCls('s-blue');
                                                this.addCls('s-red');
                                                // didie
                                                enableChildren();
                                                Ext.getCmp('memno').setReadOnly(1)

                                            }
                                        }
                                    }, {
                                        text: 'Close',
                                        margin: '5px',
                                        scale: 'large',
                                        ui: 's-button',
                                        cls: 's-grey',
                                        disabled: false,
                                        handler: function() {
                                            //                                            Ext.getCmp('fTambahan').hide();
                                            // var form = Ext.getCmp('frm-edit-member').getForm();
                                            // if(form.isDirty()){
                                            //     Ext.MessageBox.confirm('Message', 'Unsaved changes are found, do you want to discard these changes ?', function(btn) {
                                            //         if (btn == 'yes') {
                                            //             win.close();
                                            //         }
                                            //     });
                                            // } else {
                                            //     win.close();
                                            // }
                                            win.close();
                                        }
                                    }]
                                });

                                //                                var form = Ext.getCmp('tabelFinanceStat'); // this is a better approach
                                ////                                form.add({xtype:'textfield', id:'fTambahan',fieldLabel:"First Name"});
                                //                                form.add(
                                //                                           {
                                //                                                html: 'tes',
                                //                                                colspan: 4
                                //                                            }
                                //                                        );
                                //                                form.doLayout();
                                Ext.getCmp('MemberID').setValue(id);
                                Ext.getCmp('saveButton').hide();
                                //tab loan
                                store_loan.on('beforeload', function(store, operation, eOpts) {
                                    operation.params = {
                                        'id': id
                                    };
                                });
                                store_loan.load();

                                Ext.getCmp('TotalLoanMemberDetail').setValue(null);
                                Ext.getCmp('PaidLoanMemberDetail').setValue(null);
                                Ext.getCmp('OutstandingLoanMemberDetail').setValue(null);

                                Ext.Ajax.request({
                                    url: m_api + 'member/loan_summary',
                                    method: 'GET',
                                    params: {
                                        id: id
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.JSON.decode(response.responseText);
                                        Ext.getCmp('TotalLoanMemberDetail').setValue(obj.totalLoan);
                                        Ext.getCmp('PaidLoanMemberDetail').setValue(obj.totalPaid);
                                        Ext.getCmp('OutstandingLoanMemberDetail').setValue(obj.totalOutstanding);
                                    },
                                    failure: function(response, opts) {
                                        var text = response.responseText;
                                        // Ext.Msg.alert('Failure', 'Approval Failed');
                                    }
                                });
                                //tab loan
                            }
                        }
                    }, {
                        xtype: 'button',
                        margin: '0px 6px 0px 6px',
                        text: 'Import',
                        hidden: true,
                        handler: function() {
                            winImportMember.show();
                        }
                    }, {
                        xtype: 'button',
                        // margin: '0px 6px 0px 6px',
                        arrowAlign: 'top',
                        text: 'Form Print',
                        menu: menuCetak
                            // handler: function() {
                            //     window.open(window.location + "/form-pendaftara-kgg.pdf");
                            //     //preview_cetak_surat(m_api+'member/cetak_blank_member');
                            // }
                    }, {
                        xtype: 'button',
                        hidden: true,
                        margin: '0px 6px 0px 6px',
                        text: 'Disbursement',
                        handler: function() {

                            // var win = Ext.create('widget.window', {
                            //           title: 'Disbursement Membership',
                            //           id: 'win-member-clossing',
                            //           modal: true,
                            //           width: 460,
                            //           layout: 'fit',
                            //           items: Ext.create('Ext.form.Panel', {
                            //               bodyPadding: 5,
                            //               autoScroll: true,
                            //               id: 'frm-edit-member-clossing',
                            //               fieldDefaults: {
                            //                   labelAlign: 'left',
                            //                   labelWidth: 190
                            //               },
                            //               listeners:{
                            //               },
                            //               items: [
                            //                   {
                            //                       xtype: 'datefield',
                            //                       fieldLabel:'Applied Date',
                            //                       name: 'typeID'
                            //                   },{
                            //                       xtype: 'numericfield',
                            //                       fieldLabel:'Amount',
                            //                       hideTriger:true,
                            //                       name: 'typeID'
                            //                   },  {
                            //                       xtype: 'textarea',
                            //                       allowBlank: false,
                            //                       anchor:'100%',
                            //                       fieldLabel: 'Notes',
                            //                       name: 'typeCode'
                            //                   }
                            //                   ],
                            //               buttons: [{
                            //                       id: 'saveButtonCloseMember',
                            //                       text: 'Save',
                            //                       margin: '5px',
                            //                       scale: 'large',
                            //                       ui: 's-button',
                            //                       cls: 's-blue',
                            //                       handler: function() {
                            //                           var form = this.up('form').getForm();
                            //                           form.submit({
                            //                               url: m_crud,
                            //                               method: 'PUT',
                            //                               waitMsg: 'Sending data...',
                            //                               success: function(fp, o) {
                            //                                   Ext.MessageBox.alert('Success', 'Data saved.');
                            //                                   win.close(this, function() {

                            //                                   });
                            //                                   store.load();
                            //                               }
                            //                           });

                            //                       }
                            //                   }, {
                            //                       text: 'Close',
                            //                       margin: '5px',
                            //                       scale: 'large',
                            //                       ui: 's-button',
                            //                       cls: 's-grey',
                            //                       disabled: false, handler: function() {
                            //                           win.close();
                            //                       }
                            //                   }]
                            //           })
                            //       }).show();
                        }
                    }, '->', {
                        text: 'Options',
                        arrowAlign: 'top',
                        menu: menu
                    },
                    // '<b>Update Status</b>', {
                    //     id: 'updateStatus',
                    //     name: 'updateStatus',
                    //     xtype: 'combo',
                    //     emptyText: '-- Set Status --',
                    //     multiSelect: false,
                    //     store: StatusMemberStore,
                    //     displayField: 'StatusMemberName',
                    //     valueField: 'StatusMemberID',
                    //     queryMode: 'local'
                    // },
                    //  {
                    //     xtype: 'button',
                    //     margin: '0px 6px 0px 6px',
                    //     text: 'Set Status',
                    //     handler: function() {

                    //         var status = Ext.getCmp('updateStatus').getValue();

                    //         if(status==null)
                    //         {
                    //             Ext.MessageBox.alert('Warning', 'Pilih jenis status terlebih dahulu');
                    //             return false;
                    //         }

                    //         var smb = Ext.getCmp('grid-member').getSelectionModel().getSelection()[0];
                    //         Ext.MessageBox.confirm('Message', 'Apakah anda mau mengubah data ini ?', function(btn) {
                    //             if (btn == 'yes' && status != '') {
                    //                 Ext.Ajax.request({
                    //                     waitMsg: 'Please Wait',
                    //                     url: m_api+'member/update_status',
                    //                     method: 'POST',
                    //                     params: {id: smb.raw.id, status: status},
                    //                     success: function(response, opts) {
                    //                         var obj = Ext.decode(response.responseText);
                    //                         switch (obj.success) {
                    //                             case true:
                    //                                 store.load();
                    //                                 break;
                    //                             default:
                    //                                 Ext.MessageBox.alert('Warning', obj.message);
                    //                                 break;
                    //                         }
                    //                     },
                    //                     failure: function(response, opts) {
                    //                         var obj = Ext.decode(response.responseText);
                    //                         Ext.MessageBox.alert('error', 'Failed to execute. Please select member and status');
                    //                     }
                    //                 });
                    //             }
                    //         });
                    //     }
                    // },
                    '-', {
                        xtype: 'textfield',
                        name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                        id: 'key',
                        emptyText: 'Search...',
                        width: 150,
                        listeners: {
                            specialkey: submitOnEnter
                        }
                    }, {
                        id: 'filterStatus',
                        name: 'filterStatus',
                        xtype: 'combo',
                        // hidden:true,
                        width: 120,
                        emptyText: 'Status...',
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
                        margin: '0px 6px 0px 6px',
                        text: 'Search',
                        handler: function() {
                            store.getProxy().extraParams = {
                                key: Ext.getCmp('key').getValue(),
                                start: 0,
                                status: Ext.getCmp('filterStatus').getValue()
                            };
                            store.load();
                        }
                    }
                ]
            }
        ],
        columns: [{
                text: 'ID',
                dataIndex: 'id',
                hidden: true
            }, {
                text: 'No',
                xtype: 'rownumberer',
                width: '5%'
            }, {
                text: lang('No. Anggota'),
                width: 170,
                dataIndex: 'primaryNo'
            }, {
                text: lang('Nama'),
                width: 200,
                flex: 1,
                dataIndex: 'name'
            }, {
                text: lang('Kelompok'),
                width: 200,
                flex: 1,
                dataIndex: 'GroupName'
            }, {
                text: lang('Desa'),
                dataIndex: 'Village'
            }, {
                xtype: 'numbercolumn',
                format: '0,000',
                align: 'right',
                text: 'Simpanan Pokok',
                width: 150,
                dataIndex: 'saldoSimpok'
            }, {
                xtype: 'numbercolumn',
                format: '0,000',
                align: 'right',
                text: 'Simpanan Wajib',
                width: 150,
                dataIndex: 'saldoWajib'
            }, {
                xtype: 'numbercolumn',
                format: '0,000',
                align: 'right',
                text: 'Uang Pangkal',
                width: 150,
                dataIndex: 'uangPangkal'
            },
            /*{
                             xtype:'numbercolumn',
                             format: '0,000',
                             align:'right',
                             text: 'Simpanan Sukarela',
                            width: 150,
                            dataIndex: 'saldoSuka'
                        }, */
            {
                text: lang('Tgl. Daftar'),
                dataIndex: 'registeredDate'
            }, {
                text: 'Status',
                dataIndex: 'status',
                renderer: function(value) {
                    if (value == '1') {
                        return lang('Active');
                    } else if (value == '2') {
                        return lang('Inactive');
                    } else if (value == '3') {
                        return lang('Suspended');
                    } else if (value == '4') {
                        return lang('Candidate');
                    }
                }
            }, {
                text: 'Remark',
                hidden: true,
                dataIndex: 'remark'
            }
        ]
    });

    function setReadOnlyDataForm() {
        Ext.getCmp('farmerID').setReadOnly(true);
        Ext.getCmp('primaryNo').setReadOnly(true);
        Ext.getCmp('name').setReadOnly(true);
        Ext.getCmp('typeID').setReadOnly(true);
        Ext.getCmp('identityType').setReadOnly(true);
        Ext.getCmp('identityNumber').setReadOnly(true);
        Ext.getCmp('gender1').setReadOnly(true);
        Ext.getCmp('gender2').setReadOnly(true);
        Ext.getCmp('placeOfBirth').setReadOnly(true);
        Ext.getCmp('dateOfBirth').setReadOnly(true);
        Ext.getCmp('address').setReadOnly(true);
        Ext.getCmp('districtID').setReadOnly(true);
        Ext.getCmp('subdistrictID').setReadOnly(true);
        Ext.getCmp('villageID').setReadOnly(true);
        Ext.getCmp('phone').setReadOnly(true);
        Ext.getCmp('maritalStatus1').setReadOnly(true);
        Ext.getCmp('maritalStatus2').setReadOnly(true);
        Ext.getCmp('maritalStatus3').setReadOnly(true);
        Ext.getCmp('education1').setReadOnly(true);
        Ext.getCmp('education2').setReadOnly(true);
        Ext.getCmp('education3').setReadOnly(true);
        Ext.getCmp('education4').setReadOnly(true);
        Ext.getCmp('education5').setReadOnly(true);
        Ext.getCmp('education6').setReadOnly(true);
        Ext.getCmp('job').setReadOnly(true);
        Ext.getCmp('status').setReadOnly(true);
        Ext.getCmp('familyName').setReadOnly(true);
        Ext.getCmp('familyIdentityType').setReadOnly(true);
        Ext.getCmp('familyIdentityNumber').setReadOnly(true);
        Ext.getCmp('familyRelation').setReadOnly(true);
        Ext.getCmp('familyAddress').setReadOnly(true);
        Ext.getCmp('familyPhone').setReadOnly(true);
        Ext.getCmp('memberPhoto').setVisible(false);
        Ext.getCmp('memberSignature').setVisible(false);
        Ext.getCmp('isFarmer').setVisible(false);

    }

    function removeReadOnlyDataForm() {
        Ext.getCmp('farmerID').setReadOnly(false);
        Ext.getCmp('primaryNo').setReadOnly(false);
        Ext.getCmp('name').setReadOnly(false);
        Ext.getCmp('typeID').setReadOnly(false);
        Ext.getCmp('identityType').setReadOnly(false);
        Ext.getCmp('identityNumber').setReadOnly(false);
        Ext.getCmp('gender1').setReadOnly(false);
        Ext.getCmp('gender2').setReadOnly(false);
        Ext.getCmp('placeOfBirth').setReadOnly(false);
        Ext.getCmp('dateOfBirth').setReadOnly(false);
        Ext.getCmp('address').setReadOnly(false);
        Ext.getCmp('districtID').setReadOnly(false);
        Ext.getCmp('subdistrictID').setReadOnly(false);
        Ext.getCmp('villageID').setReadOnly(false);
        Ext.getCmp('phone').setReadOnly(false);
        Ext.getCmp('maritalStatus1').setReadOnly(false);
        Ext.getCmp('maritalStatus2').setReadOnly(false);
        Ext.getCmp('maritalStatus3').setReadOnly(false);
        Ext.getCmp('education1').setReadOnly(false);
        Ext.getCmp('education2').setReadOnly(false);
        Ext.getCmp('education3').setReadOnly(false);
        Ext.getCmp('education4').setReadOnly(false);
        Ext.getCmp('education5').setReadOnly(false);
        Ext.getCmp('education6').setReadOnly(false);
        Ext.getCmp('job').setReadOnly(false);
        Ext.getCmp('status').setReadOnly(false);
        Ext.getCmp('familyName').setReadOnly(false);
        Ext.getCmp('familyIdentityType').setReadOnly(false);
        Ext.getCmp('familyIdentityNumber').setReadOnly(false);
        Ext.getCmp('familyRelation').setReadOnly(false);
        Ext.getCmp('familyAddress').setReadOnly(false);
        Ext.getCmp('familyPhone').setReadOnly(false);
        Ext.getCmp('memberPhoto').setVisible(true);
        Ext.getCmp('memberSignature').setVisible(true);
        Ext.getCmp('isFarmer').setVisible(true);
    }


});


function insertFinStatus() {
    var FinStatusFS = Ext.getCmp('FinStatusFS');

    if (Ext.getCmp('svType' + 234) != undefined) {
        Ext.getCmp('svType' + 234).remove();
    }

    Ext.Ajax.request({
        url: m_get_member_saving,
        async: false,
        method: 'GET',
        params: {
            memberID: Ext.getCmp('MemberID').getValue()
        },
        success: function(form, action) {
            var d = Ext.decode(form.responseText);

            Ext.each(d, function(v, i) {
                var addField = {
                        id: 'svType' + v.memberSavingID,
                        layout: {
                            type: 'table',
                            columns: 4,
                            tableAttrs: {
                                style: {
                                    width: '70%',
                                    height: '100%',
                                    padding: 5,
                                    align: 'center'
                                }
                            }
                        },
                        defaults: {
                            bodyStyle: 'padding:5 5 5 5',
                            align: 'center',
                        },
                        //                        fieldDefaults:{
                        //                             labelWidth: 130,
                        //                             width:460
                        //                        },
                        items: [{
                            html: v.savingTypeName,
                            width: 130,
                            align: 'center'
                        }, {
                            xtype: 'textfield',
                            id: 'typeid' + v.savingTypeID,
                            fieldStyle: 'text-align: right;',
                            readOnly: true
                        }, {
                            xtype: 'button',
                            id: 'btnSavingDeac' + v.memberSavingID,
                            width: 75,
                            style: 'margin-left:5px;',
                            text: lang('Deactivate'),
                            handler: function() {
                                //                                    console.log(this)
                                setStatusSaving(v.memberSavingID, 0)
                            }
                        }, {
                            xtype: 'button',
                            id: 'btnSavingAct' + v.memberSavingID,
                            width: 75,
                            hidden: true,
                            style: 'margin-left:5px;',
                            text: lang('Activate'),
                            handler: function() {
                                //                                    console.log(this)
                                setStatusSaving(v.memberSavingID, 1)
                            }
                        }, {
                            xtype: 'button',
                            id: 'btnPrintSaving' + v.memberSavingID,
                            width: 75,
                            hidden: true,
                            text: lang('Print'),
                            style: 'margin-left:5px;',
                            handler: function() {
                                console.log('print' + v.memberSavingID);
                            }
                        }]
                    }
                    //                    console.log(Ext.getCmp('svType'+v.memberSavingID));
                if (Ext.getCmp('svType' + v.memberSavingID) == undefined) {
                    FinStatusFS.add(addField);
                }

                if (v.memberSavingStatus * 1 == 0) {
                    Ext.getCmp('btnSavingDeac' + v.memberSavingID).hide();
                    Ext.getCmp('btnSavingAct' + v.memberSavingID).show();
                } else {
                    Ext.getCmp('btnSavingDeac' + v.memberSavingID).show();
                    Ext.getCmp('btnSavingAct' + v.memberSavingID).hide();
                }

                //insert nilainya
                Ext.Ajax.request({
                    url: m_transaction + '_summary',
                    async: false,
                    method: 'GET',
                    params: {
                        memberSavingID: v.memberSavingID
                            //                                        id:Ext.getCmp('MemberID').getValue(),
                            //                                        savingTypeID:v.savingTypeID
                    },
                    success: function(fp, o) {
                        var d = Ext.decode(fp.responseText);
                        //                                        console.log(d);
                        Ext.getCmp('typeid' + v.savingTypeID).setValue(d.total);
                    }
                });
            });
        },
        failure: function(form, action) {
            var d = Ext.decode(form.responseText);
            // Ext.Msg.alert("Failed", d.error);
        }
    });



    for (i = 0; i < 3; i++) {
        //        var addField = {
        //                        id:'svType'+i,
        //                        layout: {
        //                            type: 'table',
        //                            columns: 3,
        //                            tableAttrs: {
        //                                style: {
        //                                    width: '70%',
        //                                    height: '100%',
        //                                    padding: 5,
        //                                    align: 'center'
        //                                }
        //                            }
        //                        },
        //                        defaults: {
        //                            bodyStyle: 'padding:5 5 5 5',
        //                            align: 'center',
        //                        },
        ////                        fieldDefaults:{
        ////                             labelWidth: 130,
        ////                             width:460
        ////                        },
        //                        items: [{
        //                                html: Ext.id(),
        //                                width:130,
        //                                align: 'center'
        //                            }, {
        //                                xtype: 'textfield',
        //                                fieldStyle: 'text-align: right;',
        //                                readOnly: true
        //                            }, {
        //                                xtype: 'button',
        //                                width:75,
        //                                text: lang('Deactivate'),
        //                                handler: function() {
        //
        //                                }
        //                            }
        //                        ]
        //                    }
        //         FinStatusFS.add(addField);

    }

    var loanField = {
        id: 'svType' + 234,
        layout: {
            type: 'table',
            columns: 3,
            tableAttrs: {
                style: {
                    width: '70%',
                    height: '100%',
                    padding: 5,
                    align: 'center'
                }
            }
        },
        defaults: {
            bodyStyle: 'padding:5 5 5 5',
            align: 'center',
        },
        //                        fieldDefaults:{
        //                             labelWidth: 130,
        //                             width:460
        //                        },
        items: [{
            html: lang('Active Loans'),
            width: 113
        }, {
            xtype: 'textfield',
            readOnly: true
        }, {
            xtype: 'button',
            hidden: true,
            text: lang('Proposal'),
            colspan: 2,
            width: '97%',
        }]
    }
    if (Ext.getCmp('svType' + 234) == undefined) {
        FinStatusFS.add(loanField);
    }
}

function setStatusSaving(memberSavingID, status) {

    Ext.Ajax.request({
        url: m_set_status_member_saving,
        async: false,
        method: 'POST',
        params: {
            memberSavingID: memberSavingID,
            status: status * 1
        },
        success: function(form, action) {
            var d = Ext.decode(form.responseText);
            if (d.success) {
                if (status * 1 == 0) {
                    //hide
                    Ext.getCmp('btnSavingAct' + memberSavingID).show();
                    Ext.getCmp('btnSavingDeac' + memberSavingID).hide();

                } else {
                    Ext.getCmp('btnSavingDeac' + memberSavingID).show();
                    Ext.getCmp('btnSavingAct' + memberSavingID).hide();
                }

            }
        }
    });
}

function disableChildren() {

    var form = Ext.getCmp('frm-edit-member');
    var children = form.query();
    var xtypes = ['textfield', 'textarea', 'combo', 'checkbox', 'radio', 'datefield', 'numericfield'];

    Ext.each(children, function(one, index, all) {

        if ($.inArray(one.xtype, xtypes) != -1) {
            one.setReadOnly(true);
        } else if (one.xtype === 'button' || one.xtype === 'fileuploadfield') {
            one.hide();
        }
    });
}

function enableChildren(form) {
    form = Ext.getCmp('frm-edit-member') || Ext.getCmp(form)
    var children = form.query();
    var xtypes = ['textfield', 'textarea', 'combo', 'checkbox', 'radio', 'datefield', 'numericfield'];

    Ext.each(children, function(one, index, all) {

        if ($.inArray(one.xtype, xtypes) != -1) {
            if (one.id !== 'farmerID') {
                one.setReadOnly(false);
            }
        } else if (one.xtype === 'button' || one.xtype === 'fileuploadfield') {
            one.show();
        }
    });

}

function doUpload(form) {
    let uploader = [];
    let countForm = 0;
    let start = 0;
    let ImageData = Ext.getCmp('img-photo-add-member').getEl().getAttribute('src').slice(0, 5);
    let signData = Ext.getCmp('img-sigi-add-member').getEl().getAttribute('src').slice(0, 5);

    console.log(Ext.getCmp('img-photo-add-member').getEl().getAttribute('src'))
    // console.log(signData)
    if (ImageData === 'data:' && signData === 'data:') {
        countForm += 2;
        start = 0
    } else if(ImageData === 'data:'){
        countForm += 1;
        start = 0   
    } else if(signData === 'data:'){
        countForm += 2;
        start = 1   
    }

    // console.log(Ext.getCmp('img-photo-add-member').getEl().getAttribute('src'));
    if (countForm > 0) {
        for (var i = start ; i < countForm; i++) {
            let type = form[i].id;
            uploader[i] = new Promise((resolve, reject) => {
                form[i].getForm().submit({
                    url: m_api + 'member/coop_member_image',
                    success: function(c, v) {
                        var data = v.result.data;
                        // Use createObjectURL to make a URL for the blob
                        // var image = Ext.getCmp('img-photo-add-member');
                        // image.setSrc(m_api + data);

                        // console.log(c)
                        // console.log(v.result.path);
                        // console.log(v.result.name);
                        if (type.slice(0, 8) === 'uploader') {
                            console.log('uploader', type.slice(0, 8))
                            Ext.getCmp('hidden-add-member-photo-path').setValue(v.result.path);
                            Ext.getCmp('hidden-add-member-photo-name').setValue(v.result.name);
                        }

                        if (type.slice(6, -1) === 'Sign') {
                            console.log('Sign', type.slice(6, -1))
                            Ext.getCmp('hidden-add-member-sigi-path').setValue(v.result.path);
                            Ext.getCmp('hidden-add-member-sigi-name').setValue(v.result.name);
                        }
                        let imageValues = {
                            path: v.result.path,
                            name: v.result.name,
                            type: type
                        }
                        resolve(imageValues);
                    },
                    failure: function(c, v) {
                        console.log(v)
                        // Ext.MessageBox.alert('Info', v.result.error);
                        // reject(v.result.error);
                    }
                });
            })
        }
    } else {
        return Promise.resolve(true)
    }


    return Promise.all(uploader)
}

function base64Converter(fb, signature) {
    var image;
    if (typeof signature !== 'undefined') {
        image = Ext.getCmp('img-sigi-add-member');
    } else {
        image = Ext.getCmp('img-photo-add-member');
    }
    var valueElement = fb.getEl().down('input[type=file]').dom.files[0];
    var reader = new FileReader();
    reader.addEventListener("load", function() {
        image.setSrc(reader.result);
    }, false);
    if (valueElement) {
        reader.readAsDataURL(valueElement);
    }
}