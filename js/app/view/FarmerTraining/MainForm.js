Ext.define('Koltiva.view.FarmerTraining.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmerTraining.MainForm',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setviewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;
            document.getElementById('divCommonContentRegion').style.display = 'none';

            if (thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view') {

                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {
                        id: this.viewVar.trainMasterID
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(form.responseText);
                        Ext.getCmp('id').setValue(thisObj.viewVar.trainMasterID);
                        thisObj.setFormValue(r);
                    },
                    failure: function (form, action) {
                        Ext.MessageBox.hide();
                        Ext.MessageBox.show({
                            title: lang('Failed'),
                            msg: lang('Failed to retrieve data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }
        },
        beforerender: function () {
            var thisObj = this;

            if (thisObj.viewVar.opsiDisplay != 'insert') {
                Ext.MessageBox.show({
                    msg: 'Please wait...',
                    progressText: 'Loading...',
                    width: 300,
                    wait: true,
                    waitConfig: {
                        interval: 200
                    },
                    icon: 'ext-mb-info', //custom class in msg-box.html
                    animateTarget: 'mb9'
                });
            }
        }
    },
    initComponent: function () {
        var thisObj = this;
        let labelWidth = 200;

        //Store yg dipakai =============================================================== (Begin)
        var store_training = Ext.create('Koltiva.store.FarmerTraining.CmbTraining');
        var store_farmer = Ext.create('Koltiva.store.FarmerTraining.CmbFarmer');
        var store_provinsi = Ext.create('Koltiva.store.FarmerTraining.CmbProvince');
        var store_kabupaten = Ext.create('Koltiva.store.FarmerTraining.CmbDistrict');
        var store_fasilitator = Ext.create('Koltiva.store.FarmerTraining.CmbFasilitator');
        var store_fasilitator_mitra = Ext.create('Koltiva.store.FarmerTraining.CmbFasilitatorMitra');
        var mc_sub_topic = Ext.create('Koltiva.store.FarmerTraining.CmbMcSubTopic');
        //Store yg dipakai =============================================================== (End)

        //Panel Main
        thisObj.ObjPanelMain = Ext.create('Ext.panel.Panel', {
            title: lang('Master Training Form'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            collapsible: true,
            style: 'margin-top:0px;padding-top:0px;',
            items: [{
                xtype: 'form',
                id: 'Koltiva.view.FarmerTraining.MainForm-Form',
                fileUpload: true,
                buttonAlign: 'right',
                cls: 'Sfr_PanelSubLayoutForm',
                items: [{
                    layout: 'column',
                    border: false,
                    padding: 10,
                    items: [{
                        columnWidth: 0.5,
                        layout: 'form',
                        style: 'margin-right:20px;',
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
                            }, {
                                xtype: 'hidden',
                                id: 'LabelTemp',
                                name: 'LabelTemp'
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
                                    change: function (cb, nv, ov) {
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
                            }, {
                                id: 'Provinsi',
                                name: 'Provinsi',
                                xtype: 'combo',
                                fieldLabel: lang('Provinsi'),
                                store: store_provinsi,
                                displayField: 'label',
                                valueField: 'id',
                                readOnly: false,
                                queryMode: 'local',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        store_kabupaten.load({
                                            params: {
                                                prov: nv
                                            }
                                        });
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
                                valueField: 'id',
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
                            }
                        ]
                    }, {
                        columnWidth: 0.49,
                        layout: 'form',
                        style: 'margin-left:20px;',
                        items: [{
                            xtype: 'combo',
                            store: store_fasilitator,
                            displayField: 'label',
                            valueField: 'id',
                            fieldLabel: lang('SCCP Facilitator'),
                            queryMode: 'local',
                            id: 'fasilitator_scpp',
                            name: 'fasilitator_scpp',
                            allowBlank: true,
                        }, {
                            xtype: 'combo',
                            store: store_fasilitator_mitra,
                            displayField: 'label',
                            valueField: 'id',
                            fieldLabel: lang('Partner Fasilitator'),
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
                            }, {
                                name: 'TrainingDayStatus',
                                id: 'TrainingDayStatusFull',
                                boxLabel: lang('Full day'),
                                inputValue: 'full'
                            }]
                        }, {
                            xtype: 'textfield',
                            fieldLabel: lang('Training Days'),
                            id: 'days',
                            name: 'days'
                        },{
                            xtype: 'radiogroup',
                            fieldLabel: lang('Event Status'),
                            items: [{
                                name: 'TrainingStatus',
                                id: 'TrainingStatus1',
                                boxLabel: lang('Completed'),
                                inputValue: '1'
                            }, {
                                name: 'TrainingStatus',
                                id: 'TrainingStatus2',
                                boxLabel: lang('On Going'),
                                inputValue: '2'
                            }]
                        }]
                    }]
                }],
                buttons: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Save'),
                    cls: 'Sfr_BtnFormBlue',
                    overCls: 'Sfr_BtnFormBlue-Hover',
                    id: 'Koltiva.view.FarmerTraining.MainForm-Form-BtnSave',
                    hidden: (thisObj.viewVar.opsiDisplay == 'view'),
                    handler: function () {

                        var form = this.up('form').getForm();
                        var methode;
                        if (thisObj.viewVar.opsiDisplay == 'update')
                            methode = 'PUT';
                        else
                            methode = 'POST';

                        if (form.isValid()) {
                            form.submit({
                                url: m_crud,
                                method: methode,
                                waitMsg: 'Sending data...',
                                success: function (fp, o) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data saved'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success',
                                        fn: function (btn) {
                                            if (btn == 'ok') {
                                                Ext.getCmp('Koltiva.view.FarmerTraining.MainForm').destroy(); //destory current view
                                                var FormMain = [];

                                                if (Ext.getCmp('Koltiva.view.FarmerTraining.MainForm') == undefined) {
                                                    FormMain = Ext.create('Koltiva.view.FarmerTraining.MainForm', {
                                                        viewVar: {
                                                            opsiDisplay: 'update',
                                                            trainMasterID: o.result.FarmerTrainingID
                                                        }
                                                    });
                                                } else {
                                                    //destroy, create ulang
                                                    Ext.getCmp('Koltiva.view.FarmerTraining.MainForm').destroy();
                                                    FormMain = Ext.create('Koltiva.view.FarmerTraining.MainForm', {
                                                        viewVar: {
                                                            opsiDisplay: 'update',
                                                            trainMasterID: o.result.FarmerTrainingID
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    });
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
                }]
            }]
        });


        //Panel Detail
        thisObj.ObjPanelDetail = [];
        if (thisObj.viewVar.opsiDisplay == 'view' || thisObj.viewVar.opsiDisplay == 'update') {

            if (Ext.getCmp('Koltiva.view.FarmerTraining.ParticipantPanel') == undefined) {
                thisObj.ObjPanelDetail = Ext.create('Koltiva.view.FarmerTraining.ParticipantPanel', {
                    viewVar: thisObj.viewVar
                });
            } else {
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.FarmerTraining.ParticipantPanel').destroy();
                thisObj.ObjPanelDetail = Ext.create('Koltiva.view.FarmerTraining.ParticipantPanel', {
                    viewVar: thisObj.viewVar
                });
            }
        }

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border: false,
            layout: {
                type: 'hbox'
            },
            items: [{
                id: 'Koltiva.view.FarmerTraining.MainForm-labelInfoInsert',
                html: '<div id="header_title_farmer">' + lang('Farmer Training Data') + '</div>'
            }]
        }, {
            items: [{
                id: 'Koltiva.view.FarmerTraining.MainForm-LinkBackToList',
                html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.FarmerTraining.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Training List') + '</a></li></div>'
            }]
        }, {
            html: '<br />'
        }, {
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 1,
                items: [
                    thisObj.ObjPanelMain,
                    {
                        html: '<br>'
                    },
                    thisObj.ObjPanelDetail
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (End) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.FarmerTraining.MainForm').destroy(); //destory current view
        var GridMainGrower = [];
        if (Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid') == undefined) {
            GridMainGrower = Ext.create('Koltiva.view.FarmerTraining.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid').destroy();
            GridMainGrower = Ext.create('Koltiva.view.FarmerTraining.MainGrid');
        }
    },
    setFormValue: function (r) {
        // Ext.getCmp('cpg').setValue(r.CpgBatchID);
        Ext.getCmp('training').setValue(r.CPGtrainingsID);
        Ext.getCmp('idt').setValue(r.FarmerTrainingID);
        Ext.getCmp('location').setValue(r.TotLocation);
        // Ext.getCmp('fasilitator_scpp').setValue(r.StaffID);
        // Ext.getCmp('fasilitator_mitra').setValue(r.PrivateStaffID);
        Ext.getCmp('fasilitator_scpp').setValue(r.fasilitator_scpp);
        Ext.getCmp('fasilitator_mitra').setValue(r.fasilitator_mitra);
        Ext.getCmp('TrainingStart').setValue(r.TrainingStart);
        Ext.getCmp('TrainingEnd').setValue(r.TrainingEnd);
        Ext.getCmp('days').setValue(r.TrainingDays);
        Ext.getCmp('Provinsi').setValue(r.Province);
        Ext.getCmp('Kabupaten').setValue(r.District);

        if (r.TrainingStatus == '1')
            Ext.getCmp('TrainingStatus1').setValue(true);
        if (r.TrainingStatus == '2')
            Ext.getCmp('TrainingStatus2').setValue(true);

        Ext.getCmp('Provinsi').setReadOnly(false);
        Ext.getCmp('Kabupaten').setReadOnly(false);

        Ext.getCmp('LabelTemp').setValue(r.label);

        if (r.TrainingDayStatus == 'half')
            Ext.getCmp('TrainingDayStatusHalf').setValue(true);
        if (r.TrainingDayStatus == 'full')
            Ext.getCmp('TrainingDayStatusFull').setValue(true);

        Ext.getCmp('CpgTrainingsIDSubTopic').getStore().load({
            callback: function (records, options, success) {
                if (r.subtopics != null) {
                    var setSubtopic = r.subtopics.split(',');
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue(setSubtopic);
                } else {
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                }
            }
        });
        // Ext.getCmp('DayNumber').setMaxValue(Ext.getCmp('days').getValue());

        // Ext.getCmp('parcheklistday_farmertrainingid').setValue(Ext.getCmp('idt').getValue());
        // Ext.getCmp('parcheklistday_training_name').setValue(Ext.getCmp('LabelTemp').getValue());
        // Ext.getCmp('parcheklistday_startdate').setValue(Ext.Date.format(new Date(Ext.getCmp('TrainingStart').getValue()), 'Y-m-d'));
        // Ext.getCmp('parcheklistday_enddate').setValue(Ext.Date.format(new Date(Ext.getCmp('TrainingEnd').getValue()), 'Y-m-d'));
        // Ext.getCmp('parcheklistday_daycount').setValue(Ext.getCmp('days').getValue());
    },
    isNumber: function (n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
});