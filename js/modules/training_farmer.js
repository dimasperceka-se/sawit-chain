if (Ext.getCmp('winparchecklist')) Ext.getCmp('winparchecklist').destroy();
if (Ext.getCmp('winparchecklistday')) Ext.getCmp('winparchecklistday').destroy();
if (Ext.getCmp('printAttendanceList')) Ext.getCmp('printAttendanceList').destroy();
if (Ext.getCmp('winSelectDay')) Ext.getCmp('winSelectDay').destroy();
if (Ext.getCmp('winParAdd')) Ext.getCmp('winParAdd').destroy();
Ext.onReady(function() {
    // var store_cpg_batch = Ext.create('Ext.data.Store', {
    //     extend: 'Ext.data.Model',
    //     fields: ['id', 'label'],
    //     autoLoad: false,
    //     proxy: {
    //         type: 'ajax',
    //         url: m_store_cpg_batch,
    //         reader: {
    //             type: 'json',
    //             root: 'data'
    //         }
    //     }
    // });
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
    var store_farmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_farmer,
            // extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'totalCount'
            }
        }
    });
    var store_family = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_family,
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
            extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
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
        autoLoad: true,
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
    // var store_cpg_participant = Ext.create('Ext.data.Store', {
    //     extend: 'Ext.data.Model',
    //     fields: ['id', 'label'],
    //     autoLoad: false,
    //     proxy: {
    //         type: 'ajax',
    //         url: m_store_cpg,
    //         reader: {
    //             type: 'json',
    //             root: 'data'
    //         }
    //     }
    // });
    var store_fasilitator = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_fasilitator,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_fasilitator_mitra = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_fasilitator,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    Ext.define('Participant.Model', {
        extend: 'Ext.data.Model',
        fields: ['participant_id','MemberID', 'farmer_id', 'farmer', 'participant', 'Subtitute', 'if_no', 'FamilyID', 'wstart', 'wend', 'bstart', 'bend', 'FarmerTrainingID']
    });
    var store_participant = Ext.create('Ext.data.Store', {
        model: 'Participant.Model',
        //pageSize: 10000,
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

    var store_participant_checklist = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['DayNumber', 'Attendance1', 'Attendance2', 'TrainingDate'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_participant_checklist + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var store_participant_checklist_day = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID','FamilyID','FarmerName','AnggotaName','Attendance1','Attendance2'],
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

    var store_participant_add = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['addFarmerID', 'addFarmerDisplayID', 'addFarmerName', 'Province', 'District', 'SubDistrict', 'Village'],
        //pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_participant + 's_add',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.FarmerTrainingID = Ext.getCmp('idt').getValue()
                store.proxy.extraParams.key = Ext.getCmp('keyAddPart').getValue()
                store.proxy.extraParams.prov = Ext.getCmp('provAddPart').getValue()
                store.proxy.extraParams.kab = Ext.getCmp('kabAddPart').getValue()
                // store.proxy.extraParams.cpg = Ext.getCmp('cpgAddPart').getValue()
            }
        }
    });
    var mc_family = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_family,
            reader: {
                type: 'json',
                root: 'data',
            }
        }
    });
    var store_ya_tidak = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
            {"id": "1", "label": "Ya"},
            {"id": "2", "label": "Tidak"},
        ]
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
            Ext.getCmp('Kabupaten').setValue();
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
        Ext.getCmp('idt').setValue(r.FarmerTrainingID);
        Ext.getCmp('location').setValue(r.TotLocation);
        // Ext.getCmp('fasilitator_scpp').setValue(r.StaffID);
        // Ext.getCmp('fasilitator_mitra').setValue(r.PrivateStaffID);
        Ext.getCmp('fasilitator_scpp').setValue(r.Facilitator1);
        Ext.getCmp('fasilitator_mitra').setValue(r.Facilitator2);
        Ext.getCmp('TrainingStart').setValue(r.TrainingStart);
        Ext.getCmp('TrainingEnd').setValue(r.TrainingEnd);
        Ext.getCmp('days').setValue(r.TrainingDays);
        Ext.getCmp('Provinsi').setValue(r.Province);
        Ext.getCmp('Kabupaten').setValue(r.District);

        Ext.getCmp('Provinsi').setReadOnly(false);
        Ext.getCmp('Kabupaten').setReadOnly(false);

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
        Ext.getCmp('DayNumber').setMaxValue(r.TrainingDays);

       Ext.getCmp('parcheklistday_farmertrainingid').setValue(r.FarmerTrainingID);
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
    var store_CekSurvey = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'surveya'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_CekSurvey,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var SurveyID;
    var jenis;

    var DataBeforeCetak = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        width: 420,
        height: 100,
        id: 'dataBeforeCetak',
        xtype: 'form',
        bodyPadding: 5,
        layout: {
            align: 'stretch',
            type: 'vbox'
        },
        header: false,
        title: lang('My Form'),
        items: [{
                xtype: 'combobox',
                id: 'survey',
                name: 'id',
                store: store_CekSurvey,
                fieldLabel: lang('Survey'),
                displayField: 'surveya',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        SurveyID = nv
                        //console.log(SurveyID);
                    }
                }
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
                        id: 'h_p1',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5 5 5 2',
                        scale: 'large',
                        ui: 's-button',
                        disabled: false,
                        cls: 's-blue',
                        handler: function() {
                            //winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_farmer + 'FarmerTrainingID/' + Ext.getCmp('idt').getValue() + '/SurveyID/' + SurveyID);
                        }
                    }, {
                        id: 'h_f1',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5 5 5 2',
                        scale: 'large',
                        ui: 's-button',
                        disabled: false,
                        cls: 's-blue',
                        handler: function() {
                            //winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_aff + 'FarmerTrainingID/' + Ext.getCmp('idt').getValue() + '/SurveyID/' + SurveyID);
                        }
                    },
                    {
                        id: 'h_n1',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            // winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_nutrisi + 'FarmerTrainingID/' + Ext.getCmp('idt').getValue() + '/SurveyID/' + SurveyID);
                        }
                    },
                    {
                        id: 'h_ppi',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            //winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_ppi2012 + 'FarmerTrainingID/' + Ext.getCmp('idt').getValue() + '/SurveyID/' + SurveyID);
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
                            winBeforeCetak.hide();
                        }
                    }
                ]
            }
        ]
    });   

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
                            preview_cetak_surat(m_cetak + Ext.getCmp('idt').getValue()+ '?DayNumber=' + DayNumber + '&result=1');
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
    var winBeforeCetak = Ext.create('widget.window', {
        id: 'print',
        closable: true,
        modal: true,
        layout: 'fit',
        closeAction: 'show',
        width: 430,
        height: 130,
        items: [DataBeforeCetak]
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
    function displayBeforeCetak() {
        if (!winBeforeCetak.isVisible()) {
            winBeforeCetak.show();
        } else {
            winBeforeCetak.hide(this, function() {
            });
            winBeforeCetak.toFront();
        }
        Ext.getCmp('h_p1').hide();
        Ext.getCmp('h_f1').hide();
        Ext.getCmp('h_n1').hide();
        Ext.getCmp('h_ppi').hide();
        if (jenis == 'P1') {
            Ext.getCmp('h_p1').show();
            Ext.getCmp('print').setTitle('Cetak GAP');
        } else if (jenis == 'F1') {
            Ext.getCmp('h_f1').show();
            Ext.getCmp('print').setTitle('Cetak GFP');
        } else if (jenis == 'N1') {
            Ext.getCmp('h_n1').show();
            Ext.getCmp('print').setTitle('Cetak GNP');
        } else if (jenis == 'PPI') {
            Ext.getCmp('h_ppi').show();
            Ext.getCmp('print').setTitle('Cetak PPI');
        }
    }   

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

    var DataFormParCheckList = Ext.create('Ext.form.Panel', {
        height: '100%',
        width: '100%',
        autoScroll: true,
        id: 'dataFormParCheckList',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [
            {
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_farmerid',
                                fieldLabel: lang('Farmer ID'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_farmename',
                                fieldLabel: lang('Farmer Name'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_groupname',
                                fieldLabel: lang('Farmer Group Name'),
                                readOnly: true
                            },
                        ]
                    }, {
                        columnWidth: .45,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_trainingdays',
                                fieldLabel: lang('Training Days'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_startdate',
                                fieldLabel: lang('Start Date'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_enddate',
                                fieldLabel: lang('End Date'),
                                readOnly: true
                            },
                        ]
                    }]
            }, {
                xtype: 'gridpanel',
                style: 'border:1px solid #CCC;',
                id: 'grid_participant_checklist',
                store: store_participant_checklist,
                width: '100%',
                //loadMask: true,
                selType: 'rowmodel',
                plugins: [new Ext.grid.plugin.CellEditing({clicksToEdit: 1})],
                columns: [
                    {
                        text: lang('Hari Pertemuan'),
                        dataIndex: 'DayNumber',
                        renderer: function(value) {
                            return lang('Pertemuan') + ' ' + value;
                        },
                        flex: 3,
                    }, {
                        text: lang('Training Date'),
                        dataIndex: 'TrainingDate',
                        xtype: 'datecolumn',
                        format:'Y-m-d',
                        //renderer: Ext.util.Format.dateRenderer('d M Y'),
                        editor: {
                            xtype: 'datefield',
                            id: 'TrainingDate',
                            format: 'Y-m-d',
                            submitFormat: 'Y-m-d',
                            minValue: '2010-01-01',
                            // disabledDays: [0, 6],
                            // disabledDaysText: 'Plants are not available on the weekends'
                        },
                        flex: 1,
                    }, {
                        text: lang('Pagi'),
                        id: 'sinTDayAttendancePagi',
                        dataIndex: 'Attendance1',
                        xtype: 'checkcolumn',
                        flex: 1,
                    }, {
                        text: lang('Siang'),
                        id: 'sinTDayAttendanceSiang',
                        dataIndex: 'Attendance2',
                        xtype: 'checkcolumn',
                        flex: 1,
                    },
                ],
            }
        ],
        buttons: [
            {
                id: 'save_par_check',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection();
                    var data = [];
                    $.each(Ext.getCmp('grid_participant_checklist').getStore().data.items, function(index, val) {
                        val.data.TrainingDate = Ext.util.Format.date(val.data.TrainingDate,'Y-m-d');
                        //console.log(val.data.TrainingDate);
                        data.push(val.data);
                    });
                    // console.log(data);
                    $.ajax({
                        url: m_attendance,
                        type: 'POST',
                        data: {
                            FarmerTrainingID: sm[0].data.FarmerTrainingID,
                            FarmerID: sm[0].data.farmer_id,
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
                    winParCheckList.hide();
                }
            }
        ]
    });

    var winParCheckList = Ext.widget('window', {
        title: lang('Daftar Hadir'),
        id: 'winparchecklist',
        closeAction: 'hide',
        width: '60%',
        height: '70%',
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataFormParCheckList]
    });

    function displayFormWindowParticipantCheckList() {
        if (!winParCheckList.isVisible()) {
            winParCheckList.show();
        } else {
            winParCheckList.hide();
            winParCheckList.toFront();
        }
    }

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
                                id: 'parcheklistday_farmertrainingid',
                                name: 'FarmerTrainingID',
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
                                                labelWidth: 50,
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
                plugins: [new Ext.grid.plugin.CellEditing({clicksToEdit: 1})],
                listeners: {
                    itemclick: function(dv, record, item, index, e) {
                        console.log(record.data.FarmerID);
                        mc_family.load({
                            params: {
                                key: record.data.FarmerID
                            }
                        });
                    },
                },
                columns: [
                    {
                        text: '#',
                        xtype: 'rownumberer',
                        width: 50,
                    },
                    {
                        text: '#',
                        dataIndex: 'FarmerID',
                        hidden: true
                    },
                    {
                        text: '#',
                        dataIndex: 'FamilyID',
                        hidden: true
                    },
                    {
                        text: lang('Participant Name (Farmer)'),
                        dataIndex: 'FarmerName',
                        flex: 3,
                    },
                    {
                        text: lang('Participant Substitute (Family)'),
                        dataIndex: 'AnggotaName',
                        flex: 3,
                        editor: {
                            xtype: 'combobox',
                            displayField: 'label',
                            valueField: 'label',
                            queryMmode: 'local',
                            store: mc_family,
                        }
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
                            FarmerTrainingID: Ext.getCmp('idt').getValue(),
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
                                FarmerTrainingID: Ext.getCmp('idt').getValue(),
                                DayNumber: Ext.getCmp('TrainingDay').getValue(),
                            }
                        });

                        var TrainingDayStatusHalf = Ext.getCmp('TrainingDayStatusHalf').getValue();
                        // if(TrainingDayStatusHalf == true){
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setVisible(false);
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setText(lang('Kehadiran'))
                        // }else{
                        //     Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setVisible(true);
                        //     Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setVisible(true);

                        //     Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setText(lang('Pagi'));
                        //     Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setText(lang('Siang'));
                        // }
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

    var DataFormParAdd = Ext.create('Ext.panel.Panel', {
        height: '100%',
        //autoScroll: true,
        overflowY: 'auto',
        width: '100%',
        //bodyPadding: 5,
        id: 'dataFormParAdd',
        items: [{
                xtype: 'gridpanel',
                id: 'grid_participant_add',
                store: store_participant_add,
                loadMask: true,
                dockedItems: [
                        {
                            xtype: 'toolbar',
                            items: [
                            {
                                id: 'provAddPart',
                                name: 'Provinsi',
                                xtype: 'combo',
                                fieldLabel: lang('Provinsi'),
                                labelWidth: 50,
                                store: store_provinsi_participant,
                                displayField: 'label',
                                valueField: 'id',
                                readOnly: false,
                                queryMode: 'local',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        store_kabupaten_participant.load({
                                            params: {
                                                key: Ext.getCmp('provAddPart').getValue()
                                            }});
                                    }
                                }
                            }, 
                            {
                                id: 'kabAddPart',
                                name: 'Kabupaten',
                                xtype: 'combo',
                                fieldLabel: lang('Kabupaten'),
                                labelWidth: 50,
                                store: store_kabupaten_participant,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                listeners: {
                                    // change: function(cb, nv, ov) {
                                    //     store_cpg_participant.load({
                                    //         params: {
                                    //             DistrictID: nv
                                    //         }});
                                    // }
                                }
                            },
                            // {
                            //     id: 'cpgAddPart',
                            //     name: 'CPG',
                            //     xtype: 'combo',
                            //     fieldLabel: lang('CPG'),
                            //     labelWidth: 50,
                            //     store: store_cpg_participant,
                            //     displayField: 'label',
                            //     valueField: 'id',
                            //     queryMode: 'local',
                            //     listeners: {
                            //     }
                            // },
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
                                    store_participant_add.load({
                                        params: {
                                            FarmerTrainingID: Ext.getCmp('idt').getValue(),
                                            key: Ext.getCmp('keyAddPart').getValue(),
                                            prov: Ext.getCmp('provAddPart').getValue(),
                                            kab: Ext.getCmp('kabAddPart').getValue(),
                                            // cpg: Ext.getCmp('cpgAddPart').getValue(),
                                        }
                                    });
                                }
                            }]
                    },
                    {
                        xtype: 'pagingtoolbar',
                        store: store_participant_add,
                        dock: 'top',
                        displayInfo: true
                    }, 
                    {
                        xtype: 'pagingtoolbar',
                        store: store_participant_add,
                        dock: 'bottom',
                        displayInfo: true
                    }, 
                    ],
                selType: 'checkboxmodel',
                selModel: {
                    checkOnly: true,
                    mode: "MULTI",
                    headerWidth: '10%'
                },
                columns: [
                    {
                        text: lang('NAME'),
                        dataIndex: 'addFarmerName',
                        flex: 2,
                    }, {
                        text: '',
                        dataIndex: 'addFarmerID',
                        hidden: true,
                    }, {
                        text: lang('ID'),
                        dataIndex: 'addFarmerDisplayID',
                        flex: 1,
                    }, {
                        text: lang('Provinsi'),
                        dataIndex: 'Province',
                        flex: 1,
                    }, {
                        text: lang('District'),
                        dataIndex: 'District',
                        flex: 1,
                    }, {
                        text: lang('SubDistrict'),
                        dataIndex: 'SubDistrict',
                        flex: 1,
                    }, {
                        text: lang('Village'),
                        dataIndex: 'Village',
                        flex: 1,
                    }, 
                ]
            }],
        buttons: [{
                id: 'save_par_add',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var participants = '';
                    Ext.each(Ext.getCmp('grid_participant_add').getSelectionModel().getSelection(), function(row, index, value) {
                        //participants.push(row.data.addFarmerID);
                        participants = participants + ',' + row.data.addFarmerID;
                    });
                    if (participants !== '') {
                        Ext.Ajax.request({
                            url: m_store_participant + 's',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                FarmerTrainingID: Ext.getCmp('idt').getValue(),
                                participants: participants,
                            },
                            success: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        store_participant.load({
                                            params: {
                                                training: Ext.getCmp('idt').getValue()
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
            store_participant_add.load({
                params: {
                    FarmerTrainingID: Ext.getCmp('idt').getValue(),
                }
            });
            winAddPar.show();
        } else {
            winAddPar.hide(this, function() {
            });
            winAddPar.toFront();
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        width: 1010,
        height: 560,
        bodyPadding: 5,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
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
                                xtype: 'hidden',
                                id: 'id',
                                name: 'id',
                                inputType: 'hidden'
                            }, {
                                xtype: 'hidden',
                                id: 'idt',
                                name: 'idt',
                                inputType: 'hidden'
                            }, 
                            // {
                            //     xtype: 'combo',
                            //     store: store_cpg_batch,
                            //     displayField: 'label',
                            //     valueField: 'id',
                            //     fieldLabel: lang('CPG/FFS Batch'),
                            //     queryMode: 'local',
                            //     id: 'cpg',
                            //     name: 'cpg',
                            //     allowBlank: true,
                            //     hidden: true
                            // }, 
                            {
                                xtype: 'combo',
                                store: store_training,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Topic'),
                                queryMode: 'local',
                                id: 'training',
                                name: 'training',
                                allowBlank: false,
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
                                readOnly: false,
                                queryMode: 'local',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        store_kabupaten.load({
                                            params: {
                                                key: Ext.getCmp('Provinsi').getValue()
                                            }});
                                        //Ext.getCmp('Kabupaten').enable();
                                    }
                                }
                            }, {
                                id: 'Kabupaten',
                                name: 'Kabupaten',
                                xtype: 'combo',
                                fieldLabel: lang('Kabupaten'),
                                store: store_kabupaten,
                                displayField: 'label',
                                valueField: 'label',
                                queryMode: 'local',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        // store_farmer.load({
                                        //     params: {
                                        //         kab: Ext.getCmp('Kabupaten').getValue()
                                        //     }});
                                        
                                        store_farmer.getProxy().extraParams = {
                                            kab: nv
                                        };
                                    }
                                }                      
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('ToT Location'),
                                id: 'location',
                                name: 'location'
                            }]
                    }, {
                        columnWidth: 0.5,
                        layout: 'form',
                        border: false,
                        padding: 5,
                        items: [{
                                xtype: 'combo',
                                store: store_fasilitator,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Fasilitator 1'),
                                queryMode: 'local',
                                id: 'fasilitator_scpp',
                                name: 'fasilitator_scpp',
                                allowBlank: true,
                            }, {
                                xtype: 'combo',
                                store: store_fasilitator_mitra,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Fasilitator 2'),
                                queryMode: 'local',
                                id: 'fasilitator_mitra',
                                name: 'fasilitator_mitra'
                            }, {
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
                            }]
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
                                    //     participant_id: '', farmer_id: '', farmer: '', participant: '', Subtitute: '', if_no: '',
                                    //     FamilyID: '', wstart: '', wend: '', bstart: '', bend: ''
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
                                    // console.log(sm[0].get('farmer_id'))
                                    store_family.load({
                                        params: {
                                            farmerid: sm[0].get('farmer_id')
                                        }
                                    });
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
                            {
                                xtype: 'splitbutton',
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('Daftar Hadir'),
                                menu: {
                                    items: [{
                                            text: lang('Form Kosong'),
                                            handler: function() {
                                                preview_cetak_surat(m_cetak + Ext.getCmp('idt').getValue());
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
                            // {
                            //     icon: varjs.config.base_url + 'images/icons/new/update.png',
                            //     text: lang('Attendance Check List Per Participant'),
                            //     scope: this,
                            //     handler: function() {
                            //         var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection();
                            //         // console.log(sm[0]);
                            //         if (!sm[0]) {
                            //             Ext.MessageBox.alert(lang('Warning'), lang('Silahkan pilih peserta'));
                            //         } else {
                            //             $.ajax({
                            //                 url: m_participant_detail,
                            //                 data: {
                            //                     FarmerTrainingsFarmerID: sm[0].data.participant_id
                            //                 },
                            //             })
                            //             .done(function(data) {
                            //                 displayFormWindowParticipantCheckList();

                            //                 Ext.getCmp('parcheklist_farmerid').setValue(data['FarmerID']);
                            //                 Ext.getCmp('parcheklist_farmename').setValue(data['FarmerName']);
                            //                 Ext.getCmp('parcheklist_groupname').setValue(data['GroupName']);
                            //                 Ext.getCmp('parcheklist_trainingdays').setValue(data['TrainingDays']);
                            //                 Ext.getCmp('parcheklist_startdate').setValue(data['TrainingStart']);
                            //                 Ext.getCmp('parcheklist_enddate').setValue(data['TrainingEnd']);

                            //                 if(data['TrainingDayStatus'] == 'half'){
                            //                     Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance2]').setVisible(false);
                            //                     Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance1]').setText(lang('Kehadiran'))
                            //                 }else{
                            //                     Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance1]').setVisible(true);
                            //                     Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance2]').setVisible(true);

                            //                     Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance1]').setText(lang('Pagi'))
                            //                     Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance2]').setText(lang('Siang'))
                            //                 }
                            //             });
                            //             store_participant_checklist.load({
                            //                 params: {
                            //                     FarmerTrainingID: sm[0].data.FarmerTrainingID,
                            //                     FarmerID: sm[0].data.farmer_id,
                            //                 }
                            //             })

                            //         }
                            //     }
                            // },
                            {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                text: lang('Attendance Check List Per Day'),
                                scope: this,
                                handler: function() {
                                    displayWinSelectDay();
                                }
                            },
                            {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('GAP'),
                                scope: this,
                                cls:'hide-icon',
                                handler: function() {
                                    jenis = 'P1';
                                    displayBeforeCetak();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('GFP'),
                                cls:'hide-icon',
                                scope: this,
                                handler: function() {
                                    jenis = 'F1';
                                    displayBeforeCetak();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                id: 't_n1',
                                text: lang('GNP'),
                                cls:'hide-icon',
                                scope: this,
                                handler: function() {
                                    jenis = 'N1';
                                    displayBeforeCetak();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('PPI'),
                                cls:'hide-icon',
                                scope: this,
                                handler: function() {
                                    jenis = 'PPI';
                                    displayBeforeCetak();
                                }
                            }]
                    }],
                columns: [{
                        text: lang('ID'),
                        dataIndex: 'participant_id',
                        width: '5%',
                        hidden: true
                    }, {
                        text: lang('ID'),
                        dataIndex: 'farmer_id',
                        width: '10%'
                    }, {
                        text: lang('Registered Farmer'),
                        width: '30%',
                        dataIndex: 'farmer',
                        // editor: {
                        //     xtype: 'combo',
                        //     displayField: 'label',
                        //     id: 'farmer',
                        //     name: 'farmer',
                        //     valueField: 'id',
                        //     queryMode: 'remote',
                        //     store: store_farmer,
                        //     listeners: {
                        //         change: function(cb, nv, ov) {
                        //             if (isNumber(Ext.getCmp('farmer').getValue())) {
                        //                 Ext.Ajax.request({
                        //                     waitMsg: 'Check data...',
                        //                     url: m_check,
                        //                     method: 'GET',
                        //                     params: {
                        //                         trainingid: Ext.getCmp('id').getValue(),
                        //                         farmerid: Ext.getCmp('farmer').getValue()
                        //                     },
                        //                     success: function(response, opts) {
                        //                         var obj = Ext.decode(response.responseText);
                        //                         if (!obj.data) {
                        //                             Ext.MessageBox.alert('Warning', lang('Farmer telah terdapat dalam list'));
                        //                             Ext.getCmp('farmer').setValue('');
                        //                             return;
                        //                         }
                        //                     }
                        //                 });

                        //                 store_family.load({
                        //                     params: {
                        //                         farmerid: Ext.getCmp('farmer').getValue()
                        //                     }});
                        //                 //Ext.getCmp('Kabupaten').enable();
                        //             }
                        //         }
                        //     }
                        // }
                    }, {
                        text: lang('Participant'),
                        width: '10%',
                        dataIndex: 'participant',
                        editor: {
                            xtype: 'combo',
                            store: store_ya_tidak,
                            id: 'participant',
                            name: 'participant',
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'id'
                        }
                    }, {
                        text: lang('Pengganti'),
                        width: '15%',
                        dataIndex: 'if_no',
                        editor: {
                            xtype: 'combo',
                            displayField: 'label',
                            id: 'if_no',
                            name: 'if_no',
                            valueField: 'id',
                            queryMode: 'local',
                            store: store_family
                        }
                    }, {
                        text: lang('W. Awal'),
                        width: '9%',
                        dataIndex: 'wstart',
                        editor: {
                            xtype: 'textfield'
                        }
                    }, {
                        text: lang('W. Akhir'),
                        width: '9%',
                        dataIndex: 'wend',
                        editor: {
                            xtype: 'textfield'
                        }
                    }, {
                        text: lang('B. Awal'),
                        width: '9%',
                        dataIndex: 'bstart',
                        editor: {
                            xtype: 'textfield'
                        }
                    }, {
                        text: lang('B. Akhir'),
                        width: '9%',
                        dataIndex: 'bend',
                        editor: {
                            xtype: 'textfield'
                        }
                    }],
                plugins: [RowEditing],
                listeners: {
                    'itemdblclick': function(dv, record, item, index, e) {
                        if (!m_act_update) {
                            RowEditing.cancelEdit();
                            return false;
                        } else {
                            var sm = record;
                            store_family.load({
                                params: {
                                    farmerid: sm.get('farmer_id')
                                }});
                        }
                    },
                    'canceledit': function(editor, e, eOpts) {
                        if (m_act_save == 'hide-icon') {return false}
                        store_participant.load({
                            params: {
                                training: Ext.getCmp('id').getValue()
                            }});
                    },
                    'edit': function(editor, e) {
                        if (m_act_save == 'hide-icon') {return false}
                        if (e.record.data.participant_id.trim() == '') {
                            Ext.Ajax.request({
                                waitMsg: 'Please wait...',
                                url: m_crud + '_participant',
                                method: 'POST',
                                params: {
                                    training: Ext.getCmp('id').getValue(),
                                    farmer: e.record.data.farmer,
                                    participant: e.record.data.participant,
                                    if_no: e.record.data.if_no,
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
                        } else {
                            Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please wait...',
                                        url: m_crud + '_participant',
                                        method: 'PUT',
                                        params: {
                                            id: e.record.data.participant_id,
                                            staf: e.record.data.staf,
                                            farmer: e.record.data.farmer,
                                            farmer_id: e.record.data.farmer_id,
                                            participant: e.record.data.participant,
                                            Subtitute: e.record.data.Subtitute,
                                            if_no: e.record.data.if_no,
                                            FamilyID: e.record.data.FamilyID,
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
                        }
                    }
                }
            }],
        buttons: [{
                id: 'saveButton',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue '+m_act_save,
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
        title: lang('Farmer Training'),
        closable: true,
        modal: true,
        closeAction: 'show',
        width: 1024,
        height: 600,
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
        fields: ['id', 'training', 'batch', 'tot', 'participant', 'start', 'end', 'days'],
        autoLoad: true,
        pageSize: 50,
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
            url: m_store_kabupaten,
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
        width: '100%',
        id: 'grid',
        minHeight: 250,
        //title: lang('Kader Training'),
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
                        handler: function() {
                            displayFormWindow();
                            if (m_param) Ext.getCmp('Provinsi').setValue(m_Province).setReadOnly(true);
                            else Ext.getCmp('Provinsi').setValue('').setReadOnly(false);
                            if (m_District) Ext.getCmp('Kabupaten').setValue(m_District).setReadOnly(true);
                            else Ext.getCmp('Kabupaten').setValue('').setReadOnly(false);
                            hideSave();
                            store_participant.load();
                            Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                        },
                        hidden: !m_act_add,
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
                        scope: this,
                        handler: function() {
                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            if (typeof(sm) === 'undefined') {
                                Ext.MessageBox.alert('Info', lang('Please select data to update'));
                                return false;
                            }
                            displayFormWindow();
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
                text: lang('Id'),
                dataIndex: 'id',
                width: '5%'
            }, {
                text: lang('Trainings'),
                flex: 3,
                dataIndex: 'training'
            }, {
                text: lang('District'),
                flex: 2,
                dataIndex: 'tot'
            }, {
                text: lang('Participants'),
                flex: 1,
                dataIndex: 'participant'
            }, {
                text: lang('Start'),
                flex: 1,
                dataIndex: 'start'
            }, {
                text: lang('End'),
                flex: 1,
                dataIndex: 'end'
            }, {
                text: lang('Days'),
                flex: 1,
                dataIndex: 'days'
            }, 
            ]
    });
    /*    mc_Kabupaten.on('load',function(st) {
     if (Ext.getCmp('Kab').getValue()==null) Ext.getCmp('Kab').setValue(st.getAt('0').get('label'));
     store.load({
     params: {
     key: Ext.getCmp('key').getValue(),
     kab: Ext.getCmp('Kab').getValue()
     }});
     });*/
//end of view



});
