/*
* @Author: nikolius
* @Date:   2018-07-13 16:04:59
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-23 13:09:52
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSMasterID
    - DocMasID
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinFormImsMasterDocument' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsMasterDocument',
    title: lang('IMS Master Documents - Update Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '46%',
    height: '50%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsMasterDocument-Form');

            FormNya.getForm().load({
                url: m_api + '/ims/ims_documents_master_form_data',
                method: 'GET',
                params: {
                    IMSMasterID: thisObj.viewVar.IMSMasterID,
                    DocMasID: thisObj.viewVar.DocMasID
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);

                    if(r.data.StatusLock == '1'){
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsMasterDocument-Form-BtnSave').setVisible(false);
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
    },
    initComponent: function() {
        var thisObj = this;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-IMSMasterID',
                        name: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-IMSMasterID',
                        value: thisObj.viewVar.IMSMasterID
                    },{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-DocMasID',
                        name: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-DocMasID',
                        value: thisObj.viewVar.DocMasID
                    },{
                    	xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-DocumentName',
                        name: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-DocumentName',
                        fieldLabel: lang('Document'),
                        labelWidth: 250,
                        readOnly: true
                    },{
                    	fieldLabel: lang('Check Status'),
                    	labelWidth: 250,
                        xtype: 'radiogroup',
                        columns: 2,
                        allowBlank: false,
                        msgTarget:'side',
                        items:[{
                            boxLabel: lang('File uploaded'),
                            name: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-StatusCheck',
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-StatusCheck1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('No file yet'),
                            name: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-StatusCheck',
                            inputValue: '2',
                            id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-StatusCheck2',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    },{
                    	xtype: 'datefield',
                        id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-DateCheck',
                        name: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-DateCheck',
                        fieldLabel: lang('Check Date'),
                        allowBlank: false,
                        format: 'Y-m-d H:i:s'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-DocFile',
                        name: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-DocFile',
                        fieldLabel: lang('Document File (One Drive)'),
                        labelWidth: 250
                    },{
                    	xtype: 'textarea',
                        fieldLabel: lang('Notes'),
                        id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-Remark',
                        name: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-Remark',
                        height: 75
                    },{
                        fieldLabel: lang('Lock Status'),
                    	labelWidth: 250,
                        xtype: 'radiogroup',
                        columns: 2,
                        allowBlank: false,
                        msgTarget:'side',
                        items:[{
                            boxLabel: lang('Yes'),
                            name: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-StatusLock',
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-StatusLock1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('No'),
                            name: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-StatusLock',
                            inputValue: '2',
                            id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-StatusLock2',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    }]
                }]
            }]
        }]
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinFormImsMasterDocument-Form-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsMasterDocument-Form').getForm();
                    if (FormNya.isValid()) {
                        FormNya.submit({
                            url: m_api + '/ims/ims_documents_master',
                            method: 'POST',
                            waitMsg: 'Saving data...',
                            success: function (fp, o) {
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: lang('Data saved'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                //refresh store yg manggil
                                thisObj.viewVar.CallerStore.load();

                                //tutup popup
                                thisObj.close();
                            },
                            failure: function (fp, o) {
                                var pesanNya;
                                if (o.result.message != undefined) {
                                    pesanNya = o.result.message;
                                } else {
                                    pesanNya = lang('Connection error');
                                }
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: pesanNya,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    } else {
                        Ext.MessageBox.show({
                            title: 'Attention',
                            msg: lang('Form not complete yet'),
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
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});