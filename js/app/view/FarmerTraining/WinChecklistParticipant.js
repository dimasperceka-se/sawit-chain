Ext.define('Koltiva.view.FarmerTraining.WinChecklistParticipant', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmerTraining.WinChecklistParticipant',
    title: lang('Daftar Hadir'),
    closeAction: 'destroy',
    width: '70%',
    height: '70%',
    autoScroll: true,
    modal: true,
    layout: 'fit',
    viewVar: false,
    setviewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        var store_participant_checklist_day = Ext.create('Koltiva.store.FarmerTraining.ParticipantChecklistDayGrid');
        var mc_family = Ext.create('Koltiva.store.FarmerTraining.CmbMcFamily');
        store_participant_checklist_day.load({
            params: {
                FarmerTrainingID: thisObj.viewVar.farmerTrainingID,
                DayNumber: thisObj.viewVar.trainingDay
            }
        });

        thisObj.items = [{
            xtype: 'form',
            height: '100%',
            width: '100%',
            autoScroll: true,
            id: 'dataFormParCheckListDay',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 120,
                anchor: '100%'
            },
            items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                            columnWidth: .5,
                            layout: 'form',
                            padding: 5,
                            border: false,
                            items: [{
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
                            items: [{
                                    layout: 'hbox',
                                    border: false,
                                    padding: 0,
                                    items: [{
                                            flex: 3,
                                            xtype: 'panel',
                                            items: [{
                                                xtype: 'textfield',
                                                id: 'parcheklistday_startdate',
                                                fieldLabel: lang('Training Period'),
                                                readOnly: true
                                            }, ]
                                        },
                                        {
                                            flex: 2,
                                            xtype: 'panel',
                                            items: [{
                                                xtype: 'textfield',
                                                id: 'parcheklistday_enddate',
                                                fieldLabel: lang('Until'),
                                                labelWidth: 50,
                                                readOnly: true
                                            }, ]
                                        },
                                        {
                                            flex: 1,
                                            xtype: 'panel',
                                            items: [{
                                                xtype: 'textfield',
                                                id: 'parcheklistday_daycount',
                                                fieldLabel: lang('Days'),
                                                labelWidth: 50,
                                                readOnly: true
                                            }, ]
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
                    plugins: [new Ext.grid.plugin.CellEditing({
                        clicksToEdit: 1
                    })],
                    listeners: {
                        itemclick: function (dv, record, item, index, e) {
                            console.log(record.data.FarmerID);
                            mc_family.load({
                                params: {
                                    key: record.data.FarmerID
                                }
                            });
                        },
                    },
                    columns: [{
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
            ]
        }];

        thisObj.buttons = [{
            id: 'save_par_check_day',
            text: lang('Save'),
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var data = [];
                $.each(Ext.getCmp('grid_participant_checklist_day').getStore().data.items, function (index, val) {
                    data.push(val.data);
                });
                $.ajax({
                        url: m_attendance_day,
                        type: 'POST',
                        data: {
                            FarmerTrainingID: thisObj.viewVar.farmerTrainingID,
                            DayNumber: thisObj.viewVar.trainingDay,
                            TrainingDate: Ext.Date.format(thisObj.viewVar.trainingDate, 'Y-m-d'),
                            data: data
                        },
                    })
                    .done(function () {
                        Ext.MessageBox.alert(lang('Info'), lang('Attendance saved'));
                    })
                    .fail(function () {
                        Ext.MessageBox.alert(lang('Warning'), lang('Failed to save attendance'));
                    })
                    .always(function () {
                        // console.log("complete");
                    });
            }
        }, {
            text: lang('Close'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function () {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;

            Ext.getCmp('parcheklistday_farmertrainingid').setValue(Ext.getCmp('idt').getValue());
            Ext.getCmp('parcheklistday_training_name').setValue(Ext.getCmp('LabelTemp').getValue());
            Ext.getCmp('parcheklistday_startdate').setValue(Ext.Date.format(new Date(Ext.getCmp('TrainingStart').getValue()), 'Y-m-d'));
            Ext.getCmp('parcheklistday_enddate').setValue(Ext.Date.format(new Date(Ext.getCmp('TrainingEnd').getValue()), 'Y-m-d'));
            Ext.getCmp('parcheklistday_daycount').setValue(Ext.getCmp('days').getValue());

            Ext.getCmp('parcheklistday_training_day').setValue(thisObj.viewVar.trainingDay);
            var date = new Date(thisObj.viewVar.trainingDate);
            Ext.getCmp('parcheklistday_training_date').setValue(Ext.Date.format(date, 'Y-m-d'));

            var TrainingDayStatusHalf = Ext.getCmp('TrainingDayStatusHalf').getValue();
            if (TrainingDayStatusHalf == true) {
                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setVisible(false);
                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setText(lang('Kehadiran'))
            } else {
                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setVisible(true);
                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setVisible(true);

                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setText(lang('Pagi'));
                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setText(lang('Siang'));
            }
        }
    }
});