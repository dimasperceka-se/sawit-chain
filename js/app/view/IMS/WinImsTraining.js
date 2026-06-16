/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Nov 28 2018
 *  File : WinImsTraining.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinImsTraining' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsTraining',
    title: lang('IMS - Training'),
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
        //Ini hak akses button ================================================= (Begin)
        if(thisObj.viewVar.CertEventStatus == '2'){ //Ims Status Completed
            thisObj.m_act_add = true;
            thisObj.m_act_update = true;
            thisObj.m_act_training_days_mapping = true;
        }else{
            thisObj.m_act_add = m_act_add;
            thisObj.m_act_update = m_act_update;
            thisObj.m_act_training_days_mapping = m_act_training_days_mapping;
        }
        //Ini hak akses button ================================================= (End)

        //Store =================================================
        thisObj.storeFarmerTraining = Ext.create('Koltiva.store.IMS.storeFarmerTraining',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });
        thisObj.CmbSummaryOption = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"id":"participants_not_assign", "label":lang('Participants not assign to any CPG Training')}
            ]
        });

        //Context Menu =================================================
        thisObj.ContextMenuGridTabCpgTraining = Ext.create('Ext.menu.Menu',{
            cls:'Sfr_ConMenu',
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                cls:'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsTraining-Tab-CpgTraining').getSelectionModel().getSelection()[0];
                    var WinFormFarmerTraining = Ext.create('Koltiva.view.IMS.WinFormFarmerTraining', {
                        viewVar: {
                            OpsiDisplay: 'view',
                            IMSID: thisObj.viewVar.IMSID,
                            trainindID:sm.get('id'),
                            CallerStore: thisObj.storeFarmerTraining
                        }
                    });
                    if (!WinFormFarmerTraining.isVisible()) {
                        WinFormFarmerTraining.center();
                        WinFormFarmerTraining.show();
                    } else {
                        WinFormFarmerTraining.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: thisObj.m_act_update,
                cls:'Sfr_BtnConMenuWhite',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsTraining-Tab-CpgTraining').getSelectionModel().getSelection()[0];
                    var WinFormFarmerTraining = Ext.create('Koltiva.view.IMS.WinFormFarmerTraining', {
                        viewVar: {
                            OpsiDisplay: 'update',
                            IMSID: thisObj.viewVar.IMSID,
                            trainindID:sm.get('id'),
                            CallerStore: thisObj.storeFarmerTraining
                        }
                    });
                    if (!WinFormFarmerTraining.isVisible()) {
                        WinFormFarmerTraining.center();
                        WinFormFarmerTraining.show();
                    } else {
                        WinFormFarmerTraining.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: thisObj.m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.IMS.WinImsTraining-Tab-CpgTraining').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_crud,
                                method: 'DELETE',
                                params: {id: sm.get('id')},
                                success: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', lang('Data delete successfully.'));
                                            thisObj.storeFarmerTraining.load();
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

        thisObj.items = [{
                xtype: 'form',
                id: 'Koltiva.view.IMS.WinImsTraining-Form',
                fileUpload: true,
                padding: '5 25 5 8',
                items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                                columnWidth: 1,
                                layout: 'form',
                                items: [{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 0.495,
                                                style: 'padding-right:25px;',
                                                layout: 'form',
                                                fieldDefaults: {
                                                    labelWidth: 275
                                                },
                                                items: [{
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsTraining-Form-CertEventName',
                                                        name: 'Koltiva.view.IMS.WinImsTraining-Form-CertEventName',
                                                        fieldLabel: lang('Event Name'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsTraining-Form-IMSID',
                                                        name: 'Koltiva.view.IMS.WinImsTraining-Form-IMSID',
                                                        fieldLabel: lang('Event ID'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsTraining-Form-Location',
                                                        name: 'Koltiva.view.IMS.WinImsTraining-Form-Location',
                                                        fieldLabel: lang('Location'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsTraining-Form-Year',
                                                        name: 'Koltiva.view.IMS.WinImsTraining-Form-Year',
                                                        fieldLabel: lang('Year of Certification'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }]
                                            }, {
                                                columnWidth: 0.5,
                                                style: 'padding-right:25px;',
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsTraining-Form-CertificateHolder',
                                                        name: 'Koltiva.view.IMS.WinImsTraining-Form-CertificateHolder',
                                                        fieldLabel: lang('Certificate Holders'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsTraining-Form-ProgramName',
                                                        name: 'Koltiva.view.IMS.WinImsTraining-Form-ProgramName',
                                                        fieldLabel: lang('Program Name'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsTraining-Form-CertificationBody',
                                                        name: 'Koltiva.view.IMS.WinImsTraining-Form-CertificationBody',
                                                        fieldLabel: lang('Certification Body'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinImsTraining-Form-FirstBuyer',
                                                        name: 'Koltiva.view.IMS.WinImsTraining-Form-FirstBuyer',
                                                        fieldLabel: lang('First Buyer'),
                                                        labelWidth: 200,
                                                        readOnly: true
                                                    }]
                                            }]
                                    }, {
                                        xtype: 'tabpanel',
                                        flex: 1,
                                        margin: 0,
                                        activeTab: 0,
                                        plain: true,
                                        items: [{
                                                xtype: 'gridpanel',
                                                title: lang('Farmer Training'),
                                                id: 'Koltiva.view.IMS.WinImsTraining-Tab-CpgTraining',
                                                style: 'border:1px solid #CCC;padding-right:3px;',
                                                cls: 'Sfr_GridNew',
                                                store: thisObj.storeFarmerTraining,
                                                width: '100%',
                                                loadMask: true,
                                                selType: 'rowmodel',
                                                viewConfig: {
                                                    deferEmptyText: false,
                                                    emptyText: lang('No Data Available')
                                                },
                                                dockedItems: [{
                                                    xtype: 'pagingtoolbar',
                                                    id: 'Koltiva.view.IMS.WinImsTraining-gridToolbar',
                                                    store: thisObj.storeFarmerTraining,
                                                    dock: 'bottom',
                                                    displayInfo: true
                                                }, {
                                                        xtype: 'toolbar',
                                                        items: [
                                                            {
                                                                xtype: 'button',
                                                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                margin: '0px 0px 0px 6px',
                                                                hidden: thisObj.m_act_add,
                                                                text: lang('Add Training'),
                                                                handler: function () {
                                                                    var WinFormFarmerTraining = Ext.create('Koltiva.view.IMS.WinFormFarmerTraining', {
                                                                        viewVar: {
                                                                            IMSID: thisObj.viewVar.IMSID,
                                                                            CallerStore: thisObj.storeFarmerTraining
                                                                        }
                                                                    });
                                                                    if (!WinFormFarmerTraining.isVisible()) {
                                                                        WinFormFarmerTraining.center();
                                                                        WinFormFarmerTraining.show();
                                                                    } else {
                                                                        WinFormFarmerTraining.close();
                                                                    }
                                                                }
                                                            }
                                                        ]
                                                    }],
                                                columns: [{
                                                    dataIndex: 'id',
                                                        hidden: true
                                                    }, {
                                                        text: lang('Action'),
                                                        xtype: 'actioncolumn',
                                                        width: '5%',
                                                        items: [{
                                                            icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                            handler: function (grid, rowIndex, colIndex, item, e, record) {
                                                                thisObj.ContextMenuGridTabCpgTraining.showAt(e.getXY());
                                                            }
                                                        }]
                                                    }, {
                                                        text: lang('Trainings'),
                                                        flex: 1,
                                                        dataIndex: 'training'
                                                    }, {
                                                        text: lang('Batch'),
                                                        flex: 1,
                                                        dataIndex: 'batch'
                                                    }, {
                                                        text: lang('District'),
                                                        flex: 1,
                                                        dataIndex: 'tot'
                                                    },{
                                                        text: lang('Number of Participants'),
                                                        flex: 1,
                                                        dataIndex: 'participant'
                                                    }, {
                                                        text: lang('Start'),
                                                        flex: 1,
                                                        dataIndex: 'start'
                                                    }, {
                                                        text: lang('End'),
                                                        flex: 1,
                                                        dataIndex: 'end'
                                                    }, {
                                                        text: lang('Days'),
                                                        flex: 1,
                                                        dataIndex: 'days'
                                                    }, {
                                                        text: lang('Partner'),
                                                        flex: 1,
                                                        dataIndex: 'partner_name'
                                                    }, {
                                                        text: lang('Event Status'),
                                                        flex: 1,
                                                        dataIndex: 'TrainingStatus',
                                                        renderer: function (value) {
                                                            var RetVal;

                                                            switch (parseInt(value)) {
                                                                case 1:
                                                                    RetVal = lang('Completed');
                                                                    break;
                                                                case 2:
                                                                    RetVal = lang('On Going');
                                                                    break;
                                                                case 3:
                                                                    RetVal = lang('Canceled');
                                                                    break;
                                                                default:
                                                                    RetVal = '-';
                                                                    break;
                                                            }

                                                            return RetVal;
                                                        }
                                                    }]
                                            }]
                                    }]
                            }]
                    }]
            }];

        //Button
        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
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
            var formNya = Ext.getCmp('Koltiva.view.IMS.WinImsTraining-Form');
            formNya.getForm().reset();

            //load nilainya
            formNya.getForm().load({
                url: m_api + '/ims_training/ims_training_get_form',
                method: 'GET',
                params: {
                    IMSID: thisObj.viewVar.IMSID,
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                    //console.log(r);
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
        },
        beforerender: function(){
            var thisObj = this;
        }
    },
    SummaryShowData: function(OpsiSummary){
        var thisObj = this;
        var GridSummary = Ext.getCmp('Koltiva.view.IMS.WinImsTraining-Tab-SummaryData-GridData');
        var GridSummaryToolbar = Ext.getCmp('Koltiva.view.IMS.WinImsTraining-Tab-SummaryData-GridData-PagingToolbar');
        
        if(OpsiSummary != null){
            switch(OpsiSummary){
                case 'participants_not_assign':
                    thisObj.SummaryGridStore = Ext.create('Ext.data.Store', {
                        fields: ['ID','FarmerID','ObjType','Name','Gender','District','SubDistrict','Village','FarmerGroup','ApprovalBy','ApprovalRemark'],
                        autoLoad: false,
                        pageSize: 25,
                        remoteSort: true,
                        proxy: {
                            type: 'ajax',
                            url: m_api + '/ims_training/summary_show_data_par_not_assign',
                            extraParams: {
                                IMSID: thisObj.viewVar.IMSID
                            },
                            reader: {
                                type: 'json',
                                root: 'data',
                                totalProperty: 'total'
                            }
                        }
                    });

                    GridSummaryToolbar.bindStore(thisObj.SummaryGridStore);
                    GridSummary.reconfigure(thisObj.SummaryGridStore,[{
                        text: lang('ID'),
                        dataIndex: 'ID',
                        width: '6%'
                    },{
                        text: lang('Farmer ID'),
                        dataIndex: 'FarmerID',
                        width: '6%'
                    },{
                        text: lang('Type'),
                        dataIndex: 'ObjType',
                        width: '10%'
                    },{
                        text: lang('Name'),
                        dataIndex: 'Name',
                        width: '15%'
                    },{
                        text: lang('Gender'),
                        dataIndex: 'Gender',
                        width: '6%'
                    },{
                        text: lang('District'),
                        dataIndex: 'District',
                        width: '8%'
                    },{
                        text: lang('SubDistrict'),
                        dataIndex: 'SubDistrict',
                        width: '8%'
                    },{
                        text: lang('Village'),
                        dataIndex: 'Village',
                        width: '8%'
                    },{
                        text: lang('Farmer Group'),
                        dataIndex: 'FarmerGroup',
                        width: '13%'
                    },{
                        text: lang('Approval By'),
                        dataIndex: 'ApprovalBy',
                        width: '8%'
                    },{
                        text: lang('Approval Remark'),
                        dataIndex: 'ApprovalRemark',
                        width: '11%'
                    }]);

                    //Show Grid
                    GridSummary.setVisible(true);
                    GridSummary.getStore().loadPage(1);
                break;
            }
        }else{
            Ext.MessageBox.show({
                title: 'Attention',
                msg: lang('No Summary Selected'),
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
        }
    }
});