/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jan 28 2019
 *  File : WinImsFormFinalizationPeriod.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinImsFormFinalizationPeriod' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod',
    title: lang('IMS Finalization Period Form'),
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
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form').getForm();

            FormNya.load({
                url: m_api + '/ims/ims_finalization_period_form_data',
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
            id: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-IMSID',
                        name: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-IMSID',
                        value: thisObj.viewVar.IMSID
                    },{
                        xtype: 'radiogroup',
                        allowBlank: false,
                        fieldLabel: lang('Finalization Period'),
                        labelWidth: 175,
                        columns: 2,
                        allowBlank: false,
                        id:'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-RowStatusImsFinalPeriod',
                        items: [{
                            boxLabel: lang('Ongoing'),
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriod1',
                            name: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriod'
                        }, {
                            boxLabel: lang('Completed'),
                            inputValue: '2',
                            id: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriod2',
                            name: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriod'
                        }]
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriodUser',
                        name: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriodUser',
                        fieldLabel: lang('Verified by'),
                        labelWidth: 175,
                        readOnly: true
                    },{
                        xtype: 'textareafield',
                        id: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriodComment',
                        name: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriodComment',
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
            id: 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-BtnSave',
            handler: function () {
                var FormNya = Ext.getCmp('Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form').getForm();
                var FormValidOrNot = FormNya.isValid();

                if (FormValidOrNot ==  true) {
                    FormNya.submit({
                        url: m_api + '/ims/ims_finalization_period',
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

                            thisObj.viewVar.CallerStore.load();
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