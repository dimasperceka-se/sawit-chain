/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Nov 28 2018
 *  File : WinFormImsTrainingGenerateCpg.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg',
    title: lang('IMS - Generate CPG Training Event'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '66%',
    height: '94%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.CmbSingleCpg = Ext.create('Koltiva.store.IMS.CmbCpgByImsIdSingle',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });
        thisObj.CmbParticipantType = Ext.create('Koltiva.store.IMS.CmbParticipantType');
        thisObj.CmbParticipantTypeStatis = Ext.create('Koltiva.store.IMS.CmbParticipantTypeStatis');

        thisObj.StoreGridEventTrainingMapping = Ext.create('Koltiva.store.IMS.GridEventTrainingMapping',{
        	storeVar: {
                IMSID: null,
                TrainingType: 'CPG Training',
                ActivityType: null,
                ParticipantType: null
            }
        });
        thisObj.StoreGridTrainingGapAvailableParticipant = Ext.create('Koltiva.store.IMS.GridTrainingGapAvailableParticipant',{
        	storeVar: {
                IMSID: null,
                TrainingType: 'CPG Training',
                EventType: null,
                ActivityType: null,
                CPGid: null,
                ParticipantType: null
            }
        });
        thisObj.StoreGridTrainingCocAvailableParticipant = Ext.create('Koltiva.store.IMS.GridTrainingCocAvailableParticipant',{
        	storeVar: {
                IMSID: null,
                TrainingType: 'CPG Training',
                EventType: null,
                ActivityType: null,
                CPGid: null,
                ParticipantType: null
            }
        });

        //Form Validator ========================================== (Begin)
        Ext.define('Ext.lib.Validators', {
            singleton: true,
            ValidateSingleCpg: function(){
                return function(){
                    var ValSingleCpg = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SingleCpg').getValue();
                    var ValEventType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-EventType]')[0].getGroupValue();

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
                    var ValParType = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SingleParticipantType').getValue();
                    var ValEventType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-EventType]')[0].getGroupValue();

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
                    var ValParType = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-MultipleParticipantType').getValue();
                    var ValEventType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-EventType]')[0].getGroupValue();

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
            id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    defaults:{
                        labelWidth: 200,
                    },
                    items:[{
                        xtype: 'radiogroup',
                        fieldLabel: lang('Event Type'),
                        allowBlank: false,
                        msgTarget: 'under',
                        items: [{
                            name: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-EventType',
                            id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-EventTypeSingle',
                            boxLabel: lang('Single CPG'),
                            inputValue: 'Single CPG',
                            listeners: {
                                change: function(field, nv, ov) {
                                    if(this.checked == true){
                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-ActivityTypeRemedial').setDisabled(true);
                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SectionSingleCpg').setVisible(true);
                                    }else{
                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-ActivityTypeRemedial').setDisabled(false);
                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SectionSingleCpg').setVisible(false);
                                    }

                                    thisObj.LoadGridInformation();

                                    return false;
                                }
                            }
                        },{
                            name: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-EventType',
                            id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-EventTypeMultiple',
                            boxLabel: lang('Multiple CPG'),
                            inputValue: 'Multiple CPG',
                            listeners: {
                                change: function(field, nv, ov) {
                                    if(this.checked == true){
                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-ActivityTypeRemedial').setDisabled(false);
                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SectionMultipleCpg').setVisible(true);
                                    }else{
                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SectionMultipleCpg').setVisible(false);
                                    }
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('Activity Type'),
                        allowBlank: false,
                        msgTarget: 'under',
                        items: [{
                            name: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-ActivityType',
                            id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-ActivityTypeFull',
                            boxLabel: lang('Full'),
                            inputValue: 'Full',
                            listeners: {
                                change: function(field, nv, ov) {
                                    thisObj.LoadGridInformation();
                                    return false;
                                }
                            }
                        },{
                            name: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-ActivityType',
                            id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-ActivityTypeRemedial',
                            boxLabel: lang('Remedial'),
                            inputValue: 'Remedial',
                            listeners: {
                                change: function(field, nv, ov) {
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype:'panel',
                        title: lang('Single CPG'),
                        frame: false,
                        id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SectionSingleCpg',
                        style:'margin-top:12px;',
                        cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                        hidden: true,
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 1,
                                layout:'form',
                                style:'padding: 8px 12px 8px 12px;',
                                defaults:{
                                    labelWidth: 195
                                },
                                items:[{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SingleCpg',
                                    name: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SingleCpg',
                                    store: thisObj.CmbSingleCpg,
                                    fieldLabel: lang('CPG'),
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    msgTarget: 'under',
                                    editable: false,
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SingleParticipantType').setValue(null);
                                            thisObj.CmbParticipantType.setStoreVar({
                                                IMSID: thisObj.viewVar.IMSID,
                                                CPGid: nv
                                            });
                                            thisObj.CmbParticipantType.load();

                                            thisObj.LoadGridInformation();
                                        }
                                    },
                                    validator: Ext.lib.Validators.ValidateSingleCpg()
                                },{html:'<div style="height:1px;">&nbsp;</div>'},{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SingleParticipantType',
                                    name: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SingleParticipantType',
                                    store: thisObj.CmbParticipantType,
                                    fieldLabel: lang('Participant Type'),
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    msgTarget: 'under',
                                    editable: false,
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            thisObj.LoadGridInformation();
                                        }
                                    },
                                    validator: Ext.lib.Validators.ValidateSingleParType()
                                }]
                            }]
                        }]
                    },{
                        xtype:'panel',
                        title: lang('Multiple CPG'),
                        frame: false,
                        id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SectionMultipleCpg',
                        style:'margin-top:12px;',
                        cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                        hidden: true,
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 1,
                                layout:'form',
                                style:'padding: 8px 12px 8px 12px;',
                                defaults:{
                                    labelWidth: 195
                                },
                                items:[{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-MultipleParticipantType',
                                    name: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-MultipleParticipantType',
                                    store: thisObj.CmbParticipantTypeStatis,
                                    fieldLabel: lang('Participant Type'),
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    msgTarget: 'under',
                                    editable: false,
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            thisObj.LoadGridInformation();
                                        }
                                    },
                                    validator: Ext.lib.Validators.ValidateMultipleParType()
                                }]
                            }]
                        }]
                    },{
                        xtype: 'gridpanel',
                        title: lang('Event Training Mapping'),
                        id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-GridEventTrainingMapping',
                        style: 'border:1px solid #CCC;padding-right:3px;margin-top:13px;',
                        store: thisObj.StoreGridEventTrainingMapping,
                        width: '100%',
                        loadMask: true,
                        selType: 'rowmodel',
                        viewConfig: {
                            deferEmptyText: false,
                            emptyText: lang('No Data Available')
                        },
                        columns: [{
                            text: lang('Activity Type'),
                            flex: 1,
                            dataIndex: 'ActivityType'
                        },{
                            text: lang('Participant Type'),
                            flex: 1,
                            dataIndex: 'ParticipantType'
                        },{
                            text: lang('Training GAP (days)'),
                            flex: 2,
                            dataIndex: 'TopikGAP'
                        },{
                            text: lang('Training COC (days)'),
                            flex: 2,
                            dataIndex: 'TopikCOC'
                        }]
                    },{
                        xtype: 'gridpanel',
                        title: lang('Training GAP - Available Participant'),
                        id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-GridTrainingGapAvailableParticipant',
                        style: 'border:1px solid #CCC;padding-right:3px;margin-top:13px;',
                        store: thisObj.StoreGridTrainingGapAvailableParticipant,
                        width: '100%',
                        loadMask: true,
                        selType: 'rowmodel',
                        viewConfig: {
                            deferEmptyText: false,
                            emptyText: lang('No Data Available')
                        },
                        columns: [{
                            text: 'No',
                            xtype: 'rownumberer',
                            align: 'center',
                            flex: 1,
                        },{
                            text: lang('FarmerID'),
                            flex: 1,
                            dataIndex: 'FarmerID'
                        },{
                            text: lang('Farmer Name'),
                            flex: 3,
                            dataIndex: 'FarmerName'
                        },{
                            text: lang('District'),
                            flex: 2,
                            dataIndex: 'District'
                        },{
                            text: lang('SubDistrict'),
                            flex: 2,
                            dataIndex: 'SubDistrict'
                        },{
                            text: lang('Village'),
                            flex: 2,
                            dataIndex: 'Village'
                        }]
                    },{
                        xtype: 'gridpanel',
                        title: lang('Training COC - Available Participant'),
                        id: 'Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-GridTrainingCocAvailableParticipant',
                        style: 'border:1px solid #CCC;padding-right:3px;margin-top:13px;',
                        store: thisObj.StoreGridTrainingCocAvailableParticipant,
                        width: '100%',
                        loadMask: true,
                        selType: 'rowmodel',
                        viewConfig: {
                            deferEmptyText: false,
                            emptyText: lang('No Data Available')
                        },
                        columns: [{
                            text: 'No',
                            xtype: 'rownumberer',
                            align: 'center',
                            flex: 1,
                        },{
                            text: lang('FarmerID'),
                            flex: 1,
                            dataIndex: 'FarmerID'
                        },{
                            text: lang('Farmer Name'),
                            flex: 3,
                            dataIndex: 'FarmerName'
                        },{
                            text: lang('District'),
                            flex: 2,
                            dataIndex: 'District'
                        },{
                            text: lang('SubDistrict'),
                            flex: 2,
                            dataIndex: 'SubDistrict'
                        },{
                            text: lang('Village'),
                            flex: 2,
                            dataIndex: 'Village'
                        }]
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
                text: lang('Generate Training'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();

                    if (FormValidOrNot == true) {
                        FormNya.submit({
                            url: m_api + '/ims_training/ims_training_generate',
                            method: 'POST',
                            waitMsg: 'Saving data...',
                            params: {
                                IMSID: thisObj.viewVar.IMSID
                            },
                            success: function (rp, o) {
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: o.result.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-info'
                                });

                                //refresh store yg manggil
                                thisObj.viewVar.CallerStore.load();
                                thisObj.close();
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
        }
    },
    LoadGridInformation: function(){
        var thisObj = this;
        
        if(Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-EventType]')[0].getGroupValue() == 'Single CPG'){
            ParticipantType = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SingleParticipantType').getValue();
        }else{
            ParticipantType = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-MultipleParticipantType').getValue();
        }

        thisObj.StoreGridEventTrainingMapping.storeVar.IMSID = thisObj.viewVar.IMSID;
        thisObj.StoreGridEventTrainingMapping.storeVar.TrainingType = 'CPG Training';
        thisObj.StoreGridEventTrainingMapping.storeVar.ActivityType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-ActivityType]')[0].getGroupValue();
        thisObj.StoreGridEventTrainingMapping.storeVar.ParticipantType = ParticipantType;
        thisObj.StoreGridEventTrainingMapping.load();

        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.IMSID = thisObj.viewVar.IMSID;
        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.TrainingType = 'CPG Training';
        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.EventType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-EventType]')[0].getGroupValue();
        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.ActivityType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-ActivityType]')[0].getGroupValue();
        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.CPGid = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SingleCpg').getValue();
        thisObj.StoreGridTrainingGapAvailableParticipant.storeVar.ParticipantType = ParticipantType;
        thisObj.StoreGridTrainingGapAvailableParticipant.load();

        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.IMSID = thisObj.viewVar.IMSID;
        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.TrainingType = 'CPG Training';
        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.EventType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-EventType]')[0].getGroupValue();
        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.ActivityType = Ext.ComponentQuery.query('[name=Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-ActivityType]')[0].getGroupValue();
        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.CPGid = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingGenerateCpg-Form-SingleCpg').getValue();
        thisObj.StoreGridTrainingCocAvailableParticipant.storeVar.ParticipantType = ParticipantType;
        thisObj.StoreGridTrainingCocAvailableParticipant.load();
    }
});