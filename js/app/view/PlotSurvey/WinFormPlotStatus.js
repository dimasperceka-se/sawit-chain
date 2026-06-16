/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue May 07 2019
 *  File : WinFormPlotStatus.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - MemberID
    - PlotNr
	- OpsiDisplay
    - CallerStore
    - CallFrom
*/
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
    var treeTBM = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTBM').getValue());
    var treeTR = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTR').getValue());
    var treeTM = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTM').getValue());
    var GardenAreaHa = parseInt(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-GardenAreaHa').getValue());
    
    if(isNaN(treeTBM)) treeTBM = 0;
    if(isNaN(treeTR)) treeTR = 0;
    if(isNaN(treeTM)) treeTM = 0;
    if(isNaN(GardenAreaHa)) GardenAreaHa = 0;
    
    var total = treeTBM + treeTR + treeTM;
    var totalHa = (treeTBM + treeTR + treeTM)/GardenAreaHa;
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TotalTree').setValue(total);
    Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TotalTreeHa').setValue(totalHa);

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
Ext.define('Koltiva.view.PlotSurvey.WinFormPlotStatus' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus',
    title:lang('Plantation Status Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '72%',
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
            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form').getForm().reset();
            
            if (thisObj.viewVar.CallFrom == 'Mill') {
                
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-PlantedAreaHa').setVisible(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTM').setVisible(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTBM').setVisible(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTR').setVisible(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TotalTree').setVisible(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TotalTree').setReadOnly(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TotalTreeHa').setVisible(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TotalTreeHa').setReadOnly(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-ColumnFarmPhoto').setVisible(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoInput').setVisible(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoDesc').setVisible(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-ColumnLabelRemaks').setVisible(true);
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-ColumnComment').setVisible(true);
            }
            
            if(thisObj.viewVar.OpsiDisplay == 'insert'){
                //load data form
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form').getForm().load({
                    url: m_api + '/plot_survey/plantation_status_member',
                    method: 'GET',
                    params: {
                        MemberID: this.viewVar.MemberID
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

            if(thisObj.viewVar.OpsiDisplay == 'update' || thisObj.viewVar.OpsiDisplay == 'view'){
                //load data form
                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form').getForm().load({
                    url: m_api + '/plot_survey/plantation_status_form_data',
                    method: 'GET',
                    params: {
                        MemberID: this.viewVar.MemberID,
                        PlotNr: this.viewVar.PlotNr,
                        CallFrom: this.viewVar.CallFrom
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);

                        if(thisObj.viewVar.OpsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-MemberName').readOnly = true;
                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-PlotNr').readOnly = true;
                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-GardenAreaHa').readOnly = true;
                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-GardenAreaPolygon').readOnly = true;
                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-AnnualProduction').readOnly = true;
                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-Latitude').readOnly = true;
                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-Longitude').readOnly = true;
                            
                            if (thisObj.viewVar.CallFrom == 'Mill') {
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-PlantedAreaHa').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTM').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTBM').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTR').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoInput').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoDesc').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-Comment').setReadOnly(true);
                            }
                            
                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-BtnSave').setVisible(false);
                        }
                        if (thisObj.viewVar.CallFrom == 'Mill') {
                            //photo
                            Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoOld').setValue(r.data.FarmPhotoPath);
                            if (r.data.FarmPhoto != "") {
                                var fotoUser = r.data.FarmPhoto;
                                //console.log(fotoUser);
                                checkImageExists(fotoUser, function (existsImage) {
                                    if (existsImage == true) {
//                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoOld').setValue(r.data.FarmPhoto);
                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhoto').setSrc(fotoUser);
                                    } else {
                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhoto').setSrc(m_api_base_url + '/images/no-image-icon.png');
                                    }
                                });
                            }
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
        }
    },
    initComponent: function() {
        var thisObj = this;

        var cmb_inactive_reason_garden = Ext.create('Koltiva.store.PlotSurvey.CmbInactiveReasonGarden');

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 0.5,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-MemberID',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-MemberID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-MemberDisplayID',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-MemberDisplayID',
                        fieldLabel: lang('ID'),
                        readOnly: true,
                        labelWidth: 185
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-MemberName',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-MemberName',
                        fieldLabel: lang('Name'),
                        readOnly: true,
                        labelWidth: 185
                    },{
                        xtype: 'numericfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-PlotNr',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-PlotNr',
                        fieldLabel: lang('Plantation Nr'),
                        readOnly: true,
                        labelWidth: 185,
                        allowNegative: false,
                        minValue: 0,
                        maxValue: 15
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-GardenAreaHa',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-GardenAreaHa',
                        fieldLabel: lang('Size (ha)'),
                        labelWidth: 185,
                        listeners: {
                            change: function () {
                                calcTreeTbmTmTr();
                                return false;
                            }
                        }
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-GardenAreaPolygon',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-GardenAreaPolygon',
                        fieldLabel: lang('Size Polygon (ha)'),
                        labelWidth: 185
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-AnnualProduction',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-AnnualProduction',
                        fieldLabel: lang('Annual Production (TON)'),
                        labelWidth: 185
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-PlantedAreaHa',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-PlantedAreaHa',
                        fieldLabel: lang('Planted Area (ha)'),
                        hidden: true,
                        labelWidth: 185
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTM',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTM',
                        fieldLabel: lang('Number of Productive Trees'),
                        hidden: true,
                        labelWidth: 185,
                        listeners: {
                            change: function () {
                                calcTreeTbmTmTr();
                                return false;
                            }
                        }
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTBM',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTBM',
                        fieldLabel: lang('Number of Non Productive Trees'),
                        hidden: true,
                        labelWidth: 185,
                        listeners: {
                            change: function () {
                                calcTreeTbmTmTr();
                                return false;
                            }
                        }
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTR',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TreeTR',
                        fieldLabel: lang('Number of Broken Trees'),
                        hidden: true,
                        labelWidth: 185,
                        listeners: {
                            change: function () {
                                calcTreeTbmTmTr();
                                return false;
                            }
                        }
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TotalTree',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TotalTree',
                        fieldLabel: lang('Total of Trees'),
                        hidden: true,
                        readonly: true,
                        labelWidth: 185,
                        allowNegative: false,
                        minValue: 0
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TotalTreeHa',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-TotalTreeHa',
                        fieldLabel: lang('Total Trees per hectare (Tree/Ha)'),
                        hidden: true,
                        readonly: true,
                        labelWidth: 185
                    }]
                },{
                    columnWidth: 0.5,
                    layout:'form',
                    style:'padding-left:12px;',
                    items:[{
                        fieldLabel: lang('Plantation Status'),
                        labelWidth: 200,
                        xtype: 'radiogroup',
                        allowBlank: false,
                        msgTarget: 'side',
                        columns: 2,
                        items:[{
                            boxLabel: lang('Active'),
                            name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-ActiveStatus',
                            inputValue: '1',
                            id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-ActiveStatus1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Inactive'),
                            name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-ActiveStatus',
                            inputValue: '2',
                            id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-ActiveStatus2',
                            listeners:{
                                change: function(){
                                	if(this.checked == true){
                                		Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-NotActiveReason').setDisabled(false);
                                	}else{
                                		Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-NotActiveReason').setDisabled(true);
                                	}
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-NotActiveReason',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-NotActiveReason',
                        store: cmb_inactive_reason_garden,
                        fieldLabel: lang('Inactive Reason'),
                        labelWidth: 200,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        disabled: true
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-Latitude',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-Latitude',
                        fieldLabel: lang('Latitude'),
                        labelWidth: 200
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-Longitude',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-Longitude',
                        fieldLabel: lang('Longitude'),
                        labelWidth: 200
                    },{
                        layout: 'column',
                        border: false,
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-ColumnFarmPhoto',
                        hidden: true,
                        items: [{
                                columnWidth: 1,
                                border: false,
                                layout: {
                                    type: 'hbox',
                                    pack: 'end'
                                },
                                items: [{
                                        xtype: 'image',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhoto',
                                        width: '300px',
                                        height: '200px',
                                        src: m_api_base_url + '/images/no-image-icon.png'
                                    }, {
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoOld',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoOld',
                                        inputType: 'hidden'
                                    }]
                            }]
                    }, {
                        xtype: 'fileuploadfield',
                        fieldLabel: lang('Farm Photo'),
                        labelWidth: 230,
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoInput',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoInput',
                        hidden: true,
                        buttonText: 'Browse',
                        listeners: {
                            'change': function (fb, v) {
                                Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form').getForm().submit({
                                    url: m_api + '/plot_survey/farm_photo_mill',
                                    clientValidation: false,
                                    params: {
                                        opsiDisplay: thisObj.viewVar.OpsiDisplay,
                                        MemberID: thisObj.viewVar.MemberID,
                                        PlotNr: Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-PlotNr').getValue()
                                    },
                                    waitMsg: 'Sending Photo...',
                                    success: function (fp, o) {
                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhoto').setSrc(o.result.file);
                                        Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoOld').setValue(o.result.filepath);
                                    }
                                });
                            }
                        }
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoDesc',
                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-FarmPhotoDesc',
                        fieldLabel: lang('Photo Comment'),
                        hidden: true,
                        labelWidth: 230,
                    },{
                        layout: 'column',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-ColumnLabelRemaks',
                        border: false,
                        hidden: true,
                        items:[{
                                columnWidth: 1,
                                layout:'form',
                                items:[{
                                        xtype:'label',
                                        cls: 'x-form-item-label',
                                        text: lang('Remarks for the farm')
                                    }]
                            }]
                    },{
                        layout: 'column',
                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-ColumnComment',
                        border: false,
                        hidden: true,
                        style:'margin-top:-16px;padding-top:0px;',
                        items:[{
                                layout:'column',
                                columnWidth: 1,
                                style:'margin-top:0px;padding-top:0px;',
                                items:[{
                                        columnWidth: 1,
                                        xtype:'textarea',
                                        id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-Comment',
                                        name: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-Comment',
                                        width: '100%'
                                    }]
                            }]
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.PlotSurvey.WinFormPlotStatus-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
            	var FormNya = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotStatus-Form').getForm();
                if (FormNya.isValid()) {
                    FormNya.submit({
                        url: m_api + '/plot_survey/plantation_status',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        params: {
                            CallFrom: thisObj.viewVar.CallFrom,
                            OpsiDisplay: thisObj.viewVar.OpsiDisplay
                        },
                        success: function(rp, o){
                            var r = Ext.decode(o.response.responseText);
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: r.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });
                            
                            //load store CallerStore
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