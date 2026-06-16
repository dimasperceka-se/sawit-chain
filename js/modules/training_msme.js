if (Ext.getCmp('winparchecklistday')) Ext.getCmp('winparchecklistday').destroy();
if (Ext.getCmp('printAttendanceList')) Ext.getCmp('printAttendanceList').destroy();
if (Ext.getCmp('winSelectDay')) Ext.getCmp('winSelectDay').destroy();
if (Ext.getCmp('winParAdd')) Ext.getCmp('winParAdd').destroy();

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
//add/update
    var store_service_provider = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_list_service_provider,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_training = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_store_training,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'totalCount'
            }
        }
    });
    var store_provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_provinsi,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_District = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'district'],
        proxy: {
            type: 'ajax',
            url: m_district_data,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_fasilitator = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_fasilitator,
            extraParams: {workarea: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_staff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_staff,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    //fields: ['participant_id','farmer','participant','if_no','wstart','wend','bstart','bend']
    Ext.define('Participant.Model', {
        extend: 'Ext.data.Model',
        fields: ['participant_id', 'id_staff', 'name', 'wstart', 'wend', 'bstart', 'bend']
    });
    var store_participant = Ext.create('Ext.data.Store', {
        model: 'Participant.Model',
        //pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_participant + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var mc_sub_topic = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/cpg/training_subtopic',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.CpgTrainingsID = Ext.getCmp('training').getValue();
            }
        }
    });

    function hideSave() {
        Ext.getCmp('saveButton').hide();
        if (Ext.getCmp('id').getValue() === '' && m_act_add) {
            Ext.getCmp('saveButton').show();
        }
        if (Ext.getCmp('id').getValue() !== '' && m_act_update) {
            Ext.getCmp('saveButton').show();
        }
    }
    function displayFormWindow() {
        if (!win.isVisible()) {
            store_participant.load();
            DataForm.getForm().reset();
            // Ext.Ajax.request({
            //     url: m_label_provinsi,
            //     method: 'GET',
            //     params: {id: m_param},
            //     success: function(fp, o) {
            //         var r = Ext.decode(fp.responseText);
            //         Ext.getCmp('Provinsi').setValue(r.id);
            //     }
            // });
            win.show();
        } else {
            win.hide(this, function() {
            });
            win.toFront();
        }
        Ext.getCmp('win').center()
    }
    function setFormValue(r) {
        // Ext.getCmp('cpg').setValue(r.CpgBatchID);
        Ext.getCmp('training').setValue(r.CPGtrainingsID);
        Ext.getCmp('location').setValue(r.TotLocation);
        Ext.getCmp('fasilitator_scpp').setValue(r.FacProgramStaffID);
        Ext.getCmp('fasilitator_mitra').setValue(r.FacPartnerStaffID);
        Ext.getCmp('TrainingStart').setValue(r.TrainingStart);
        Ext.getCmp('TrainingEnd').setValue(r.TrainingEnd);
        Ext.getCmp('days').setValue(r.TrainingDays);
        Ext.getCmp('Provinsi').setValue(r.Province);
        setTimeout(function () {
            Ext.getCmp('DistrictID').setValue(r.TrainingDistrictID);
        }, 1000);
        Ext.getCmp('ServiceProvID').setValue(r.ServiceProvID);
        // Ext.getCmp('ServiceProvStaffName').setValue(r.ServiceProvStaffName);

        Ext.getCmp('Provinsi').setReadOnly(false);
        Ext.getCmp('DistrictID').setReadOnly(false);

        if(r.TrainingDayStatus == 'half')
            Ext.getCmp('TrainingDayStatusHalf').setValue(true);
        if(r.TrainingDayStatus == 'full')
            Ext.getCmp('TrainingDayStatusFull').setValue(true);

        mc_sub_topic.load({
            callback : function(records, options, success) {
                if(r.subtopics != null){
                    var setSubtopic = r.subtopics.split(',');
                    //console.log(setSubtopic);
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue(setSubtopic);
                }else{
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                }
            }
        });

        Ext.getCmp('parcheklistday_trainingid').setValue(r.MSMETrainingID);
        Ext.getCmp('parcheklistday_training_name').setValue(r.label);
        Ext.getCmp('parcheklistday_startdate').setValue(Ext.Date.format(new Date(r.TrainingStart), 'Y-m-d'));
        Ext.getCmp('parcheklistday_enddate').setValue(Ext.Date.format(new Date(r.TrainingEnd), 'Y-m-d'));
        Ext.getCmp('parcheklistday_daycount').setValue(r.TrainingDays);
    }

    var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'RowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        width: 1010,
        height: 660,
        bodyPadding: 5,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 180,
            anchor: '95%'
        },
        items: [{
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .5,
                        layout: 'form',
                        border: false,
                        padding: 5,
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
                                    change: function(cb, nv, ov) {
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
                            },{
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
                                    change: function(cb, nv, ov) {
                                        ProvinceID = nv
                                        store_District.load({
                                            params: {
                                                id: nv
                                            }});
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
                                xtype: 'textfield',
                                fieldLabel: lang('ToT Location'),
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
                            }, 
                            ]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        border: false,
                        padding: 5,
                        items: [
                            {
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
                            // {
                            //     xtype: 'textfield',
                            //     fieldLabel: lang('Service Provider Facilitator'),
                            //     emptyText: lang('Semi colon separated names'),
                            //     id: 'ServiceProvStaffName',
                            //     name: 'ServiceProvStaffName',
                            //     validator: function(value){
                            //         if (Ext.getCmp('ServiceProvID').getValue() && value === '') {
                            //             return lang('Please input Service Provider Facilitator');
                            //         }
                            //         return true;
                            //     }
                            // }, 
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
                                },{
                                    name: 'TrainingDayStatus',
                                    id: 'TrainingDayStatusFull',
                                    boxLabel: lang('Full day'),
                                    inputValue: 'full'
                                }]
                            },{
                                xtype: 'textfield',
                                fieldLabel: lang('Training Days'),
                                id: 'days',
                                name: 'days'
                            }
                            ]
                    }]
            }, {
                xtype: 'gridpanel',
                id: 'gtraining',
                title: lang('Training Participants'),
                store: store_participant,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                cls: m_act_save,
                                hidden: !m_act_add,
                                text: lang('Add'),
                                scope: this,
                                handler: function() {
                                    displayAddWindowParticipant();
                                    // RowEditing.cancelEdit();
                                    // var r = Ext.create('Participant.Model', {
                                    //     id: '', staf: '', wstart: '', wend: '', bstart: '', bend: ''
                                    // });
                                    // store_participant.insert(0, r);
                                    // RowEditing.startEdit(0, 0);
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                cls: m_act_save,
                                hidden: !m_act_update,
                                text: lang('Update'),
                                scope: this,
                                handler: function() {
                                    RowEditing.cancelEdit();
                                    var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection();
                                    RowEditing.startEdit(sm[0].index, 0);
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/decline.png',
                                cls: m_act_save,
                                hidden: !m_act_delete,
                                text: lang('Delete'),
                                scope: this,
                                handler: function() {
                                    var sma = Ext.getCmp('gtraining').getSelectionModel().getSelection()[0];
                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: 'Please Wait',
                                                url: m_crud + '_participant',
                                                method: 'DELETE',
                                                params: {id: sma.get('participant_id')},
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            store_participant.load({
                                                                params: {
                                                                    training: Ext.getCmp('id').getValue()
                                                                }});
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
                            }, 
                            // {
                            //     icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                            //     text: lang('Daftar Hadir'),
                            //     scope: this,
                            //     handler: function() {
                            //         win.hide();
                            //         preview_cetak_surat(m_cetak + Ext.getCmp('id').getValue());

                            //         //window.location = m_cetak+Ext.getCmp('id').getValue();
                            //     }
                            // },
                            {
                                xtype: 'splitbutton',
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('Daftar Hadir'),
                                menu: {
                                    items: [{
                                            text: lang('Form Kosong'),
                                            handler: function() {
                                                preview_cetak_surat(m_cetak + Ext.getCmp('id').getValue());
                                            }
                                        }
                                        , {
                                            text: lang('Form Hasil'),
                                            handler: function() {
                                                //preview_cetak_surat(m_cetak + Ext.getCmp('idt').getValue(),'Form Hasil');
                                                displayBeforeCetakAttendanceList();
                                            }
                                        }
                                    ]
                                }
                            }, 
                            {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                text: lang('Attendance Check List Per Day'),
                                scope: this,
                                handler: function() {
                                    displayWinSelectDay();
                                }
                            },
                            ]
                    }],
                columns: [{
                        text: lang('ID'),
                        dataIndex: 'participant_id',
                        width: '10%',
                        hidden: true
                    }, {
                        text: lang('ID'),
                        dataIndex: 'id_staff',
                        width: '10%',
                    }, {
                        text: lang('Name'),
                        width: '50%',
                        dataIndex: 'name',
                        // editor: {
                        //     xtype: 'combo',
                        //     displayField: 'label',
                        //     id: 'staf',
                        //     name: 'staf',
                        //     valueField: 'id',
                        //     queryMode: 'remote',
                        //     store: store_staff,
                        //     typeAhead: true,
                        //     listeners: {
                        //         change: function(cb, nv, ov) {
                        //             if (isNumber(Ext.getCmp('staf').getValue())) {
                        //                 Ext.Ajax.request({
                        //                     waitMsg: 'Check data...',
                        //                     url: m_check,
                        //                     method: 'GET',
                        //                     params: {
                        //                         trainingid: Ext.getCmp('id').getValue(),
                        //                         staffid: Ext.getCmp('staf').getValue()
                        //                     },
                        //                     success: function(response, opts) {
                        //                         var obj = Ext.decode(response.responseText);
                        //                         if (!obj.data) {
                        //                             Ext.MessageBox.alert('Warning', lang('Staff telah terdapat dalam list'));
                        //                             Ext.getCmp('staf').setValue('');
                        //                             return;
                        //                         }
                        //                     }
                        //                 });
                        //             }
                        //         }
                        //     }
                        // }
                    }, {
                        text: lang('W. Awal'),
                        width: '10%',
                        dataIndex: 'wstart',
                        editor: {
                            xtype: 'textfield'
                        }
                    }, {
                        text: lang('W. Akhir'),
                        width: '10%',
                        dataIndex: 'wend',
                        editor: {
                            xtype: 'textfield'
                        }
                    }, {
                        text: lang('B. Awal'),
                        width: '10%',
                        dataIndex: 'bstart',
                        editor: {
                            xtype: 'textfield'
                        }
                    }, {
                        text: lang('B. Akhir'),
                        width: '10%',
                        dataIndex: 'bend',
                        editor: {
                            xtype: 'textfield'
                        }
                    }],
                plugins: [RowEditing],
                listeners: {
                    'itemdblclick': function() {
                        if (!m_act_update) {
                            RowEditing.cancelEdit();
                            return false;
                        }
                    },
                    'canceledit': function(editor, e, eOpts) {
                        store_participant.load({
                            params: {
                                training: Ext.getCmp('id').getValue()
                            }});
                    },
                    'edit': function(editor, e) {
                        // if (e.record.data.participant_id.trim() == '') {
                        //     Ext.Ajax.request({
                        //         waitMsg: 'Please wait...',
                        //         url: m_crud + '_participant',
                        //         method: 'POST',
                        //         params: {
                        //             training: Ext.getCmp('id').getValue(),
                        //             participant_id: e.record.data.participant_id,
                        //             wstart: e.record.data.wstart,
                        //             wend: e.record.data.wend,
                        //             bstart: e.record.data.bstart,
                        //             bend: e.record.data.bend,
                        //         },
                        //         success: function(response, opts) {
                        //             var obj = Ext.decode(response.responseText);
                        //             switch (obj.success) {
                        //                 case true:
                        //                     Ext.MessageBox.alert('Success', obj.message);
                        //                     store_participant.load({
                        //                         params: {
                        //                             training: Ext.getCmp('id').getValue()
                        //                         }});
                        //                     break;
                        //                 default:
                        //                     Ext.MessageBox.alert('Warning', obj.message);
                        //                     break;
                        //             }
                        //         },
                        //         failure: function(response, opts) {
                        //             var obj = Ext.decode(response.responseText);
                        //             Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                        //         }
                        //     });
                        // } else {
                            Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please wait...',
                                        url: m_crud + '_participant',
                                        method: 'PUT',
                                        params: {
                                            id: e.record.data.participant_id,
                                            // staf: e.record.data.staf,
                                            // stafid: e.record.data.id_staff,
                                            wstart: e.record.data.wstart,
                                            wend: e.record.data.wend,
                                            bstart: e.record.data.bstart,
                                            bend: e.record.data.bend,
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_participant.load({
                                                        params: {
                                                            training: Ext.getCmp('id').getValue()
                                                        }});
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
                        // }
                    }
                }
            }],
        buttons: [{
                id: 'saveButton',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ' + m_act_save,
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('id').getValue() != '')
                        methode = 'PUT';
                    else
                        methode = 'POST';
                    if (form.isValid()) {
                        form.submit({
                            url: m_crud,
                            method: methode,
                            waitMsg: 'Sending data...',
                            success: function(fp, o) {
                                Ext.MessageBox.alert('Success', lang('Data saved.'));
                            }
                        });
                        win.hide(this, function() {
                            store.load({
                                params: {
                                    key: Ext.getCmp('key').getValue(),
                                    kab: Ext.getCmp('Kab').getValue()
                                }});
                        });
                    }
                }
            }, {
                text: lang('Close'),
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
        id: 'win',
        title: lang('MSME Training'),
        closable: true,
        modal: true,
        closeAction: 'show',
        width: 1024,
        height: 700,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataForm]
    });
// end of add/update

//view
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'training', 'batch', 'tot', 'participant', 'start', 'end', 'days', 'partner_name'],
        autoLoad: true,
        pageSize: 20,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            extraParams: {prov: m_param,dist: m_DistrictID,subdist: m_SubDistrictID},
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.key = Ext.getCmp('key').getValue();
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
            extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue(),
                    kab: Ext.getCmp('Kab').getValue()
                }});
        }
    }
    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        id: 'grid',
        width: '100%',
        minHeight: 250,
        //title: lang('Business Training'),
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                store_participant.load({
                    params: {
                        training: sm.get('id')
                    }});
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {id: sm.get('id')},
                    success: function(fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('id').setValue(sm.get('id'));
                        setFormValue(r);
                        hideSave();
                    }
                });
            }
        },
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store,
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        scope: this,
                        handler: function(){
                            displayFormWindow();
                            if (m_param) Ext.getCmp('Provinsi').setValue(m_Province).setReadOnly(true);
                            else Ext.getCmp('Provinsi').setValue('').setReadOnly(false);
                            if (m_District) Ext.getCmp('DistrictID').setValue(m_DistrictID).setReadOnly(true);
                            else Ext.getCmp('DistrictID').setValue('').setReadOnly(false);
                            Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                            hideSave();
                        },
                        hidden: !m_act_add
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
                        scope: this,
                        handler: function() {
                            displayFormWindow();
                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            store_participant.load({
                                params: {
                                    training: sm.get('id')
                                }});
                            Ext.Ajax.request({
                                url: m_crud,
                                method: 'GET',
                                params: {id: sm.get('id')},
                                success: function(fp, o) {
                                    var r = Ext.decode(fp.responseText);
                                    Ext.getCmp('id').setValue(sm.get('id'));
                                    setFormValue(r);
                                    hideSave();
                                }
                            });
                        },
                        hidden: !m_act_update
                    }, {
                        itemId: 'remove',
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        hidden: !m_act_delete,
                        text: lang('Hapus'),
                        scope: this,
                        handler: function() {
                            var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_crud,
                                        method: 'DELETE',
                                        params: {id: smb.raw.id},
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
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    }, {
                        xtype: 'textfield',
                        name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                        id: 'key',
                        listeners: {
                            specialkey: submitOnEnter
                        }
                    }, {
                        id: 'Kab',
                        name: 'Kab',
                        xtype: 'combo',
                        store: mc_Kabupaten,
                        displayField: 'label',
                        valueField: 'label',
                        queryMode: 'local',
                        selectOnFocus: true,
                        hidden: true,
                        listeners: {
                            specialkey: submitOnEnter
                        }
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store.load({
                                params: {
                                    key: Ext.getCmp('key').getValue(),
                                    kab: Ext.getCmp('Kab').getValue()
                                }
                            });
                        }
                    }]
            }],
        columns: [{
                text: lang('ID'),
                dataIndex: 'id',
                width: '5%'
            }, {
                text: lang('Trainings'),
                width: '25%',
                dataIndex: 'training'
            }, {
                text: lang('District'),
                width: '20%',
                dataIndex: 'tot'
            }, {
                text: lang('Participants'),
                width: '10%',
                dataIndex: 'participant'
            }, {
                text: lang('Start'),
                width: '10%',
                dataIndex: 'start'
            }, {
                text: lang('End'),
                width: '10%',
                dataIndex: 'end'
            }, {
                text: lang('Days'),
                width: '5%',
                dataIndex: 'days'
            }, {
                text: lang('Partner'),
                width: '10%',
                dataIndex: 'partner_name'
            }]
    });
    // mc_Kabupaten.on('load', function(st) {
    //     if (Ext.getCmp('Kab').getValue() == null)
    //         Ext.getCmp('Kab').setValue(st.getAt('0').get('label'));
    //     store.load({
    //         params: {
    //             key: Ext.getCmp('key').getValue(),
    //             kab: Ext.getCmp('Kab').getValue()
    //         }});
    // });
//end of view
    
    var store_provinsi_participant = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_provinsi,
            extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_kabupaten_participant = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_kabupaten,
            extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_kecamatan_participant = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_kecamatan,
            extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_cpg_participant = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_cpg,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_participant_add_staff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name', 'type'],
        //pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_participant + 's_add_staff',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.training = Ext.getCmp('id').getValue()
                store.proxy.extraParams.key = Ext.getCmp('keyAddPart').getValue()
            }
        }
    });
    var store_participant_add_farmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name', 'Province', 'District', 'SubDistrict', 'GroupName'],
        //pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_participant + 's_add_farmer',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.training = Ext.getCmp('id').getValue()
                store.proxy.extraParams.key = Ext.getCmp('keyAddPart').getValue()
            }
        }
    });
    var DataFormParAdd = Ext.create('Ext.panel.Panel', {
        height: '100%',
        //autoScroll: true,
        overflowY: 'auto',
        width: '100%',
        //bodyPadding: 5,
        id: 'dataFormParAdd',
        items: [
            {
                xtype: 'gridpanel',
                id: 'grid_participant_add_staff',
                store: store_participant_add_staff,
                loadMask: true,
                hidden: false,
                dockedItems: [
                        {
                            xtype: 'toolbar',
                            items: [
                            {
                                id: 'roleAddPart',
                                name: 'Role',
                                xtype: 'combo',
                                fieldLabel: lang('Role'),
                                labelWidth: 50,
                                store: Ext.create('Ext.data.Store', {
                                    fields: ['id', 'label'],
                                    data: [
                                        {'id':'trader', 'label':lang('Trader'),},
                                        {'id':'do', 'label':lang('DO'),},
                                        {'id':'mill', 'label':lang('Mill'),},
                                    ]
                                }),
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                listeners: {
                                }
                            },
                            {
                                xtype: 'textfield',
                                name: 'keyAddPart',
                                id: 'keyAddPart',
                                emptyText: 'Cari berdasar nama/ID',
                                width: 150,
                                listeners: {}
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                margin: '0px 0px 0px 6px',
                                text: lang('Search'),
                                handler: function() {
                                    store_participant_add_staff.load({
                                        params: {
                                            training: Ext.getCmp('id').getValue(),
                                            role: Ext.getCmp('roleAddPart').getValue(),
                                            key: Ext.getCmp('keyAddPart').getValue(),
                                        }
                                    });
                                }
                            }]
                    },
                    {
                        xtype: 'pagingtoolbar',
                        store: store_participant_add_staff,
                        dock: 'top',
                        displayInfo: true
                    }, 
                    {
                        xtype: 'pagingtoolbar',
                        store: store_participant_add_staff,
                        dock: 'bottom',
                        displayInfo: true
                    }, 
                    ],
                selType: 'checkboxmodel',
                selModel: {
                    checkOnly: true,
                    mode: "MULTI",
                    headerWidth: 50
                },
                columns: [
                    {
                        text: lang('ID'),
                        dataIndex: 'id',
                        flex: 1,
                    },
                    {
                        text: lang('NAME'),
                        dataIndex: 'name',
                        flex: 2,
                    }, {
                        text: lang('Role'),
                        dataIndex: 'type',
                        flex: 1,
                    }, 
                    // {
                    //     text: lang('District'),
                    //     dataIndex: 'district',
                    //     flex: 2,
                    // },
                ]
            },
            ],
        buttons: [{
                id: 'save_par_add',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var participants = '';
                    var type = Ext.getCmp('typeAddPart').getValue();
                    var selection;
                    if (type == 'farmer') {
                        selection = Ext.getCmp('grid_participant_add_farmer').getSelectionModel().getSelection();
                    } else if (type == 'staff') {
                        selection = Ext.getCmp('grid_participant_add_staff').getSelectionModel().getSelection();
                    }
                    Ext.each(selection, function(row, index, value) {
                        //participants.push(row.data.addFarmerID);
                        participants = participants + ',' + row.data.id;
                    });
                    if (participants !== '') {
                        Ext.Ajax.request({
                            url: m_store_participant + 's',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                TrainingID: Ext.getCmp('id').getValue(),
                                type: Ext.getCmp('typeAddPart').getValue(),
                                participants: participants,
                            },
                            success: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                console.log(obj);
                                switch (obj.success) {
                                    case true:
                                        store_participant.load({
                                            params: {
                                                training: Ext.getCmp('id').getValue()
                                            }
                                        });
                                        winAddPar.hide();
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert("Warning", "Please select participants");
                    }
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winAddPar.hide();
                }
            }]
    });
    var winAddPar = Ext.widget('window', {
        title: lang('Add Participants'),
        id: 'winParAdd',
        closeAction: 'hide',
        height: '70%',
        width: '70%',
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataFormParAdd]
    });

    function displayAddWindowParticipant() {
        if (!winAddPar.isVisible()) {
            // store_participant_add.load({
            //     params: {
            //         training: Ext.getCmp('id').getValue(),
            //     }
            // });
            winAddPar.show();
        } else {
            winAddPar.hide(this, function() {
            });
            winAddPar.toFront();
        }
    }
    
    var DataWinSelectDay = Ext.create('Ext.form.Panel', {
        height: '100%',
        width: '100%',
        autoScroll: true,
        padding: 10,
        id: 'dataWinSelectDay',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'numberfield',
                fieldLabel: lang('Training Day'),
                id: 'TrainingDay',
                name: 'TrainingDay',
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                minValue: 1,
                allowBlank: false
            },
            {
                xtype: 'datefield',
                format: 'Y-m-d',
                anchor: '100%',
                fieldLabel: lang('Training Date'),
                id: 'TrainingDate2',
                name: 'TrainingDate',
                // maxValue: new Date(),
                allowBlank: false
            }
        ],
        buttons: [
            {
                text: lang('Select'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        displayFormWindowParticipantCheckListDay();
                        Ext.getCmp('parcheklistday_training_day').setValue(Ext.getCmp('TrainingDay').getValue());
                        var date = new Date(Ext.getCmp('TrainingDate2').getValue());
                        Ext.getCmp('parcheklistday_training_date').setValue(Ext.Date.format(date, 'Y-m-d'));

                        store_participant_checklist_day.load({
                            params: {
                                TrainingID: Ext.getCmp('id').getValue(),
                                DayNumber: Ext.getCmp('TrainingDay').getValue(),
                            }
                        });

                        var TrainingDayStatusHalf = Ext.getCmp('TrainingDayStatusHalf').getValue();
                        if(TrainingDayStatusHalf == true){
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setVisible(false);
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setText(lang('Kehadiran'))
                        }else{
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setVisible(true);
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setVisible(true);

                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setText(lang('Pagi'));
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setText(lang('Siang'));
                        }
                    }
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winSelectDay.hide();
                }
            }
        ]
    }
    );

    var winSelectDay = Ext.widget('window', {
        title: lang('Training Day List'),
        id: 'winSelectDay',
        closeAction: 'hide',
        width: 500,
        height: 200,
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataWinSelectDay]
    });

    function displayWinSelectDay() {
        // TrainingDate
        var min = new Date(Ext.getCmp('TrainingStart').getValue());
        var max = new Date(Ext.getCmp('TrainingEnd').getValue());
        Ext.getCmp('TrainingDate2').setMinValue(min);
        Ext.getCmp('TrainingDate2').setMaxValue(max);
        Ext.getCmp('TrainingDay').setMaxValue(Ext.getCmp('days').getValue());
        if (!winSelectDay.isVisible()) {
            winSelectDay.show();
        } else {
            winSelectDay.hide(this, function() {
            });
            winSelectDay.toFront();
        }
    }


    var store_participant_checklist_day = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','PartType','PartStaffID','PartFarmerID','name','Attendance1','Attendance2'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_participant_checklist_day,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var DataFormParCheckListDay = Ext.create('Ext.form.Panel', {
        height: '100%',
        width: '100%',
        autoScroll: true,
        id: 'dataFormParCheckListDay',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [
            {
                layout: 'column',
                border: false,
                items: [
                    {
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [
                            {
                                xtype: 'hiddenfield',
                                id: 'parcheklistday_trainingid',
                                name: 'TrainingID',
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklistday_training_name',
                                fieldLabel: lang('Training Name'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklistday_training_day',
                                fieldLabel: lang('Training Day'),
                                readOnly: true
                            },
                        ]
                    },
                    {
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [
                            {
                                layout: 'hbox',
                                border: false,
                                padding: 0,
                                items: [
                                    {
                                        flex: 3,
                                        xtype: 'panel',
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                id: 'parcheklistday_startdate',
                                                fieldLabel: lang('Training Period'),
                                                readOnly: true
                                            },
                                        ]
                                    },
                                    {
                                        flex: 2,
                                        xtype: 'panel',
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                id: 'parcheklistday_enddate',
                                                fieldLabel: lang('Until'),
                                                labelWidth: 50,
                                                readOnly: true
                                            },
                                        ]
                                    },
                                    {
                                        flex: 1,
                                        xtype: 'panel',
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                id: 'parcheklistday_daycount',
                                                fieldLabel: lang('Days'),
                                                labelWidth: 40,
                                                readOnly: true
                                            },
                                        ]
                                    },
                                ]
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklistday_training_date',
                                fieldLabel: lang('Training Date'),
                                readOnly: true
                            },
                        ]
                    }
                ]
            },
            {
                xtype: 'gridpanel',
                style: 'border:1px solid #CCC;',
                id: 'grid_participant_checklist_day',
                store: store_participant_checklist_day,
                width: '100%',
                //loadMask: true,
                selType: 'rowmodel',
                // plugins: [new Ext.grid.plugin.CellEditing({clicksToEdit: 1})],
                // listeners: {
                //     itemclick: function(dv, record, item, index, e) {
                //         mc_family.load({
                //             params: {
                //                 key: record.data.FarmerID
                //             }
                //         });
                //     },
                // },
                columns: [
                    {
                        text: '#',
                        xtype: 'rownumberer',
                        width: 50,
                    },
                    {
                        dataIndex: 'id',
                        hidden: true
                    },
                    {
                        dataIndex: 'PartType',
                        hidden: true
                    },
                    {
                        dataIndex: 'PartStaffID',
                        hidden: true
                    },
                    {
                        dataIndex: 'PartFarmerID',
                        hidden: true
                    },
                    {
                        text: lang('Participant Name'),
                        dataIndex: 'name',
                        flex: 3,
                    },
                    {
                        text: lang('Pagi'),
                        dataIndex: 'Attendance1',
                        xtype: 'checkcolumn',
                        flex: 1,
                    },
                    {
                        text: lang('Siang'),
                        dataIndex: 'Attendance2',
                        xtype: 'checkcolumn',
                        flex: 1,
                    },
                ],
            }
        ],
        buttons: [
            {
                id: 'save_par_check_day',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    // var sm = Ext.getCmp('grid_participant_').getSelectionModel().getSelection();
                    var data = [];
                    $.each(Ext.getCmp('grid_participant_checklist_day').getStore().data.items, function(index, val) {
                        // val.data.TrainingDate = Ext.util.Format.date(val.data.TrainingDate,'Y-m-d');
                        //console.log(val.data.TrainingDate);
                        data.push(val.data);
                    });
                    // console.log(data);
                    $.ajax({
                        url: m_attendance_day,
                        type: 'POST',
                        data: {
                            TrainingID: Ext.getCmp('id').getValue(),
                            DayNumber: Ext.getCmp('TrainingDay').getValue(),
                            TrainingDate: Ext.Date.format(Ext.getCmp('TrainingDate2').getValue(),'Y-m-d'),
                            data: data
                        },
                    })
                    .done(function() {
                        Ext.MessageBox.alert(lang('Info'), lang('Attendance saved'));
                    })
                    .fail(function() {
                        Ext.MessageBox.alert(lang('Warning'), lang('Failed to save attendance'));
                    })
                    .always(function() {
                        // console.log("complete");
                    });
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winParCheckListDay.hide();
                }
            }
        ]
    });

    var winParCheckListDay = Ext.widget('window', {
        title: lang('Daftar Hadir'),
        id: 'winparchecklistday',
        closeAction: 'hide',
        width: '70%',
        height: '70%',
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataFormParCheckListDay]
    });

    function displayFormWindowParticipantCheckListDay() {
        if (!winParCheckListDay.isVisible()) {
            winParCheckListDay.show();
        } else {
            winParCheckListDay.hide(this, function() {
            });
            winParCheckListDay.toFront();
        }
    }  

    var DataBeforeCetakAttendanceList = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        width: 420,
        height: 100,
        id: 'dataBeforeCetakAttendanceList',
        xtype: 'form',
        bodyPadding: 5,
        layout: {
            align: 'stretch',
            type: 'vbox'
        },
        header: false,
        title: lang('Print Attendance List'),
        items: [{
                xtype: 'numberfield',
                id: 'DayNumber',
                name: 'DayNumber',
                fieldLabel: lang('Day Number'),
                minValue: 1,
            },
            {
                xtype: 'container',
                height: 43,
                layout: {
                    align: 'stretch',
                    pack: 'center',
                    padding: 2,
                    type: 'hbox'
                },
                items: [
                    {
                        id: 'h_AttendanceList',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5 5 5 2',
                        scale: 'large',
                        ui: 's-button',
                        disabled: false,
                        cls: 's-blue',
                        handler: function() {
                            var DayNumber = Ext.getCmp('DayNumber').getValue();
                            if (!isNumber(DayNumber)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih Hari'));
                                return;
                            }
                            winBeforeCetakAttendanceList.hide();
                            //preview_cetak_surat(m_cetak_basic_farmer + 'FarmerTrainingID/' + FarmerTrainingID + '/SurveyID/' + SurveyID);
                            preview_cetak_surat(m_cetak + Ext.getCmp('id').getValue()+ '?DayNumber=' + DayNumber + '&result=1');
                        }
                    },
                    {
                        xtype: 'button',
                        text: lang('Batal'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            winBeforeCetakAttendanceList.hide();
                        }
                    }
                ]
            }
        ]
    });

    var winBeforeCetakAttendanceList = Ext.create('widget.window', {
        id: 'printAttendanceList',
        closable: true,
        modal: true,
        layout: 'fit',
        closeAction: 'show',
        width: 450,
        height: 130,
        items: [DataBeforeCetakAttendanceList]
    });  

    function displayBeforeCetakAttendanceList() {
        if (!winBeforeCetakAttendanceList.isVisible()) {
            winBeforeCetakAttendanceList.show();
        } else {
            winBeforeCetakAttendanceList.hide(this, function() {
            });
            winBeforeCetakAttendanceList.toFront();
        }
         Ext.getCmp('printAttendanceList').setTitle(lang('Print Attendance List'));
    }

});
