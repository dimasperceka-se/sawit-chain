function checkImageExists(imageUrl, callBack) {
    var imageData = new Image();
    imageData.onload = function () {
        callBack(true);
    };
    imageData.onerror = function () {
        callBack(false);
    };
    imageData.src = imageUrl;
}
function calcTreeTbmTmTr() {
    var treeTBM = parseInt(Ext.getCmp('Koltiva.view.Refinery.WinFormSPCode-Form-TreeTBM').getValue());
    var treeTR = parseInt(Ext.getCmp('Koltiva.view.Refinery.WinFormSPCode-Form-TreeTR').getValue());
    var treeTM = parseInt(Ext.getCmp('Koltiva.view.Refinery.WinFormSPCode-Form-TreeTM').getValue());
    var GardenAreaHa = parseInt(Ext.getCmp('Koltiva.view.Refinery.WinFormSPCode-Form-GardenAreaHa').getValue());
    
    if(isNaN(treeTBM)) treeTBM = 0;
    if(isNaN(treeTR)) treeTR = 0;
    if(isNaN(treeTM)) treeTM = 0;
    if(isNaN(GardenAreaHa)) GardenAreaHa = 0;
    
    var total = treeTBM + treeTR + treeTM;
    var totalHa = (treeTBM + treeTR + treeTM)/GardenAreaHa;
    Ext.getCmp('Koltiva.view.Refinery.WinFormSPCode-Form-TotalTree').setValue(total);
    Ext.getCmp('Koltiva.view.Refinery.WinFormSPCode-Form-TotalTreeHa').setValue(totalHa);

    //validasi dengan TbmTmTr dengan planting material
//        var totPlantingMate = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').getValue());
//        if(total != totPlantingMate){
//            Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').addCls('notif-red');
//            Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').addCls('notif-red');
//        }else{
//            Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TypePlantMateTotalTreeNr').removeCls('notif-red');
//            Ext.get('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TreeTotalTBMTMTR').removeCls('notif-red');
//        }
}
Ext.define('Koltiva.view.Refinery.WinFormSPCode' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Refinery.WinFormSPCode',
    title:lang('SPB Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '30%',
    maxHeight: 700,
//    height: '50%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            Ext.getCmp('Koltiva.view.Refinery.WinFormSPCode-Form').getForm().reset();

            Ext.getCmp('Koltiva.view.Refinery.WinFormSPCode-Form-RefineryID').setValue(thisObj.viewVar.RefineryID);

            if(thisObj.viewVar.OpsiDisplay == 'update' || thisObj.viewVar.OpsiDisplay == 'view'){
                //load data form
                Ext.getCmp('Koltiva.view.Refinery.WinFormSPCode-Form').getForm().load({
                    url: m_api + '/refinery/sp_code_form',
                    method: 'GET',
                    params: {
                        SPCodeID: this.viewVar.SPCodeID,
                        RefineryID: this.viewVar.RefineryID,
                        CallFrom: this.viewVar.CallFrom
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
        }
    },
    initComponent: function() {
        var thisObj = this;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.Refinery.WinFormSPCode-Form',
            padding:'5 10 5 10',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Refinery.WinFormSPCode-Form-SPCodeID',
                        name: 'SPCodeID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Refinery.WinFormSPCode-Form-RefineryID',
                        name: 'RefineryID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Refinery.WinFormSPCode-Form-SuratNr',
                        name: 'SuratNr',
                        fieldLabel: lang('Nomor Surat'),
                        labelAlign:'top',
                        labelWidth: 185,
                        allowBlank : false
                    },{
                        xtype:'textarea',
                        fieldLabel: lang('Keterangan'),
                        labelAlign:'top',
                        id: 'Koltiva.view.Refinery.WinFormSPCode-Form-Note',
                        name: 'Note',
                        width: '100%'
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.Refinery.WinFormSPCode-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
            	var FormNya = Ext.getCmp('Koltiva.view.Refinery.WinFormSPCode-Form').getForm();
                if (FormNya.isValid()) {
                    FormNya.submit({
                        url: m_api + '/refinery/submit_sp_code',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        params: {
                            RefineryID: thisObj.viewVar.RefineryID,
                            CallFrom: thisObj.viewVar.CallFrom,
                            OpsiDisplay: thisObj.viewVar.OpsiDisplay
                        },
                        success: function(rp, o){
                            var r = Ext.decode(o.response.responseText);
                            Ext.MessageBox.show({
                                title: lang('Information'),
                                msg: lang("Data Saved"),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });
                            
                            //refresh store
                            Ext.getCmp('Koltiva.view.Refinery.SPCodePanel-gridSPCode').getStore().reload();
                            thisObj.close();
                        },
                        failure: function(rp, o){
                            try {
                                var r = Ext.decode(o.response.responseText);
                                Ext.MessageBox.show({
                                    title: lang('Error'),
                                    msg: lang('Failed to Save Data'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                            catch(err) {
                                Ext.MessageBox.show({
                                    title: lang('Error'),
                                    msg: lang('Connection Error'),
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
                        msg: lang('Form not valid yet'),
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
    }
});