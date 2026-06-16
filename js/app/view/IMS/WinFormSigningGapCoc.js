/*
* @Author: Nikolius Lau
* @Date:   2018-08-08 13:39:26
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-21 17:57:44
*/
/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinFormSigningGapCoc' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormSigningGapCoc',
    title: lang('Signing Lock - GAP & CoC Participants'),
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
            id: 'Koltiva.view.IMS.WinFormSigningGapCoc-Form',
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
	                        id: 'Koltiva.view.IMS.WinFormSigningGapCoc-Form-IMSID',
	                        name: 'Koltiva.view.IMS.WinFormSigningGapCoc-Form-IMSID',
	                        value: thisObj.viewVar.IMSID
		                },{
                        	xtype: 'textfield',
	                        name: 'Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocByRealname',
	                        id: 'Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocByRealname',
	                        fieldLabel: lang('Signing by'),
	                        readOnly: true
                        },{
                        	xtype: 'textfield',
	                        name: 'Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocDatetime',
	                        id: 'Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocDatetime',
	                        fieldLabel: lang('Timestamp'),
	                        readOnly: true
                        },{
                        	xtype:'textarea',
                            id: 'Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocRemark',
                            name: 'Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocRemark',
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
                id: 'Koltiva.view.IMS.WinFormSigningGapCoc-Form-BtnSave',
                handler: function () {
                    Ext.Ajax.request({
                        url: m_api + '/ims/signing_lock_gap_coc',
                        method: 'POST',
                        params: {
                            IMSID: thisObj.viewVar.IMSID,
                            SigningLockGapCocRemark: Ext.getCmp('Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocRemark').getValue()
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

                                    Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnApprove').setVisible(false);
                                    Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-Training-BtnGenTrainCandidate').setVisible(false);
                                    Ext.getCmp('Koltiva.view.IMS.WinImsAcqPro-Tab-TrainingApproved-BtnProcessCandidateTraining').setVisible(false);
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
                url: m_api + '/ims/signing_lock_gap_coc_form_open',
                method: 'POST',
                params: {
                    IMSID: thisObj.viewVar.IMSID
                },
                success: function(response, action) {
                	//console.log(response);
                	var objReturn = Ext.decode(response.responseText);
                	//console.log(objReturn);

                	if(objReturn.SigningLockGapCocBy == null){
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocByRealname').setVisible(false);
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocDatetime').setValue(objReturn.SigningLockGapCocDatetime);
                	}else{
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocByRealname').setVisible(true);
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocByRealname').setValue(objReturn.SigningLockGapCocByRealname);
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocDatetime').setValue(objReturn.SigningLockGapCocDatetime);
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocRemark').setValue(objReturn.SigningLockGapCocRemark);

                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningGapCoc-Form-SigningLockGapCocRemark').setReadOnly(true);
                		Ext.getCmp('Koltiva.view.IMS.WinFormSigningGapCoc-Form-BtnSave').setVisible(false);
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