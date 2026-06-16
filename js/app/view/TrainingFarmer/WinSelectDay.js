/*
 * @Author: fikri
 * @Date:   2019-11-18 15:49:07
 */
Ext.define('Koltiva.view.TrainingFarmer.WinSelectDay', {
    extend: 'Ext.window.Window',
    modal: true,
    closable: true,
    closeAction: 'destroy',
    viewVar: false,
    title: lang('Training Day List'),
    id: 'Koltiva.view.TrainingFarmer.WinSelectDay',
//    id: 'winSelectDay',
    width: 500,
    height: 200,
    autoScroll: true,
    layout: 'fit',
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.items = [{
                xtype: 'form',
                margin: 10,
                id: 'dataWinSelectDay',
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 120,
                    anchor: '100%'
                },
                items: [{
                        xtype: 'numberfield',
                        fieldLabel: lang('Training Day'),
                        id: 'TrainingDay',
                        name: 'TrainingDay',
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        minValue: 1,
                        allowBlank: false
                    }, {
                        xtype: 'datefield',
                        format: 'Y-m-d',
                        anchor: '100%',
                        fieldLabel: lang('Training Date'),
                        id: 'TrainingDate2',
                        name: 'TrainingDate',
                        // maxValue: new Date(),
                        allowBlank: false
                    }]
            }];

        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    var form = Ext.getCmp('dataWinSelectDay').getForm();
                    if (form.isValid()) {
                        if (Ext.getCmp('Koltiva.view.TrainingFarmer.WinParCheckListDay'))
                            Ext.getCmp('Koltiva.view.TrainingFarmer.WinParCheckListDay').destroy();
                        var WinParCheckListDay = Ext.create('Koltiva.view.TrainingFarmer.WinParCheckListDay', {
                            viewVar: {
                                TrainingID: thisObj.viewVar.TrainingID,
                                TrainingDay: Ext.getCmp('TrainingDay').getValue(),
                                TrainingDate2: Ext.getCmp('TrainingDate2').getValue(),
                                TrainingDayStatusHalf: thisObj.viewVar.TrainingDayStatusHalf,
                                DayNumber: thisObj.viewVar.DayNumber,
                                callerStore: thisObj.viewVar.callStore
                            }
                        });
                        if (!WinParCheckListDay.isVisible()) {
                            WinParCheckListDay.center();
                            WinParCheckListDay.show();
                        } else {
                            WinParCheckListDay.close();
                        }
                    }
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
            // TrainingDate
            var min = new Date(thisObj.viewVar.TrainingStart);
            var max = new Date(thisObj.viewVar.TrainingEnd);
            Ext.getCmp('TrainingDate2').setMinValue(min);
            Ext.getCmp('TrainingDate2').setMaxValue(max);
            Ext.getCmp('TrainingDay').setMaxValue(thisObj.viewVar.TrainingDays);
        }
    }
});