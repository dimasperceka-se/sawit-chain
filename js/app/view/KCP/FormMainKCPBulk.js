/*
* @Author: nikolius
* @Date:   2017-08-21 10:19:23
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-15 17:18:34
*/
/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar (MillID)
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
    function checkImageExists(imageUrl, callBack) {
        var imageData = new Image();
        imageData.onload = function() {
            callBack(true);
        };
        imageData.onerror = function() {
            callBack(false);
        };
        imageData.src = imageUrl;
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)
function init_map() {
    var lat = Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Latitude').getValue();
    var longs = Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Longitude').getValue();
	 
    if (Math.abs(lat) > 0 && Math.abs(longs)) {
        $('#map').gmap3({
            map: {
                options: {
                    center: [lat, longs],
                    zoom: 14,
                    //mapTypeControl: false,
                    panControl: true,
                    zoomControl: true,
                    //scaleControl: false,
                    streetViewControl: false,
                    rotateControl: false,
                    rotateControlOptions: false,
                    overviewMapControl: false,
                    OverviewMapControlOptions: false,
                    scrollwheel: true
                }
            },
            marker: {
                latLng:[lat, longs]
            }
        }); 
    }
}

Ext.define('Koltiva.view.KCP.FormMainKCPBulk' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.KCP.FormMainKCPBulk',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this;
        var ObjPanelKanan = [];
        
        //store yg dipakai (begin)
        var cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        cmb_province.load();
        var cmb_district = Ext.create('Koltiva.store.Grower.CmbDistrict');
        var cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        var cmb_village = Ext.create('Koltiva.store.Grower.CmbVillage');

        var cmb_year_option = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption',{
            storeVar: {
                yearRange: 80
            }
        });

        var cmb_role_kcp = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"id":"kcp", "label":lang('KCP')},
                {"id":"bulking", "label":lang('Bulking')}
            ]
        });
        
        var cmb_legalstatus = Ext.create('Koltiva.store.ComboGeneral.CmbLegalStatus');
        var MainGridProducts = Ext.create('Koltiva.view.Mill.GridProduct');
        //store yg dipakai (end)

        //Panel Basic Data ================================================================================================= (Begin)
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('KCP / Bulking Data'),
            frame: true,
            id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData',
            fileUpload: true,
            margin:'0 0 20 0',
            padding: '10 0 0 0',
            items: [{
                xtype: 'tabpanel',
                flex: 1,
                margin: 2,
                activeTab: 0,
                plain: true,
                cls:'tabSce',
                id: 'Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-TabPanel',
                items:[{
                    xtype: 'panel',
                    title: lang('General'),
                    padding: '0 0 0 5',
                    items:[{
                        columnWidth: 1,
                        layout:'form',
                        items:[{
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 0.495,
                                layout:'form',
                                style:'margin-right:10px;',
                                items:[{
                                    xtype: 'panel',
                                    title: lang('Basic Data'),
                                    frame: false,
                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-GeneralSection',
                                    style: 'margin-top:10px;',
                                    cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-KCPID',
                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-KCPID',
                                    inputType: 'hidden'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-SupplychainID',
                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-SupplychainID',
                                    inputType: 'hidden'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-KCPDisplayID',
                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-KCPDisplayID',
                                    fieldLabel: 'DisplayID',
                                    readOnly:true,
                                    labelAlign:'top'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-KCPName',
                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-KCPName',
                                    fieldLabel: lang('Name'),
                                    allowBlank: false,
                                    labelAlign:'top'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-CompanyName',
                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-CompanyName',
                                    fieldLabel: lang('Company Name'),
                                    allowBlank: false,
                                    labelAlign:'top'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Alias',
                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Alias',
                                    fieldLabel: lang('Alias'),
                                    allowBlank: false,
                                    labelAlign:'top'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-KCPRole',
                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-KCPRole',
                                    store: cmb_role_kcp,
                                    fieldLabel: lang('Role'),
                                    labelAlign:'top',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    allowBlank: false,
                                    valueField: 'id'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Year',
                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Year',
                                    store: cmb_year_option,
                                    fieldLabel: lang('Year Established'),
                                    labelAlign:'top',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id'
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Status',
                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Status',
                                    store: cmb_legalstatus,
                                    fieldLabel: lang('Legal Status of the Company'),
                                    labelAlign:'top',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id'
                                },{
                                    html:'<div></div>'
                                },{
                                    layout:'column',
                                    border:false,
                                    style:'margin-top:-20px',
                                    items:[{
                                        columnWidth: 1,
                                        border: false,
                                        layout:'form',
                                        items:[{
                                        }]
                                    }]
                                }]
                            },{
                                columnWidth: 0.5,
                                border: false,
                                layout:'form',
                                items:[{
                                    title: lang('Address and Location'),
                                    frame: false,
                                    id: 'Koltiva.view.Mill.MainForm-FormBasicData-GeneralSection',
                                    style: 'margin-top:10px;',
                                    cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                },{
                                    html:'<div></div>'
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        layout:'form',
                                        style: 'margin-right: 5px',
                                        items:[{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Province',
                                            name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Province',
                                            store: cmb_province,
                                            fieldLabel: lang('Province'),
                                            labelAlign:'top',
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    cmb_district.load({
                                                        params: {
                                                            ProvinceID: nv
                                                        }
                                                    });
                                                    Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-District').setValue('');
                                                    Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Subdistrict').setValue('');
                                                    Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Village').setValue('');
                                                }
                                            }
                                        },{
                                            html:'<div></div>'
                                        },{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-District',
                                            name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-District',
                                            store: cmb_district,
                                            fieldLabel: lang('District'),
                                            labelAlign:'top',
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    cmb_subdistrict.load({
                                                        params: {
                                                            DistrictID: nv
                                                        }
                                                    });
                                                    Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Subdistrict').setValue('');
                                                    Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Village').setValue('');
                                                }
                                            }
                                        },{
                                            html:'<div></div>'
                                        },{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Subdistrict',
                                            name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Subdistrict',
                                            store: cmb_subdistrict,
                                            fieldLabel: lang('Subdistrict'),
                                            labelAlign:'top',
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    cmb_village.load({
                                                        params: {
                                                            SubdistrictID: nv
                                                        }
                                                    });
                                                    Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Village').setValue('');
                                                }
                                            }
                                        },{
                                            html:'<div></div>'
                                        },{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Village',
                                            name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Village',
                                            store: cmb_village,
                                            fieldLabel: lang('Village'),
                                            labelAlign:'top',
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            allowBlank: false
                                        },{
                                            html:'<div></div>'
                                        }]
                                    },{
                                        columnWidth: 1,
                                        layout:'form',
                                        style: 'margin-left: 5px',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Phone',
                                            name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Phone',
                                            fieldLabel: lang('Phone'),
                                            labelAlign:'top'
                                        },{
                                            html:'<div></div>'
                                        },{
                                            xtype: 'textarea',
                                            fieldLabel: lang('Address'),
                                            labelAlign:'top',
                                            id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Address',
                                            name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Address',
                                            height: 125
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 0.495,
                                                layout:'form',
                                                style: 'margin-right: 5px',
                                                items:[{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Latitude',
                                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Latitude',
                                                    labelAlign: 'top',
                                                    fieldLabel: lang('Latitude')
                                                }]
                                            },{
                                                columnWidth: 0.495,
                                                layout:'form',
                                                style: 'margin-left: 5px',
                                                items:[{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Longitude',
                                                    name: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Longitude',
                                                    labelAlign: 'top',
                                                    fieldLabel: lang('Longitude')
                                                }]
                                            }]
                                        }]
                                    }]
                                }]
                            }]
                        }]
                    },{
                        html:'<div></div>'
                    },{
                        layout:'column',
                        border:false,
                        style:'margin-top:10px',
                        items:[{
                            xtype: 'component',
                            autoEl: {
                                html: '<div id="map" style="width:100%;height:250px;background:#e1e1e1;border:1px solid #e1e1e1;border-radius: 1%"></div>',
                                style:'width:100%;'
                            }	
                        }] 
                    }]
                }]
            }],
            buttons: [{
                text: lang('Save'),
                id: 'Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-btnSaveForm',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    if (objPanelBasicData.isValid()) {
                        objPanelBasicData.submit({
                            url: m_api + '/kcp_bulk/data',
                            method:'POST',
                            waitMsg: 'Saving data...',
                            success: function(fp, o) {
                                //console.log(o);
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: lang('Data saved'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk').destroy(); //destory current view
                                //create object View untuk FormMainTrader
                                if(Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk') == undefined){
                                    var FormMainTrader = Ext.create('Koltiva.view.KCP.FormMainKCPBulk', {
                                        opsiDisplay: 'update',
                                        viewVar: {
                                            KCPID: o.result.KCPID
                                        }
                                    });
                                }else{
                                    //destroy, create ulang
                                    Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk').destroy();
                                    var FormMainTrader = Ext.create('Koltiva.view.KCP.FormMainKCPBulk', {
                                        opsiDisplay: 'update',
                                        viewVar: {
                                            KCPID: o.result.KCPID
                                        }
                                    });
                                }
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
            }]
        });
        //Panel Basic Data ================================================================================================= (End)

        //isi layout utama ================================================================================================= (Begin)
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                html:'<h3 style="margin:0px;padding:0px;">'+lang('Data')+'</h3>'
            },{
                id: 'Koltiva.view.KCP.FormMainKCPBulk-labelInfoInsert',
                html:'',
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to KCP/Bulking List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk').destroy(); //destory current view
                        if(Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking') == undefined){
                            var GridMainTrader = Ext.create('Koltiva.view.KCP.GridMainKCPBulking');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking').destroy();
                            var GridMainTrader = Ext.create('Koltiva.view.KCP.GridMainKCPBulking');
                        }
                    }
                }
            }
        },{
            html:'<br />'
        },{
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 0.6,
                items:[
                    objPanelBasicData
                ]
            },{
                //RIGHT CONTENT
                columnWidth: 0.4,
                items: ObjPanelKanan
            }]
        }];
        //isi layout utama ================================================================================================= (End)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'none';

            //Khusus WAGS
            if(m_partner == '14'){
                Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-MillGroup').setVisible(false);
            }

            //insert
            if(thisObj.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-labelInfoInsert').update('<h5 style="margin:8px 0 0 15px;padding:0px;">('+lang('Add New Data')+')</h5>');
                            
                //form reset
                Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData').getForm().reset();

                // thisObj.ObjPanelSPCode.collapse();
                // thisObj.ObjPanelSPCode.setViewVar({
                //     MillID:null
                // });

                // var grid_sp_code_panel = Ext.data.StoreManager.lookup('Koltiva.store.Mill.GridSPCodepanel');
                // grid_sp_code_panel.setStoreVar({MillID:null});
                // grid_sp_code_panel.load();

            }

            //update
            if(thisObj.opsiDisplay == 'update' || thisObj.opsiDisplay == 'view'){
                Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-labelInfoInsert').update('');
                
                //form reset
                Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData').getForm().reset();

                //load data form
                Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData').getForm().load({
                    url: m_api + '/kcp_bulk/data',
                    method: 'GET',
                    params: {
                        KCPID: this.viewVar.KCPID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);

                        //untuk handle combo bertingkat
                        var cmb_province = Ext.data.StoreManager.lookup('store.Grower.CmbProvince');
                        var cmb_district = Ext.data.StoreManager.lookup('store.Grower.CmbDistrict');
                        var cmb_subdistrict = Ext.data.StoreManager.lookup('store.Grower.CmbSubdistrict');
                        var cmb_village = Ext.data.StoreManager.lookup('store.Grower.CmbVillage');
                        cmb_province.load({
                            callback: function(records, operation, success){
                                Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Province').setValue(r.data.Province);
                                if (success == true) {
                                    cmb_district.load({
                                        params: {
                                            ProvinceID: r.data.Province
                                        },
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-District').setValue(r.data.District);
                                                cmb_subdistrict.load({
                                                    params: {
                                                        DistrictID: r.data.District
                                                    },
                                                    callback: function(records, operation, success){
                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Subdistrict').setValue(r.data.Subdistrict);
                                                            cmb_village.load({
                                                                params: {
                                                                    SubdistrictID: r.data.Subdistrict
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-Village').setValue(r.data.Village);
                                                                    }
                                                                }
                                                            });
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                        });

                        if(thisObj.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-btnSaveForm').setVisible(false);
                            Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-MemberPhotoInput').setVisible(false);
                            Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-LocationPhotoInput').setVisible(false);
                        }
                        init_map();//gmaps3
                    },failure: function(form, action) {
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