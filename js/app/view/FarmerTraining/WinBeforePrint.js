Ext.define('Koltiva.view.FarmerTraining.WinBeforePrint', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmerTraining.WinBeforePrint',
    closable: true,
    modal: true,
    layout: 'fit',
    closeAction: 'destroy',
    width: 450,
    height: 150,
    viewVar: false,
    setviewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.items = [{
            xtype: 'form',
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
            }]
        }];

        thisObj.buttons = [{
            id: 'h_AttendanceList',
            text: lang('Cetak'),
            icon: varjs.config.base_url + 'images/icons/silk/printer.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var DayNumber = Ext.getCmp('DayNumber').getValue();
                if (!isNumber(DayNumber)) {
                    Ext.MessageBox.alert('Warning', lang('Silahkan pilih Hari'));
                    return;
                }
                thisObj.close();
                preview_cetak_surat(m_cetak + thisObj.viewVar.farmerTrainingID + '?DayNumber=' + DayNumber + '&result=1');
            }
        }, {
            text: lang('Batal'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function () {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    }
});

function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}