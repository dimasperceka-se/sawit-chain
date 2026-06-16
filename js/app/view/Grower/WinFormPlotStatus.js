/*
* @Author: nikolius
* @Date:   2017-05-30 19:16:34
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 11:37:02
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
    2. opsiDisplay
    3. callerStoreId
    4. PlotNr
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Grower.WinFormPlotStatus' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Grower.WinFormPlotStatus',
    title: lang('Plot Status Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '35%',
    height: '50%',
    overflowY: 'auto',
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai
        var cmb_inactive_reason_plot = Ext.create('Koltiva.store.Grower.CmbInactiveReasonPlot');
        var cmb_other_commodity = Ext.create('Koltiva.store.Grower.CmbOtherCommodity');

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Grower.WinFormPlotStatus-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Grower.WinFormPlotStatus-Form-MemberID',
                        name: 'Koltiva.view.Grower.WinFormPlotStatus-Form-MemberID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Grower.WinFormPlotStatus-Form-PlotNr',
                        name: 'Koltiva.view.Grower.WinFormPlotStatus-Form-PlotNr',
                        fieldLabel: lang('Plot Nr'),
                        readOnly: true,
                        labelWidth: 175
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Grower.WinFormPlotStatus-Form-AreaHa',
                        name: 'Koltiva.view.Grower.WinFormPlotStatus-Form-AreaHa',
                        fieldLabel: lang('Area (Ha)'),
                        readOnly: true
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Grower.WinFormPlotStatus-Form-LastSurvey',
                        name: 'Koltiva.view.Grower.WinFormPlotStatus-Form-LastSurvey',
                        fieldLabel: lang('Last Survey'),
                        readOnly: true
                    },{
                        fieldLabel: lang('Active Status'),
                        xtype: 'radiogroup',
                        allowBlank: false,
                        msgTarget: 'side',
                        columns: 2,
                        items:[{
                            boxLabel: lang('Active'),
                            name: 'Koltiva.view.Grower.WinFormPlotStatus-Form-ActiveStatus',
                            inputValue: '1',
                            id: 'Koltiva.view.Grower.WinFormPlotStatus-Form-ActiveStatus1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Inactive'),
                            name: 'Koltiva.view.Grower.WinFormPlotStatus-Form-ActiveStatus',
                            inputValue: '2',
                            id: 'Koltiva.view.Grower.WinFormPlotStatus-Form-ActiveStatus2',
                            listeners:{
                                change: function(){
                                    if(this.checked == true){
                                        Ext.getCmp('Koltiva.view.Grower.WinFormPlotStatus-Form-InactiveStatus').setDisabled(false);
                                    }else{
                                        Ext.getCmp('Koltiva.view.Grower.WinFormPlotStatus-Form-InactiveStatus').setDisabled(true);
                                    }
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.Grower.WinFormPlotStatus-Form-InactiveStatus',
                        name: 'Koltiva.view.Grower.WinFormPlotStatus-Form-InactiveStatus',
                        store: cmb_inactive_reason_plot,
                        fieldLabel: lang('Inactive Reason'),
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        disabled: true,
                        listeners:{
                            change: function(cb, nv, ov) {
                                if(nv=='2'){
                                    Ext.getCmp('Koltiva.view.Grower.WinFormPlotStatus-Form-OtherCommodity').setDisabled(false);
                                }else{
                                    Ext.getCmp('Koltiva.view.Grower.WinFormPlotStatus-Form-OtherCommodity').setDisabled(true);
                                }
                                return false;
                            }
                        }
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.Grower.WinFormPlotStatus-Form-OtherCommodity',
                        name: 'Koltiva.view.Grower.WinFormPlotStatus-Form-OtherCommodity',
                        store: cmb_other_commodity,
                        fieldLabel: lang('Other Commodity'),
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        labelWidth: 175,
                        disabled: true
                    },{
                        xtype: 'textareafield',
                        id: 'Koltiva.view.Grower.WinFormPlotStatus-Form-Remark',
                        name: 'Koltiva.view.Grower.WinFormPlotStatus-Form-Remark',
                        fieldLabel: lang('Remark'),
                        grow: true,
                        anchor: '100%'
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.Grower.WinFormPlotStatus-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var formNya = Ext.getCmp('Koltiva.view.Grower.WinFormPlotStatus-Form').getForm();
                if (formNya.isValid()) {
                    formNya.submit({
                        url: m_api + '/grower/plot_status',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //form reset
                            formNya.reset();

                            //refresh store FamLab yg manggil
                            thisObj.formVar.callerStoreId.load();

                            //tutup popup
                            thisObj.close();
                        },
                        failure: function(fp, o){
                            var pesanNya;
                            if(o.result.message != undefined){
                                pesanNya = o.result.message;
                            }else{
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
                }else{
                    Ext.MessageBox.show({
                        title: 'Attention',
                        msg: lang('Form not complete yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            text: lang('Close'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.Grower.WinFormPlotStatus-Form');
            formNya.getForm().reset();

            //set MemberID
            Ext.getCmp('Koltiva.view.Grower.WinFormPlotStatus-Form-MemberID').setValue(thisObj.formVar.MemberID);

            if(thisObj.formVar.opsiDisplay == 'update' || thisObj.formVar.opsiDisplay == 'view'){
                formNya.getForm().load({
                    url: m_api + '/grower/member_plot_status_data',
                    method: 'GET',
                    params: {
                        MemberID: thisObj.formVar.MemberID,
                        PlotNr: thisObj.formVar.PlotNr
                    },
                    success: function(form, action) {
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

        }
    }
});