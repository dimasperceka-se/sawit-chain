/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Dec 03 2018
 *  File : WinFormImsTrainingCetakDaftarHadir.js
 *******************************************/

/**
    Param2 yg diperlukan ketika load View ini
    - TrainingID
    - Type
    - TrainingDays
 */

Ext.define('Koltiva.view.IMS.WinFormImsTrainingCetakDaftarHadir' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsTrainingCetakDaftarHadir',
    title: lang('IMS - Print Daftar Hadir Training'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '28%',
    height: '22%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.CmbTrainingDayNumber = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api+'/cpg/data_DayNumber',
                extraParams: {
                    dayNumber: thisObj.viewVar.TrainingDays
                },
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormImsTrainingCetakDaftarHadir-Form',
            padding:'5 18 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    defaults: {
                        labelWidth: 140
                    },
                    items:[{
                        xtype: 'combo',
                        store: thisObj.CmbTrainingDayNumber,
                        displayField: 'id',
                        valueField: 'id',
                        fieldLabel: lang('Day Number'),
                        baseCls: 'Sfr_FormInputMandatory',
                        id: 'Koltiva.view.IMS.WinFormImsTrainingCetakDaftarHadir-Form-DayNumber',
                        name: 'Koltiva.view.IMS.WinFormImsTrainingCetakDaftarHadir-Form-DayNumber',
                        queryMode: 'local',
                        allowBlank: false
                    }]
                }]
            }]
        }];

        //Button
        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/printout.png',
                text: lang('Print'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormGreen',
                overCls: 'Sfr_BtnFormGreen-Hover',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCetakDaftarHadir-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();
                    var UrlCetak;

                    if (FormValidOrNot == true) {
                        switch (thisObj.viewVar.Type) {
                            case 'CpgTraining':
                                UrlCetak = m_api + '/cpg/cetak/' + thisObj.viewVar.TrainingID + '/DayNumber/' + Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingCetakDaftarHadir-Form-DayNumber').getValue();

                                preview_cetak_surat(UrlCetak);
                                break;
                            default:
                                Ext.MessageBox.show({
                                    title: 'Attention',
                                    msg: 'Type not found',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-info'
                                });
                                break;
                        }

                    } else {
                        Ext.MessageBox.show({
                            title: 'Attention',
                            msg: 'Form not valid yet',
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

        this.callParent(arguments);
    }
});