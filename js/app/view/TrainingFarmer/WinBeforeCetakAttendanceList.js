/*
 * @Author: fikri
 * @Date:   2019-11-26 11:28:07
 */
function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}
Ext.define('Koltiva.view.TrainingFarmer.WinBeforeCetakAttendanceList', {
    extend: 'Ext.window.Window',
    title: lang('Print Attendance List'),
//    id: 'printAttendanceList',
    id: 'Koltiva.view.TrainingFarmer.WinBeforeCetakAttendanceList',
    modal: true,
    closable: true,
    closeAction: 'destroy',
    width: 450,
    height: 130,
    layout: 'fit',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
            if (thisObj.viewVar.DayNumber)
                Ext.getCmp('Koltiva.view.TrainingFarmer.Form-DayNumber').setMaxValue(thisObj.viewVar.DayNumber);
        }
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.items = [{
                xtype: 'panel',
                layout: {
                    align: 'stretch',
                    type: 'vbox'
                },
                items: [{
                        xtype: 'form',
                        autoScroll: true,
                        width: 420,
                        height: 100,
                        id: 'dataBeforeCetakAttendanceList',
                        bodyPadding: 5,
                        items: [{
                                xtype: 'numberfield',
                                fieldLabel: lang('Day Number'),
                                id: 'Koltiva.view.TrainingFarmer.Form-DayNumber',
                                name: 'DayNumber',
                                minValue: 1,
                            }, {
                                xtype: 'container',
                                height: 43,
                                layout: {
                                    align: 'stretch',
                                    pack: 'center',
                                    padding: 2,
                                    type: 'hbox'
                                },
                                items: [{
                                        id: 'h_AttendanceList',
                                        xtype: 'button',
                                        text: lang('Cetak'),
                                        margin: '5 5 5 2',
                                        scale: 'large',
                                        ui: 's-button',
                                        disabled: false,
                                        cls: 's-blue',
                                        handler: function () {
                                            var DayNumber = Ext.getCmp('Koltiva.view.TrainingFarmer.Form-DayNumber').getValue();
                                            if (!isNumber(DayNumber)) {
                                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih Hari'));
                                                return;
                                            }
                                            thisObj.close();
                                            //preview_cetak_surat(m_cetak_basic_farmer + 'FarmerTrainingID/' + FarmerTrainingID + '/SurveyID/' + SurveyID);
                                            preview_cetak_surat(m_cetak + thisObj.viewVar.idt + '?DayNumber=' + DayNumber + '&result=1');
                                        }
                                    }, {
                                        xtype: 'button',
                                        text: lang('Batal'),
                                        icon: varjs.config.base_url + 'images/icons/new/close.png',
                                        cls: 'Sfr_BtnFormGrey',
                                        overCls: 'Sfr_BtnFormGrey-Hover',
                                        disabled: false,
                                        handler: function () {
                                            thisObj.close();
                                        }
                                    }]
                            }]
                    }]
            }];
        this.callParent(arguments);
    }
});