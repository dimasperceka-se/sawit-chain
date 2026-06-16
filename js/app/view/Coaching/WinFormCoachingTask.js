/******************************************
 *  Author : hasbycs@gmail.com
 *  Created On : 2021-10-06
 *  File : WinApplyFilter.js
 *******************************************/

/*
 Param2 yg diperlukan ketika load View ini
 - LuKMLStoreGrid
 */

Ext.define('Koltiva.view.Coaching.WinFormCoachingTask', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Coaching.WinFormCoachingTask',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Coaching Task Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '45%',
    height: 700,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;

            if (thisObj.viewVar.OpsiDisplay == 'insert') {
                Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityID').setValue(thisObj.viewVar.ActivityID);
            }

            if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
                Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-Topic').setReadOnly(true);
                Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-CategoryID').setReadOnly(true);
                if (thisObj.viewVar.OpsiDisplay == 'view') {
                    Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-saveButton').setVisible(false);
                }

                //load formnya
                Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form').getForm().load({
                    url: m_api + '/coaching/coaching_task_data_form',
                    method: 'GET',
                    params: {
                        ActivityNCID: thisObj.viewVar.ActivityNCID
                    },
                    success: function (form, action) {
                        var r = Ext.decode(action.response.responseText);
                    },
                    failure: function (form, action) {
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Failed to retrieve data',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }
        }
    },
    initComponent: function () {
        var thisObj = this;
        var labelWidth = 200;

        //Store ========================= (Begin)
        thisObj.CmbCategory         = Ext.create('Koltiva.store.Coaching.CmbCategory');
        thisObj.CmbCoachingTopic    = Ext.create('Koltiva.store.Coaching.CmbCoachingTopic');
        thisObj.CmbCoachingSubTopic = Ext.create('Koltiva.store.Coaching.CmbCoachingSubTopic');
        thisObj.CmbFinding          = Ext.create('Koltiva.store.Coaching.CmbFinding');
        thisObj.CmbRecommendation   = Ext.create('Koltiva.store.Coaching.CmbRecommendation');        
        //Store ========================= (End)

        thisObj.items = [{
                xtype: 'form',
                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form',
                fileUpload: true,
                buttonAlign: 'center',
                cls: 'Sfr_PanelSubLayoutForm',
                autoScroll: true,
                bodyPadding: 5,
                layout: 'form',
                items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                                layout: 'form',
                                padding:'5 20 5 8',
                                items: [{
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityNCID',
                                        xtype: 'hiddenfield',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityNCID'
                                    },{
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityID',
                                        xtype: 'hiddenfield',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityID'
                                    },
                                    {
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-CategoryID',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-CategoryID',
                                        xtype: 'combo',
                                        labelWidth: labelWidth,
                                        fieldLabel: lang('Category'),
                                        store: thisObj.CmbCategory,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        baseCls: 'Sfr_FormInputMandatory',
                                        allowBlank: false,
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                thisObj.CmbCoachingTopic.load({
                                                    params: {
                                                        CategoryID: nv
                                                    }
                                                });
                                                if (thisObj.viewVar.OpsiDisplay == 'insert') {
                                                    Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-Topic').setValue('');
                                                }
                                            }
                                        }
                                    },{
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Topic',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Topic',
                                        xtype: 'combo',
                                        labelWidth: labelWidth,
                                        fieldLabel: lang('Coaching Topic'),
                                        store: thisObj.CmbCoachingTopic,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        baseCls: 'Sfr_FormInputMandatory',
                                        allowBlank: false,
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                thisObj.CmbCoachingSubTopic.load({
                                                    params: {
                                                        TopicID: nv
                                                    }
                                                });
                                                if (thisObj.viewVar.OpsiDisplay == 'insert') {
                                                    Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-Subtopic').setValue('');
                                                }
                                            }
                                        }
                                    },
                                    {
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Subtopic',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Subtopic',
                                        xtype: 'combo',
                                        labelWidth: labelWidth,
                                        fieldLabel: lang('Coaching Subtopic'),
                                        store: thisObj.CmbCoachingSubTopic,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        baseCls: 'Sfr_FormInputMandatory',
                                        allowBlank: false,
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                let val = '';
                                                if(Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus1').getValue() == true){
                                                    val = 1;
                                                }
                                                if(Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus2').getValue() == true){
                                                    val = 2;
                                                }
                                                if(Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus2').getValue() == true){
                                                    val =3;
                                                }

                                                thisObj.CmbFinding.load({
                                                    params: {
                                                        SubtopicID: nv,
                                                        UrgentlyStatus : val
                                                    }
                                                });

                                                thisObj.CmbRecommendation.load({
                                                    params: {
                                                        SubtopicID: nv
                                                    }
                                                });
                                                if (thisObj.viewVar.OpsiDisplay == 'insert') {
                                                    Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-Recommendation').setValue('');
                                                }
                                            }
                                        }
                                    },
                                    {
                                        fieldLabel: lang('Urgently Status'),
                                        xtype: 'radiogroup',
                                        baseCls: 'Sfr_FormInputMandatory',
                                        allowBlank: false,
                                        labelWidth: labelWidth,
                                        columns: 2,
                                        items: [{
                                            boxLabel: lang('High'),
                                            name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus',
                                            inputValue: '1',
                                            id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus1'
                                        }, {
                                            boxLabel: lang('Medium'),
                                            name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus',
                                            inputValue: '2',
                                            id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus2'
                                        }, {
                                            boxLabel: lang('Low'),
                                            name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus',
                                            inputValue: '3',
                                            id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus3'
                                        }],
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                let val = '';
                                                if(Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus1').getValue() == true){
                                                    val = 1;
                                                }
                                                if(Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus2').getValue() == true){
                                                    val = 2;
                                                }
                                                if(Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-UrgentlyStatus2').getValue() == true){
                                                    val =3;
                                                }

                                                let SubtopicID = Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-Subtopic').getValue();
                                                
                                                thisObj.CmbFinding.load({
                                                    params: {
                                                        SubtopicID: SubtopicID,
                                                        UrgentlyStatus: val
                                                    }
                                                });
                                                if (thisObj.viewVar.OpsiDisplay == 'insert') {
                                                    Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form-Finding').setValue('');
                                                }
                                            }
                                        }
                                    },{
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Finding',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Finding',
                                        xtype: 'combo',
                                        labelWidth: labelWidth,
                                        fieldLabel: lang('Finding'),
                                        store: thisObj.CmbFinding,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local'
                                    }, {
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-FindingOtherText',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-FindingOtherText',
                                        labelWidth: labelWidth,
                                        fieldLabel: lang('Others Finding')
                                    }, {
                                        fieldLabel: lang('Activity Type'),
                                        xtype: 'radiogroup',
                                        columns: 2,
                                        labelWidth: labelWidth,
                                        items: [{
                                                boxLabel: lang('Suggestion'),
                                                name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityType',
                                                inputValue: '1',
                                                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityType1'
                                            }, {
                                                boxLabel: lang('Practice'),
                                                name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityType',
                                                inputValue: '2',
                                                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityType2'
                                            }, {
                                                boxLabel: lang('Training'),
                                                name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityType',
                                                inputValue: '3',
                                                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActivityType3'
                                            }]
                                    },{
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Recommendation',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Recommendation',
                                        xtype: 'combo',
                                        labelWidth: labelWidth,
                                        fieldLabel: lang('Recommendation'),
                                        store: thisObj.CmbRecommendation,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local'
                                    }, {
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-RecomOtherText',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-RecomOtherText',
                                        labelWidth: labelWidth,
                                        fieldLabel: lang('Others Recommendation')
                                    }, {
                                        xtype: 'textareafield',
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Target',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Target',
                                        labelWidth: labelWidth,
                                        fieldLabel: lang('Target')
                                    }, {
                                        fieldLabel: lang('Followup Status'),
                                        xtype: 'radiogroup',
                                        columns: 2,
                                        labelWidth: labelWidth,
                                        items: [{
                                                boxLabel: lang('Fixed'),
                                                name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-FollowupStatus',
                                                inputValue: 'Fixed',
                                                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-FollowupStatus1'
                                            }, {
                                                boxLabel: lang('Pending'),
                                                name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-FollowupStatus',
                                                inputValue: 'Pending',
                                                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-FollowupStatus2'
                                            }, {
                                                boxLabel: lang('Rejected'),
                                                name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-FollowupStatus',
                                                inputValue: 'Rejected',
                                                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-FollowupStatus3'
                                            }]
                                    }, {
                                        xtype: 'datefield',
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Deadline',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Deadline',
                                        format: 'Y-m-d',
                                        labelWidth: labelWidth,
                                        fieldLabel: lang('Deadline')
                                    }, {
                                        fieldLabel: lang('Status'),
                                        xtype: 'radiogroup',
                                        columns: 2,
                                        labelWidth: labelWidth,
                                        items: [{
                                                boxLabel: lang('Cancelled'),
                                                name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActNCStatus',
                                                inputValue: '1',
                                                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActNCStatus1'
                                            }, {
                                                boxLabel: lang('Not Started'),
                                                name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActNCStatus',
                                                inputValue: '2',
                                                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActNCStatus2'
                                            }, {
                                                boxLabel: lang('In Progress'),
                                                name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActNCStatus',
                                                inputValue: '3',
                                                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActNCStatus3'
                                            }, {
                                                boxLabel: lang('Completed'),
                                                name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActNCStatus',
                                                inputValue: '4',
                                                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-ActNCStatus4'
                                            }]
                                    }, {
                                        xtype: 'textareafield',
                                        id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Explanation',
                                        name: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-Explanation',
                                        labelWidth: labelWidth,
                                        fieldLabel: lang('Explanation')
                                    }]
                            }]
                    }]
            }];

        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                id: 'Koltiva.view.Coaching.WinFormCoachingTask-Form-saveButton',
                text: lang('Save'),
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    var form = Ext.getCmp('Koltiva.view.Coaching.WinFormCoachingTask-Form');
                    if (form.isValid()) {
                        form.submit({
                            url: m_api + '/coaching/coaching_task_data',
                            waitMsg: lang('Sending data...'),
                            params: {
                                OpsiDisplay: thisObj.viewVar.OpsiDisplay
                            },
                            success: function (fp, o) {
                                Ext.MessageBox.alert('Success', lang('Data saved.'));
                                thisObj.close();
                                thisObj.viewVar.CallerStore.load();
                            },
                            failure: function (fp, o) {
                                try {
                                    var r = Ext.decode(o.response.responseText);
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                } catch (err) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: 'Connection Error',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            }
                        });
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    }
});