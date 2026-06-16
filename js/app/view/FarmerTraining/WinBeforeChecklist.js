Ext.define('Koltiva.view.FarmerTraining.WinBeforeChecklist', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmerTraining.WinBeforeChecklist',
    title: lang('Training Day List'),
    closeAction: 'destroy',
    width: 500,
    height: 200,
    autoScroll: true,
    modal: true,
    layout: 'fit',
    viewVar: false,
    setviewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.items = [{
            xtype: 'form',
            height: '100%',
            width: '100%',
            padding: 10,
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
            ]
        }];

        thisObj.buttons = [{
            text: lang('Select'),
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var form = Ext.getCmp('dataWinSelectDay').getForm();
                if (form.isValid()) {
                    var date = new Date(Ext.getCmp('TrainingDate2').getValue());
                    var winChecklistParticipant = Ext.create('Koltiva.view.FarmerTraining.WinChecklistParticipant', {
                        viewVar: {
                            farmerTrainingID: thisObj.viewVar.farmerTrainingID,
                            trainingDate: Ext.Date.format(date, 'Y-m-d'),
                            trainingDay: Ext.getCmp('TrainingDay').getValue(),

                        }
                    });
                    if (!winChecklistParticipant.isVisible()) {
                        winChecklistParticipant.center();
                        winChecklistParticipant.show();
                    } else {
                        winChecklistParticipant.close();
                    }
                }
            }
        }, {
            text: lang('Close'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function () {
                thisObj.close();
            }
        }]

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;

            Ext.getCmp('TrainingDate2').setMinValue(thisObj.viewVar.min);
            Ext.getCmp('TrainingDate2').setMaxValue(thisObj.viewVar.max);
            Ext.getCmp('TrainingDay').setMaxValue(thisObj.viewVar.maxDay);
        }
    }
});