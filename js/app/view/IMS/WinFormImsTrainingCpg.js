/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Dec 03 2018
 *  File : WinFormImsTrainingCpg.js
 *******************************************/

/**
    Param2 yg diperlukan ketika load View ini
    - IMSID
    - OpsiDisplay
    - CallerStore
    - CpgBatchTrainingID
    - EventType (Single CPG, Multiple CPG)
 */

Ext.define('Koltiva.view.IMS.WinFormImsTrainingCpg' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsTrainingCpg',
    title: lang('IMS - Training Form (CPG Training)'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '98%',
    height: '94%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //Store
        thisObj.CmbFasilitator = Ext.create('Koltiva.store.IMS.CmbFasilitatorTraining');
        thisObj.CmbPenyuluh = Ext.create('Koltiva.store.IMS.CmbPenyuluhTraining');
        thisObj.GridParticipants = Ext.create('Koltiva.store.IMS.GridTrainingCpgTrainingParticipants',{
        	storeVar: {
                CpgBatchTrainingID: thisObj.viewVar.CpgBatchTrainingID
            }
        });

        //Context Menu 
        thisObj.ContextMenuGridPar = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    hidden: m_act_delete,
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpg-GridParticipants').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/ims_training/cpg_training_participant',
                                    method: 'DELETE',
                                    params: {
                                        FarmerID: sm.get('FarmerID'),
                                        CpgBatchTrainingID: thisObj.viewVar.CpgBatchTrainingID
                                    },
                                    success: function (response, opts) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data deleted'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //refresh store
                                        thisObj.GridParticipants.load();
                                        Ext.getCmp('Koltiva.view.IMS.WinImsTraining').store_tab_cpg_training.load();
                                    },
                                    failure: function (rp, o) {
                                        try {
                                            var r = Ext.decode(rp.responseText);
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
                        });
                    }
                }]
        });

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                        	columnWidth: 0.495,
                            style:'padding-right:25px;',
                            layout:'form',
                            defaults: {
					            labelWidth: 175
					        },
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-CPGid',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-CPGid',
                                fieldLabel: lang('CPG ID'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-BatchNr',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-BatchNr',
                                fieldLabel: lang('Training Batch'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingTopic',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingTopic',
                                fieldLabel: lang('Topik'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingSubtopic',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingSubtopic',
                                fieldLabel: lang('Subtopik'),
                                readOnly: true
                            },{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Tipe Aktifitas'),
                                columns: 2,
                                id:'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-RowActivityType',
                                disabled:true,
                                items:[{
                                    boxLabel: lang('Full'),
                                    name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-ActivityType',
                                    inputValue: 'full',
                                    id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-ActivityTypeFull',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('Refresh'),
                                    name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-ActivityType',
                                    inputValue: 'refresh',
                                    id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-ActivityTypeRefresh',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Remedial'),
                                disabled:true,
                                columns: 2,
                                id:'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-RowRemedial',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-RemidialType',
                                    inputValue: 'yes',
                                    id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-RemidialTypeYes',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-RemidialType',
                                    inputValue: 'no',
                                    id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-RemidialTypeNo',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'datefield',
                                fieldLabel: lang('Training Start'),
                                baseCls: 'Sfr_FormInputMandatory',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingStart',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingStart',
                                format: 'Y-m-d',
                                allowBlank: false
                            },{
                                xtype: 'datefield',
                                fieldLabel: lang('Training End'),
                                baseCls: 'Sfr_FormInputMandatory',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingEnd',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingEnd',
                                format: 'Y-m-d',
                                allowBlank: false
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            defaults: {
					            labelWidth: 175
					        },
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-ParticipantType',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-ParticipantType',
                                fieldLabel: lang('Participant Type'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingDays',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingDays',
                                fieldLabel: lang('Jumlah Pertemuan'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-CertProgramLabel',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-CertProgramLabel',
                                fieldLabel: lang('Certified Programs'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-CertHolderLabel',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-CertHolderLabel',
                                fieldLabel: lang('Certificate Holder'),
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-IMSLabel',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-IMSLabel',
                                fieldLabel: lang('IMS Label'),
                                readOnly: true
                            },{
                                xtype: 'combo',
                                store: thisObj.CmbFasilitator,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Fasilitator'),
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-FacilitatorPersonID',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-FacilitatorPersonID',
                                queryMode: 'local'
                            },{
                                xtype: 'combo',
                                store: thisObj.CmbPenyuluh,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Penyuluh'),
                                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-ExtensionStaffID',
                                name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-ExtensionStaffID',
                                queryMode: 'local'
                            },{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Event Status'),
                                baseCls: 'Sfr_FormInputMandatory',
                                columns: 3,
                                id:'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-RowTrainingStatus',
                                allowBlank: false,
                                items:[{
                                    boxLabel: lang('On Going'),
                                    name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingStatus',
                                    inputValue: '2',
                                    id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingStatus2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('Completed'),
                                    name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingStatus',
                                    inputValue: '1',
                                    id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingStatus1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('Canceled'),
                                    name: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingStatus',
                                    inputValue: '3',
                                    id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingStatus3',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        }]
                    },{
                        xtype: 'gridpanel',
                        title: lang('Participants'),
                        id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-GridParticipants',
                        style: 'border:1px solid #CCC;padding-right:3px;',
                        cls: 'Sfr_GridNew',
                        store: thisObj.GridParticipants,
                        width: '100%',
                        loadMask: true,
                        selType: 'rowmodel',
                        viewConfig: {
                            deferEmptyText: false,
                            emptyText: lang('No Data Available')
                        },
                        dockedItems: [{
                            xtype: 'toolbar',
                            items: [{
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                margin: '0px 20px 0px 6px',
                                cls:'Sfr_BtnGridGreen',
                                overCls:'Sfr_BtnGridGreen-Hover',
                                hidden: true,
                                id:'Koltiva.view.IMS.WinFormImsTrainingCpg-GridParticipants-BtnAddParticipants',
                                text: lang('Add Participants'),
                                handler: function() {
                                    var WinFormImsTrainingCpgTrainingAddParticipants = Ext.create('Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants',{
                                        viewVar:{
                                            IMSID : thisObj.viewVar.IMSID,
                                            CallerStore: thisObj.GridParticipants,
                                            CpgBatchTrainingID: thisObj.viewVar.CpgBatchTrainingID,
                                            ParticipantType: Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpg-Form-ParticipantType').getValue()
                                        }
                                    });
                                    if (!WinFormImsTrainingCpgTrainingAddParticipants.isVisible()) {
                                        WinFormImsTrainingCpgTrainingAddParticipants.center();
                                        WinFormImsTrainingCpgTrainingAddParticipants.show();
                                    } else {
                                        WinFormImsTrainingCpgTrainingAddParticipants.close();
                                    }
                                }
                            },{
                                xtype: 'splitbutton',
                                icon: varjs.config.base_url + 'images/icons/new/printout.png',
                                text: lang('Daftar Hadir'),
                                cls:'Sfr_BtnGridPaleBlue',
                                overCls:'Sfr_BtnGridPaleBlue-Hover',
                                menu: {
                                    items: [{
                                        text: lang('Form Kosong'),
                                        handler: function () {
                                            var UrlCetakDaftarHadirCpg = m_api+'/cpg/cetak/'+thisObj.viewVar.CpgBatchTrainingID+'/blank';
                                            preview_cetak_surat(UrlCetakDaftarHadirCpg);
                                        }
                                    }, {
                                        text: lang('Form Hasil'),
                                        handler: function () {
                                            var WinFormImsTrainingCetakDaftarHadir = Ext.create('Koltiva.view.IMS.WinFormImsTrainingCetakDaftarHadir',{
                                                viewVar:{
                                                    TrainingID : thisObj.viewVar.CpgBatchTrainingID,
                                                    Type: 'CpgTraining',
                                                    TrainingDays: Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpg-Form-TrainingDays').getValue()
                                                }
                                            });
                                            if (!WinFormImsTrainingCetakDaftarHadir.isVisible()) {
                                                WinFormImsTrainingCetakDaftarHadir.center();
                                                WinFormImsTrainingCetakDaftarHadir.show();
                                            } else {
                                                WinFormImsTrainingCetakDaftarHadir.close();
                                            }
                                        }
                                    }]
                                }
                            }]
                        }],
                        columns: [{
                            text: lang('Action'),
                            xtype:'actioncolumn',
                            width: '6%',
                            hidden: true,
                            items:[{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function(grid, rowIndex, colIndex, item, e, record) {
                                    thisObj.ContextMenuGridPar.showAt(e.getXY());
                                }
                            }]
                        },{
                            text: lang('Farmer ID'),
                            width: '10%',
                            dataIndex: 'FarmerID'
                        },{
                            text: lang('Farmer Name'),
                            flex: 1,
                            dataIndex: 'FarmerName'
                        },{
                            text: lang('Farmer Group'),
                            flex: 1,
                            dataIndex: 'FarmerGroup'
                        },{
                            text: lang('Attendance (%)'),
                            width: '12%',
                            dataIndex: 'AttendancePersentase'
                        },{
                            text: lang('Passed'),
                            width: '12%',
                            dataIndex: 'TrainingPassed',
                            renderer: function (value) {
                                var RetVal;
                                
                                switch(value){
                                    case '1':
                                        RetVal = lang('Yes');
                                    break;
                                    case '2':
                                        RetVal = lang('No');
                                    break;
                                    default:
                                        RetVal = '-';
                                    break;
                                }
                
                                return RetVal;
                            }
                        },{
                            text: lang('Pre Test'),
                            dataIndex: 'WritingAwal',
                            width: '10%'
                        },{
                            text: lang('Post Test'),
                            dataIndex: 'WritingAkhir',
                            width: '9%',
                        }]
                    }]
                }]
            }]
        }];

        //Button
        thisObj.buttons = [{
                text: lang('Save'),
                margin: '5 15 5 5',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinFormImsTrainingCpg-Form-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpg-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();

                    if (FormValidOrNot == true) {
                        FormNya.submit({
                            url: m_api + '/ims_training/cpg_training_form',
                            method: 'POST',
                            waitMsg: 'Saving data...',
                            params: {
                                CpgBatchTrainingID: thisObj.viewVar.CpgBatchTrainingID
                            },
                            success: function (rp, o) {
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: o.result.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                //refresh store yg manggil
                                thisObj.viewVar.CallerStore.load();
                            },
                            failure: function (rp, o) {
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

            //form reset
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpg-Form').getForm();
            FormNya.reset();

            if(thisObj.viewVar.OpsiDisplay == 'update' || thisObj.viewVar.OpsiDisplay == 'view'){
                //load formnya
                FormNya.load({
                    url: m_api + '/ims_training/cpg_training_form_data',
                    method: 'GET',
                    params: {
                        CpgBatchTrainingID: thisObj.viewVar.CpgBatchTrainingID,
                        IMSID: thisObj.viewVar.IMSID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);


                        //Btn Add Participants
                        if(thisObj.viewVar.EventType == 'Multiple CPG'){
                            if(m_act_add == false){
                                Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpg-GridParticipants-BtnAddParticipants').setVisible(true);
                            }
                            //Column Action
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpg-GridParticipants').columns[0].setVisible(true);
                        }

                        if(thisObj.viewVar.OpsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpg-Form-BtnSave').setVisible(false);
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpg-GridParticipants-BtnAddParticipants').setVisible(false);
                        }
                    },
                    failure: function(form, action) {
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
    }
});