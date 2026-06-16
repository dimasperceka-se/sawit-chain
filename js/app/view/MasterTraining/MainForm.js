Ext.define('Koltiva.view.MasterTraining.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.MasterTraining.MainForm',
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
        var store_service_provider = Ext.create('Koltiva.store.MasterTraining.CmbServiceProvider');
        var store_training = Ext.create('Koltiva.store.MasterTraining.CmbTraining');
        var store_provinsi = Ext.create('Koltiva.store.MasterTraining.CmbProvince');
        var store_District = Ext.create('Koltiva.store.MasterTraining.CmbDistrict');
        var store_fasilitator = Ext.create('Koltiva.store.MasterTraining.CmbFasilitator');
        var mc_sub_topic = Ext.create('Koltiva.store.MasterTraining.CmbSubTopic');
        //Store yg dipakai =============================================================== (End)

        //Panel Main
        thisObj.ObjPanelMain = Ext.create('Ext.panel.Panel', {
            title: lang('Master Training Form'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            collapsible: true,
            style:'margin-top:0px;padding-top:0px;',
            items: [{
                xtype: 'form',
                id: 'Koltiva.view.MasterTraining.MainForm-Form',
                fileUpload: true,
                buttonAlign: 'right',
                cls: 'Sfr_PanelSubLayoutForm',
                items:[{
                    layout: 'column',
                    border: false,
                    padding: 10,
                    items: [{
                        columnWidth: 0.5,
                        layout: 'form',
                        style:'margin-right:20px;',
                        items: [{
                                xtype: 'textfield',
                                id: 'id',
                                name: 'id',
                                inputType: 'hidden'
                            }, {
                                xtype: 'combo',
                                store: store_training,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Topic'),
                                queryMode: 'local',
                                allowBlank: false,
                                id: 'training',
                                name: 'training',
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
                                valueField: 'label',
                                queryMode: 'local',
                                readOnly: false,
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        ProvinceID = nv
                                        store_District.load({
                                            params: {
                                                id: nv
                                            }
                                        });
                                    }
                                }
                            }, {
                                id: 'DistrictID',
                                name: 'DistrictID',
                                xtype: 'combo',
                                fieldLabel: lang('District'),
                                store: store_District,
                                displayField: 'district',
                                valueField: 'id',
                                queryMode: 'local'
                            }, {
                                xtype: 'radiogroup',
                                fieldLabel: lang('Training Purpose'),
                                allowBlank: false,
                                msgTarget: 'side',
                                items: [{
                                    name: 'TrainingPurpose',
                                    id: 'TrainingPurposeCore',
                                    boxLabel: lang('Core'),
                                    inputValue: 'Core'
                                }, {
                                    name: 'TrainingPurpose',
                                    id: 'TrainingPurposeGeneral',
                                    boxLabel: lang('General'),
                                    inputValue: 'General'
                                }]
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Location'),
                                id: 'location',
                                name: 'location'
                            },
                            {
                                xtype: 'combo',
                                store: store_service_provider,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Service Provider'),
                                queryMode: 'local',
                                id: 'ServiceProvID',
                                name: 'ServiceProvID'
                        }]
                    },{
                        columnWidth: 0.49,
                        layout: 'form',
                        style:'margin-left:20px;',
                        items: [{
                                xtype: 'combo',
                                store: store_fasilitator,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Fasilitator 1'),
                                queryMode: 'local',
                                allowBlank: false,
                                id: 'fasilitator_scpp',
                                name: 'fasilitator_scpp'
                            },
                            {
                                xtype: 'combo',
                                store: store_fasilitator,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Fasilitator 2'),
                                queryMode: 'local',
                                id: 'fasilitator_mitra',
                                name: 'fasilitator_mitra'
                            },
                            {
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
                        }]
                    }]
                }],
                buttons: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Save'),
                    cls: 'Sfr_BtnFormBlue',
                    overCls: 'Sfr_BtnFormBlue-Hover',
                    id: 'Koltiva.view.MasterTraining.MainForm-Form-BtnSave',
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
                                                Ext.getCmp('Koltiva.view.MasterTraining.MainForm').destroy(); //destory current view
                                                var FormMain = [];

                                                if(Ext.getCmp('Koltiva.view.MasterTraining.MainForm') == undefined){
                                                    FormMain = Ext.create('Koltiva.view.MasterTraining.MainForm', {
                                                        viewVar: {
                                                            opsiDisplay: 'update',
                                                            trainMasterID: o.result.TrainMasterID
                                                        }
                                                    });
                                                }else{
                                                    //destroy, create ulang
                                                    Ext.getCmp('Koltiva.view.MasterTraining.MainForm').destroy();
                                                    FormMain = Ext.create('Koltiva.view.MasterTraining.MainForm', {
                                                        viewVar: {
                                                            opsiDisplay: 'update',
                                                            trainMasterID: o.result.TrainMasterID
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
                                    }
                                    catch(err) {
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

            if (Ext.getCmp('Koltiva.view.MasterTraining.ParticipantPanel') == undefined) {
                thisObj.ObjPanelDetail = Ext.create('Koltiva.view.MasterTraining.ParticipantPanel', {
                    viewVar: thisObj.viewVar
                });
            } else {
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.MasterTraining.ParticipantPanel').destroy();
                thisObj.ObjPanelDetail = Ext.create('Koltiva.view.MasterTraining.ParticipantPanel', {
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
                id: 'Koltiva.view.MasterTraining.MainForm-labelInfoInsert',
                html: '<div id="header_title_farmer">' + lang('Training Master Data') + '</div>'
            }]
        }, {
            items: [{
                id: 'Koltiva.view.MasterTraining.MainForm-LinkBackToList',
                html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.MasterTraining.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Training List') + '</a></li></div>'
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
                        html:'<br>'
                    },
                    thisObj.ObjPanelDetail
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (End) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.MasterTraining.MainForm').destroy(); //destory current view
        var GridMainGrower = [];
        if (Ext.getCmp('Koltiva.view.MasterTraining.MainGrid') == undefined) {
            GridMainGrower = Ext.create('Koltiva.view.MasterTraining.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.MasterTraining.MainGrid').destroy();
            GridMainGrower = Ext.create('Koltiva.view.MasterTraining.MainGrid');
        }
    },
    setFormValue: function(r) {
        Ext.getCmp('training').setValue(r.CPGtrainingsID);
        Ext.getCmp('location').setValue(r.TotLocation);
        Ext.getCmp('fasilitator_scpp').setValue(r.FacProgramPersonID);
        Ext.getCmp('fasilitator_mitra').setValue(r.FacPrivatePersonID);
        Ext.getCmp('TrainingStart').setValue(r.TrainingStart);
        Ext.getCmp('TrainingEnd').setValue(r.TrainingEnd);
        Ext.getCmp('days').setValue(r.TrainingDays);
        Ext.getCmp('Provinsi').setValue(r.Province);
        Ext.getCmp('DistrictID').setValue(r.DistrictID);
        Ext.getCmp('ServiceProvID').setValue(r.ServiceProvID);

        Ext.getCmp('Provinsi').setReadOnly(false);
        Ext.getCmp('DistrictID').setReadOnly(false);

        if (r.TrainingDayStatus == 'half')
            Ext.getCmp('TrainingDayStatusHalf').setValue(true);
        if (r.TrainingDayStatus == 'full')
            Ext.getCmp('TrainingDayStatusFull').setValue(true);

        if (r.TrainingPurpose == 'Core')
            Ext.getCmp('TrainingPurposeCore').setValue(true);
        if (r.TrainingPurpose == 'General')
            Ext.getCmp('TrainingPurposeGeneral').setValue(true);

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
    },
    isNumber: function(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
});