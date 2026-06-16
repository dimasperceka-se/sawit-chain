/*
* @Author: nikolius
* @Date:   2018-03-26 17:21:11
* @Last Modified by:   nikolius
* @Last Modified time: 2018-06-04 16:09:48
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
    - callerStore
    - callerStoreTrainApprove
*/

Ext.define('Koltiva.view.IMS.WinFormApprovalTrain' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormApprovalTrain',
    title: lang('Training - Approval'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '46%',
    height: '40%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormApprovalTrain-Form',
            padding: '15 20 5 8',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 250,
                padding: 18
            },
            items: [{
                xtype: 'panel',
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-IMSID',
                        name: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-IMSID'
                    },{
                        columnWidth: 1,
                        layout: 'form',
                        items: [{
                        	xtype: 'radiogroup',
                            hidden: true,
		                	fieldLabel: lang('Training Status'),
		                    columns: 2,
	                        id:'Koltiva.view.IMS.WinFormApprovalTrain-Form-TrainStatus',
		                    items:[{
		                        boxLabel: lang('Lock'),
		                        name: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-TrainStatus',
		                        inputValue: '1',
		                        id: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-TrainStatus1',
		                        listeners:{
		                            change: function(){
		                                return false;
		                            }
		                        }
		                    },{
		                        boxLabel: lang('Open'),
		                        name: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-TrainStatus',
		                        inputValue: '2',
		                        id: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-TrainStatus2',
		                        listeners:{
		                            change: function(){
		                                return false;
		                            }
		                        }
		                    }]
                        },{
                        	xtype: 'numericfield',
	                        name: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-ParPassTrain',
	                        id: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-ParPassTrain',
	                        fieldLabel: lang('Nr of participant that passed training'),
	                        readOnly: true,
	                        allowNegative: false,
                            minValue: 0
                        },{
                        	xtype: 'numericfield',
                            hidden: true,
	                        name: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-ParApprovedTrain',
	                        id: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-ParApprovedTrain',
	                        fieldLabel: lang('Nr of participant that have been approved'),
	                        readOnly: true,
	                        allowNegative: false,
                            minValue: 0
                        },{
                        	xtype: 'textfield',
                            hidden: true,
	                        name: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-ApproveBy',
	                        id: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-ApproveBy',
	                        fieldLabel: lang('Approve By'),
	                        readOnly: true
                        },{
                        	xtype: 'textfield',
                            hidden: true,
	                        name: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-ApprovalDate',
	                        id: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-ApprovalDate',
	                        fieldLabel: lang('Approval Date'),
	                        readOnly: true
                        },{
                        	xtype:'textarea',
                            id: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-TrainApprovalRemark',
                            name: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-TrainApprovalRemark',
                            fieldLabel: lang('Approval Remark')
                        }]
                    }]
                }]
            }]
        }];

        //buttons ---------------------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.buttons = [{
                text: lang('Approve'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinFormApprovalTrain-Form-BtnSave',
                handler: function () {
                    Ext.Ajax.request({
                        url: m_api + '/ims/approval_train',
                        method: 'POST',
                        params: {
                            IMSID: thisObj.viewVar.IMSID,
                            ParPassTrain: Ext.getCmp('Koltiva.view.IMS.WinFormApprovalTrain-Form-ParPassTrain').getValue(),
                            TrainApprovalRemark: Ext.getCmp('Koltiva.view.IMS.WinFormApprovalTrain-Form-TrainApprovalRemark').getValue()
                        },
                        success: function (response, action) {
                            //console.log(response);
                            var objReturn = Ext.decode(response.responseText);

                            switch (objReturn.success_val) {
                                case true:
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Approval Success'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb7',
                                        icon: 'ext-mb-success'
                                    });

                                    thisObj.viewVar.callerStore.load();
                                    thisObj.viewVar.callerStoreTrainApprove.load();
                                    thisObj.close();
                                    break;
                                case false:
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: objReturn.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb7',
                                        icon: 'ext-mb-info'
                                    });
                                    break;
                            }
                        },
                        failure: function (response, action) {
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: 'Network Connection Error',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
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
        //buttons ---------------------------------------------------------------------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //Ambil datanya, lihat apakah perlu di approve atau hanya tampilkan
            Ext.Ajax.request({
                url: m_api + '/ims/approval_train_form_open',
                method: 'POST',
                params: {
                    IMSID: thisObj.viewVar.IMSID
                },
                success: function(response, action) {
                	//console.log(response);
                	var objReturn = Ext.decode(response.responseText);
                    Ext.getCmp('Koltiva.view.IMS.WinFormApprovalTrain-Form-ParPassTrain').setValue(objReturn.CountTrainPass);
                },
                failure: function(response, action){
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Network Connection Error',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });
        }
    }
});