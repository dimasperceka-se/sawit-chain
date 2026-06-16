Ext.define('Koltiva.view.MasterTraining.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.MasterTraining.MainForm',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setviewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;
            document.getElementById('divCommonContentRegion').style.display = 'none';
            // Ext.MessageBox.hide();
            if (thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view') {

                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {
                        id: this.viewVar.trainMasterID
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(form.responseText);
                        thisObj.setFormValue(r);
                    },
                    failure: function (form, action) {
                        Ext.MessageBox.hide();
                        Ext.MessageBox.show({
                            title: lang('Failed'),
                            msg: lang('Failed to retrieve data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }
        },
        beforerender: function () {
            var thisObj = this;

            if (thisObj.viewVar.opsiDisplay != 'insert') {
                Ext.MessageBox.show({
                    msg: 'Please wait...',
                    progressText: 'Loading...',
                    width: 300,
                    wait: true,
                    waitConfig: {
                        interval: 200
                    },
                    icon: 'ext-mb-info', //custom class in msg-box.html
                    animateTarget: 'mb9'
                });
            }
        }
    },
    initComponent: function () {
        var thisObj = this;
        let labelWidth = 200;

        //Store yg dipakai =============================================================== (Begin)
        var store_service_provider = Ext.create('Koltiva.store.MasterTraining.CmbServiceProvider');
        var store_training = Ext.create('Koltiva.store.MasterTraining.CmbTraining');
        var store_provinsi = Ext.create('Koltiva.store.MasterTraining.CmbProvince');
        var store_District = Ext.create('Koltiva.store.MasterTraining.CmbDistrict');
        var store_fasilitator = Ext.create('Koltiva.store.MasterTraining.CmbFasilitator');
        var store_staff = Ext.create('Koltiva.store.MasterTraining.CmbStaff');
        var store_participant = Ext.create('Koltiva.store.MasterTraining.ParticipantGrid');
        var mc_sub_topic = Ext.create('Koltiva.store.MasterTraining.CmbSubTopic');
        //Store yg dipakai =============================================================== (End)

        //Panel Main
        thisObj.ObjPanelMain = Ext.create('Ext.panel.Panel', {
            title: lang('Master Training Form'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            collapsible: true,
            style:'margin-top:0px;padding-top:0px;',
            items: [{
                xtype: 'form',
                id: 'Koltiva.view.MasterTraining.MainForm-Form',
                fileUpload: true,
                buttonAlign: 'right',
                cls: 'Sfr_PanelSubLayoutForm',
                items:[{
                    layout: 'column',
                    border: false,
                    padding: 10,
                    items: [{
                        columnWidth: 0.5,
                        layout: 'form',
                        style:'margin-right:20px;',
                        items: [{
                                xtype: 'textfield',
                                id: 'id',
                                name: 'id',
                                inputType: 'hidden'
                            }, {
                                xtype: 'combo',
                                store: store_training,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Topic'),
                                queryMode: 'local',
                                allowBlank: false,
                                id: 'training',
                                name: 'training',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        mc_sub_topic.load();
                                    }
                                }
                            }, {
                                xtype: 'boxselect',
                                id: 'CpgTrainingsIDSubTopic',
                                name: 'CpgTrainingsIDSubTopic[]',
                                store: mc_sub_topic,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                fieldLabel: lang('Subtopics'),
                                stacked: true,
                                pinList: false,
                                triggerOnClick: false,
                                filterPickList: true
                            }, {
                                id: 'Provinsi',
                                name: 'Provinsi',
                                xtype: 'combo',
                                fieldLabel: lang('Provinsi'),
                                store: store_provinsi,
                                displayField: 'label',
                                valueField: 'label',
                                queryMode: 'local',
                                readOnly: false,
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        ProvinceID = nv
                                        store_District.load({
                                            params: {
                                                id: nv
                                            }
                                        });
                                    }
                                }
                            }, {
                                id: 'DistrictID',
                                name: 'DistrictID',
                                xtype: 'combo',
                                fieldLabel: lang('District'),
                                store: store_District,
                                displayField: 'district',
                                valueField: 'id',
                                queryMode: 'local'
                            }, {
                                xtype: 'radiogroup',
                                fieldLabel: lang('Training Purpose'),
                                allowBlank: false,
                                msgTarget: 'side',
                                items: [{
                                    name: 'TrainingPurpose',
                                    id: 'TrainingPurposeCore',
                                    boxLabel: lang('Core'),
                                    inputValue: 'Core'
                                }, {
                                    name: 'TrainingPurpose',
                                    id: 'TrainingPurposeGeneral',
                                    boxLabel: lang('General'),
                                    inputValue: 'General'
                                }]
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Location'),
                                id: 'location',
                                name: 'location'
                            },
                            {
                                xtype: 'combo',
                                store: store_service_provider,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Service Provider'),
                                queryMode: 'local',
                                id: 'ServiceProvID',
                                name: 'ServiceProvID'
                        }]
                    },{
                        columnWidth: 0.49,
                        layout: 'form',
                        style:'margin-left:20px;',
                        items: [{
                                xtype: 'combo',
                                store: store_fasilitator,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Fasilitator 1'),
                                queryMode: 'local',
                                allowBlank: false,
                                id: 'fasilitator_scpp',
                                name: 'fasilitator_scpp'
                            },
                            {
                                xtype: 'combo',
                                store: store_fasilitator,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Fasilitator 2'),
                                queryMode: 'local',
                                id: 'fasilitator_mitra',
                                name: 'fasilitator_mitra'
                            },
                            {
                                xtype: 'datefield',
                                format: 'Y-m-d',
                                fieldLabel: lang('Training Start'),
                                id: 'TrainingStart',
                                name: 'TrainingStart'
                            }, {
                                xtype: 'datefield',
                                fieldLabel: lang('Training End'),
                                format: 'Y-m-d',
                                id: 'TrainingEnd',
                                name: 'TrainingEnd'
                            }, {
                                xtype: 'radiogroup',
                                fieldLabel: lang('Day Status'),
                                items: [{
                                    name: 'TrainingDayStatus',
                                    id: 'TrainingDayStatusHalf',
                                    boxLabel: lang('Half day'),
                                    inputValue: 'half'
                                }, {
                                    name: 'TrainingDayStatus',
                                    id: 'TrainingDayStatusFull',
                                    boxLabel: lang('Full day'),
                                    inputValue: 'full'
                                }]
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Training Days'),
                                id: 'days',
                                name: 'days'
                        }]
                    }]
                }],
                buttons: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Save'),
                    cls: 'Sfr_BtnFormBlue',
                    overCls: 'Sfr_BtnFormBlue-Hover',
                    id: 'Koltiva.view.MasterTraining.MainForm-Form-BtnSave',
                    hidden: (thisObj.viewVar.opsiDisplay == 'view' && (!m_act_update || m_act_add)),
                    handler: function () {

                        var form = this.up('form').getForm();
                        var methode;
                        if (thisObj.viewVar.opsiDisplay == 'update')
                            methode = 'PUT';
                        else
                            methode = 'POST';

                        if (form.isValid()) {
                            form.submit({
                                url: m_crud,
                                method: methode,
                                waitMsg: 'Sending data...',
                                success: function (fp, o) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data saved'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success',
                                        fn: function (btn) {
                                            if (btn == 'ok') {
                                                Ext.getCmp('Koltiva.view.MasterTraining.MainForm').destroy(); //destory current view
                                                var FormMain = [];

                                                if(Ext.getCmp('Koltiva.view.MasterTraining.MainForm') == undefined){
                                                    FormMain = Ext.create('Koltiva.view.MasterTraining.MainForm', {
                                                        viewVar: {
                                                            opsiDisplay: 'update',
                                                            trainMasterID: o.result.TrainMasterID
                                                        }
                                                    });
                                                }else{
                                                    //destroy, create ulang
                                                    Ext.getCmp('Koltiva.view.MasterTraining.MainForm').destroy();
                                                    FormMain = Ext.create('Koltiva.view.MasterTraining.MainForm', {
                                                        viewVar: {
                                                            opsiDisplay: 'update',
                                                            trainMasterID: o.result.TrainMasterID
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    });
                                },
                                failure: function (fp, o) {
                                    try {
                                        var r = Ext.decode(o.response.responseText);
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                    catch(err) {
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: 'Connection Error',
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                }
                            });
                        }

                    }
                }]
            }]
        });


        //Panel Detail
        thisObj.ObjPanelDetail = [];
        if (thisObj.viewVar.opsiDisplay == 'view' || thisObj.viewVar.opsiDisplay == 'update') {

            var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
                id: 'RowEditing',
                clicksToMoveEditor: 0,
                autoCancel: false,
                errorSummary: false,
                clicksToEdit: 2
            });

            store_participant.load({
                params: {
                    training: thisObj.viewVar.trainMasterID
                }
            });

            thisObj.ObjPanelDetail = Ext.create('Ext.grid.Panel', {
                id: 'gtraining',
                title: lang('Training Participants'),
                store: store_participant,
                width: '100%',
                minHeight: '30%',
                loadMask: true,
                selType: 'rowmodel',
                frame: true,
                cls: 'Sfr_PanelLayoutForm',
                collapsible: true,
                dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', 
                        cls:'Sfr_BtnGridGreen', 
                        overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        hidden: !m_act_add,
                        scope: this,
                        handler: function () {
                            RowEditing.cancelEdit();
                            var r = Ext.create('Koltiva.model.MasterTraining.ParticipantGrid', {
                                id: '',
                                staf: '',
                                wstart: '',
                                wend: '',
                                bstart: '',
                                bend: ''
                            });
                            store_participant.insert(0, r);
                            RowEditing.startEdit(0, 0);
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        cls:'Sfr_BtnGridGreen', 
                        overCls:'Sfr_BtnGridGreen-Hover',
                        hidden: !m_act_update,
                        text: lang('Update'),
                        scope: this,
                        handler: function () {
                            RowEditing.cancelEdit();
                            var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection();
                            RowEditing.startEdit(sm[0].index, 0);
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        cls:'Sfr_BtnGridRed', 
                        overCls:'Sfr_BtnGridRed-Hover',
                        hidden: !m_act_delete,
                        text: lang('Delete'),
                        scope: this,
                        handler: function () {
                            var sma = Ext.getCmp('gtraining').getSelectionModel().getSelection()[0];
                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_crud + '_participant',
                                        method: 'DELETE',
                                        params: {
                                            id: sma.get('participant_id')
                                        },
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    store_participant.load({
                                                        params: {
                                                            training: thisObj.viewVar.trainMasterID
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                        cls: 'Sfr_BtnGridPaleBlue',
                        overCls:'Sfr_BtnGridPaleBlue-Hover',
                        text: lang('Daftar Hadir'),
                        scope: this,
                        handler: function () {
                            preview_cetak_surat(m_cetak + thisObj.viewVar.trainMasterID);
                        }
                    }]
                }],
                columns: [{
                    text: lang('ID'),
                    dataIndex: 'participant_id',
                    hidden: true
                }, {
                    text: lang('ID'),
                    dataIndex: 'id_staff',
                    flex: 0.5,
                }, {
                    text: lang('Staff'),
                    flex: 2,
                    dataIndex: 'staf',
                    editor: {
                        xtype: 'combo',
                        displayField: 'label',
                        id: 'staf',
                        name: 'staf',
                        valueField: 'id',
                        queryMode: 'remote',
                        store: store_staff,
                        typeAhead: true,
                        listeners: {
                            change: function (cb, nv, ov) {
                                if (thisObj.isNumber(Ext.getCmp('staf').getValue())) {
                                    Ext.Ajax.request({
                                        waitMsg: 'Check data...',
                                        url: m_check,
                                        method: 'GET',
                                        params: {
                                            trainingid: thisObj.viewVar.trainMasterID,
                                            staffid: Ext.getCmp('staf').getValue()
                                        },
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            if (!obj.data) {
                                                Ext.MessageBox.alert('Warning', lang('Staff telah terdapat dalam list'));
                                                Ext.getCmp('staf').setValue('');
                                                return;
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }
                }, {
                    text: lang('W. Awal'),
                    flex: 1,
                    dataIndex: 'wstart',
                    editor: {
                        xtype: 'textfield'
                    }
                }, {
                    text: lang('W. Akhir'),
                    flex: 1,
                    dataIndex: 'wend',
                    editor: {
                        xtype: 'textfield'
                    }
                }, {
                    text: lang('B. Awal'),
                    flex: 1,
                    dataIndex: 'bstart',
                    editor: {
                        xtype: 'textfield'
                    }
                }, {
                    text: lang('B. Akhir'),
                    flex: 1,
                    dataIndex: 'bend',
                    editor: {
                        xtype: 'textfield'
                    }
                }],
                plugins: [RowEditing],
                listeners: {
                    'itemdblclick': function () {
                        if (!m_act_update) {
                            RowEditing.cancelEdit();
                            return false;
                        }
                    },
                    'canceledit': function (editor, e, eOpts) {
                        store_participant.load({
                            params: {
                                training: thisObj.viewVar.trainMasterID
                            }
                        });
                    },
                    'edit': function (editor, e) {
                        if (e.record.data.participant_id.trim() == '') {
                            Ext.Ajax.request({
                                waitMsg: 'Please wait...',
                                url: m_crud + '_participant',
                                method: 'POST',
                                params: {
                                    training: thisObj.viewVar.trainMasterID,
                                    staf: e.record.data.staf,
                                    wstart: e.record.data.wstart,
                                    wend: e.record.data.wend,
                                    bstart: e.record.data.bstart,
                                    bend: e.record.data.bend,
                                },
                                success: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store_participant.load({
                                                params: {
                                                    training: thisObj.viewVar.trainMasterID
                                                }
                                            });
                                            break;
                                        default:
                                            Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                    }
                                },
                                failure: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                }
                            });
                        } else {
                            Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please wait...',
                                        url: m_crud + '_participant',
                                        method: 'PUT',
                                        params: {
                                            id: e.record.data.participant_id,
                                            staf: e.record.data.staf,
                                            stafid: e.record.data.id_staff,
                                            wstart: e.record.data.wstart,
                                            wend: e.record.data.wend,
                                            bstart: e.record.data.bstart,
                                            bend: e.record.data.bend,
                                        },
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_participant.load({
                                                        params: {
                                                            training: thisObj.viewVar.trainMasterID
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    }
                }
            });

        }

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border: false,
            layout: {
                type: 'hbox'
            },
            items: [{
                id: 'Koltiva.view.MasterTraining.MainForm-labelInfoInsert',
                html: '<div id="header_title_farmer">' + lang('Training Master Data') + '</div>'
            }]
        }, {
            items: [{
                id: 'Koltiva.view.MasterTraining.MainForm-LinkBackToList',
                html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.MasterTraining.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Training List') + '</a></li></div>'
            }]
        }, {
            html: '<br />'
        }, {
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 1,
                items: [
                    thisObj.ObjPanelMain,
                    {
                        html:'<br>'
                    },
                    thisObj.ObjPanelDetail
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (End) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.MasterTraining.MainForm').destroy(); //destory current view
        var GridMainGrower = [];
        if (Ext.getCmp('Koltiva.view.MasterTraining.MainGrid') == undefined) {
            GridMainGrower = Ext.create('Koltiva.view.MasterTraining.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.MasterTraining.MainGrid').destroy();
            GridMainGrower = Ext.create('Koltiva.view.MasterTraining.MainGrid');
        }
    },
    setFormValue: function(r) {
        Ext.getCmp('training').setValue(r.CPGtrainingsID);
        Ext.getCmp('location').setValue(r.TotLocation);
        Ext.getCmp('fasilitator_scpp').setValue(r.FacProgramPersonID);
        Ext.getCmp('fasilitator_mitra').setValue(r.FacPrivatePersonID);
        Ext.getCmp('TrainingStart').setValue(r.TrainingStart);
        Ext.getCmp('TrainingEnd').setValue(r.TrainingEnd);
        Ext.getCmp('days').setValue(r.TrainingDays);
        Ext.getCmp('Provinsi').setValue(r.Province);
        Ext.getCmp('DistrictID').setValue(r.DistrictID);
        Ext.getCmp('ServiceProvID').setValue(r.ServiceProvID);

        Ext.getCmp('Provinsi').setReadOnly(false);
        Ext.getCmp('DistrictID').setReadOnly(false);

        if (r.TrainingDayStatus == 'half')
            Ext.getCmp('TrainingDayStatusHalf').setValue(true);
        if (r.TrainingDayStatus == 'full')
            Ext.getCmp('TrainingDayStatusFull').setValue(true);

        if (r.TrainingPurpose == 'Core')
            Ext.getCmp('TrainingPurposeCore').setValue(true);
        if (r.TrainingPurpose == 'General')
            Ext.getCmp('TrainingPurposeGeneral').setValue(true);

        Ext.getCmp('CpgTrainingsIDSubTopic').getStore().load({
            callback: function (records, options, success) {
                if (r.subtopics != null) {
                    var setSubtopic = r.subtopics.split(',');
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue(setSubtopic);
                } else {
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                }
            }
        });
    },
    isNumber: function(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
});