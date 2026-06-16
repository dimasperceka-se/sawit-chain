/*
* @Author: nikolius
* @Date:   2018-03-26 15:14:55
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-26 17:15:05
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinFormApprovalSocia' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormApprovalSocia',
    title: lang('Socialization - Approval'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '54%',
    height: '50%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormApprovalSocia-Form',
            padding: '5 20 5 8',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 250,
                padding: 10
            },
            items: [{
                xtype: 'panel',
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-IMSID',
                        name: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-IMSID'
                    },{
                        columnWidth: 1,
                        layout: 'form',
                        items: [{
                        	xtype: 'radiogroup',
		                	fieldLabel: lang('Socialization Status'),
		                    columns: 2,
	                        id:'Koltiva.view.IMS.WinFormApprovalSocia-Form-SocStatus',
		                    items:[{
		                        boxLabel: lang('Lock'),
		                        name: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-SocStatus',
		                        inputValue: '1',
		                        id: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-SocStatus1',
		                        listeners:{
		                            change: function(){
		                                return false;
		                            }
		                        }
		                    },{
		                        boxLabel: lang('Open'),
		                        name: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-SocStatus',
		                        inputValue: '2',
		                        id: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-SocStatus2',
		                        listeners:{
		                            change: function(){
		                                return false;
		                            }
		                        }
		                    }]
                        },{
                        	xtype: 'numericfield',
	                        name: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-ParPassSoc',
	                        id: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-ParPassSoc',
	                        fieldLabel: lang('Nr of participant that passed socialization'),
	                        readOnly: true,
	                        allowNegative: false,
                            minValue: 0
                        },{
                        	xtype: 'numericfield',
	                        name: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-ParApprovedSoc',
	                        id: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-ParApprovedSoc',
	                        fieldLabel: lang('Nr of participant that have been approved'),
	                        readOnly: true,
	                        allowNegative: false,
                            minValue: 0
                        },{
                        	xtype: 'textfield',
	                        name: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-ApproveBy',
	                        id: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-ApproveBy',
	                        fieldLabel: lang('Approve By'),
	                        readOnly: true
                        },{
                        	xtype: 'textfield',
	                        name: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-ApprovalDate',
	                        id: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-ApprovalDate',
	                        fieldLabel: lang('Approval Date'),
	                        readOnly: true
                        },{
                        	xtype:'textarea',
                            id: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-SocApprovalRemark',
                            name: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-SocApprovalRemark',
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
                id: 'Koltiva.view.IMS.WinFormApprovalSocia-Form-BtnSave',
                handler: function () {
                    Ext.Ajax.request({
                        url: m_api + '/ims/approval_socia',
                        method: 'POST',
                        params: {
                            IMSID: thisObj.viewVar.IMSID,
                            SocStatus: Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-SocStatus').getValue(),
                            SocApprovalRemark: Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-SocApprovalRemark').getValue()
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
                margin: '5px',
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
                url: m_api + '/ims/approval_socia_form_open',
                method: 'POST',
                params: {
                    IMSID: thisObj.viewVar.IMSID
                },
                success: function(response, action) {
                	//console.log(response);
                	var objReturn = Ext.decode(response.responseText);

                	if(objReturn.SocStatus == "2"){
                		//Open
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-SocStatus2').setValue(true);
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-ParPassSoc').setValue(objReturn.ParPassSoc);

                		//display / hide
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-ParApprovedSoc').setVisible(false);
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-ApproveBy').setVisible(false);
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-ApprovalDate').setVisible(false);
                	}

                	if(objReturn.SocStatus == "1"){
                		//Locked
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-SocStatus1').setValue(true);
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-ParApprovedSoc').setValue(objReturn.ParApprovedSoc);
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-ApproveBy').setValue(objReturn.SocUserApprove);
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-ApprovalDate').setValue(objReturn.SocApprovalDate);
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSelec-Form-SocApprovalRemark').setReadOnly(true);

                		//display / hide
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-ParPassSoc').setVisible(false);
                		Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSocia-Form-BtnSave').setVisible(false);
                	}
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