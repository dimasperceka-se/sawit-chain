/*
 * @Author: fikri
 * @Date:   2019-11-26 15:49:07
 */
Ext.define('Koltiva.view.TrainingFarmer.WinParCheckListDay', {
    extend: 'Ext.window.Window',
    modal: true,
    closable: true,
    closeAction: 'destroy',
    viewVar: false,
    title: lang('Daftar Hadir'),
    id: 'Koltiva.view.TrainingFarmer.WinParCheckListDay',
//    id: 'winparchecklistday',
    width: '70%',
    height: '70%',
    autoScroll: true,
//    layout: 'fit',
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        var store_participant_checklist_day = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['FarmerID', 'FamilyID', 'FarmerName', 'AnggotaName', 'Attendance1', 'Attendance2'],
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
        var mc_family = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_family,
                reader: {
                    type: 'json',
                    root: 'data',
                }
            }
        });

        thisObj.items = [{
                xtype: 'form',
                margin: 10,
                height: '100%',
                width: '100%',
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
                                    }, {
                                        xtype: 'textfield',
                                        id: 'parcheklistday_training_name',
                                        fieldLabel: lang('Training Name'),
                                        readOnly: true
                                    }, {
                                        xtype: 'textfield',
                                        id: 'parcheklistday_training_day',
                                        fieldLabel: lang('Training Day'),
                                        readOnly: true
                                    }]
                            }, {
                                columnWidth: .5,
                                layout: 'form',
                                padding: 5,
                                border: false,
                                items: [{
                                        xtype: 'textfield',
                                        id: 'parcheklistday_training_name',
                                        fieldLabel: lang('Training Name'),
                                        readOnly: true
                                    }, {
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
                                                    }]
                                            }, {
                                                flex: 2,
                                                xtype: 'panel',
                                                items: [{
                                                        xtype: 'textfield',
                                                        id: 'parcheklistday_enddate',
                                                        fieldLabel: lang('Until'),
                                                        labelWidth: 50,
                                                        readOnly: true
                                                    }]
                                            }, {
                                                flex: 1,
                                                xtype: 'panel',
                                                items: [{
                                                        xtype: 'textfield',
                                                        id: 'parcheklistday_daycount',
                                                        fieldLabel: lang('Days'),
                                                        labelWidth: 50,
                                                        readOnly: true
                                                    }]
                                            }
                                        ]
                                    }, {
                                        xtype: 'textfield',
                                        id: 'parcheklistday_training_date',
                                        fieldLabel: lang('Training Date'),
                                        readOnly: true
                                    }]
                            }]
                    }, {
                        xtype: 'gridpanel',
                        style: 'border:1px solid #CCC;',
                        id: 'grid_participant_checklist_day',
                        store: store_participant_checklist_day,
                        cls: 'Sfr_GridNew',
                        width: '100%',
                        //loadMask: true,
                        selType: 'rowmodel',
                        plugins: [new Ext.grid.plugin.CellEditing({
                                clicksToEdit: 1
                            })],
                        listeners: {
                            itemclick: function (dv, record, item, index, e) {
                                //console.log(record.data.FarmerID);
                                mc_family.load({
                                    params: {
                                        key: record.data.FarmerID
                                    }
                                });
                            }
                        },
                        columns: [{
                                text: '#',
                                xtype: 'rownumberer',
                                width: 50,
                            }, {
                                text: '#',
                                dataIndex: 'FarmerID',
                                hidden: true
                            }, {
                                text: '#',
                                dataIndex: 'FamilyID',
                                hidden: true
                            }, {
                                text: lang('Participant Name (Farmer)'),
                                dataIndex: 'FarmerName',
                                flex: 3,
                            }, {
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
                            }, {
                                text: lang('Kehadiran'),
                                dataIndex: 'Attendance1',
                                xtype: 'checkcolumn',
                                flex: 1,
                            }/*,
                             {
                             text: lang('Siang'),
                             dataIndex: 'Attendance2',
                             xtype: 'checkcolumn',
                             flex: 1,
                             },*/]
                    }]
            }];

        thisObj.buttons = [{
                id: 'save_par_check_day',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    // var sm = Ext.getCmp('grid_participant_').getSelectionModel().getSelection();
                    var data = [];
                    $.each(Ext.getCmp('grid_participant_checklist_day').getStore().data.items, function (index, val) {
                        // val.data.TrainingDate = Ext.util.Format.date(val.data.TrainingDate,'Y-m-d');
                        //console.log(val.data.TrainingDate);
                        data.push(val.data);
                    });
                    // console.log(data);
                    $.ajax({
                        url: m_attendance_day,
                        type: 'POST',
                        data: {
                            FarmerTrainingID: thisObj.viewVar.TrainingID,
                            DayNumber: thisObj.viewVar.DayNumber,
                            TrainingDate: Ext.Date.format(thisObj.viewVar.TrainingDate2, 'Y-m-d'),
                            data: data
                        },
                    }).done(function () {
                        Ext.MessageBox.alert(lang('Info'), lang('Attendance saved'));
                    }).fail(function () {
                        Ext.MessageBox.alert(lang('Warning'), lang('Failed to save attendance'));
                    }).always(function () {
                        // console.log("complete");
                    });
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
            Ext.getCmp('parcheklistday_training_day').setValue(thisObj.viewVar.TrainingDay);
            var date = new Date(thisObj.viewVar.TrainingDate2);
            Ext.getCmp('parcheklistday_training_date').setValue(Ext.Date.format(date, 'Y-m-d'));
            Ext.getCmp('grid_participant_checklist_day').getStore().load({
                params: {
                    FarmerTrainingID: thisObj.viewVar.TrainingID,
                    DayNumber: thisObj.viewVar.DayNumber
                }
            });
            if (thisObj.viewVar.TrainingID) {
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {id: thisObj.viewVar.TrainingID},
                    success: function (fp, o) {
                        var r = Ext.decode(fp.responseText);

                        Ext.getCmp('parcheklistday_farmertrainingid').setValue(r.FarmerTrainingID);
                        Ext.getCmp('parcheklistday_training_name').setValue(r.label);
                        Ext.getCmp('parcheklistday_startdate').setValue(Ext.Date.format(new Date(r.TrainingStart), 'Y-m-d'));
                        Ext.getCmp('parcheklistday_enddate').setValue(Ext.Date.format(new Date(r.TrainingEnd), 'Y-m-d'));
                        Ext.getCmp('parcheklistday_daycount').setValue(r.TrainingDays);
                    }
                });
            }


            var TrainingDayStatusHalf = thisObj.viewVar.TrainingDayStatusHalf;
            if (TrainingDayStatusHalf == true) {
//                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setVisible(false);
                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setText(lang('Kehadiran'))
            } else {
                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setVisible(true);
//                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setVisible(true);

                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setText(lang('Pagi'));
//                Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setText(lang('Siang'));
            }
        }
    }
});