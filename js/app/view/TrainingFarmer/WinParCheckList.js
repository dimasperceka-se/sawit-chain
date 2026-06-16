/*
 * @Author: fikri
 * @Date:   2019-11-18 15:49:07
 */
Ext.define('Koltiva.view.TrainingFarmer.WinParCheckList', {
    extend: 'Ext.window.Window',
    title: lang('Daftar Hadir'),
    id: 'Koltiva.view.TrainingFarmer.WinParCheckList',
    modal: true,
    closable: true,
    closeAction: 'destroy',
    width: '60%',
    height: '70%',
    autoScroll: true,
    layout: 'fit',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;
        var sm = thisObj.viewVar.sm;
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

        thisObj.items = [{
                xtype: 'panel',
                height: '100%',
                width: '100%',
                autoScroll: true,
                id: 'dataFormParCheckList',
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
                                        xtype: 'textfield',
                                        id: 'parcheklist_farmerid',
                                        fieldLabel: lang('Farmer ID'),
                                        readOnly: true
                                    }, {
                                        xtype: 'textfield',
                                        id: 'parcheklist_farmename',
                                        fieldLabel: lang('Farmer Name'),
                                        readOnly: true
                                    }, {
                                        xtype: 'textfield',
                                        id: 'parcheklist_groupname',
                                        fieldLabel: lang('Farmer Group Name'),
                                        readOnly: true
                                    }]
                            }, {
                                columnWidth: .45,
                                layout: 'form',
                                padding: 5,
                                border: false,
                                items: [{
                                        xtype: 'textfield',
                                        id: 'parcheklist_trainingdays',
                                        fieldLabel: lang('Training Days'),
                                        readOnly: true
                                    }, {
                                        xtype: 'textfield',
                                        id: 'parcheklist_startdate',
                                        fieldLabel: lang('Start Date'),
                                        readOnly: true
                                    }, {
                                        xtype: 'textfield',
                                        id: 'parcheklist_enddate',
                                        fieldLabel: lang('End Date'),
                                        readOnly: true
                                    }]
                            }]
                    }, {
                        xtype: 'gridpanel',
                        style: 'border:1px solid #CCC;',
                        id: 'grid_participant_checklist',
                        store: store_participant_checklist,
                        cls: 'Sfr_GridNew',
                        width: '100%',
                        //loadMask: true,
                        selType: 'rowmodel',
                        plugins: [new Ext.grid.plugin.CellEditing({
                                clicksToEdit: 1
                            })],
                        columns: [{
                                text: lang('Hari Pertemuan'),
                                dataIndex: 'DayNumber',
                                flex: 3,
                                renderer: function (value) {
                                    return lang('Pertemuan') + ' ' + value;
                                }
                            }, {
                                text: lang('Training Date'),
                                dataIndex: 'TrainingDate',
                                xtype: 'datecolumn',
                                format: 'Y-m-d',
                                flex: 1,
                                //renderer: Ext.util.Format.dateRenderer('d M Y'),
                                editor: {
                                    xtype: 'datefield',
                                    id: 'TrainingDate',
                                    format: 'Y-m-d',
                                    submitFormat: 'Y-m-d',
                                    minValue: '2010-01-01',
                                    // disabledDays: [0, 6],
                                    // disabledDaysText: 'Plants are not available on the weekends'
                                }
                            }, {
                                text: lang('Pagi'),
                                id: 'sinTDayAttendancePagi',
                                dataIndex: 'Attendance1',
                                xtype: 'checkcolumn',
                                flex: 1
                            }, {
                                text: lang('Siang'),
                                id: 'sinTDayAttendanceSiang',
                                dataIndex: 'Attendance2',
                                xtype: 'checkcolumn',
                                flex: 1,
                            }]
                    }]
            }];

        thisObj.buttons = [{
                id: 'save_par_check',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    var data = [];
                    $.each(Ext.getCmp('grid_participant_checklist').getStore().data.items, function (index, val) {
                        val.data.TrainingDate = Ext.util.Format.date(val.data.TrainingDate, 'Y-m-d');
                        //console.log(val.data.TrainingDate);
                        data.push(val.data);
                    });
                    //console.log(data);
                    $.ajax({
                        url: m_attendance,
                        type: 'POST',
                        data: {
                            FarmerTrainingID: sm[0].data.FarmerTrainingID,
                            FarmerID: sm[0].data.MemberID,
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
                    thisObj.hide();
                }
            }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
            var sm = thisObj.viewVar.sm;
            var data = thisObj.viewVar.data;


            console.log(sm);

            Ext.getCmp('grid_participant_checklist').getStore().load({
                params: {
                    FarmerTrainingID: sm[0].data.FarmerTrainingID,
                    FarmerID: sm[0].data.MemberID
                }
            });
            if (data) {
                Ext.getCmp('parcheklist_farmerid').setValue(data['FarmerID']);
                Ext.getCmp('parcheklist_farmename').setValue(data['FarmerName']);
                Ext.getCmp('parcheklist_groupname').setValue(data['GroupName']);
                Ext.getCmp('parcheklist_trainingdays').setValue(data['TrainingDays']);
                Ext.getCmp('parcheklist_startdate').setValue(data['TrainingStart']);
                Ext.getCmp('parcheklist_enddate').setValue(data['TrainingEnd']);
//            store_DayNumber.load({
//                params: {
//                    dayNumber: data['TrainingDays']
//                }
//            });

                if (data['TrainingDayStatus'] == 'half') {
                    Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance2]').setVisible(false);
                    Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance1]').setText(lang('Kehadiran'))
                } else {
                    Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance1]').setVisible(true);
                    Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance2]').setVisible(true);

                    Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance1]').setText(lang('Pagi'))
                    Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance2]').setText(lang('Siang'))
                }
            }
        }
    }
});