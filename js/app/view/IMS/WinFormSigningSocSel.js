/*
* @Author: Nikolius Lau
* @Date:   2018-08-07 16:31:19
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-21 17:39:32
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinFormSigningSocSel' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormSigningSocSel',
    title: lang('Signing Lock - Selection Participants'),
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

        //items ---------------------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormSigningSocSel-Form',
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
                        columnWidth: 1,
                        layout: 'form',
                        items: [{
	                    	xtype: 'hiddenfield',
	                        id: 'Koltiva.view.IMS.WinFormSigningSocSel-Form-IMSID',
	                        name: 'Koltiva.view.IMS.WinFormSigningSocSel-Form-IMSID',
	                        value: thisObj.viewVar.IMSID
		                },{
                        	xtype: 'textfield',
	                        name: 'Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelByRealname',
	                        id: 'Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelByRealname',
	                        fieldLabel: lang('Signing by'),
	                        readOnly: true
                        },{
                        	xtype: 'textfield',
	                        name: 'Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelDatetime',
	                        id: 'Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelDatetime',
	                        fieldLabel: lang('Timestamp'),
	                        readOnly: true
                        },{
                        	xtype:'textarea',
                            id: 'Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelRemark',
                            name: 'Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelRemark',
                            fieldLabel: lang('Remark')
                        }]
                    }]
                }]
            }]
        }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (end)

        //buttons ---------------------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.buttons = [{
                text: lang('Sign Lock'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinFormSigningSocSel-Form-BtnSave',
                handler: function () {
                    Ext.Ajax.request({
                        url: m_api + '/ims/signing_lock_soc_sel',
                        method: 'POST',
                        params: {
                            IMSID: thisObj.viewVar.IMSID,
                            SigningLockSocSelRemark: Ext.getCmp('Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelRemark').getValue()
                        },
                        success: function (response, action) {
                            //console.log(response);
                            var objReturn = Ext.decode(response.responseText);

                            switch (objReturn.success_val) {
                                case true:
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Signing Lock Success'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb7',
                                        icon: 'ext-mb-success'
                                    });

                                    Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-BtnApprove').setVisible(false);
                                    Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnGenSocSel').setVisible(false);
                                    Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Selection-BtnImportCertFarmer').setVisible(false);
                                    Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-SelectionApproved-BtnProcessCandidateSelection').setVisible(false);
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
                url: m_api + '/ims/signing_lock_soc_sel_form_open',
                method: 'POST',
                params: {
                    IMSID: thisObj.viewVar.IMSID
                },
                success: function(response, action) {
                	//console.log(response);
                	var objReturn = Ext.decode(response.responseText);
                	//console.log(objReturn);

                	if(objReturn.SigningLockSocSelBy == null){
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelByRealname').setVisible(false);
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelDatetime').setValue(objReturn.SigningLockSocSelDatetime);
                	}else{
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelByRealname').setVisible(true);
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelByRealname').setValue(objReturn.SigningLockSocSelByRealname);
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelDatetime').setValue(objReturn.SigningLockSocSelDatetime);
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelRemark').setValue(objReturn.SigningLockSocSelRemark);

                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningSocSel-Form-SigningLockSocSelRemark').setReadOnly(true);
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningSocSel-Form-BtnSave').setVisible(false);
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