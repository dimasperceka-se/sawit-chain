/*
* @Author: nikolius
* @Date:   2018-03-26 16:50:08
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-07 16:03:57
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinFormApprovalSelec' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormApprovalSelec',
    title: lang('Selection - Approval'),
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
            id: 'Koltiva.view.IMS.WinFormApprovalSelec-Form',
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
                        id: 'Koltiva.view.IMS.WinFormApprovalSelec-Form-IMSID',
                        name: 'Koltiva.view.IMS.WinFormApprovalSelec-Form-IMSID',
                        value: thisObj.viewVar.IMSID
                    },{
                        columnWidth: 1,
                        layout: 'form',
                        items: [{
                        	xtype: 'numericfield',
	                        name: 'Koltiva.view.IMS.WinFormApprovalSelec-Form-ParPassSel',
	                        id: 'Koltiva.view.IMS.WinFormApprovalSelec-Form-ParPassSel',
	                        fieldLabel: lang('Nr of participant that passed selection'),
	                        readOnly: true,
	                        allowNegative: false,
                            minValue: 0
                        },{
                        	xtype:'textarea',
                            id: 'Koltiva.view.IMS.WinFormApprovalSelec-Form-SelApprovalRemark',
                            name: 'Koltiva.view.IMS.WinFormApprovalSelec-Form-SelApprovalRemark',
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
                id: 'Koltiva.view.IMS.WinFormApprovalSelec-Form-BtnSave',
                handler: function () {
                    Ext.Ajax.request({
                        url: m_api + '/ims/approval_selec',
                        method: 'POST',
                        params: {
                            IMSID: thisObj.viewVar.IMSID,
                            ParPassSel: Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSelec-Form-ParPassSel').getValue(),
                            SelApprovalRemark: Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSelec-Form-SelApprovalRemark').getValue()
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

                                    thisObj.viewVar.CallerStoreSocialization.load();
                                    thisObj.viewVar.CallerStoreSelection.load();
                                    thisObj.viewVar.CallerStoreSelectionApproved.load();
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

            Ext.Ajax.request({
                url: m_api + '/ims/approval_selec_form_open',
                method: 'POST',
                params: {
                    IMSID: thisObj.viewVar.IMSID
                },
                success: function(response, action) {
                	//console.log(response);
                	var objReturn = Ext.decode(response.responseText);

                    Ext.getCmp('Koltiva.view.IMS.WinFormApprovalSelec-Form-ParPassSel').setValue(objReturn.ParPassSel);
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