/**
 * Form IMS Training Event Mapping
 */

Ext.define('Koltiva.view.IMS.WinFormImsTrainingEventMapping', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping',
    title: lang('Form'),
    modal: true,
    width: '41%',
    height: 270,
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.storeCmbParticipantType = Ext.create('Koltiva.store.IMS.CmbImsTrainingEventMapping');

        thisObj.items = [{
            xtype: 'form',
            height: '100%',
            width: '100%',
            bodyPadding: 5,
            id: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-Form',
            items: [
                {
                    xtype: 'textfield',
                    id: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-IMSID',
                    name: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-IMSID',
                    value: thisObj.viewVar.IMSID,
                    hidden: true
                },{
                    xtype      : 'fieldcontainer',
                    fieldLabel : lang('Activity Type'),
                    defaultType: 'radiofield',
                    defaults: {
                        flex: 0.5
                    },
                    labelWidth: 150,
                    width: 600,
                    layout: 'hbox',
                    allowBlank: false,
                    items: [
                        {
                            boxLabel  : lang('Full'),
                            name      : 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-ActivityType',
                            inputValue: 'Full',
                            id        : 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-ActivityType-Full',
                            readOnly: (thisObj.viewVar.opsiDisplay === 'add') ? false : true
                        }, {
                            boxLabel  : lang('Remedial'),
                            name      : 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-ActivityType',
                            inputValue: 'Remedial',
                            id        : 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-ActivityType-Remidial',
                            readOnly: (thisObj.viewVar.opsiDisplay === 'add') ? false : true
                        }
                    ]
                },{
                    xtype: 'combobox',
                    id: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-ParticipantType',
                    name: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-ParticipantType',
                    fieldLabel: lang('Participant Type'),
                    width: 600,
                    labelWidth: 150,
                    labelAlign: 'left',
                    store: thisObj.storeCmbParticipantType,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    allowBlank: false,
                    readOnly: (thisObj.viewVar.opsiDisplay === 'add') ? false : true
                },{
                    xtype: 'textfield',
                    id: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-TopikGAP',
                    name: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-TopikGAP',
                    fieldLabel: lang('Topic GAP'),
                    width: 600,
                    labelWidth: 150,
                    labelAlign: 'left',
                    allowBlank: false,
                },{
                    xtype: 'textfield',
                    id: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-TopikCOC',
                    name: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-TopikCOC',
                    fieldLabel: lang('Topic COC'),
                    width: 600,
                    labelWidth: 150,
                    labelAlign: 'left',
                    allowBlank: false,
                }
            ],
                buttons: [
                    {
                        icon: varjs.config.base_url + 'images/icons/new/save.png',
                        id: 'Koltiva.view.IMS.WinFormImsTrainingEventMapping-ButtonSave',
                        text: lang('Save'),
                        margin: '5px',
                        cls: 'Sfr_BtnFormBlue',
                        overCls: 'Sfr_BtnFormBlue-Hover',
                        hidden: (thisObj.viewVar.opsiDisplay === 'view') ? true : false,
                        handler: function () {
                            var form = this.up('form').getForm(),
                                    method = (thisObj.viewVar.opsiDisplay == 'add') ? 'POST' : 'PUT';
                            if (form.isValid()) {
                                form.submit({
                                    url: m_api + "/ims_training/training_event_mapping",
                                    method: method,
                                    clientValidation: true,
                                    waitMsg: lang('Saving data') + '...',
                                    success: function (form, action) {
                                        thisObj.viewVar.caller.reloadGridAndCloseWin();
                                        Ext.MessageBox.alert(lang('Message'), action.result.message);
                                    },
                                    failure: function (form, action) {
                                        let cb = JSON.parse(action.response.responseText);
                                        thisObj.viewVar.caller.reloadGridAndCloseWin();
                                        Ext.MessageBox.alert(lang('Message'), cb.message);
                                        console.log(action);
                                    }
                                });
                            }
                        }
                    }, {
                        margin: '5px',
                        icon: varjs.config.base_url + 'images/icons/new/close.png',
                        text: lang('Close'),
                        cls: 'Sfr_BtnFormGrey',
                        overCls: 'Sfr_BtnFormGrey-Hover',
                        disabled: false,
                        handler: function () {
                            thisObj.close();
                        }
                    }
                ]
            }];

        this.callParent(arguments);
    }
});