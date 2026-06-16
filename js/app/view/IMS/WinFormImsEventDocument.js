/*
* @Author: nikolius
* @Date:   2018-07-16 13:49:37
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-23 13:10:06
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
    - DocEveID
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinFormImsEventDocument' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsEventDocument',
    title: lang('IMS Event Documents - Update Form'),
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
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsEventDocument-Form');

            FormNya.getForm().load({
                url: m_api + '/ims/ims_documents_event_form_data',
                method: 'GET',
                params: {
                    IMSID: thisObj.viewVar.IMSID,
                    DocEveID: thisObj.viewVar.DocEveID
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
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
            id: 'Koltiva.view.IMS.WinFormImsEventDocument-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-IMSID',
                        name: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-IMSID',
                        value: thisObj.viewVar.IMSID
                    },{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-DocEveID',
                        name: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-DocEveID',
                        value: thisObj.viewVar.DocEveID
                    },{
                    	xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-DocumentName',
                        name: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-DocumentName',
                        fieldLabel: lang('Document'),
                        labelWidth: 250,
                        readOnly: true
                    },{
                    	fieldLabel: lang('Check Status'),
                    	labelWidth: 200,
                        xtype: 'radiogroup',
                        columns: 2,
                        allowBlank: false,
                        msgTarget:'side',
                        items:[{
                            boxLabel: lang('File uploaded'),
                            name: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-StatusCheck',
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-StatusCheck1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('No file yet'),
                            name: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-StatusCheck',
                            inputValue: '2',
                            id: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-StatusCheck2',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    },{
                    	xtype: 'datefield',
                        id: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-DateCheck',
                        name: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-DateCheck',
                        fieldLabel: lang('Check Date'),
                        allowBlank: false,
                        format: 'Y-m-d H:i:s'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-DocFile',
                        name: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-DocFile',
                        fieldLabel: lang('Document File (One Drive)'),
                        labelWidth: 250
                    },{
                    	xtype: 'textarea',
                        fieldLabel: lang('Remark'),
                        id: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-Remark',
                        name: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-Remark',
                        height: 75
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
                cls: 'Sfr_BtnFormGreen',
                overCls: 'Sfr_BtnFormGreen-Hover',
                id: 'Koltiva.view.IMS.WinFormImsEventDocument-Form-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsEventDocument-Form').getForm();

                    if (FormNya.isValid()) {
                        FormNya.submit({
                            url: m_api + '/ims/ims_documents_event',
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