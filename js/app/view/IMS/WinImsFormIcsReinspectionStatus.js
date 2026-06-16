/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Jan 29 2019
 *  File : WinImsFormIcsReinspectionStatus.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinImsFormIcsReinspectionStatus' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus',
    title: lang('IMS - ICS Reinspection'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '46%',
    height: '60%',
    cls: 'Sfr_LayoutPopupWindows',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        show: function(){
            var thisObj = this;
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form').getForm();

            FormNya.load({
                url: m_api + '/ims/ims_ics_reinspection_form_data',
                method: 'GET',
                params: {
                    IMSID: thisObj.viewVar.IMSID
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
                    thisObj.close();
                }
            });
        }
    },
    initComponent: function() {
        var thisObj = this;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-IMSID',
                        name: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-IMSID',
                        value: thisObj.viewVar.IMSID
                    },{
                        xtype: 'radiogroup',
                        allowBlank: false,
                        fieldLabel: lang('Status'),
                        labelWidth: 175,
                        columns: 2,
                        allowBlank: false,
                        id:'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-RowStatusIcsReinspect',
                        items: [{
                            boxLabel: lang('Ongoing'),
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspect1',
                            name: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspect'
                        }, {
                            boxLabel: lang('Completed'),
                            inputValue: '2',
                            id: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspect2',
                            name: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspect'
                        }]
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspectUser',
                        name: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspectUser',
                        fieldLabel: lang('Set by'),
                        labelWidth: 175,
                        readOnly: true
                    },{
                        xtype: 'textareafield',
                        id: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspectComment',
                        name: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspectComment',
                        fieldLabel: lang('Comment'),
                        labelWidth: 175,
                        height:200
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls:'Sfr_BtnFormBlue',
            overCls:'Sfr_BtnFormBlue-Hover',
            text: lang('Save'),
            id: 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-BtnSave',
            handler: function () {
                var FormNya = Ext.getCmp('Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form').getForm();
                var FormValidOrNot = FormNya.isValid();

                if (FormValidOrNot ==  true) {
                    FormNya.submit({
                        url: m_api + '/ims/ims_ics_reinspection_form',
                        method:'POST',
                        params: {
                            IMSID: thisObj.viewVar.IMSID
                        },
                        waitMsg: 'Saving data...',
                        success: function(rp, o){
                            var r = Ext.decode(o.response.responseText);

                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: r.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            switch(r.StatusIcsReinspect){
                                case '1':
                                    Ext.getCmp('imsEventDetailGridIcsReinspect_BtnAddFarmer').setDisabled(false);
                                    Ext.getCmp('imsEventDetailGridIcsReinspect_BtnRegenerateIcs').setDisabled(false);
                
                                    //Set Title
                                    Ext.getCmp('imsEventDetailGridIcsReinspect').setTitle(lang('ICS Reinspection (Ongoing)'));

                                    //Set Form IMS
                                    Ext.getCmp('StatusIcsReinspect1').setValue(true);
                                break;
                                case '2':
                                    Ext.getCmp('imsEventDetailGridIcsReinspect_BtnAddFarmer').setDisabled(true);
                                    Ext.getCmp('imsEventDetailGridIcsReinspect_BtnRegenerateIcs').setDisabled(true);
                                    Ext.getCmp('imsEventDetailGridIcsReinspect').setTitle(lang('ICS Reinspection (Completed)'));

                                    //Set Form IMS
                                    Ext.getCmp('StatusIcsReinspect2').setValue(true);
                                break;
                            }

                            thisObj.viewVar.CallerStoreAfl.load(); //load store grid 'AFL'
                            thisObj.close();
                        },
                        failure: function(rp, o){
                            try {
                                var r = Ext.decode(o.response.responseText);
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                            catch(err) {
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
                }else{
                    Ext.MessageBox.show({
                        title: 'Attention',
                        msg: 'Form not valid yet',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
			text: lang('Close'),
			cls:'Sfr_BtnFormGrey',
			overCls:'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});