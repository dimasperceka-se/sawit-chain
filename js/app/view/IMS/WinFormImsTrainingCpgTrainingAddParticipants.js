/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Dec 05 2018
 *  File : WinFormImsTrainingCpgTrainingAddParticipants.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini    
    - IMSID 
    - CallerStore
    - CpgBatchTrainingID
    - ParticipantType
*/

Ext.define('Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants',
    title: lang('IMS - CPG Training Add Participant'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '62%',
    height: '86%',
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //Store ========================================= (Begin)
        thisObj.StoreParticipantList = Ext.create('Koltiva.store.IMS.ImsTrainingGridCpgTrainingAddPar',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID,
                CpgBatchTrainingID: thisObj.viewVar.CpgBatchTrainingID,
                ParticipantType: thisObj.viewVar.ParticipantType
            }
        });
        //Store ========================================= (End)

        //items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'gridpanel',
            title: lang('Participant List'),
            id: 'Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants-Form-GridParList',
            style: 'border:1px solid #CCC;',
            store: thisObj.StoreParticipantList,
            width: '100%',
            loadMask: true,
            selType: 'checkboxmodel',
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No Data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.StoreParticipantList,
                dock: 'bottom',
                displayInfo: true,
                style:'padding-right:12px;'
            },{
                xtype: 'toolbar',
                items: [{
                    name: 'Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants-Form-GridParList-SearchStringParam',
                    id: 'Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants-Form-GridParList-SearchStringParam',
                    xtype: 'textfield',
                    baseCls:'Sfr_TxtfieldSearchGrid',
                    width: 200,
                    emptyText: lang('ID / Name')
                },{
                    name: 'Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants-Form-GridParList-SearchCpgParam',
                    id: 'Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants-Form-GridParList-SearchCpgParam',
                    xtype: 'textfield',
                    baseCls:'Sfr_TxtfieldSearchGrid',
                    width: 200,
                    emptyText: lang('ID / Name Farmer Group')
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                    margin: '0px 0px 0px 6px',
                    cls:'Sfr_BtnGridBlue',
                    overCls:'Sfr_BtnGridBlue-Hover',
                    text: lang('Search'),
                    handler: function() {
                        thisObj.StoreParticipantList.storeVar = {
                            IMSID: thisObj.viewVar.IMSID,
                            CpgBatchTrainingID: thisObj.viewVar.CpgBatchTrainingID,
                            ParticipantType: thisObj.viewVar.ParticipantType,
                            SearchStringParam: Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants-Form-GridParList-SearchStringParam').getValue(),
                            SearchCpgParam: Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants-Form-GridParList-SearchCpgParam').getValue()
                        };
                        thisObj.StoreParticipantList.load();
                    }
                }]
            }],
            columns: [{
                HeaderCheckbox: true,
                dataIndex : 'CheckData',
                width:'5%'
            },{
                text: lang('Farmer ID'),
                width: '12%',
                dataIndex: 'FarmerID'
            },{
                text: lang('Name'),
                width: '25%',
                dataIndex: 'FarmerName'
            },{
                text: lang('Gender'),
                width: '9%',
                dataIndex: 'Gender'
            },{
                text: lang('Sub District'),
                width: '14%',
                dataIndex: 'SubDistrict'
            },{
                text: lang('Village'),
                width: '12%',
                dataIndex: 'Village'
            },{
                text: lang('Farmer Group'),
                width: '19%',
                dataIndex: 'FarmerGroup'
            }]
        }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (End)

        thisObj.buttons = [{
                text: lang('Add Participant'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants-Form-BtnSave',
                handler: function () {
                    var gridSelected = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCpgTrainingAddParticipants-Form-GridParList').getSelectionModel().getSelection();

                    var IdSelectedArr = [];
                    for (var i = gridSelected.length - 1; i >= 0; i--) {
                        IdSelectedArr.push(gridSelected[i].get('FarmerID'));
                    }

                    if (IdSelectedArr.length > 0) {
                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/ims_training/cpg_training_add_par',
                            method: 'POST',
                            params: {
                                FarmerIDSel: Ext.encode(IdSelectedArr),
                                CpgBatchTrainingID: thisObj.viewVar.CpgBatchTrainingID
                            },
                            success: function (rp, o) {
                                var r = Ext.decode(rp.responseText);
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                //Store load
                                thisObj.StoreParticipantList.load();
                                thisObj.viewVar.CallerStore.load();
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
                    } else {
                        Ext.MessageBox.show({
                            title: 'Notifications',
                            msg: 'No item selected',
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
    }
});