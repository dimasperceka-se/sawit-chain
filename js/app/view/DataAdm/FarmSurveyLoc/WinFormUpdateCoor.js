/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 08-01-2020
 *  File : WinFormUpdateCoor.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - FarmerID
    - GardenNr
    - SurveyNr
*/

Ext.define('Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Update Coordinates'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '40%',
    height: '40%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        var labelWidth = 200;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'textfield',
                        id: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-FarmerID',
                        name: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-FarmerID',
                        fieldLabel: lang('Farmer ID'),
                        readOnly: true,
                        labelWidth: labelWidth
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-GardenNr',
                        name: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-GardenNr',
                        fieldLabel: lang('GardenNr'),
                        readOnly: true,
                        labelWidth: labelWidth
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-SurveyNr',
                        name: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-SurveyNr',
                        fieldLabel: lang('SurveyNr'),
                        readOnly: true,
                        labelWidth: labelWidth
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-Latitude',
                        name: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-Latitude',
                        fieldLabel: lang('Latitude'),
                        allowBlank: false,
                        labelWidth: labelWidth
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-Longitude',
                        name: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-Longitude',
                        fieldLabel: lang('Longitude'),
                        allowBlank: false,
                        labelWidth: labelWidth
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
            id: 'Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-BtnSave',
            handler: function () {
                var FormSubmit = Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form').getForm();
                if (FormSubmit.isValid()) {

                    FormSubmit.submit({
                        url: m_api + '/data_adm/farm_survey_loc/update_coor',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            //form reset
                            FormSubmit.reset();

                            //tutup popup
                            thisObj.close();

                            //trigger button show data
                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-BtnShowData').fireHandler(); //buat trigger click event
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
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-FarmerID').setValue(thisObj.viewVar.FarmerID);
            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-GardenNr').setValue(thisObj.viewVar.GardenNr);
            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form-SurveyNr').setValue(thisObj.viewVar.SurveyNr);

            //load data
            var FormNya = Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor-Form');
            FormNya.getForm().load({
                url: m_api + '/data_adm/farm_survey_loc/update_coor_form_data',
                method: 'GET',
                params: {
                    FarmerID: thisObj.viewVar.FarmerID,
                    GardenNr: thisObj.viewVar.GardenNr,
                    SurveyNr: thisObj.viewVar.SurveyNr
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                },
                failure: function(form, action) {
                    Ext.MessageBox.show({
                        title: lang('Failed'),
                        msg: lang('Failed to retrieve data'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });

        }
    }
});