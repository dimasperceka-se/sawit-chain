/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Nov 28 2018
 *  File : WinFormFarmerTraining.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
    - CallerStore
*/
function setFormValue(r) {
    Ext.getCmp('cpg').setValue(r.CpgBatchID);
    Ext.getCmp('training').setValue(r.CPGtrainingsID);
    Ext.getCmp('idt').setValue(r.FarmerTrainingID);
    Ext.getCmp('location').setValue(r.TotLocation);
    // Ext.getCmp('fasilitator_scpp').setValue(r.StaffID);
    // Ext.getCmp('fasilitator_mitra').setValue(r.PrivateStaffID);
    Ext.getCmp('fasilitator_scpp').setValue(r.FacProgramPersonID);
    Ext.getCmp('fasilitator_mitra').setValue(r.FacPrivatePersonID);
    Ext.getCmp('TrainingStart').setValue(r.TrainingStart);
    Ext.getCmp('TrainingEnd').setValue(r.TrainingEnd);
    Ext.getCmp('days').setValue(r.TrainingDays);
    Ext.getCmp('Provinsi').setValue(r.TrainingProvince);
    Ext.getCmp('Kabupaten').setValue(r.TrainingDistrict);

    Ext.getCmp('Provinsi').setReadOnly(false);
    Ext.getCmp('Kabupaten').setReadOnly(false);

    if (r.TrainingDayStatus == 'half')
        Ext.getCmp('TrainingDayStatusHalf').setValue(true);
    if (r.TrainingDayStatus == 'full')
        Ext.getCmp('TrainingDayStatusFull').setValue(true);

    Ext.getCmp('CpgTrainingsIDSubTopic').getStore().load({
        callback: function (records, options, success) {
            if (success == true) {
                if (r.subtopics != null) {
                    var setSubtopic = r.subtopics.split(',');
                //console.log(setSubtopic);
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue(setSubtopic);
                } else {
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                }
            }
        }
    });

    Ext.getCmp('DayNumber').setValue(r.TrainingDays);


}

Ext.define('Koltiva.view.IMS.WinFormFarmerTraining' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormFarmerTraining',
    title: lang('IMS - Farmer Training Event'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '94%',
    height: '94%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        var StatusHeightParticipant = 0;

        //Context Menu
        thisObj.ContextMenuGtraining = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    hidden: m_act_update,
                    itemId: 'TrainFarmerBtnUpdate',
//                    cls: 'Sfr_BtnConMenuWhite ' + m_act_save,
                    handler: function () {
                        var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection();
                        var storeGridParticipant = Ext.data.StoreManager.lookup('store_participant');
                        var lastRow = storeGridParticipant.getAt(storeGridParticipant.getCount() - 1);
                        
                        if (StatusHeightParticipant != 0) {
                            var heightGridNow = Ext.getCmp('gtraining').getHeight();
                            heightGridNow = heightGridNow - 55;
                            Ext.getCmp('gtraining').setHeight(heightGridNow);
                            StatusHeightParticipant = 0;
                        }

                        if (sm[0].data.participant_id == lastRow.data.participant_id) {
                            var heightGridNow = Ext.getCmp('gtraining').getHeight();
                            heightGridNow = heightGridNow + 55;
                            Ext.getCmp('gtraining').setHeight(heightGridNow);
                            StatusHeightParticipant = StatusHeightParticipant + 1;
                        }
                        
                        RowEditing.cancelEdit();
                        RowEditing.startEdit(sm[0].index, 0);
                        // console.log(sm[0].get('farmer_id'))
                        store_family.load({
                            params: {
                                farmerid: sm[0].get('MemberID')
                            }
                        });
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Attendance Check List Per Participant'),
                    itemId: 'TrainFarmerBtnAttListParticipant',
                    scope: this,
                    handler: function () {
                        var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection();
                        // console.log(sm[0]);
                        if (!sm[0]) {
                            Ext.MessageBox.alert(lang('Warning'), lang('Silahkan pilih peserta'));
                        } else {
                            $.ajax({
                                url: m_participant_detail,
                                data: {
                                    FarmerTrainingsFarmerID: sm[0].data.participant_id
                                }
                            }).done(function (data) {
                                if (Ext.getCmp('Koltiva.view.TrainingFarmer.WinParCheckList'))
                                    Ext.getCmp('Koltiva.view.TrainingFarmer.WinParCheckList').destroy();
                                var WinParCheckList = Ext.create('Koltiva.view.TrainingFarmer.WinParCheckList', {
                                    viewVar: {
                                        sm: sm,
                                        data: data,
                                        idt: Ext.getCmp('idt').getValue(),
                                        callStore: store_participant
                                    }
                                });
                                if (!WinParCheckList.isVisible()) {
                                    WinParCheckList.center();
                                    WinParCheckList.show();
                                } else {
                                    WinParCheckList.close();
                                }
                            });
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    hidden: m_act_delete,
                    itemId: 'TrainFarmerBtnDelete',
//                    cls: 'Sfr_BtnConMenuWhite ' + m_act_save,
                    handler: function () {
                        var sma = Ext.getCmp('gtraining').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_crud + '_participant',
                                    method: 'DELETE',
                                    params: {id: sma.get('participant_id')},
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store_participant.load({
                                                    params: {
                                                        training: Ext.getCmp('id').getValue()
                                                    }});
                                                break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                        }
                                    },
                                    failure: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                    }
                                });
                            }
                        });
                    }
                }]
        });

        
        //store
        var store_cpg_batch = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_store_cpg_batch,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        var store_training = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 50,
            proxy: {
                type: 'ajax',
                url: m_store_training,
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'totalCount'
                }
            }
        });
        var store_farmer = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_store_farmer,
                // extraParams: {prov: m_param},
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'totalCount'
                }
            }
        });
        var mc_sub_topic = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/cpg/training_subtopic',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function (store, options) {
                    store.proxy.extraParams.CpgTrainingsID = Ext.getCmp('training').getValue();
                }
            }
        });
        var store_family = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_store_family,
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'totalCount'
                }
            }
        });

        var store_provinsi = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_store_provinsi,
                extraParams: {prov: m_param},
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        var store_kabupaten = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_store_kabupaten,
                extraParams: {prov: m_param},
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        var store_fasilitator = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_store_fasilitator,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        var store_fasilitator_mitra = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_store_fasilitator_mitra,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        var store_participant = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            storeId: 'store_participant',
            fields: ['participant_id', 'farmer_id','MemberID', 'farmer', 'participant', 'Subtitute', 'if_no', 'FamilyID', 'wstart', 'wend', 'bstart', 'bend', 'FarmerTrainingID','Percentage'],
            //pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: m_store_participant + 's',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });
        var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'RowEditing',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });
        var store_ya_tidak = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [
                {"id": "1", "label": "Ya"},
                {"id": "2", "label": "Tidak"},
            ]
        });

        //Form Validator ========================================== (Begin)
        Ext.define('Ext.lib.Validators', {
            singleton: true,
            ValidateSingleCpg: function(){
                return function(){
                    var ValSingleCpg = Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining-Form-SingleCpg').getValue();
                    var ValEventType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormFarmerTraining-Form-EventType]')[0].getGroupValue();

                    if(ValEventType == 'Single CPG'){
                        if(ValSingleCpg == null){
                            return lang('This field is required');
                        }else{
                            return true;
                        }
                    }else{
                        return true;
                    }
                }
            },
            ValidateSingleParType: function(){
                return function(){
                    var ValParType = Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining-Form-SingleParticipantType').getValue();
                    var ValEventType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormFarmerTraining-Form-EventType]')[0].getGroupValue();

                    if(ValEventType == 'Single CPG'){
                        if(ValParType == null){
                            return lang('This field is required');
                        }else{
                            return true;
                        }
                    }else{
                        return true;
                    }
                }
            },
            ValidateMultipleParType: function(){
                return function(){
                    var ValParType = Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining-Form-MultipleParticipantType').getValue();
                    var ValEventType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormFarmerTraining-Form-EventType]')[0].getGroupValue();

                    if(ValEventType == 'Multiple CPG'){
                        if(ValParType == null){
                            return lang('This field is required');
                        }else{
                            return true;
                        }
                    }else{
                        return true;
                    }
                }
            }
        });
        //Form Validator ========================================== (End)


        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormFarmerTraining-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout: 'form',
                    cls: 'Sfr_PanelLayoutFormContainer',
                    items: [{
                            layout: 'column',
                            border: false,
                            items: [{
                                    columnWidth: 0.5,
                                    layout: 'form',
                                    style: 'padding:10px 0px 10px 5px;',
                                    items: [{
                                            xtype: 'panel',
                                            title: lang('Data Training'),
                                            frame: false,
                                            id: 'Koltiva.view.IMS.WinFormFarmerTraining-Form-DataPerusahaan',
                                            style: 'margin-top:22px;',
                                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                            items: [{
                                                    layout: 'column',
                                                    border: false,
                                                    items: [{
                                                            columnWidth: 1,
                                                            layout: 'form',
                                                            style: 'padding:10px 0px 0px 0px;',
                                                            defaults: {
                                                                labelAlign: 'top'
                                                            },
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
                                                                    id: 'DayNumber',
                                                                    name: 'DayNumber',
                                                                    inputType: 'hidden'
                                                                }, {
                                                                    xtype: 'combo',
                                                                    store: store_cpg_batch,
                                                                    displayField: 'label',
                                                                    valueField: 'id',
                                                                    fieldLabel: lang('CPG/FFS Batch'),
                                                                    queryMode: 'local',
                                                                    id: 'cpg',
                                                                    name: 'cpg',
                                                                    allowBlank: false,
                                                                }, {
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
                                                                    allowBlank: false,
                                                                    triggerOnClick: false,
                                                                    filterPickList: true
                                                                }]
                                                        }]
                                                }]
                                        }, {
                                            xtype: 'panel',
                                            title: lang('Region'),
                                            frame: false,
                                            id: 'Koltiva.view.IMS.WinFormFarmerTraining-Form-Region',
                                            style: 'margin-top:22px;',
                                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                            items: [{
                                                    layout: 'column',
                                                    border: false,
                                                    items: [{
                                                            columnWidth: 1,
                                                            layout: 'form',
                                                            style: 'padding:10px 0px 0px 0px;',
                                                            defaults: {
                                                                labelAlign: 'top'
                                                            },
                                                            items: [{
                                                                    id: 'Provinsi',
                                                                    name: 'Provinsi',
                                                                    xtype: 'combo',
                                                                    fieldLabel: lang('Provinsi'),
                                                                    store: store_provinsi,
                                                                    displayField: 'label',
                                                                    valueField: 'id',
                                                                    readOnly: false,
                                                                    allowBlank: false,
                                                                    queryMode: 'local',
                                                                    listeners: {
                                                                        change: function (cb, nv, ov) {
                                                                            store_kabupaten.load({
                                                                                params: {
                                                                                    prov: Ext.getCmp('Provinsi').getValue()
                                                                                }});
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
                                                                    allowBlank: false,
                                                                    listeners: {
                                                                        change: function (cb, nv, ov) {
                                                                            // store_farmer.load({
                                                                            //     params: {
                                                                            //         kab: Ext.getCmp('Kabupaten').getValue()
                                                                            //     }});

//                                                                                                    store_farmer.getProxy().extraParams = {
//                                                                                                        kab: nv
//                                                                                                    };
                                                                        }
                                                                    }
                                                                }, {
                                                                    xtype: 'textfield',
                                                                    fieldLabel: lang('ToT Location'),
                                                                    id: 'location',
                                                                    name: 'location'
                                                                }]
                                                        }]
                                                }]
                                        }]
                                }, {
                                    columnWidth: 0.5,
                                    layout: 'form',
                                    style: 'padding:10px 5px 10px 20px;',
                                    items: [{
                                            xtype: 'panel',
                                            title: lang('Facilitator'),
                                            frame: false,
                                            id: 'Koltiva.view.IMS.WinFormFarmerTraining-Form-Lokasi',
                                            style: 'margin-top:22px;',
                                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                            items: [{
                                                    layout: 'column',
                                                    border: false,
                                                    items: [{
                                                            columnWidth: 1,
                                                            layout: 'form',
                                                            style: 'padding:10px 0px 0px 0px;',
                                                            defaults: {
                                                                labelAlign: 'top'
                                                            },
                                                            items: [{
                                                                    xtype: 'combo',
                                                                    store: store_fasilitator,
                                                                    displayField: 'label',
                                                                    valueField: 'id',
                                                                    fieldLabel: lang('Fasilitator SCPP'),
                                                                    queryMode: 'local',
                                                                    id: 'fasilitator_scpp',
                                                                    name: 'fasilitator_scpp',
                                                                    allowBlank: true,
                                                                }, {
                                                                    xtype: 'combo',
                                                                    store: store_fasilitator_mitra,
                                                                    displayField: 'label',
                                                                    valueField: 'id',
                                                                    fieldLabel: lang('Fasilitator Mitra'),
                                                                    allowBlank: true,
                                                                    queryMode: 'local',
                                                                    id: 'fasilitator_mitra',
                                                                    name: 'fasilitator_mitra'
                                                                }]
                                                        }]
                                                }]
                                        }, {
                                            xtype: 'panel',
                                            title: lang('Training Time'),
                                            frame: false,
                                            id: 'Koltiva.view.IMS.WinFormFarmerTraining-Form-Time',
                                            style: 'margin-top:22px;',
                                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                            items: [{
                                                    layout: 'column',
                                                    border: false,
                                                    items: [{
                                                            columnWidth: 1,
                                                            layout: 'form',
                                                            style: 'padding:10px 0px 0px 0px;',
                                                            defaults: {
                                                                labelAlign: 'top'
                                                            },
                                                            items: [{
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
                                                                    hidden: true,
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
                                                                    fieldLabel: lang('Number of Meeting'),
                                                                    id: 'days',
                                                                    name: 'days'
                                                                }, {
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
                                                }]
                                        }]
                                }, {
                                    columnWidth: 1,
                                    layout: 'form',
                                    style: 'padding:10px 5px 10px 10px;',
                                    items: [{
                                            xtype: 'gridpanel',
                                            id: 'gtraining',
                                            title: lang('Training Participants'),
                                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                            store: store_participant,
                                            cls: 'Sfr_GridNew',
                                            width: '100%',
                                            minHeight:300,
                                            loadMask: true,
                                            selType: 'rowmodel',
                                            dockedItems: [{
                                                    xtype: 'toolbar',
                                                    items: [{
                                                            icon: varjs.config.base_url + 'images/icons/new/add.png',
                                                            cls: 'Sfr_BtnGridGreen',
                                                            hidden: (thisObj.viewVar.OpsiDisplay === 'update') ? false : true,
                                                            overCls: 'Sfr_BtnGridGreen-Hover',
                                                            text: lang('Add'),
                                                            id: 'TrainFarmerBtnAdd',
                                                            scope: this,
                                                            handler: function () {
                                                                if (Ext.getCmp('Koltiva.view.IMS.WinFormParticipant'))
                                                                    Ext.getCmp('Koltiva.view.IMS.WinFormParticipant').destroy();
                                                                var WinFormParticipant = Ext.create('Koltiva.view.IMS.WinFormParticipant', {
                                                                    viewVar: {
                                                                        idt: Ext.getCmp('idt').getValue(),
                                                                        callStore: store_participant
                                                                    }
                                                                });
                                                                if (!WinFormParticipant.isVisible()) {
                                                                    WinFormParticipant.center();
                                                                    WinFormParticipant.show();
                                                                } else {
                                                                    WinFormParticipant.close();
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'splitbutton',
                                                            icon: varjs.config.base_url + 'images/icons/new/printout_black.png',
                                                            text: lang('Daftar Hadir'),
                                                            menu: {
                                                                items: [{
                                                                        text: lang('Form Kosong'),
                                                                        handler: function () {
                                                                            preview_cetak_surat(m_cetak + Ext.getCmp('idt').getValue());
                                                                        }
                                                                    }, {
                                                                        text: lang('Form Hasil'),
                                                                        handler: function () {
                                                                            //preview_cetak_surat(m_cetak + Ext.getCmp('idt').getValue(),'Form Hasil');

                                                                            if (Ext.getCmp('Koltiva.view.TrainingFarmer.WinBeforeCetakAttendanceList'))
                                                                                Ext.getCmp('Koltiva.view.TrainingFarmer.WinBeforeCetakAttendanceList').destroy();
                                                                            var WinBeforeCetakAttendanceList = Ext.create('Koltiva.view.TrainingFarmer.WinBeforeCetakAttendanceList', {
                                                                                viewVar: {
                                                                                    idt: Ext.getCmp('idt').getValue(),
                                                                                    DayNumber: Ext.getCmp('DayNumber').getValue(),
                                                                                    callStore: store_participant
                                                                                }
                                                                            });
                                                                            if (!WinBeforeCetakAttendanceList.isVisible()) {
                                                                                WinBeforeCetakAttendanceList.center();
                                                                                WinBeforeCetakAttendanceList.show();
                                                                            } else {
                                                                                WinBeforeCetakAttendanceList.close();
                                                                            }
                                                                        }
                                                                    }]
                                                            }
                                                        }, {
                                                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                                                            text: lang('Attendance Check List Per Day'),
                                                            cls:'Sfr_BtnGridPaleBlue',
                                                            overCls:'Sfr_BtnGridPaleBlue-Hover',
                                                            id: 'TrainFarmerBtnAttCheckList',
                                                            scope: this,
                                                            handler: function () {
                                                                if (Ext.getCmp('Koltiva.view.TrainingFarmer.WinSelectDay'))
                                                                    Ext.getCmp('Koltiva.view.TrainingFarmer.WinSelectDay').destroy();
                                                                var WinSelectDay = Ext.create('Koltiva.view.TrainingFarmer.WinSelectDay', {
                                                                    viewVar: {
                                                                        TrainingStart: Ext.getCmp('TrainingStart').getValue(),
                                                                        TrainingEnd: Ext.getCmp('TrainingEnd').getValue(),
                                                                        TrainingDays: Ext.getCmp('days').getValue(),
                                                                        TrainingDayStatusHalf: Ext.getCmp('TrainingDayStatusHalf').getValue(),
                                                                        DayNumber: Ext.getCmp('DayNumber').getValue(),
                                                                        idt: Ext.getCmp('idt').getValue(),
                                                                        TrainingID: thisObj.viewVar.trainindID,
                                                                        callStore: store_participant
                                                                    }
                                                                });
                                                                if (!WinSelectDay.isVisible()) {
                                                                    WinSelectDay.center();
                                                                    WinSelectDay.show();
                                                                } else {
                                                                    WinSelectDay.close();
                                                                }
                                                            }
                                                        }, {
                                                            icon: varjs.config.base_url + 'images/icons/new/folder_table.png',
                                                            text: lang('Attachment Training Files'),
                                                            scope: this,
                                                            handler: function () {
                                                                //console.log(Ext.getCmp('idt').getValue());
                                                                var GridTrainAttachmentFiles = Ext.create('Koltiva.view.Train.GridTrainAttachmentFiles', {
                                                                    viewVar: {
                                                                        TrainID: Ext.getCmp('idt').getValue(),
                                                                        TrainType: 'farmer'
                                                                    }
                                                                });
                                                                if (!GridTrainAttachmentFiles.isVisible()) {
                                                                    GridTrainAttachmentFiles.center();
                                                                    GridTrainAttachmentFiles.show();
                                                                } else {
                                                                    GridTrainAttachmentFiles.close();
                                                                }
                                                            }
                                                        }, {
                                                            icon: varjs.config.base_url + 'images/icons/new/printout.png',
                                                            text: lang('GAP'),
                                                            cls:'Sfr_BtnGridPaleBlue',
                                                            overCls:'Sfr_BtnGridPaleBlue-Hover',
                                                            scope: this,
                                                            cls: 'hide-icon',
                                                            handler: function () {
                                                                if (Ext.getCmp('Koltiva.view.TrainingFarmer.WinBeforeCetak'))
                                                                    Ext.getCmp('Koltiva.view.TrainingFarmer.WinBeforeCetak').destroy();
                                                                var WinBeforeCetak = Ext.create('Koltiva.view.TrainingFarmer.WinBeforeCetak', {
                                                                    viewVar: {
                                                                        jenis: 'P1',
                                                                        idt: Ext.getCmp('idt').getValue(),
                                                                        callStore: store_participant
                                                                    }
                                                                });
                                                                if (!WinBeforeCetak.isVisible()) {
                                                                    WinBeforeCetak.center();
                                                                    WinBeforeCetak.show();
                                                                } else {
                                                                    WinBeforeCetak.close();
                                                                }
                                                            }
                                                        }, {
                                                            icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                                            text: lang('GFP'),
                                                            cls: 'hide-icon',
                                                            scope: this,
                                                            handler: function () {
                                                                if (Ext.getCmp('Koltiva.view.TrainingFarmer.WinBeforeCetak'))
                                                                    Ext.getCmp('Koltiva.view.TrainingFarmer.WinBeforeCetak').destroy();
                                                                var WinBeforeCetak = Ext.create('Koltiva.view.TrainingFarmer.WinBeforeCetak', {
                                                                    viewVar: {
                                                                        jenis: 'F1',
                                                                        idt: Ext.getCmp('idt').getValue(),
                                                                        callStore: store_participant
                                                                    }
                                                                });
                                                                if (!WinBeforeCetak.isVisible()) {
                                                                    WinBeforeCetak.center();
                                                                    WinBeforeCetak.show();
                                                                } else {
                                                                    WinBeforeCetak.close();
                                                                }
                                                            }
                                                        }, {
                                                            icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                                            id: 't_n1',
                                                            text: lang('GNP'),
                                                            cls: 'hide-icon',
                                                            scope: this,
                                                            handler: function () {
                                                                if (Ext.getCmp('Koltiva.view.TrainingFarmer.WinBeforeCetak'))
                                                                    Ext.getCmp('Koltiva.view.TrainingFarmer.WinBeforeCetak').destroy();
                                                                var WinBeforeCetak = Ext.create('Koltiva.view.TrainingFarmer.WinBeforeCetak', {
                                                                    viewVar: {
                                                                        jenis: 'N1',
                                                                        idt: Ext.getCmp('idt').getValue(),
                                                                        callStore: store_participant
                                                                    }
                                                                });
                                                                if (!WinBeforeCetak.isVisible()) {
                                                                    WinBeforeCetak.center();
                                                                    WinBeforeCetak.show();
                                                                } else {
                                                                    WinBeforeCetak.close();
                                                                }
                                                            }
                                                        }, {
                                                            icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                                            text: lang('PPI'),
                                                            cls: 'hide-icon',
                                                            scope: this,
                                                            handler: function () {
                                                                if (Ext.getCmp('Koltiva.view.TrainingFarmer.WinBeforeCetak'))
                                                                    Ext.getCmp('Koltiva.view.TrainingFarmer.WinBeforeCetak').destroy();
                                                                var WinBeforeCetak = Ext.create('Koltiva.view.TrainingFarmer.WinBeforeCetak', {
                                                                    viewVar: {
                                                                        jenis: 'PP1',
                                                                        idt: Ext.getCmp('idt').getValue(),
                                                                        callStore: store_participant
                                                                    }
                                                                });
                                                                if (!WinBeforeCetak.isVisible()) {
                                                                    WinBeforeCetak.center();
                                                                    WinBeforeCetak.show();
                                                                } else {
                                                                    WinBeforeCetak.close();
                                                                }
                                                            }
                                                        }]
                                                }],
                                            columns: [{
                                                    text: '',
                                                    xtype: 'actioncolumn',
                                                    hidden: (thisObj.viewVar.OpsiDisplay === 'update') ? false : true,
                                                    width: '4%',
                                                    items: [{
                                                            icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                            handler: function (grid, rowIndex, colIndex, item, e, record) {
                                                                thisObj.ContextMenuGtraining.showAt(e.getXY());
                                                            }
                                                        }]
                                                }, {
                                                    text: lang('ID'),
                                                    dataIndex: 'participant_id',
                                                    width: '5%',
                                                    hidden: true
                                                }, {
                                                    text: lang('MemberID'),
                                                    dataIndex: 'MemberID',
                                                    width: '5%',
                                                    hidden: true
                                                }, {
                                                    text: lang('ID'),
                                                    dataIndex: 'farmer_id',
                                                    width: '10%'
                                                }, {
                                                    text: lang('Registered Farmer'),
                                                    flex: 1.5,
                                                    dataIndex: 'farmer'
                                                }, {
                                                    text: lang('Participant'),
                                                    flex: 1,
                                                    dataIndex: 'participant',
                                                    editor: {
                                                        xtype: 'combo',
                                                        store: store_ya_tidak,
                                                        id: 'participant',
                                                        name: 'participant',
                                                        queryMode: 'local',
                                                        displayField: 'label',
                                                        valueField: 'id'
                                                    }
                                                }, {
                                                    text: lang('Pengganti'),
                                                    flex: 1,
                                                    dataIndex: 'if_no',
                                                    editor: {
                                                        xtype: 'combo',
                                                        displayField: 'label',
                                                        id: 'if_no',
                                                        name: 'if_no',
                                                        valueField: 'id',
                                                        queryMode: 'local',
                                                        store: store_family
                                                    }
                                                }, {
                                                    text: lang('Attendance (%)'),
                                                    width: '9%',
                                                    dataIndex: 'Percentage'
                                                },{
                                                    text: lang('W. Awal'),
                                                    width: '9%',
                                                    dataIndex: 'wstart',
                                                    editor: {
                                                        xtype: 'textfield'
                                                    }
                                                }, {
                                                    text: lang('W. Akhir'),
                                                    width: '9%',
                                                    dataIndex: 'wend',
                                                    editor: {
                                                        xtype: 'textfield'
                                                    }
                                                }, {
                                                    text: lang('B. Awal'),
                                                    width: '8%',
                                                    dataIndex: 'bstart',
                                                    editor: {
                                                        xtype: 'textfield'
                                                    }
                                                }, {
                                                    text: lang('B. Akhir'),
                                                    width: '8%',
                                                    dataIndex: 'bend',
                                                    editor: {
                                                        xtype: 'textfield'
                                                    }
                                                }],
                                            plugins: [RowEditing],
                                            listeners: {
                                                'itemdblclick': function (dv, record, item, index, e) {
                                                    if (!m_act_update) {
                                                        RowEditing.cancelEdit();
                                                        return false;
                                                    } else {
                                                        var sm = record;
                                                        store_family.load({
                                                            params: {
                                                                farmerid: sm.get('MemberID')
                                                            }});
                                                    }
                                                },
                                                'canceledit': function (editor, e, eOpts) {
                                                    store_participant.load({
                                                        params: {
                                                            training: Ext.getCmp('id').getValue()
                                                        }});
                                                },
                                                'edit': function (editor, e) {
                                                    if (e.record.data.participant_id.trim() == '') {
                                                        Ext.Ajax.request({
                                                            waitMsg: 'Please wait...',
                                                            url: m_crud + '_participant',
                                                            method: 'POST',
                                                            params: {
                                                                training: Ext.getCmp('id').getValue(),
                                                                farmer: e.record.data.farmer,
                                                                participant: e.record.data.participant,
                                                                if_no: e.record.data.if_no,
                                                                wstart: e.record.data.wstart,
                                                                wend: e.record.data.wend,
                                                                bstart: e.record.data.bstart,
                                                                bend: e.record.data.bend,
                                                            },
                                                            success: function (response, opts) {
                                                                var obj = Ext.decode(response.responseText);
                                                                switch (obj.success) {
                                                                    case true:
                                                                        Ext.MessageBox.alert('Success', obj.message);
                                                                        store_participant.load({
                                                                            params: {
                                                                                training: Ext.getCmp('id').getValue()
                                                                            }});
                                                                        break;
                                                                    default:
                                                                        Ext.MessageBox.alert('Warning', obj.message);
                                                                        break;
                                                                }
                                                            },
                                                            failure: function (response, opts) {
                                                                var obj = Ext.decode(response.responseText);
                                                                Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                            }
                                                        });
                                                    } else {
                                                        Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function (btn) {
                                                            if (btn == 'yes') {
                                                                Ext.Ajax.request({
                                                                    waitMsg: 'Please wait...',
                                                                    url: m_crud + '_participant',
                                                                    method: 'PUT',
                                                                    params: {
                                                                        id: e.record.data.participant_id,
                                                                        staf: e.record.data.staf,
                                                                        farmer: e.record.data.farmer,
                                                                        farmer_id: e.record.data.farmer_id,
                                                                        participant: e.record.data.participant,
                                                                        Subtitute: e.record.data.Subtitute,
                                                                        if_no: e.record.data.if_no,
                                                                        FamilyID: e.record.data.FamilyID,
                                                                        wstart: e.record.data.wstart,
                                                                        wend: e.record.data.wend,
                                                                        bstart: e.record.data.bstart,
                                                                        bend: e.record.data.bend,
                                                                    },
                                                                    success: function (response, opts) {
                                                                        var obj = Ext.decode(response.responseText);
                                                                        switch (obj.success) {
                                                                            case true:
                                                                                Ext.MessageBox.alert('Success', obj.message);
                                                                                store_participant.load({
                                                                                    params: {
                                                                                        training: Ext.getCmp('id').getValue()
                                                                                    }});
                                                                                break;
                                                                            default:
                                                                                Ext.MessageBox.alert('Warning', obj.message);
                                                                                break;
                                                                        }
                                                                    },
                                                                    failure: function (response, opts) {
                                                                        var obj = Ext.decode(response.responseText);
                                                                        Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                                    }
                                                                });
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        }]
                                }]
                        }]
                }]
            }]
        }];

        thisObj.buttons = [{
                text: lang('Save Training'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                id:'saveButton',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    var form = Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining-Form').getForm();
                    var methode;
                    if (Ext.getCmp('id').getValue() != '')
                        methode = 'PUT';
                    else
                        methode = 'POST';
                    if (form.isValid()) {
                        form.submit({
                            url: m_crud,
                            method: methode,
                            waitMsg: 'Sending data...',
                            params: {
                                IMSID: thisObj.viewVar.IMSID
                            },
                            success: function (fp, o) {
                                Ext.MessageBox.alert('Success', lang('Data saved.'));
                                // Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining').destroy(); //destory current view
                                // var MainForm = [];
                                // if (Ext.getCmp('Koltiva.view.TrainingFarmer.MainForm') == undefined) {
                                //     MainForm = Ext.create('Koltiva.view.TrainingFarmer.MainForm', {
                                //         viewVar: {
                                //             OpsiDisplay: 'update',
                                //             trainindID: o.result.id
                                //         }
                                //     });
                                // } else {
                                //     Ext.getCmp('Koltiva.view.TrainingFarmer.MainForm').destroy();
                                //     MainForm = Ext.create('Koltiva.view.TrainingFarmer.MainForm', {
                                //         viewVar: {
                                //             OpsiDisplay: 'update',
                                //             trainindID: o.result.id
                                //         }
                                //     });
                                // }
                                Ext.data.StoreManager.lookup('Koltiva.store.IMS.storeFarmerTraining').load();
    
                                //tutup popup
                                thisObj.close();
                            }
                        });
                    } else {
                        Ext.MessageBox.show({
                            title: 'Attention',
                            msg: 'Form not valid yet',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }, {
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            if (thisObj.viewVar.OpsiDisplay == 'insert') {
                //form reset
                Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining-Form').getForm().reset();

                if (m_param)
                    Ext.getCmp('Provinsi').setValue(m_param).setReadOnly(true);
                else
                    Ext.getCmp('Provinsi').setValue('').setReadOnly(false);
                if (m_districtid)
                    Ext.getCmp('Kabupaten').setValue(m_districtid).setReadOnly(true);
                else
                    Ext.getCmp('Kabupaten').setValue('').setReadOnly(false);

                Ext.getCmp('gtraining').setVisible(false);
//                Ext.getCmp('gtraining').getStore().load();
                Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);

            }
            if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
                if (thisObj.viewVar.OpsiDisplay == 'view') {
                    Ext.getCmp('saveButton').hide();
                }
                Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining-Form').getForm().reset();

                Ext.getCmp('gtraining').getStore().load({
                    params: {
                        training: thisObj.viewVar.trainindID
                    }
                });
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {id: thisObj.viewVar.trainindID},
                    success: function (fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('id').setValue(thisObj.viewVar.trainindID);
                        setFormValue(r);
                        
                        //Event Status
                        switch (r.TrainingStatus) {
                            case '1':
                                Ext.getCmp('TrainingStatus1').setValue(true);

                                Ext.getCmp('TrainFarmerBtnAdd').setDisabled(true);
                                thisObj.ContextMenuGtraining.items.get('TrainFarmerBtnUpdate').setDisabled(true);
                                thisObj.ContextMenuGtraining.items.get('TrainFarmerBtnDelete').setDisabled(true);
                                thisObj.ContextMenuGtraining.items.get('TrainFarmerBtnAttListParticipant').setDisabled(true);
//                                Ext.getCmp('TrainFarmerBtnUpdate').setDisabled(true);
//                                Ext.getCmp('TrainFarmerBtnDelete').setDisabled(true);
//                                Ext.getCmp('TrainFarmerBtnAttListParticipant').setDisabled(true);
                                Ext.getCmp('TrainFarmerBtnAttCheckList').setDisabled(true);
                                break;
                            case '2':
                                Ext.getCmp('TrainingStatus2').setValue(true);

                                Ext.getCmp('TrainFarmerBtnAdd').setDisabled(false);
                                thisObj.ContextMenuGtraining.items.get('TrainFarmerBtnUpdate').setDisabled(false);
                                thisObj.ContextMenuGtraining.items.get('TrainFarmerBtnDelete').setDisabled(false);
                                thisObj.ContextMenuGtraining.items.get('TrainFarmerBtnAttListParticipant').setDisabled(false);
//                                Ext.getCmp('TrainFarmerBtnUpdate').setDisabled(false);
//                                Ext.getCmp('TrainFarmerBtnDelete').setDisabled(false);
                                thisObj.ContextMenuGtraining.items.get('TrainFarmerBtnAttListParticipant').setDisabled(false);
                                Ext.getCmp('TrainFarmerBtnAttCheckList').setDisabled(false);
                                break;
                            default:
                                Ext.getCmp('TrainingStatus1').setValue(false);
                                Ext.getCmp('TrainingStatus2').setValue(false);

                                Ext.getCmp('TrainFarmerBtnAdd').setDisabled(false);
                                thisObj.ContextMenuGtraining.items.get('TrainFarmerBtnUpdate').setDisabled(false);
                                thisObj.ContextMenuGtraining.items.get('TrainFarmerBtnDelete').setDisabled(false);
                                thisObj.ContextMenuGtraining.items.get('TrainFarmerBtnAttListParticipant').setDisabled(false);
//                                Ext.getCmp('TrainFarmerBtnUpdate').setDisabled(false);
//                                Ext.getCmp('TrainFarmerBtnDelete').setDisabled(false);
//                                Ext.getCmp('TrainFarmerBtnAttListParticipant').setDisabled(false);
                                Ext.getCmp('TrainFarmerBtnAttCheckList').setDisabled(false);
                                break;
                        }
                    }
                });
            }
        }
    },
    LoadGridInformation: function(){
        var thisObj = this;
        
        if(Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormFarmerTraining-Form-EventType]')[0].getGroupValue() == 'Single CPG'){
            ParticipantType = Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining-Form-SingleParticipantType').getValue();
        }else{
            ParticipantType = Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining-Form-MultipleParticipantType').getValue();
        }

        thisObj.StoreGridEventTrainingMapping.storeVar.IMSID = thisObj.viewVar.IMSID;
        thisObj.StoreGridEventTrainingMapping.storeVar.TrainingType = 'CPG Training';
        thisObj.StoreGridEventTrainingMapping.storeVar.ActivityType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormFarmerTraining-Form-ActivityType]')[0].getGroupValue();
        thisObj.StoreGridEventTrainingMapping.storeVar.ParticipantType = ParticipantType;
        thisObj.StoreGridEventTrainingMapping.load();

        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.IMSID = thisObj.viewVar.IMSID;
        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.TrainingType = 'CPG Training';
        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.EventType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormFarmerTraining-Form-EventType]')[0].getGroupValue();
        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.ActivityType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormFarmerTraining-Form-ActivityType]')[0].getGroupValue();
        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.CPGid = Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining-Form-SingleCpg').getValue();
        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.ParticipantType = ParticipantType;
        thisObj.StoreGridTrainingGapAvailableParticipant.load();

        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.IMSID = thisObj.viewVar.IMSID;
        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.TrainingType = 'CPG Training';
        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.EventType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormFarmerTraining-Form-EventType]')[0].getGroupValue();
        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.ActivityType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormFarmerTraining-Form-ActivityType]')[0].getGroupValue();
        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.CPGid = Ext.getCmp('Koltiva.view.IMS.WinFormFarmerTraining-Form-SingleCpg').getValue();
        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.ParticipantType = ParticipantType;
        thisObj.StoreGridTrainingCocAvailableParticipant.load();
    }
});